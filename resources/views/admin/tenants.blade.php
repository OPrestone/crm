@extends('layouts.app')
@section('title', 'Tenants')
@section('page-title', 'Tenant Management')
@section('content')
<div class="page-header">
    <div><h1>Tenants</h1></div>
    <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Tenant</a>
</div>
<div class="card mb-4"><div class="card-body">
    <form method="GET" class="row g-2">
        <div class="col-md-5"><div class="search-box"><i class="bi bi-search search-icon"></i><input type="text" name="search" class="form-control" placeholder="Search tenants..." value="{{ request('search') }}"></div></div>
        <div class="col-md-2"><select name="status" class="form-select"><option value="">All Status</option><option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option><option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option></select></div>
        <div class="col-md-2"><select name="plan" class="form-select"><option value="">All Plans</option>@foreach(['free','starter','pro','enterprise'] as $p)<option value="{{ $p }}" {{ request('plan') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>@endforeach</select></div>
        <div class="col-md-2"><button type="submit" class="btn btn-outline-primary w-100">Filter</button></div>
    </form>
</div></div>
<div class="card table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Tenant</th><th>Plan</th><th>Users</th><th>Contacts</th><th>Deals</th><th>Status</th><th>Created</th><th></th></tr></thead>
                <tbody>
                @forelse($tenants as $tenant)
                <tr>
                    <td><div class="fw-600">{{ $tenant->name }}</div><div class="text-muted" style="font-size:12px;">{{ $tenant->slug }}</div></td>
                    <td><span class="badge bg-primary-subtle text-primary">{{ ucfirst($tenant->plan) }}</span></td>
                    <td>{{ $tenant->users_count }}/{{ $tenant->max_users }}</td>
                    <td>{{ $tenant->contacts_count }}</td>
                    <td>{{ $tenant->deals_count }}</td>
                    <td><span class="badge bg-{{ $tenant->status === 'active' ? 'success' : ($tenant->status === 'suspended' ? 'warning' : 'danger') }}-subtle text-{{ $tenant->status === 'active' ? 'success' : ($tenant->status === 'suspended' ? 'warning' : 'danger') }}">{{ ucfirst($tenant->status) }}</span></td>
                    <td class="text-muted" style="font-size:12px;">{{ $tenant->created_at->format('M j, Y') }}</td>
                    <td class="d-flex gap-1">
                        <a href="{{ route('admin.tenants.edit', $tenant) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="{{ route('admin.plugins.tenant', $tenant) }}" class="btn btn-sm btn-outline-secondary" title="Manage Plugins"><i class="bi bi-puzzle-fill"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No tenants found</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-top">{{ $tenants->links() }}</div>
    </div>
</div>
@endsection
