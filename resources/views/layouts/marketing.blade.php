<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Enterprise CRM') — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <style>
        body { background: #fff; }
        .mkt-nav { background: #0f172a; padding: 0 24px; height: 60px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 1000; box-shadow: 0 1px 0 rgba(255,255,255,.05); }
        .mkt-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .mkt-logo-icon { width: 34px; height: 34px; background: #0d6efd; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; color: #fff; }
        .mkt-logo-text { font-weight: 700; font-size: 16px; color: #fff; }
        .mkt-nav-links { display: flex; align-items: center; gap: 4px; }
        .mkt-nav-links a { color: rgba(255,255,255,.7); text-decoration: none; padding: 6px 12px; border-radius: 6px; font-size: 14px; transition: all .15s; }
        .mkt-nav-links a:hover { color: #fff; background: rgba(255,255,255,.08); }
        .mkt-nav-links a.active { color: #fff; }
        .mkt-nav-cta { display: flex; align-items: center; gap: 8px; }
        .mkt-footer { background: #0f172a; color: rgba(255,255,255,.5); text-align: center; padding: 24px; font-size: 13px; }
        @media (max-width: 576px) { .mkt-nav-links { display: none; } }
    </style>
    @stack('styles')
</head>
<body>

<nav class="mkt-nav">
    <a href="{{ url('/') }}" class="mkt-logo">
        <div class="mkt-logo-icon">C</div>
        <span class="mkt-logo-text">{{ config('app.name') }} CRM</span>
    </a>
    <div class="mkt-nav-links">
        <a href="{{ route('pricing') }}" class="{{ request()->routeIs('pricing') ? 'active' : '' }}">Pricing</a>
        <a href="{{ route('how-to') }}" class="{{ request()->routeIs('how-to') ? 'active' : '' }}">How-To Docs</a>
        <a href="{{ route('login') }}">Sign In</a>
    </div>
    <div class="mkt-nav-cta">
        <a href="{{ route('pricing') }}" class="btn btn-outline-light btn-sm d-none d-md-inline-flex">View Plans</a>
        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Get Started Free</a>
    </div>
</nav>

<main>
    @yield('content')
</main>

<footer class="mkt-footer">
    <div>© {{ date('Y') }} {{ config('app.name') }} CRM · <a href="{{ route('pricing') }}" class="text-white-50 text-decoration-none">Pricing</a> · <a href="{{ route('how-to') }}" class="text-white-50 text-decoration-none">Docs</a> · <a href="{{ route('login') }}" class="text-white-50 text-decoration-none">Sign In</a></div>
</footer>

<script src="{{ asset('assets/vendor/bootstrap.bundle.min.js') }}"></script>
@stack('scripts')
</body>
</html>
