@extends('layouts.app')
@section('title', 'Admin Panel')
@section('page-title', 'Admin Panel')
@section('content')
<div class="page-header">
    <div><h1>Admin Panel</h1><p class="text-muted mb-0">Platform management</p></div>
    <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Tenant</a>
</div>
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-icon bg-primary-soft"><i class="bi bi-building"></i></div><div><div class="stat-value">{{ $stats['tenants'] }}</div><div class="stat-label">Total Tenants</div><div class="stat-change text-success">{{ $stats['active_tenants'] }} active</div></div></div></div>
    <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-icon bg-info-soft"><i class="bi bi-people"></i></div><div><div class="stat-value">{{ $stats['users'] }}</div><div class="stat-label">Total Users</div></div></div></div>
    <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-icon bg-warning-soft"><i class="bi bi-person-lines-fill"></i></div><div><div class="stat-value">{{ $stats['contacts'] }}</div><div class="stat-label">All Contacts</div></div></div></div>
    <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-icon bg-success-soft"><i class="bi bi-currency-dollar"></i></div><div><div class="stat-value">${{ number_format($stats['revenue'], 0) }}</div><div class="stat-label">Platform Revenue</div></div></div></div>
</div>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card"><div class="card-header bg-transparent pt-4 px-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-700 mb-0">Recent Tenants</h5>
            <a href="{{ route('admin.tenants') }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Tenant</th><th>Plan</th><th>Users</th><th>Contacts</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @foreach($tenants as $tenant)
                <tr>
                    <td><div class="fw-600">{{ $tenant->name }}</div><div class="text-muted" style="font-size:12px;">{{ $tenant->email }}</div></td>
                    <td><span class="badge bg-primary-subtle text-primary">{{ ucfirst($tenant->plan) }}</span></td>
                    <td>{{ $tenant->users_count }}</td>
                    <td>{{ $tenant->contacts_count }}</td>
                    <td><span class="badge bg-{{ $tenant->status === 'active' ? 'success' : 'danger' }}-subtle text-{{ $tenant->status === 'active' ? 'success' : 'danger' }}">{{ ucfirst($tenant->status) }}</span></td>
                    <td><a href="{{ route('admin.tenants.edit', $tenant) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div></div>
    </div>
    <div class="col-lg-5">
        <div class="card"><div class="card-header bg-transparent pt-4 px-4">
            <h5 class="fw-700 mb-0">Recent Users</h5>
        </div>
        <div class="card-body p-0">
            @foreach($recentUsers as $user)
            <div class="d-flex align-items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div class="avatar-circle" style="width:36px;height:36px;font-size:13px;flex-shrink:0;">{{ $user->initials }}</div>
                <div class="flex-1">
                    <div class="fw-600" style="font-size:13px;">{{ $user->name }}</div>
                    <div class="text-muted" style="font-size:11px;">{{ $user->tenant?->name ?? 'No tenant' }} • {{ $user->roles->first()?->name ?? 'staff' }}</div>
                </div>
                <div class="text-muted" style="font-size:11px;">{{ $user->created_at->diffForHumans() }}</div>
            </div>
            @endforeach
        </div></div>
    </div>
</div>
@endsection
