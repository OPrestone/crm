<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ auth()->user()?->tenant?->name ?? config('app.name') }} CRM</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    @stack('styles')
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<nav class="sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <div class="brand-icon">C</div>
        <div>
            <div class="brand-name">{{ Str::limit(auth()->user()?->tenant?->name ?? 'CRM', 16) }}</div>
            <div class="brand-sub">ENTERPRISE CRM</div>
        </div>
    </a>
    @php
        $isSuperAdmin  = auth()->user()->isSuperAdmin();
        $tenant        = auth()->user()->tenant;
        $enabledPlugins = ($tenant && !$isSuperAdmin) ? $tenant->enabledPluginSlugs() : [];
        $has = fn(string $slug) => $isSuperAdmin || in_array($slug, $enabledPlugins);
    @endphp
    <div class="sidebar-nav">

        @if($isSuperAdmin)
        {{-- Super Admin sidebar --}}
        <div class="sidebar-section">Platform</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>Dashboard
        </a>
        <a href="{{ route('admin.tenants') }}" class="nav-item-link {{ request()->routeIs('admin.tenants*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-building-fill"></i></span>Tenants
        </a>
        <a href="{{ route('admin.users') }}" class="nav-item-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-people-fill"></i></span>Users
        </a>
        <a href="{{ route('admin.plugins.index') }}" class="nav-item-link {{ request()->routeIs('admin.plugins*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-puzzle-fill"></i></span>Plugins
        </a>

        @else
        {{-- Tenant sidebar (plugin-gated) --}}
        <div class="sidebar-section">Main</div>
        <a href="{{ route('dashboard') }}" class="nav-item-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-grid-1x2-fill"></i></span>Dashboard
        </a>

        {{-- CRM --}}
        @if($has('contacts') || $has('companies') || $has('leads') || $has('deals') || $has('tasks'))
        <div class="sidebar-section">CRM</div>
        @endif

        @if($has('contacts'))
        <a href="{{ route('contacts.index') }}" class="nav-item-link {{ request()->routeIs('contacts.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-person-lines-fill"></i></span>Contacts
        </a>
        @endif

        @if($has('companies'))
        <a href="{{ route('companies.index') }}" class="nav-item-link {{ request()->routeIs('companies.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-building"></i></span>Companies
        </a>
        @endif

        @if($has('leads'))
        <a href="{{ route('leads.index') }}" class="nav-item-link {{ request()->routeIs('leads.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-funnel-fill"></i></span>Leads
        </a>
        @endif

        @if($has('deals'))
        <a href="{{ route('deals.index') }}" class="nav-item-link {{ request()->routeIs('deals.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-briefcase-fill"></i></span>Deals
        </a>
        @endif

        @if($has('tasks'))
        <a href="{{ route('tasks.index') }}" class="nav-item-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-check2-square"></i></span>Tasks
        </a>
        @endif

        {{-- Sales --}}
        @if($has('products') || $has('quotes') || $has('invoicing') || $has('goals'))
        <div class="sidebar-section">Sales</div>
        @endif

        @if($has('products'))
        <a href="{{ route('products.index') }}" class="nav-item-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-box-seam-fill"></i></span>Products
        </a>
        @endif

        @if($has('quotes'))
        <a href="{{ route('quotes.index') }}" class="nav-item-link {{ request()->routeIs('quotes.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-file-earmark-ruled-fill"></i></span>Quotes
        </a>
        @endif

        @if($has('invoicing'))
        <a href="{{ route('invoices.index') }}" class="nav-item-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-receipt"></i></span>Invoices
        </a>
        @endif

        @if($has('goals'))
        <a href="{{ route('goals.index') }}" class="nav-item-link {{ request()->routeIs('goals.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-bullseye"></i></span>Goals
        </a>
        @endif

        {{-- Operations --}}
        @if($has('calendar') || $has('helpdesk') || $has('documents'))
        <div class="sidebar-section">Operations</div>
        @endif

        @if($has('calendar'))
        <a href="{{ route('appointments.index') }}" class="nav-item-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-calendar3"></i></span>Calendar
        </a>
        @endif

        @if($has('helpdesk'))
        <a href="{{ route('tickets.index') }}" class="nav-item-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-headset"></i></span>Help Desk
        </a>
        @endif

        @if($has('documents'))
        <a href="{{ route('documents.index') }}" class="nav-item-link {{ request()->routeIs('documents.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-folder2-open"></i></span>Documents
        </a>
        @endif

        {{-- AI --}}
        @if($has('ai_tools'))
        <div class="sidebar-section">AI &amp; Intelligence</div>
        <a href="{{ route('ai.index') }}" class="nav-item-link {{ request()->routeIs('ai.index','ai.lead-score','ai.deal-insight','ai.contact-enrich') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-robot"></i></span>AI Assistant
        </a>
        <a href="{{ route('ai.insights') }}" class="nav-item-link {{ request()->routeIs('ai.insights') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-graph-up-arrow"></i></span>Pipeline Intelligence
        </a>
        <a href="{{ route('ai.email') }}" class="nav-item-link {{ request()->routeIs('ai.email') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-envelope-paper"></i></span>Email Composer
        </a>
        @endif

        {{-- Pro / Enterprise stubs --}}
        @if($has('email_campaigns') || $has('web_forms') || $has('contracts') || $has('forecasting'))
        <div class="sidebar-section">Marketing</div>
        @endif
        @if($has('email_campaigns'))
        <a href="{{ route('email_campaigns.index') }}" class="nav-item-link {{ request()->routeIs('email_campaigns.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-envelope-paper-fill"></i></span>Email Campaigns
        </a>
        @endif
        @if($has('web_forms'))
        <a href="{{ route('web_forms.index') }}" class="nav-item-link {{ request()->routeIs('web_forms.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-ui-checks-grid"></i></span>Web Forms
        </a>
        @endif
        @if($has('contracts'))
        <a href="{{ route('contracts.index') }}" class="nav-item-link {{ request()->routeIs('contracts.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-file-earmark-text-fill"></i></span>Contracts
        </a>
        @endif
        @if($has('forecasting'))
        <a href="{{ route('forecasting.index') }}" class="nav-item-link {{ request()->routeIs('forecasting.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-graph-up-arrow"></i></span>Forecasting
        </a>
        @endif

        @if($has('commissions') || $has('territories') || $has('audit_log') || $has('api_access'))
        <div class="sidebar-section">Enterprise</div>
        @endif
        @if($has('commissions'))
        <a href="{{ route('commissions.index') }}" class="nav-item-link {{ request()->routeIs('commissions.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-cash-coin"></i></span>Commissions
        </a>
        @endif
        @if($has('territories'))
        <a href="{{ route('territories.index') }}" class="nav-item-link {{ request()->routeIs('territories.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-map-fill"></i></span>Territories
        </a>
        @endif
        @if($has('audit_log'))
        <a href="{{ route('audit_log.index') }}" class="nav-item-link {{ request()->routeIs('audit_log.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-journal-check"></i></span>Audit Log
        </a>
        @endif
        @if($has('api_access'))
        <a href="{{ route('api_access.index') }}" class="nav-item-link {{ request()->routeIs('api_access.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-code-slash"></i></span>API &amp; Webhooks
        </a>
        @endif

        {{-- Compliance --}}
        @if($has('id_verification'))
        <div class="sidebar-section">Compliance</div>
        <a href="{{ route('id-verification.index') }}" class="nav-item-link {{ request()->routeIs('id-verification.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-shield-check"></i></span>ID Verification
        </a>
        @endif

        {{-- Tools --}}
        @if($has('cards') || $has('reports'))
        <div class="sidebar-section">Tools</div>
        @endif

        @if($has('cards'))
        <a href="{{ route('cards.index') }}" class="nav-item-link {{ request()->routeIs('cards.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-credit-card-2-front"></i></span>Card Generator
        </a>
        @endif

        @if($has('reports'))
        <a href="{{ route('reports.index') }}" class="nav-item-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-bar-chart-line-fill"></i></span>Reports
        </a>
        @endif

        {{-- Account --}}
        @if($has('notifications') || $has('settings'))
        <div class="sidebar-section">Account</div>
        @endif

        @if($has('notifications'))
        <a href="{{ route('notifications.index') }}" class="nav-item-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-bell-fill"></i></span>Notifications
        </a>
        @endif

        @if($has('settings') && auth()->user()->isTenantAdmin())
        <a href="{{ route('settings.index') }}" class="nav-item-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="bi bi-gear-fill"></i></span>Settings
        </a>
        @endif

        @endif {{-- end if isSuperAdmin --}}
    </div>
    <div class="sidebar-footer">
        <div class="d-flex align-items-center gap-2">
            <div class="avatar-circle" style="width:32px;height:32px;font-size:12px;">{{ auth()->user()->initials }}</div>
            <div style="overflow:hidden;">
                <div class="text-white fw-600" style="font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ Str::limit(auth()->user()->name, 18) }}</div>
                <div style="font-size:11px;color:rgba(255,255,255,.5);">{{ auth()->user()->roles->first()?->name ?? 'User' }}</div>
            </div>
        </div>
    </div>
</nav>
<div class="main-wrapper">
    <header class="topbar">
        <button class="topbar-btn d-lg-none" id="sidebarToggle"><i class="bi bi-list fs-5"></i></button>
        <h1 class="page-title d-none d-md-block">@yield('page-title', 'Dashboard')</h1>
        <div class="flex-1 d-md-none"></div>
        <div class="topbar-actions">
            <button class="topbar-btn dark-toggle" id="darkModeToggle" title="Toggle dark mode"><i class="bi bi-moon-fill"></i></button>
            <div class="dropdown">
                <button class="topbar-btn position-relative" data-bs-toggle="dropdown">
                    <i class="bi bi-bell-fill"></i>
                    @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                    @if($unread > 0)<span class="notification-badge">{{ $unread > 9 ? '9+' : $unread }}</span>@endif
                </button>
                <div class="dropdown-menu dropdown-menu-end p-0" style="width:300px;">
                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                        <span class="fw-600">Notifications</span>
                        @if($unread > 0)<a href="{{ route('notifications.readAll') }}" class="text-primary text-decoration-none" style="font-size:12px;">Mark all read</a>@endif
                    </div>
                    <div class="notification-list">
                        @forelse(auth()->user()->crmNotifications()->latest()->take(8)->get() as $notif)
                        <div class="notification-item {{ !$notif->isRead() ? 'unread' : '' }}" onclick="window.location='{{ $notif->url ?? '#' }}'">
                            <div class="d-flex gap-2">
                                <div class="avatar-circle bg-{{ $notif->color }} text-white" style="width:30px;height:30px;font-size:12px;flex-shrink:0;"><i class="bi bi-{{ $notif->icon }}"></i></div>
                                <div class="flex-1">
                                    <div class="fw-600" style="font-size:13px;">{{ $notif->title }}</div>
                                    <div class="text-muted" style="font-size:11px;">{{ $notif->created_at->diffForHumans() }}</div>
                                </div>
                                @if(!$notif->isRead())<div style="width:7px;height:7px;background:#0d6efd;border-radius:50%;margin-top:4px;flex-shrink:0;"></div>@endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-4" style="font-size:13px;">No notifications yet</div>
                        @endforelse
                    </div>
                    <div class="border-top text-center py-2">
                        <a href="{{ route('notifications.index') }}" class="text-primary text-decoration-none" style="font-size:13px;">View all</a>
                    </div>
                </div>
            </div>
            <div class="dropdown">
                <button class="btn p-0 border-0" data-bs-toggle="dropdown">
                    <div class="avatar-circle">{{ auth()->user()->initials }}</div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li class="px-3 py-2 border-bottom mb-1">
                        <div class="fw-600">{{ auth()->user()->name }}</div>
                        <div class="text-muted" style="font-size:12px;">{{ auth()->user()->email }}</div>
                    </li>
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person"></i> My Profile</a></li>
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isTenantAdmin())
                    <li><a class="dropdown-item" href="{{ route('settings.index') }}"><i class="bi bi-gear"></i> Settings</a></li>
                    @endif
                    <li><a class="dropdown-item" href="{{ route('how-to') }}" target="_blank"><i class="bi bi-book"></i> How-To Guide</a></li>
                    <li><a class="dropdown-item" href="{{ route('pricing') }}" target="_blank"><i class="bi bi-tag"></i> View Plans</a></li>
                    <li>
                        <button class="dropdown-item" onclick="restartTour()"><i class="bi bi-play-circle"></i> Restart Tour</button>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> Sign Out</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <main class="content-area fade-in">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible auto-dismiss fade show mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible auto-dismiss fade show mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{!! session('error') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @yield('content')
    </main>
</div>
<script src="{{ asset('assets/vendor/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.min.js') }}"></script>
<script src="{{ asset('assets/vendor/sortable.min.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>
<script>
function restartTour() {
    fetch('{{ route("onboarding.restart") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
    }).then(r => r.json()).then(data => {
        if (data.redirect) window.location.href = data.redirect;
    });
}
</script>
@stack('scripts')
</body>
</html>
