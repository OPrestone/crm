@extends('layouts.app')
@section('title', 'Domain Management')
@section('page-title', 'Domain Management')
@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div class="fw-700 fs-4 text-primary">{{ $withSubdomain->count() }}</div>
            <div class="text-muted small">Active Subdomains</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div class="fw-700 fs-4 text-success">{{ $withCustom->where('domain_status', 'active')->count() }}</div>
            <div class="text-muted small">Verified Custom Domains</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div class="fw-700 fs-4 text-warning">{{ $pending->count() }}</div>
            <div class="text-muted small">Pending Verification</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div class="fw-700 fs-4 text-danger">{{ $failed->count() }}</div>
            <div class="text-muted small">Failed Verification</div>
        </div>
    </div>
</div>

@if($pending->count() > 0)
<div class="card border-warning mb-4">
    <div class="card-header bg-warning bg-opacity-10 border-warning d-flex align-items-center gap-2 pt-3 px-4">
        <i class="bi bi-exclamation-triangle-fill text-warning"></i>
        <h6 class="fw-700 mb-0">Pending Domain Verification ({{ $pending->count() }})</h6>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Tenant</th><th>Domain</th><th>Plan</th><th>Requested</th><th>Actions</th></tr></thead>
            <tbody>
            @foreach($pending as $tenant)
            <tr>
                <td><strong>{{ $tenant->name }}</strong><div class="text-muted small">{{ $tenant->email }}</div></td>
                <td><code>{{ $tenant->custom_domain }}</code></td>
                <td><span class="badge bg-primary">{{ ucfirst($tenant->plan) }}</span></td>
                <td class="text-muted small">{{ $tenant->updated_at->diffForHumans() }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.domain.approve', $tenant) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="bi bi-check-lg me-1"></i>Force Approve
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.domain.revoke', $tenant) }}" class="d-inline ms-1"
                        onsubmit="return confirm('Revoke domain {{ $tenant->custom_domain }}?')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-x-lg me-1"></i>Revoke
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="row g-4">
<div class="col-lg-6">
<div class="card">
<div class="card-header bg-transparent pt-4 px-4">
    <h5 class="fw-700 mb-0"><i class="bi bi-link-45deg me-1 text-primary"></i>Subdomains ({{ $withSubdomain->count() }})</h5>
</div>
<div class="card-body p-0">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Tenant</th><th>Subdomain</th><th>Plan</th></tr></thead>
        <tbody>
        @forelse($withSubdomain as $tenant)
        <tr>
            <td>{{ $tenant->name }}</td>
            <td>
                <code>{{ $tenant->subdomain }}.{{ parse_url(config('app.url'), PHP_URL_HOST) }}</code>
            </td>
            <td><span class="badge bg-primary">{{ ucfirst($tenant->plan) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="3" class="text-center text-muted py-4">No subdomains claimed yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
</div>
</div>

<div class="col-lg-6">
<div class="card">
<div class="card-header bg-transparent pt-4 px-4">
    <h5 class="fw-700 mb-0"><i class="bi bi-globe me-1 text-success"></i>Custom Domains ({{ $withCustom->count() }})</h5>
</div>
<div class="card-body p-0">
    <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Tenant</th><th>Domain</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse($withCustom as $tenant)
        <tr>
            <td>{{ $tenant->name }}</td>
            <td><code>{{ $tenant->custom_domain }}</code></td>
            <td>
                <span class="badge
                    @if($tenant->domain_status === 'active') bg-success
                    @elseif($tenant->domain_status === 'failed') bg-danger
                    @elseif($tenant->domain_status === 'pending') bg-warning text-dark
                    @else bg-secondary @endif">
                    {{ ucfirst($tenant->domain_status) }}
                </span>
            </td>
            <td>
                @if($tenant->domain_status !== 'active')
                <form method="POST" action="{{ route('admin.domain.approve', $tenant) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-xs btn-outline-success py-0 px-1" title="Approve">
                        <i class="bi bi-check-lg"></i>
                    </button>
                </form>
                @endif
                <form method="POST" action="{{ route('admin.domain.revoke', $tenant) }}" class="d-inline"
                    onsubmit="return confirm('Revoke?')">
                    @csrf
                    <button type="submit" class="btn btn-xs btn-outline-danger py-0 px-1" title="Revoke">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center text-muted py-4">No custom domains registered yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
</div>
</div>
</div>

@endsection
