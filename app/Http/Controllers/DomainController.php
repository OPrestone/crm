<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DomainController extends Controller
{
    private function tenant()
    {
        return Auth::user()->tenant;
    }

    // GET /settings/domain
    public function index()
    {
        $tenant = $this->tenant();
        return view('settings.domain', compact('tenant'));
    }

    // POST /settings/domain/subdomain
    public function claimSubdomain(Request $request)
    {
        $tenant = $this->tenant();

        if (!$tenant->canUseSubdomain()) {
            return back()->with('error', 'Subdomain access requires the Starter plan or above. <a href="/pricing">Upgrade now →</a>');
        }

        $request->validate([
            'subdomain' => [
                'required',
                'alpha_dash',
                'min:3',
                'max:63',
                Rule::unique('tenants', 'subdomain')->ignore($tenant->id),
                'not_in:www,app,api,mail,admin,ftp,smtp,imap,pop,dev,staging,test,crm,cdn,static',
            ],
        ]);

        $tenant->update(['subdomain' => strtolower($request->subdomain)]);
        AuditLog::record('tenant.subdomain_claimed', $tenant, [], ['subdomain' => $request->subdomain]);

        return back()->with('success', 'Subdomain claimed! Your portal is now accessible at ' . $tenant->portalUrl());
    }

    // DELETE /settings/domain/subdomain
    public function removeSubdomain()
    {
        $tenant = $this->tenant();
        AuditLog::record('tenant.subdomain_removed', $tenant, ['subdomain' => $tenant->subdomain], []);
        $tenant->update(['subdomain' => null]);
        return back()->with('success', 'Subdomain removed.');
    }

    // POST /settings/domain/custom
    public function requestCustomDomain(Request $request)
    {
        $tenant = $this->tenant();

        if (!$tenant->canUseCustomDomain()) {
            return back()->with('error', 'Custom domains require the Pro plan or above. <a href="/pricing">Upgrade now →</a>');
        }

        $request->validate([
            'custom_domain' => [
                'required',
                'string',
                'max:253',
                'regex:/^(?!-)[A-Za-z0-9\-]{1,63}(?<!-)(\.[A-Za-z0-9\-]{1,63})*\.[A-Za-z]{2,}$/',
                Rule::unique('tenants', 'custom_domain')->ignore($tenant->id),
            ],
        ], [
            'custom_domain.regex' => 'Please enter a valid domain name (e.g. crm.yourcompany.com).',
        ]);

        $domain = strtolower($request->custom_domain);
        $token  = 'crm-verify-' . Str::random(32);

        $tenant->update([
            'custom_domain'    => $domain,
            'domain_status'    => 'pending',
            'domain_txt_record'=> $token,
            'domain_verified_at' => null,
        ]);

        AuditLog::record('tenant.custom_domain_requested', $tenant, [], ['custom_domain' => $domain]);

        return back()->with('success', 'Domain saved. Follow the DNS instructions below to verify ownership.');
    }

    // POST /settings/domain/custom/verify
    public function verifyCustomDomain()
    {
        $tenant = $this->tenant();

        if (!$tenant->custom_domain || !$tenant->domain_txt_record) {
            return back()->with('error', 'No custom domain pending verification.');
        }

        $verified = $tenant->verifyCustomDomain();

        if ($verified) {
            AuditLog::record('tenant.custom_domain_verified', $tenant, [], ['custom_domain' => $tenant->custom_domain]);
            return back()->with('success', 'Domain verified successfully! Your CRM is now accessible at https://' . $tenant->custom_domain);
        }

        return back()->with('error', 'DNS verification failed. Ensure the TXT record has propagated (can take up to 48 hours) and try again.');
    }

    // DELETE /settings/domain/custom
    public function removeCustomDomain()
    {
        $tenant = $this->tenant();
        AuditLog::record('tenant.custom_domain_removed', $tenant, ['custom_domain' => $tenant->custom_domain], []);
        $tenant->update([
            'custom_domain'    => null,
            'domain_status'    => 'inactive',
            'domain_txt_record'=> null,
            'domain_verified_at' => null,
        ]);
        return back()->with('success', 'Custom domain removed.');
    }

    // POST /settings/domain/smtp (save email settings)
    public function updateSmtp(Request $request)
    {
        $tenant = $this->tenant();

        $data = $request->validate([
            'smtp_host'       => 'nullable|string|max:255',
            'smtp_port'       => 'nullable|integer|min:1|max:65535',
            'smtp_user'       => 'nullable|string|max:255',
            'smtp_pass'       => 'nullable|string|max:255',
            'smtp_from_name'  => 'nullable|string|max:100',
            'smtp_from_email' => 'nullable|email',
            'smtp_encryption' => 'nullable|in:tls,ssl,none',
            'email_notifications' => 'boolean',
        ]);

        $tenant->update($data);
        AuditLog::record('tenant.smtp_updated', $tenant);
        return back()->with('success', 'Email settings saved.');
    }
}
