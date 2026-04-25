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
        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-600">Email Address</label>
                <input type="email" name="email" id="emailInput" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="you@company.com" required autofocus>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-600">Password</label>
                <input type="password" name="password" id="passwordInput" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required>
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

        {{-- ── Demo Accounts ──────────────────────────────────────────── --}}
        <div class="mt-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <hr class="flex-grow-1 m-0" style="border-color:#e2e8f0;">
                <span class="text-muted px-1" style="font-size:12px;white-space:nowrap;">Try a demo account</span>
                <hr class="flex-grow-1 m-0" style="border-color:#e2e8f0;">
            </div>
            <div class="d-flex flex-column gap-2">
                <button type="button"
                        class="demo-account-btn"
                        onclick="fillDemo('admin@crm.io','password')">
                    <span class="demo-avatar demo-avatar-purple">
                        <i class="bi bi-shield-check"></i>
                    </span>
                    <span class="demo-info">
                        <span class="demo-role">Platform Admin</span>
                        <span class="demo-email">admin@crm.io</span>
                    </span>
                    <span class="demo-badge">Super Admin</span>
                </button>
                <button type="button"
                        class="demo-account-btn"
                        onclick="fillDemo('demo@acme.com','password')">
                    <span class="demo-avatar demo-avatar-blue">
                        <i class="bi bi-building"></i>
                    </span>
                    <span class="demo-info">
                        <span class="demo-role">Acme Corp</span>
                        <span class="demo-email">demo@acme.com</span>
                    </span>
                    <span class="demo-badge demo-badge-blue">Tenant Admin</span>
                </button>
            </div>
            <p class="text-center text-muted mt-2 mb-0" style="font-size:11px;">
                <i class="bi bi-lock me-1"></i>Read-only demo &mdash; password is <code>password</code>
            </p>
        </div>

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

    <style>
        .demo-account-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 10px 14px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            cursor: pointer;
            text-align: left;
            transition: all .15s ease;
        }
        .demo-account-btn:hover {
            background: #eef2ff;
            border-color: #818cf8;
            box-shadow: 0 2px 8px rgba(99,102,241,.12);
        }
        .demo-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            font-size: 16px;
            flex-shrink: 0;
        }
        .demo-avatar-purple { background:#ede9fe; color:#7c3aed; }
        .demo-avatar-blue   { background:#dbeafe; color:#2563eb; }
        .demo-info {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 0;
        }
        .demo-role  { font-size: 13px; font-weight: 600; color: #1e293b; }
        .demo-email { font-size: 11px; color: #64748b; }
        .demo-badge {
            font-size: 10px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
            background: #ede9fe;
            color: #7c3aed;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .demo-badge-blue { background: #dbeafe; color: #2563eb; }
    </style>

    <script>
        function fillDemo(email, password) {
            document.getElementById('emailInput').value    = email;
            document.getElementById('passwordInput').value = password;
            document.getElementById('emailInput').classList.add('is-valid');
            document.getElementById('passwordInput').classList.add('is-valid');
            document.getElementById('loginForm').submit();
        }
    </script>
</x-guest-layout>
