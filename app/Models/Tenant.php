<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Tenant extends Model
{
    protected $fillable = [
        'name', 'slug', 'email', 'phone', 'website', 'address', 'industry',
        'plan', 'status', 'max_users', 'max_contacts', 'currency', 'timezone',
        'subdomain', 'custom_domain', 'domain_status', 'domain_txt_record', 'domain_verified_at',
        'logo', 'primary_color', 'accent_color', 'sidebar_style', 'font_family',
        'dark_mode', 'email_notifications',
        'smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_from_name', 'smtp_from_email', 'smtp_encryption',
    ];

    protected $attributes = [
        'plan'   => 'free',
        'status' => 'active',
        'max_users' => 5,
        'max_contacts' => 500,
        'currency' => 'USD',
    ];

    protected $casts = [
        'email_notifications' => 'boolean',
        'domain_verified_at'  => 'datetime',
    ];

    public function users()          { return $this->hasMany(User::class); }
    public function contacts()       { return $this->hasMany(Contact::class); }
    public function companies()      { return $this->hasMany(Company::class); }
    public function leads()          { return $this->hasMany(Lead::class); }
    public function deals()          { return $this->hasMany(Deal::class); }
    public function invoices()       { return $this->hasMany(Invoice::class); }
    public function pipelineStages() { return $this->hasMany(PipelineStage::class); }
    public function tenantPlugins()  { return $this->hasMany(TenantPlugin::class); }
    public function auditLogs()      { return $this->hasMany(AuditLog::class); }

    // -------------------------------------------------------------------------
    // Domain helpers
    // -------------------------------------------------------------------------

    /** Plan allows subdomain (starter+) */
    public function canUseSubdomain(): bool
    {
        return in_array($this->plan, ['starter', 'pro', 'enterprise']);
    }

    /** Plan allows custom domain (pro+) */
    public function canUseCustomDomain(): bool
    {
        return in_array($this->plan, ['pro', 'enterprise']);
    }

    /** Generate a TXT verification token for custom domain DNS check */
    public function generateDomainToken(): string
    {
        $token = 'crm-verify-' . Str::random(32);
        $this->update(['domain_txt_record' => $token, 'domain_status' => 'pending']);
        return $token;
    }

    /** Attempt DNS TXT record verification */
    public function verifyCustomDomain(): bool
    {
        if (!$this->custom_domain || !$this->domain_txt_record) return false;

        $verifyHost = '_crm-verify.' . $this->custom_domain;
        try {
            $records = dns_get_record($verifyHost, DNS_TXT) ?: [];
            foreach ($records as $record) {
                $txt = is_array($record['txt'] ?? null) ? implode('', $record['txt']) : ($record['txt'] ?? '');
                if ($txt === $this->domain_txt_record) {
                    $this->update(['domain_status' => 'active', 'domain_verified_at' => now()]);
                    return true;
                }
            }
        } catch (\Throwable) {}

        $this->update(['domain_status' => 'failed']);
        return false;
    }

    /** The active public URL for this tenant */
    public function portalUrl(): string
    {
        if ($this->custom_domain && $this->domain_status === 'active') {
            return 'https://' . $this->custom_domain;
        }
        if ($this->subdomain) {
            $appHost = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';
            return config('app.url_scheme', 'https') . '://' . $this->subdomain . '.' . $appHost;
        }
        return config('app.url');
    }

    /** Domain badge label */
    public function domainBadgeLabel(): ?string
    {
        if ($this->custom_domain && $this->domain_status === 'active') return $this->custom_domain;
        if ($this->subdomain) {
            $appHost = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';
            return $this->subdomain . '.' . $appHost;
        }
        return null;
    }

    // -------------------------------------------------------------------------
    // Plugin helpers
    // -------------------------------------------------------------------------

    public function hasPlugin(string $slug): bool
    {
        return in_array($slug, $this->enabledPluginSlugs());
    }

    public function enabledPluginSlugs(): array
    {
        return Cache::remember("tenant_{$this->id}_plugins", 60, function () {
            $allPlugins = Plugin::allPlugins();
            $overrides  = $this->tenantPlugins()->with('plugin')->get()->keyBy('plugin_id');
            $enabled    = [];

            foreach ($allPlugins as $plugin) {
                if (!$plugin->active) continue;

                if ($overrides->has($plugin->id)) {
                    if ($overrides[$plugin->id]->enabled) {
                        $enabled[] = $plugin->slug;
                    }
                } elseif ($plugin->isIncludedInPlan($this->plan)) {
                    $enabled[] = $plugin->slug;
                }
            }

            return $enabled;
        });
    }

    public function clearPluginCache(): void
    {
        Cache::forget("tenant_{$this->id}_plugins");
    }

    public function pluginsForDisplay(): \Illuminate\Support\Collection
    {
        $allPlugins = Plugin::orderBy('sort_order')->get();
        $overrides  = $this->tenantPlugins()->with('plugin')->get()->keyBy('plugin_id');

        return $allPlugins->map(function ($plugin) use ($overrides) {
            $includedByPlan = $plugin->isIncludedInPlan($this->plan);

            if ($overrides->has($plugin->id)) {
                $ov     = $overrides[$plugin->id];
                $status = $ov->enabled ? 'enabled' : 'disabled';
                $source = $ov->is_override ? 'manual' : ($includedByPlan ? 'plan' : 'manual');
            } else {
                $status = $includedByPlan ? 'enabled' : 'disabled';
                $source = $includedByPlan ? 'plan' : 'unavailable';
            }

            return (object)[
                'plugin'          => $plugin,
                'status'          => $status,
                'source'          => $source,
                'included_by_plan'=> $includedByPlan,
                'has_override'    => $overrides->has($plugin->id),
            ];
        });
    }
}
