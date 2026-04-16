{{-- First-login onboarding walkthrough modal --}}
<div class="modal fade" id="onboardingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="onboardingLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden" style="border-radius:20px;">

            {{-- Progress bar --}}
            <div style="height:4px;background:#e5e7eb;">
                <div id="onboardingProgress" style="height:4px;background:linear-gradient(90deg,#0d6efd,#6610f2);transition:width .4s ease;width:20%;"></div>
            </div>

            <div class="modal-body p-0">
                {{-- Step container --}}
                <div id="onboardingSteps">

                    {{-- Step 1: Welcome --}}
                    <div class="onboarding-step" data-step="1">
                        <div class="row g-0">
                            <div class="col-md-5 d-flex align-items-center justify-content-center p-5" style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 100%);min-height:360px;">
                                <div class="text-center text-white">
                                    <div style="width:80px;height:80px;background:rgba(255,255,255,.1);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;border:1px solid rgba(255,255,255,.2);">
                                        <i class="bi bi-hand-wave" style="font-size:36px;color:#fbbf24;"></i>
                                    </div>
                                    <div class="fw-700" style="font-size:1.3rem;">Welcome!</div>
                                    <div style="color:rgba(255,255,255,.6);font-size:13px;margin-top:6px;">Your CRM is ready</div>
                                </div>
                            </div>
                            <div class="col-md-7 p-5 d-flex flex-column justify-content-center">
                                <div class="text-muted small mb-2 fw-600 text-uppercase" style="letter-spacing:1px;">Step 1 of 5</div>
                                <h3 class="fw-700 mb-3">Welcome to {{ config('app.name') }} CRM</h3>
                                <p class="text-muted mb-4">This quick tour will get you up and running in under 3 minutes. We'll show you the key features so you can start winning deals right away.</p>
                                <div class="d-flex flex-column gap-2">
                                    @foreach(['Add your first contact','Set up the pipeline','Create a deal','Invite your team'] as $item)
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <span style="font-size:14px;">{{ $item }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Dashboard --}}
                    <div class="onboarding-step d-none" data-step="2">
                        <div class="row g-0">
                            <div class="col-md-5 d-flex align-items-center justify-content-center p-5" style="background:linear-gradient(135deg,#0d6efd22 0%,#e8f0fe 100%);min-height:360px;">
                                <div class="text-center">
                                    <div style="width:80px;height:80px;background:#0d6efd;border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                                        <i class="bi bi-speedometer2" style="font-size:36px;color:#fff;"></i>
                                    </div>
                                    <div class="fw-700 text-primary" style="font-size:1.1rem;">Dashboard</div>
                                    <div class="text-muted" style="font-size:12px;margin-top:4px;">Your sales command centre</div>
                                </div>
                            </div>
                            <div class="col-md-7 p-5 d-flex flex-column justify-content-center">
                                <div class="text-muted small mb-2 fw-600 text-uppercase" style="letter-spacing:1px;">Step 2 of 5</div>
                                <h3 class="fw-700 mb-3">Your Dashboard</h3>
                                <p class="text-muted mb-3">The dashboard gives you a live snapshot of your entire business — leads, deals, revenue, and tasks — all in one place.</p>
                                <div class="d-flex flex-column gap-3">
                                    @php $tips = [['bi-graph-up-arrow text-success','Revenue widget','See monthly revenue vs last period'],['bi-funnel-fill text-warning','Pipeline overview','Active deals across all stages'],['bi-list-task text-primary','Tasks due today','Never miss a follow-up']]; @endphp
                                    @foreach($tips as $t)
                                    <div class="d-flex gap-3 align-items-start">
                                        <i class="bi {{ $t[0] }}" style="font-size:20px;margin-top:2px;flex-shrink:0;"></i>
                                        <div>
                                            <div class="fw-600" style="font-size:13px;">{{ $t[1] }}</div>
                                            <div class="text-muted" style="font-size:12px;">{{ $t[2] }}</div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3: Add contacts --}}
                    <div class="onboarding-step d-none" data-step="3">
                        <div class="row g-0">
                            <div class="col-md-5 d-flex align-items-center justify-content-center p-5" style="background:linear-gradient(135deg,#10b98122 0%,#d1fae5 100%);min-height:360px;">
                                <div class="text-center">
                                    <div style="width:80px;height:80px;background:#10b981;border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                                        <i class="bi bi-people-fill" style="font-size:36px;color:#fff;"></i>
                                    </div>
                                    <div class="fw-700 text-success" style="font-size:1.1rem;">Contacts</div>
                                    <div class="text-muted" style="font-size:12px;margin-top:4px;">Your relationship database</div>
                                </div>
                            </div>
                            <div class="col-md-7 p-5 d-flex flex-column justify-content-center">
                                <div class="text-muted small mb-2 fw-600 text-uppercase" style="letter-spacing:1px;">Step 3 of 5</div>
                                <h3 class="fw-700 mb-3">Add Your First Contact</h3>
                                <p class="text-muted mb-3">Contacts are the foundation of your CRM. Every person you work with — prospects, customers, partners — lives here.</p>
                                <div class="bg-light rounded-3 p-3 mb-3" style="font-size:13px;">
                                    <div class="fw-600 mb-2"><i class="bi bi-lightning-charge-fill text-warning me-1"></i>Quick actions:</div>
                                    <div class="d-flex flex-column gap-1 text-muted">
                                        <span>→ <strong>Add Contact</strong> — fill name, email, phone</span>
                                        <span>→ <strong>Import CSV</strong> — bulk upload from spreadsheet</span>
                                        <span>→ <strong>Link to Company</strong> — see a full account view</span>
                                    </div>
                                </div>
                                <a href="{{ route('contacts.create') }}" class="btn btn-success btn-sm w-100" onclick="completeOnboarding()">
                                    <i class="bi bi-plus-circle me-1"></i>Add My First Contact Now
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Step 4: Pipeline --}}
                    <div class="onboarding-step d-none" data-step="4">
                        <div class="row g-0">
                            <div class="col-md-5 d-flex align-items-center justify-content-center p-5" style="background:linear-gradient(135deg,#f59e0b22 0%,#fef3c7 100%);min-height:360px;">
                                <div class="text-center">
                                    <div style="width:80px;height:80px;background:#f59e0b;border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                                        <i class="bi bi-funnel-fill" style="font-size:36px;color:#fff;"></i>
                                    </div>
                                    <div class="fw-700 text-warning" style="font-size:1.1rem;">Pipeline</div>
                                    <div class="text-muted" style="font-size:12px;margin-top:4px;">Visual deal tracking</div>
                                </div>
                            </div>
                            <div class="col-md-7 p-5 d-flex flex-column justify-content-center">
                                <div class="text-muted small mb-2 fw-600 text-uppercase" style="letter-spacing:1px;">Step 4 of 5</div>
                                <h3 class="fw-700 mb-3">Your Sales Pipeline</h3>
                                <p class="text-muted mb-3">The pipeline Kanban board lets your team visualise every deal and lead at a glance. Drag cards between stages as deals progress.</p>
                                <div class="d-flex gap-2 flex-wrap mb-3">
                                    @foreach(['Prospecting','Qualification','Proposal','Negotiation','Closed Won'] as $stage)
                                    <span class="badge rounded-pill" style="background:#0d6efd22;color:#0d6efd;font-weight:600;font-size:11px;">{{ $stage }}</span>
                                    @endforeach
                                </div>
                                <div class="text-muted" style="font-size:13px;background:#f8faff;border-radius:8px;padding:10px 14px;">
                                    <i class="bi bi-info-circle text-primary me-1"></i>
                                    Customise your pipeline stages under <strong>Settings → Pipeline</strong>.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 5: Invite team --}}
                    <div class="onboarding-step d-none" data-step="5">
                        <div class="row g-0">
                            <div class="col-md-5 d-flex align-items-center justify-content-center p-5" style="background:linear-gradient(135deg,#8b5cf622 0%,#ede9fe 100%);min-height:360px;">
                                <div class="text-center">
                                    <div style="width:80px;height:80px;background:#8b5cf6;border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                                        <i class="bi bi-person-plus-fill" style="font-size:36px;color:#fff;"></i>
                                    </div>
                                    <div class="fw-700" style="color:#8b5cf6;font-size:1.1rem;">Team</div>
                                    <div class="text-muted" style="font-size:12px;margin-top:4px;">Collaborate with ease</div>
                                </div>
                            </div>
                            <div class="col-md-7 p-5 d-flex flex-column justify-content-center">
                                <div class="text-muted small mb-2 fw-600 text-uppercase" style="letter-spacing:1px;">Step 5 of 5</div>
                                <h3 class="fw-700 mb-3">You're All Set!</h3>
                                <p class="text-muted mb-4">Your CRM workspace is ready. Invite your team and start closing deals together.</p>
                                <div class="d-flex flex-column gap-2 mb-4">
                                    @foreach(['Invite team members under Settings → Users','Assign roles: Admin, Manager, Staff','Each user sees only what they need'] as $item)
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-check-circle-fill" style="color:#8b5cf6;"></i>
                                        <span style="font-size:13px;">{{ $item }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('settings.index') }}" class="btn btn-primary flex-fill" onclick="completeOnboarding()">
                                        <i class="bi bi-people me-1"></i>Invite Team
                                    </a>
                                    <button type="button" class="btn btn-light flex-fill" onclick="completeOnboarding()">
                                        <i class="bi bi-house me-1"></i>Go to Dashboard
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Footer nav --}}
            <div class="modal-footer border-0 bg-light px-5 py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex gap-2 align-items-center">
                    @for($i=1;$i<=5;$i++)
                    <div class="onboarding-dot rounded-circle" data-dot="{{ $i }}" style="width:8px;height:8px;background:{{ $i===1 ? '#0d6efd' : '#d1d5db' }};transition:background .3s;cursor:pointer;" onclick="goToStep({{ $i }})"></div>
                    @endfor
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light btn-sm" id="onboardingPrev" style="display:none;" onclick="prevStep()">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </button>
                    <button type="button" class="btn btn-sm text-muted" onclick="skipOnboarding()" style="font-size:12px;">Skip tour</button>
                    <button type="button" class="btn btn-primary btn-sm" id="onboardingNext" onclick="nextStep()">
                        Next <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
(function() {
    let currentStep = 1;
    const totalSteps = 5;

    window.goToStep = function(step) {
        document.querySelectorAll('.onboarding-step').forEach(el => el.classList.add('d-none'));
        document.querySelector('[data-step="'+step+'"]').classList.remove('d-none');
        document.querySelectorAll('.onboarding-dot').forEach((dot, i) => {
            dot.style.background = (i + 1) === step ? '#0d6efd' : '#d1d5db';
        });
        document.getElementById('onboardingProgress').style.width = (step / totalSteps * 100) + '%';
        document.getElementById('onboardingPrev').style.display = step > 1 ? '' : 'none';
        const nextBtn = document.getElementById('onboardingNext');
        if (step === totalSteps) {
            nextBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Finish';
            nextBtn.onclick = completeOnboarding;
        } else {
            nextBtn.innerHTML = 'Next <i class="bi bi-arrow-right ms-1"></i>';
            nextBtn.onclick = nextStep;
        }
        currentStep = step;
    };

    window.nextStep = function() {
        if (currentStep < totalSteps) goToStep(currentStep + 1);
    };

    window.prevStep = function() {
        if (currentStep > 1) goToStep(currentStep - 1);
    };

    window.skipOnboarding = function() {
        completeOnboarding();
    };

    window.completeOnboarding = function() {
        fetch('{{ route("onboarding.complete") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
        }).catch(() => {});
        const modal = bootstrap.Modal.getInstance(document.getElementById('onboardingModal'));
        if (modal) modal.hide();
    };

    document.addEventListener('DOMContentLoaded', function() {
        const el = document.getElementById('onboardingModal');
        if (el) {
            new bootstrap.Modal(el, { backdrop: 'static', keyboard: false }).show();
        }
    });
})();
</script>
