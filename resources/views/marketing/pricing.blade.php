@extends('layouts.marketing')
@section('title', 'Pricing')

@push('styles')
<style>
.pricing-hero { background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%); color: #fff; padding: 72px 0 80px; text-align: center; }
.pricing-hero h1 { font-size: clamp(2rem, 5vw, 3rem); font-weight: 800; margin-bottom: 16px; }
.pricing-hero p { font-size: 18px; color: rgba(255,255,255,.7); max-width: 560px; margin: 0 auto; }
.pricing-toggle { display: flex; align-items: center; justify-content: center; gap: 12px; margin-top: 32px; font-size: 14px; }
.pricing-toggle .badge-save { background: #22c55e; color: #fff; font-size: 11px; padding: 2px 8px; border-radius: 20px; font-weight: 700; }
.plan-cards { padding: 60px 0 80px; }
.plan-card { border-radius: 16px; border: 2px solid #e5e7eb; background: #fff; padding: 32px 28px; height: 100%; transition: all .2s; position: relative; }
.plan-card:hover { border-color: #0d6efd; box-shadow: 0 8px 30px rgba(13,110,253,.1); transform: translateY(-2px); }
.plan-card.popular { border-color: #0d6efd; box-shadow: 0 8px 30px rgba(13,110,253,.15); }
.popular-badge { position: absolute; top: -13px; left: 50%; transform: translateX(-50%); background: #0d6efd; color: #fff; font-size: 11px; font-weight: 700; padding: 4px 16px; border-radius: 20px; white-space: nowrap; }
.plan-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; font-size: 22px; }
.plan-name { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
.plan-desc { color: #6b7280; font-size: 14px; margin-bottom: 20px; }
.plan-price { font-size: 40px; font-weight: 800; color: #0f172a; line-height: 1; }
.plan-price sup { font-size: 20px; vertical-align: top; margin-top: 8px; }
.plan-price sub { font-size: 14px; font-weight: 400; color: #6b7280; }
.plan-cta { margin: 24px 0; }
.plan-features { list-style: none; padding: 0; margin: 0; }
.plan-features li { display: flex; align-items: flex-start; gap: 8px; padding: 6px 0; font-size: 14px; color: #374151; border-bottom: 1px solid #f3f4f6; }
.plan-features li:last-child { border-bottom: none; }
.plan-features .check { color: #22c55e; font-size: 16px; flex-shrink: 0; margin-top: 1px; }
.plan-features .dot { color: #d1d5db; font-size: 16px; flex-shrink: 0; margin-top: 1px; }
.faq-section { background: #f8faff; padding: 60px 0; }
.comparison-table { padding: 60px 0; }
.comparison-table table th { background: #f8faff; font-weight: 600; }
.comparison-table .check-y { color: #22c55e; font-size: 18px; }
.comparison-table .check-n { color: #e5e7eb; font-size: 18px; }
</style>
@endpush

@section('content')
<section class="pricing-hero">
    <div class="container">
        <div class="d-inline-flex align-items-center gap-2 bg-white bg-opacity-10 rounded-pill px-3 py-1 mb-3" style="font-size:13px;color:rgba(255,255,255,.8);">
            <i class="bi bi-stars text-warning"></i> No credit card required · Cancel anytime
        </div>
        <h1>Simple, transparent pricing</h1>
        <p>Start free and scale as your team grows. Every plan includes unlimited contacts and full data ownership.</p>
        <div class="pricing-toggle">
            <span style="color:rgba(255,255,255,.8);">Monthly</span>
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="billingToggle" style="cursor:pointer;">
            </div>
            <span style="color:rgba(255,255,255,.8);">Annual <span class="badge-save">Save 20%</span></span>
        </div>
    </div>
</section>

<section class="plan-cards">
    <div class="container">
        <div class="row g-4 justify-content-center">

            {{-- Free --}}
            <div class="col-md-6 col-lg-3">
                <div class="plan-card h-100">
                    <div class="plan-icon bg-secondary-subtle"><i class="bi bi-rocket-takeoff text-secondary"></i></div>
                    <div class="plan-name">Free</div>
                    <div class="plan-desc">For individuals getting started</div>
                    <div class="plan-price"><sup>$</sup>0<sub>/mo</sub></div>
                    <div class="plan-cta">
                        <a href="{{ route('register', ['plan' => 'free']) }}" class="btn btn-outline-dark w-100">Start Free</a>
                    </div>
                    <ul class="plan-features">
                        <li><i class="bi bi-check-circle-fill check"></i>Contacts & Companies</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Lead tracking (Kanban)</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Task management</li>
                        <li><i class="bi bi-check-circle-fill check"></i>1 user</li>
                        <li><i class="bi bi-check-circle-fill check"></i>500 contacts</li>
                        <li><i class="bi bi-dash-circle dot"></i>Deals pipeline</li>
                        <li><i class="bi bi-dash-circle dot"></i>Invoicing</li>
                        <li><i class="bi bi-dash-circle dot"></i>Reports & Analytics</li>
                    </ul>
                </div>
            </div>

            {{-- Starter --}}
            <div class="col-md-6 col-lg-3">
                <div class="plan-card h-100">
                    <div class="plan-icon bg-info-subtle"><i class="bi bi-lightning-charge-fill text-info"></i></div>
                    <div class="plan-name">Starter</div>
                    <div class="plan-desc">For small growing teams</div>
                    <div class="plan-price"><sup>$</sup><span class="price-val" data-monthly="29" data-annual="23">29</span><sub>/mo</sub></div>
                    <div class="plan-cta">
                        <a href="{{ route('register', ['plan' => 'starter']) }}" class="btn btn-outline-info w-100">Start Free Trial</a>
                    </div>
                    <ul class="plan-features">
                        <li><i class="bi bi-check-circle-fill check"></i>Everything in Free</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Deals pipeline</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Products &amp; Quotes</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Invoicing (PDF)</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Calendar &amp; appointments</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Help Desk (tickets)</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Document storage (10 GB)</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Goals &amp; targets</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Reports &amp; analytics</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Up to 5 users</li>
                    </ul>
                </div>
            </div>

            {{-- Pro --}}
            <div class="col-md-6 col-lg-3">
                <div class="plan-card popular h-100">
                    <div class="popular-badge"><i class="bi bi-stars me-1"></i>Most Popular</div>
                    <div class="plan-icon bg-primary-subtle"><i class="bi bi-briefcase-fill text-primary"></i></div>
                    <div class="plan-name">Pro</div>
                    <div class="plan-desc">For scaling sales teams</div>
                    <div class="plan-price"><sup>$</sup><span class="price-val" data-monthly="79" data-annual="63">79</span><sub>/mo</sub></div>
                    <div class="plan-cta">
                        <a href="{{ route('register', ['plan' => 'pro']) }}" class="btn btn-primary w-100">Start Free Trial</a>
                    </div>
                    <ul class="plan-features">
                        <li><i class="bi bi-check-circle-fill check"></i>Everything in Starter</li>
                        <li><i class="bi bi-check-circle-fill check"></i>AI lead scoring &amp; insights</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Business card generator</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Email campaigns</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Web forms &amp; lead capture</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Contract management</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Sales forecasting</li>
                        <li><i class="bi bi-check-circle-fill check"></i>50 GB document storage</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Unlimited users</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Priority support</li>
                    </ul>
                </div>
            </div>

            {{-- Enterprise --}}
            <div class="col-md-6 col-lg-3">
                <div class="plan-card h-100">
                    <div class="plan-icon bg-dark-subtle"><i class="bi bi-building-fill text-dark"></i></div>
                    <div class="plan-name">Enterprise</div>
                    <div class="plan-desc">For large organisations</div>
                    <div class="plan-price"><sup>$</sup><span class="price-val" data-monthly="199" data-annual="159">199</span><sub>/mo</sub></div>
                    <div class="plan-cta">
                        <button type="button" class="btn btn-dark w-100" data-bs-toggle="modal" data-bs-target="#contactSalesModal">Contact Sales</button>
                    </div>
                    <ul class="plan-features">
                        <li><i class="bi bi-check-circle-fill check"></i>Everything in Pro</li>
                        <li><i class="bi bi-check-circle-fill check"></i>ID Verification (KYC)</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Commission tracking</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Territory management</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Audit log &amp; compliance</li>
                        <li><i class="bi bi-check-circle-fill check"></i>REST API &amp; Webhooks</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Unlimited storage</li>
                        <li><i class="bi bi-check-circle-fill check"></i>SLA &amp; dedicated support</li>
                        <li><i class="bi bi-check-circle-fill check"></i>Custom integrations</li>
                        <li><i class="bi bi-check-circle-fill check"></i>SSO / SAML</li>
                    </ul>
                </div>
            </div>

        </div>

        {{-- Trust bar --}}
        <div class="text-center mt-5">
            <div class="d-flex align-items-center justify-content-center gap-4 flex-wrap text-muted" style="font-size:13px;">
                <span><i class="bi bi-shield-fill-check text-success me-1"></i>SOC 2 Type II</span>
                <span><i class="bi bi-lock-fill text-primary me-1"></i>GDPR Compliant</span>
                <span><i class="bi bi-credit-card me-1"></i>No credit card to start</span>
                <span><i class="bi bi-arrow-counterclockwise me-1"></i>Cancel anytime</span>
            </div>
        </div>
    </div>
</section>

{{-- Feature comparison --}}
<section class="comparison-table">
    <div class="container">
        <h2 class="fw-700 text-center mb-2">Compare plans</h2>
        <p class="text-muted text-center mb-5">A full breakdown of what's included in each plan.</p>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th style="width:35%">Feature</th>
                        <th class="text-center">Free</th>
                        <th class="text-center">Starter</th>
                        <th class="text-center text-primary fw-700">Pro</th>
                        <th class="text-center">Enterprise</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $rows = [
                        ['Contacts & Companies',         true,true,true,true],
                        ['Leads Pipeline (Kanban)',       true,true,true,true],
                        ['Task Management',              true,true,true,true],
                        ['Deals Pipeline',               false,true,true,true],
                        ['Products & Quotes',            false,true,true,true],
                        ['Invoicing (PDF)',               false,true,true,true],
                        ['Calendar & Appointments',      false,true,true,true],
                        ['Help Desk (Tickets)',           false,true,true,true],
                        ['Document Storage',             false,true,true,true],
                        ['Goals & Targets',              false,true,true,true],
                        ['Reports & Analytics',          false,true,true,true],
                        ['AI Lead Scoring',              false,false,true,true],
                        ['AI Pipeline Insights',         false,false,true,true],
                        ['Business Card Generator',      false,false,true,true],
                        ['Email Campaigns',              false,false,true,true],
                        ['Web Forms & Lead Capture',     false,false,true,true],
                        ['Contract Management',          false,false,true,true],
                        ['Sales Forecasting',            false,false,true,true],
                        ['ID Verification (KYC)',        false,false,false,true],
                        ['Commission Tracking',          false,false,false,true],
                        ['Territory Management',         false,false,false,true],
                        ['Audit Log & Compliance',       false,false,false,true],
                        ['REST API & Webhooks',          false,false,false,true],
                    ];
                    @endphp
                    @foreach($rows as $row)
                    <tr>
                        <td>{{ $row[0] }}</td>
                        @for($i=1;$i<=4;$i++)
                        <td class="text-center">
                            @if($row[$i])<i class="bi bi-check-circle-fill check-y"></i>
                            @else<i class="bi bi-dash check-n"></i>@endif
                        </td>
                        @endfor
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="faq-section">
    <div class="container" style="max-width:760px;">
        <h2 class="fw-700 text-center mb-5">Frequently asked questions</h2>
        <div class="accordion" id="faqAccordion">
            @php $faqs = [
                ['Can I switch plans later?', 'Yes, you can upgrade or downgrade at any time. Changes take effect immediately and your data is always preserved.'],
                ['Is there a free trial?', 'Every paid plan comes with a 14-day free trial — no credit card required. You can explore all features before committing.'],
                ['What happens to my data if I cancel?', 'Your data remains accessible for 30 days after cancellation. You can export everything as CSV or JSON at any time.'],
                ['Can I invite my team?', 'Starter supports up to 5 users. Pro and Enterprise support unlimited users with role-based access control.'],
                ['Is my data secure?', 'Yes. All data is encrypted at rest and in transit. We are SOC 2 Type II certified and GDPR compliant.'],
                ['Do you offer discounts for nonprofits or startups?', 'Yes! <a href="#contact-sales" class="text-primary fw-600">Contact our sales team</a> for special pricing for nonprofits, early-stage startups, and educational institutions.'],
            ]; @endphp
            @foreach($faqs as $i => $faq)
            <div class="accordion-item border-0 border-bottom">
                <h2 class="accordion-header">
                    <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }} bg-transparent fw-600" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}">
                        {{ $faq[0] }}
                    </button>
                </h2>
                <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">{!! $faq[1] !!}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section style="background:linear-gradient(135deg,#0d6efd 0%,#6610f2 100%);padding:64px 0;text-align:center;color:#fff;">
    <div class="container">
        <h2 class="fw-700 mb-2">Ready to supercharge your sales team?</h2>
        <p style="color:rgba(255,255,255,.8);font-size:16px;margin-bottom:28px;">Join thousands of teams already using {{ config('app.name') }} CRM. Start free today.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('register') }}" class="btn btn-light btn-lg fw-600"><i class="bi bi-rocket-takeoff me-2"></i>Get Started Free</a>
            <a href="{{ route('how-to') }}" class="btn btn-outline-light btn-lg"><i class="bi bi-play-circle me-2"></i>Watch How-To Guide</a>
        </div>
    </div>
</section>

{{-- ───── Contact Sales Modal ───── --}}
<div class="modal fade" id="contactSalesModal" tabindex="-1" aria-labelledby="contactSalesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            {{-- ── Success state (shown after form submitted) ── --}}
            @if(session('sales_success'))
            <div class="modal-body px-4 py-5 text-center">
                <div class="mb-3" style="font-size:4rem;">🎉</div>
                <h4 class="fw-800 mb-2">Thanks, {{ session('sales_name') }}!</h4>
                <p class="text-muted mb-4">We received your enquiry and will reach out to <strong>{{ session('sales_email') }}</strong> within one business day.</p>
                <p class="small text-muted mb-4">In the meantime, you can explore the platform on the free plan — no credit card required.</p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg fw-600"><i class="bi bi-rocket-takeoff me-1"></i>Start Free Trial</a>
                    <button type="button" class="btn btn-outline-secondary btn-lg" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
            @else
            {{-- Default form state --}}
            <div id="salesFormState">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <div>
                        <h4 class="modal-title fw-800 mb-1" id="contactSalesModalLabel">Talk to our Sales Team</h4>
                        <p class="text-muted small mb-0">Tell us about your team and we'll reach out within one business day.</p>
                    </div>
                    <button type="button" class="btn-close ms-3" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pb-4 pt-3">
                    @if(session('sales_error'))
                    <div class="alert alert-danger py-2 small">{{ session('sales_error') }}</div>
                    @endif
                    <form id="salesInquiryForm" method="POST" action="{{ route('contact.sales') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-600">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Jane Smith" value="{{ old('name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-600">Work Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       placeholder="jane@company.com" value="{{ old('email') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-600">Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="company" class="form-control @error('company') is-invalid @enderror"
                                       placeholder="prestech Inc." value="{{ old('company') }}" required>
                                @error('company')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-600">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" placeholder="+1 555 000 0000" value="{{ old('phone') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-600">Team Size <span class="text-danger">*</span></label>
                                <select name="team_size" class="form-select @error('team_size') is-invalid @enderror" required>
                                    <option value="" disabled selected>Select team size…</option>
                                    <option value="1-10" @selected(old('team_size')=='1-10')>1–10 people</option>
                                    <option value="11-50" @selected(old('team_size')=='11-50')>11–50 people</option>
                                    <option value="51-200" @selected(old('team_size')=='51-200')>51–200 people</option>
                                    <option value="201-500" @selected(old('team_size')=='201-500')>201–500 people</option>
                                    <option value="500+" @selected(old('team_size')=='500+')>500+ people</option>
                                </select>
                                @error('team_size')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-600">Industry</label>
                                <select name="industry" class="form-select">
                                    <option value="" disabled selected>Select industry…</option>
                                    <option @selected(old('industry')=='Technology')>Technology</option>
                                    <option @selected(old('industry')=='Finance')>Finance</option>
                                    <option @selected(old('industry')=='Healthcare')>Healthcare</option>
                                    <option @selected(old('industry')=='Real Estate')>Real Estate</option>
                                    <option @selected(old('industry')=='Retail')>Retail</option>
                                    <option @selected(old('industry')=='Manufacturing')>Manufacturing</option>
                                    <option @selected(old('industry')=='Consulting')>Consulting</option>
                                    <option @selected(old('industry')=='Other')>Other</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-600">What are you looking to solve?</label>
                                <textarea name="message" class="form-control" rows="3"
                                    placeholder="e.g. We need a CRM for 80 reps, with custom pipelines and API integration…">{{ old('message') }}</textarea>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                    <p class="text-muted small mb-0"><i class="bi bi-shield-check me-1 text-success"></i>We never share your info. Expected response: <strong>&lt; 1 business day.</strong></p>
                                    <button type="submit" class="btn btn-dark btn-lg px-4 fw-600">
                                        <i class="bi bi-send me-1"></i>Send Enquiry
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('billingToggle').addEventListener('change', function() {
    document.querySelectorAll('.price-val').forEach(el => {
        el.textContent = this.checked ? el.dataset.annual : el.dataset.monthly;
    });
});

// Auto-open modal on success or on validation errors
@if(session('sales_success') || ($errors->any() && old('name')))
window.addEventListener('DOMContentLoaded', function() {
    new bootstrap.Modal(document.getElementById('contactSalesModal')).show();
});
@endif

// Also wire any "contact sales" text links in FAQ
document.querySelectorAll('a[href="#contact-sales"]').forEach(function(el) {
    el.setAttribute('data-bs-toggle', 'modal');
    el.setAttribute('data-bs-target', '#contactSalesModal');
    el.removeAttribute('href');
    el.style.cursor = 'pointer';
});
</script>
@endpush
