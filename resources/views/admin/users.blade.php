@extends('layouts.app')
@section('title', 'Users')
@section('page-title', 'All Users')
@section('content')
<div class="page-header"><div><h1>All Users</h1></div></div>
<div class="card mb-4"><div class="card-body">
    <form method="GET" class="row g-2">
        <div class="col-md-6"><div class="search-box"><i class="bi bi-search search-icon"></i><input type="text" name="search" class="form-control" placeholder="Search users..." value="{{ request('search') }}"></div></div>
        <div class="col-md-2"><button type="submit" class="btn btn-outline-primary w-100">Search</button></div>
    </form>
</div></div>
<div class="card table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>User</th><th>Tenant</th><th>Role</th><th>Joined</th></tr></thead>
                <tbody>
                @forelse($users as $user)
                <tr>
                    <td><div class="d-flex align-items-center gap-2"><div class="avatar-circle" style="width:34px;height:34px;font-size:12px;flex-shrink:0;">{{ $user->initials }}</div><div><div class="fw-600">{{ $user->name }}</div><div class="text-muted" style="font-size:12px;">{{ $user->email }}</div></div></div></td>
                    <td>{{ $user->tenant?->name ?? '—' }}</td>
                    <td><span class="badge bg-primary-subtle text-primary">{{ $user->roles->first()?->name ?? 'staff' }}</span></td>
                    <td class="text-muted" style="font-size:12px;">{{ $user->created_at->format('M j, Y') }}</td>
                </tr>
                @empty<tr><td colspan="4" class="text-center text-muted py-4">No users found</td></tr>@endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-top">{{ $users->links() }}</div>
    </div>
</div>
@endsection
