<x-guest-layout>
    <div class="auth-card">
        <div class="auth-logo">
            <div class="logo-icon">C</div>
            <h4 class="fw-700 mb-1">Welcome back</h4>
            <p class="text-muted mb-0" style="font-size:14px;">Sign in to your CRM account</p>
        </div>
        @if(session('status'))
            <div class="alert alert-success mb-3">{{ session('status') }}</div>
        @endif
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-600">Email Address</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="you@company.com" required autofocus>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-600">Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-primary text-decoration-none" style="font-size:13px;">Forgot password?</a>
                @endif
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </button>
        </form>
        @if(Route::has('register'))
        <div class="text-center mt-4" style="font-size:14px;">
            Don't have an account? <a href="{{ route('register') }}" class="text-primary fw-600">Create one free</a>
        </div>
        @endif
        <div class="text-center mt-2" style="font-size:13px;">
            <a href="{{ route('pricing') }}" class="text-muted me-3"><i class="bi bi-tag me-1"></i>Pricing</a>
            <a href="{{ route('how-to') }}" class="text-muted"><i class="bi bi-book me-1"></i>How-To Guide</a>
        </div>
    </div>
</x-guest-layout>
