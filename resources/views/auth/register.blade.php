<x-guest-layout>
<style>
.register-wrap { display:flex; min-height:calc(100vh - 0px); }
.register-form-side { flex:1; display:flex; align-items:center; justify-content:center; padding:40px 24px; background:#fff; }
.register-promo-side { flex:1; display:flex; align-items:center; background:linear-gradient(150deg,#0f172a 0%,#1e3a5f 100%); padding:48px 40px; }
@media(max-width:991px){ .register-promo-side{display:none;} }
.plan-badge { display:inline-flex; align-items:center; gap:8px; padding:6px 16px; border-radius:20px; font-size:13px; font-weight:600; margin-bottom:20px; }
</style>

<div class="register-wrap">

    {{-- LEFT: form --}}
    <div class="register-form-side">
        <div style="max-width:440px;width:100%;">
            <div class="auth-logo">
                <div class="logo-icon">C</div>
                <h4 class="fw-700 mb-1">Create your CRM</h4>
                @php $plan = request('plan', 'free'); @endphp
                <p class="text-muted mb-0" style="font-size:14px;">
                    @if($plan === 'free') Start free — no credit card needed
                    @else 14-day free trial — no credit card needed @endif
                </p>
            </div>

            {{-- Selected plan badge --}}
            @php $planMeta = ['free'=>['name'=>'Free','color'=>'secondary','icon'=>'bi-rocket-takeoff'],'starter'=>['name'=>'Starter','color'=>'info','icon'=>'bi-lightning-charge-fill'],'pro'=>['name'=>'Pro','color'=>'primary','icon'=>'bi-briefcase-fill'],'enterprise'=>['name'=>'Enterprise','color'=>'dark','icon'=>'bi-building-fill']][$plan] ?? ['name'=>'Free','color'=>'secondary','icon'=>'bi-rocket-takeoff']; @endphp
            <div class="text-center">
                <span class="plan-badge bg-{{ $planMeta['color'] }}-subtle text-{{ $planMeta['color'] }}">
                    <i class="bi {{ $planMeta['icon'] }}"></i>{{ $planMeta['name'] }} Plan
                    <a href="{{ route('pricing') }}" class="ms-2 text-{{ $planMeta['color'] }}" style="font-size:11px;font-weight:400;">Change →</a>
                </span>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <input type="hidden" name="plan" value="{{ $plan }}">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-600">Company / Organization Name <span class="text-danger">*</span></label>
                        <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}" placeholder="prestech Inc." required autofocus>
                        @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-600">Your Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="John Doe" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-600">Work Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="you@company.com" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min 8 characters" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-600">
                            <i class="bi bi-rocket-takeoff me-2"></i>Create My CRM Account
                        </button>
                    </div>
                </div>
            </form>

            <div class="text-center mt-3" style="font-size:12px;color:#9ca3af;">
                By creating an account you agree to our Terms of Service and Privacy Policy.
            </div>
            <hr class="my-3">
            <div class="text-center" style="font-size:13px;">
                Already have an account? <a href="{{ route('login') }}" class="text-primary fw-600">Sign in</a>
                &nbsp;·&nbsp;<a href="{{ route('pricing') }}" class="text-primary fw-600">View plans</a>
                &nbsp;·&nbsp;<a href="{{ route('how-to') }}" class="text-primary fw-600">Docs</a>
            </div>
        </div>
    </div>

    {{-- RIGHT: perks panel --}}
    <div class="register-promo-side">
        <div class="text-white" style="max-width:420px;">
            <div class="mb-5">
                <div class="d-inline-flex align-items-center gap-2 rounded-pill px-3 py-1 mb-3" style="background:rgba(255,255,255,.08);font-size:12px;">
                    <i class="bi bi-stars text-warning"></i> Enterprise-grade · 27 modules
                </div>
                <h2 class="fw-700" style="font-size:1.9rem;line-height:1.2;">Everything your sales team needs to win</h2>
                <p style="color:rgba(255,255,255,.6);font-size:15px;margin-top:10px;">From leads to invoices — one platform, zero friction.</p>
            </div>
            @php $perks = [
                ['bi-people-fill','#3b82f6','Contacts & Companies','Full 360° relationship management'],
                ['bi-funnel-fill','#f59e0b','Leads & Deals Pipeline','Visual Kanban — move deals fast'],
                ['bi-file-earmark-ruled-fill','#10b981','Quotes & Invoices','Professional PDFs in one click'],
                ['bi-robot','#8b5cf6','AI Intelligence','Lead scoring, insights, email composer'],
                ['bi-headset','#06b6d4','Help Desk','Full ticketing & customer support'],
                ['bi-shield-fill-check','#ef4444','Enterprise Security','SOC 2, GDPR, audit log, SSO'],
            ]; @endphp
            @foreach($perks as $p)
            <div class="d-flex gap-3 align-items-start mb-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:38px;height:38px;background:{{ $p[1] }}22;border:1px solid {{ $p[1] }}44;">
                    <i class="bi {{ $p[0] }}" style="font-size:16px;color:{{ $p[1] }};"></i>
                </div>
                <div>
                    <div class="fw-600" style="font-size:13px;">{{ $p[2] }}</div>
                    <div style="font-size:12px;color:rgba(255,255,255,.5);">{{ $p[3] }}</div>
                </div>
            </div>
            @endforeach
            <div class="mt-4 pt-3" style="border-top:1px solid rgba(255,255,255,.1);">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex" style="margin-left:6px;">
                        @foreach(['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6'] as $c)
                        <div class="rounded-circle border border-white" style="width:26px;height:26px;background:{{ $c }};margin-left:-8px;"></div>
                        @endforeach
                    </div>
                    <span style="font-size:12px;color:rgba(255,255,255,.6);">Trusted by 2,000+ sales teams worldwide</span>
                </div>
            </div>
        </div>
    </div>

</div>
</x-guest-layout>
