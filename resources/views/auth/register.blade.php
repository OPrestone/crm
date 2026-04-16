<x-guest-layout>
    <div class="auth-card" style="max-width:500px;">
        <div class="auth-logo">
            <div class="logo-icon">C</div>
            <h4 class="fw-700 mb-1">Create your CRM</h4>
            <p class="text-muted mb-0" style="font-size:14px;">Start your free account — no credit card needed</p>
        </div>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-600">Company / Organization Name <span class="text-danger">*</span></label>
                    <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}" placeholder="Acme Inc." required>
                    @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-600">Your Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="John Doe" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-600">Email Address <span class="text-danger">*</span></label>
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
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="bi bi-rocket-takeoff me-2"></i>Create My CRM Account
                    </button>
                </div>
            </div>
        </form>
        <div class="text-center mt-4" style="font-size:14px;">
            Already have an account? <a href="{{ route('login') }}" class="text-primary fw-600">Sign in</a>
        </div>
    </div>
</x-guest-layout>
