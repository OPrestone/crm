<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Tenant extends Model
{
    protected $fillable = [
        'name', 'slug', 'email', 'phone', 'website', 'address', 'industry',
        'plan', 'status', 'max_users', 'max_contacts', 'currency', 'timezone',
    ];

    protected $attributes = [
        'plan' => 'free',
        'status' => 'active',
        'max_users' => 5,
        'max_contacts' => 500,
        'currency' => 'USD',
    ];

    public function users()         { return $this->hasMany(User::class); }
    public function contacts()      { return $this->hasMany(Contact::class); }
    public function companies()     { return $this->hasMany(Company::class); }
    public function leads()         { return $this->hasMany(Lead::class); }
    public function deals()         { return $this->hasMany(Deal::class); }
    public function invoices()      { return $this->hasMany(Invoice::class); }
    public function pipelineStages(){ return $this->hasMany(PipelineStage::class); }
    public function tenantPlugins() { return $this->hasMany(TenantPlugin::class); }

    public function hasPlugin(string $slug): bool
    {
        $enabled = $this->enabledPluginSlugs();
        return in_array($slug, $enabled);
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
                $ov = $overrides[$plugin->id];
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
