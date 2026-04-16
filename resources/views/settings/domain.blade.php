@extends('layouts.app')
@section('title', 'Domain Settings')
@section('page-title', 'Domain & Email Settings')
@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-1"></i>{!! session('success') !!}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-1"></i>{!! session('error') !!}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

@php $appHost = parse_url(config('app.url'), PHP_URL_HOST) ?? request()->getHost(); @endphp

<ul class="nav nav-tabs mb-4">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-subdomain"><i class="bi bi-link-45deg me-1"></i>Subdomain</a></li>
    <li class="nav-item"><a class="nav-link {{ !$tenant->canUseCustomDomain() ? 'text-muted' : '' }}" data-bs-toggle="tab" href="#tab-custom"><i class="bi bi-globe me-1"></i>Custom Domain</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-smtp"><i class="bi bi-envelope me-1"></i>Email / SMTP</a></li>
</ul>

<div class="tab-content">
{{-- ===================== SUBDOMAIN ===================== --}}
<div class="tab-pane fade show active" id="tab-subdomain">
<div class="row g-4">
<div class="col-lg-7">
<div class="card">
<div class="card-header bg-transparent pt-4 px-4">
    <h5 class="fw-700 mb-1">Subdomain Access</h5>
    <p class="text-muted small mb-0">Let your team access the CRM at a branded subdomain of <strong>{{ $appHost }}</strong>.</p>
</div>
<div class="card-body px-4">
@if(!$tenant->canUseSubdomain())
    <div class="alert alert-warning d-flex align-items-center gap-3">
        <i class="bi bi-lock fs-4"></i>
        <div>
            <strong>Starter plan required</strong><br>
            <span class="small">Upgrade to claim a subdomain. <a href="/pricing">View plans →</a></span>
        </div>
    </div>
@else
    @if($tenant->subdomain)
    <div class="alert alert-success d-flex align-items-start gap-3 mb-4">
        <i class="bi bi-check-circle-fill fs-4 mt-1 flex-shrink-0"></i>
        <div class="flex-fill">
            <strong>Active Subdomain</strong><br>
            <a href="{{ $tenant->portalUrl() }}" target="_blank" class="fw-600 fs-5 text-decoration-none">
                {{ $tenant->subdomain }}.{{ $appHost }}
            </a>
            <div class="mt-2">
                <button class="btn btn-sm btn-outline-secondary me-2" onclick="navigator.clipboard.writeText('{{ $tenant->portalUrl() }}')" title="Copy URL">
                    <i class="bi bi-clipboard me-1"></i>Copy URL
                </button>
                <form method="POST" action="{{ route('settings.domain.subdomain.remove') }}" class="d-inline"
                    onsubmit="return confirm('Remove subdomain {{ $tenant->subdomain }}.{{ $appHost }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Remove</button>
                </form>
            </div>
        </div>
    </div>
    <hr>
    <p class="text-muted small mb-3">To change your subdomain, remove the current one first.</p>
    @else
    <form method="POST" action="{{ route('settings.domain.subdomain') }}">
        @csrf
        <label class="form-label fw-600">Choose a subdomain</label>
        <div class="input-group mb-1">
            <input type="text" name="subdomain" class="form-control @error('subdomain') is-invalid @enderror"
                   placeholder="yourcompany" value="{{ old('subdomain') }}"
                   pattern="[a-zA-Z0-9\-]+" minlength="3" maxlength="63" required>
            <span class="input-group-text bg-light text-muted">.{{ $appHost }}</span>
            <button type="submit" class="btn btn-primary">Claim Subdomain</button>
        </div>
        @error('subdomain')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        <div class="form-text">Use only letters, numbers, and hyphens (3–63 chars). Reserved names are not allowed.</div>
    </form>
    @endif
@endif
</div>
</div>
</div>
<div class="col-lg-5">
<div class="card border-0 bg-light">
<div class="card-body">
<h6 class="fw-700 mb-3"><i class="bi bi-info-circle me-1 text-primary"></i>How Subdomains Work</h6>
<ul class="list-unstyled small text-muted mb-0">
    <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Instantly active — no DNS setup needed</li>
    <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>All users access CRM at <code>subdomain.{{ $appHost }}</code></li>
    <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Shareable, bookmarkable URL for your team</li>
    <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>SSL/HTTPS included automatically</li>
    <li class="mb-2"><i class="bi bi-info-circle text-info me-2"></i>Available on Starter, Pro, and Enterprise plans</li>
    <li><i class="bi bi-info-circle text-warning me-2"></i>Custom domains (your own URL) require Pro+</li>
</ul>
</div>
</div>
</div>
</div>
</div>

{{-- ===================== CUSTOM DOMAIN ===================== --}}
<div class="tab-pane fade" id="tab-custom">
<div class="row g-4">
<div class="col-lg-7">
<div class="card">
<div class="card-header bg-transparent pt-4 px-4">
    <h5 class="fw-700 mb-1">Custom Domain</h5>
    <p class="text-muted small mb-0">Connect your own domain (e.g. <strong>crm.yourcompany.com</strong>) to your CRM portal.</p>
</div>
<div class="card-body px-4">
@if(!$tenant->canUseCustomDomain())
    <div class="alert alert-warning d-flex align-items-center gap-3">
        <i class="bi bi-lock fs-4"></i>
        <div>
            <strong>Pro plan required</strong><br>
            <span class="small">Upgrade to connect a custom domain. <a href="/pricing">View plans →</a></span>
        </div>
    </div>
@else
    {{-- Current domain status --}}
    @if($tenant->custom_domain)
    <div class="alert @if($tenant->domain_status === 'active') alert-success @elseif($tenant->domain_status === 'failed') alert-danger @else alert-warning @endif mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <strong>{{ $tenant->custom_domain }}</strong>
                <span class="badge ms-2
                    @if($tenant->domain_status === 'active') bg-success
                    @elseif($tenant->domain_status === 'failed') bg-danger
                    @else bg-warning text-dark @endif">
                    {{ ucfirst($tenant->domain_status) }}
                </span>
                @if($tenant->domain_status === 'active' && $tenant->domain_verified_at)
                <div class="small text-muted mt-1">Verified {{ $tenant->domain_verified_at->diffForHumans() }}</div>
                @endif
            </div>
            <form method="POST" action="{{ route('settings.domain.custom.remove') }}"
                onsubmit="return confirm('Remove custom domain {{ $tenant->custom_domain }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
            </form>
        </div>
    </div>

    @if($tenant->domain_status === 'pending' || $tenant->domain_status === 'failed')
    {{-- DNS verification instructions --}}
    <div class="card bg-dark text-light mb-4" style="border-radius:8px">
        <div class="card-body">
            <h6 class="fw-700 mb-3"><i class="bi bi-terminal me-1"></i>DNS Verification Required</h6>
            <p class="small mb-3">Add the following TXT record to your DNS settings for <strong>{{ $tenant->custom_domain }}</strong>:</p>
            <div class="row g-2 mb-3">
                <div class="col-md-5">
                    <div class="small text-muted mb-1">Record Type</div>
                    <code class="d-block bg-secondary bg-opacity-25 rounded p-2">TXT</code>
                </div>
                <div class="col-md-7">
                    <div class="small text-muted mb-1">Host / Name</div>
                    <div class="input-group input-group-sm">
                        <code class="d-block bg-secondary bg-opacity-25 rounded-start p-2 flex-fill">_crm-verify</code>
                        <button class="btn btn-sm btn-secondary" onclick="navigator.clipboard.writeText('_crm-verify')" title="Copy"><i class="bi bi-clipboard"></i></button>
                    </div>
                </div>
                <div class="col-12">
                    <div class="small text-muted mb-1">Value</div>
                    <div class="input-group input-group-sm">
                        <code class="flex-fill bg-secondary bg-opacity-25 rounded-start p-2 text-break" style="word-break:break-all">{{ $tenant->domain_txt_record }}</code>
                        <button class="btn btn-sm btn-secondary" onclick="navigator.clipboard.writeText('{{ $tenant->domain_txt_record }}')" title="Copy"><i class="bi bi-clipboard"></i></button>
                    </div>
                </div>
            </div>
            <div class="small text-warning"><i class="bi bi-clock me-1"></i>DNS changes can take up to 48 hours to propagate worldwide.</div>
        </div>
    </div>
    <form method="POST" action="{{ route('settings.domain.custom.verify') }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-success me-2"><i class="bi bi-shield-check me-1"></i>Verify DNS Now</button>
    </form>
    @endif

    {{-- Also allow setting a new domain --}}
    @if($tenant->domain_status !== 'active')
    <hr><h6 class="fw-600 mt-3">Update Domain</h6>
    @endif
    @endif

    @if(!$tenant->custom_domain || $tenant->domain_status !== 'active')
    <form method="POST" action="{{ route('settings.domain.custom') }}">
        @csrf
        <label class="form-label fw-600">Your Custom Domain</label>
        <div class="input-group mb-1">
            <span class="input-group-text"><i class="bi bi-globe"></i></span>
            <input type="text" name="custom_domain" class="form-control @error('custom_domain') is-invalid @enderror"
                   placeholder="crm.yourcompany.com"
                   value="{{ old('custom_domain', $tenant->custom_domain) }}" required>
            <button type="submit" class="btn btn-primary">{{ $tenant->custom_domain ? 'Update' : 'Add Domain' }}</button>
        </div>
        @error('custom_domain')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        <div class="form-text">Enter the full subdomain you want to use (e.g. crm.yourcompany.com). Do not include http:// or https://.</div>
    </form>
    @endif

    @if($tenant->domain_status === 'active')
    <div class="d-flex gap-2 mt-3">
        <a href="https://{{ $tenant->custom_domain }}" target="_blank" class="btn btn-success">
            <i class="bi bi-box-arrow-up-right me-1"></i>Open {{ $tenant->custom_domain }}
        </a>
        <button class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText('https://{{ $tenant->custom_domain }}')">
            <i class="bi bi-clipboard me-1"></i>Copy URL
        </button>
    </div>
    @endif
@endif
</div>
</div>
</div>
<div class="col-lg-5">
<div class="card border-0 bg-light">
<div class="card-body">
<h6 class="fw-700 mb-3"><i class="bi bi-info-circle me-1 text-primary"></i>How Custom Domains Work</h6>
<ol class="small text-muted ps-3 mb-3">
    <li class="mb-2">Enter your desired domain (e.g. <code>crm.acme.com</code>)</li>
    <li class="mb-2">Add the provided TXT record to your DNS provider (Cloudflare, GoDaddy, etc.)</li>
    <li class="mb-2">Click <strong>Verify DNS Now</strong> once the record has propagated</li>
    <li class="mb-2">Also add a <strong>CNAME record</strong> pointing <code>crm.acme.com</code> → <code>{{ $appHost }}</code></li>
    <li>Your CRM is live at your custom domain!</li>
</ol>
<div class="alert alert-info py-2 small mb-0">
    <i class="bi bi-shield-lock me-1"></i>
    For HTTPS on custom domains, a valid SSL certificate must be provisioned by your infrastructure provider (e.g. Cloudflare proxy, Let's Encrypt, or your hosting panel).
</div>
</div>
</div>
</div>
</div>
</div>

{{-- ===================== SMTP ===================== --}}
<div class="tab-pane fade" id="tab-smtp">
<div class="row g-4">
<div class="col-lg-8">
<div class="card">
<div class="card-header bg-transparent pt-4 px-4">
    <h5 class="fw-700 mb-1">Email / SMTP Settings</h5>
    <p class="text-muted small mb-0">Configure a custom SMTP server to send emails from your own address.</p>
</div>
<div class="card-body px-4">
<form method="POST" action="{{ route('settings.domain.smtp') }}">
    @csrf
    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label fw-600">SMTP Host</label>
            <input type="text" name="smtp_host" class="form-control" placeholder="smtp.gmail.com" value="{{ old('smtp_host', $tenant->smtp_host) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-600">Port</label>
            <input type="number" name="smtp_port" class="form-control" placeholder="587" value="{{ old('smtp_port', $tenant->smtp_port) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-600">Username</label>
            <input type="text" name="smtp_user" class="form-control" placeholder="you@yourcompany.com" value="{{ old('smtp_user', $tenant->smtp_user) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-600">Password / App Password</label>
            <input type="password" name="smtp_pass" class="form-control" placeholder="{{ $tenant->smtp_pass ? '••••••••' : 'Enter password' }}" value="">
            @if($tenant->smtp_pass)<div class="form-text text-success"><i class="bi bi-check-circle me-1"></i>Password saved. Leave blank to keep current.</div>@endif
        </div>
        <div class="col-md-6">
            <label class="form-label fw-600">From Name</label>
            <input type="text" name="smtp_from_name" class="form-control" placeholder="{{ $tenant->name }}" value="{{ old('smtp_from_name', $tenant->smtp_from_name) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-600">From Email</label>
            <input type="email" name="smtp_from_email" class="form-control" placeholder="noreply@yourcompany.com" value="{{ old('smtp_from_email', $tenant->smtp_from_email) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-600">Encryption</label>
            <select name="smtp_encryption" class="form-select">
                <option value="tls" @selected(($tenant->smtp_encryption ?? 'tls') === 'tls')>TLS (port 587)</option>
                <option value="ssl" @selected($tenant->smtp_encryption === 'ssl')>SSL (port 465)</option>
                <option value="none" @selected($tenant->smtp_encryption === 'none')>None (port 25)</option>
            </select>
        </div>
        <div class="col-md-8">
            <label class="form-label fw-600 d-block mb-2">Email Notifications</label>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="email_notifications" value="1" id="email_notif"
                    @checked($tenant->email_notifications ?? true)>
                <label class="form-check-label" for="email_notif">Send system email notifications to users</label>
            </div>
        </div>
        <div class="col-12 pt-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Email Settings</button>
        </div>
    </div>
</form>
</div>
</div>
</div>
<div class="col-lg-4">
<div class="card border-0 bg-light">
<div class="card-body">
<h6 class="fw-700 mb-3"><i class="bi bi-info-circle me-1 text-primary"></i>Tips</h6>
<ul class="list-unstyled small text-muted mb-0">
    <li class="mb-2"><i class="bi bi-google text-danger me-1"></i><strong>Gmail:</strong> Use port 587, TLS, and an App Password (not your main password)</li>
    <li class="mb-2"><i class="bi bi-microsoft text-primary me-1"></i><strong>Outlook/365:</strong> Use smtp.office365.com, port 587, TLS</li>
    <li class="mb-2"><i class="bi bi-envelope me-1"></i><strong>Mailgun / SendGrid:</strong> Check your provider's SMTP credentials page</li>
    <li><i class="bi bi-shield me-1"></i>Credentials are stored encrypted. Leave Password blank to keep the existing one.</li>
</ul>
</div>
</div>
</div>
</div>
</div>
</div>

@endsection
