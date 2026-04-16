@extends('layouts.app')
@section('title', 'Contracts')
@section('page-title', 'Contracts')
@section('content')
<div class="page-header">
    <div><h1>Contract Management</h1></div>
    <div class="d-flex gap-2">
        <a href="{{ route('contracts.templates') }}" class="btn btn-outline-secondary"><i class="bi bi-file-text me-1"></i>Templates</a>
        <a href="{{ route('contracts.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Contract</a>
    </div>
</div>

@if(session('success'))<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>@endif

<div class="row g-3 mb-4">
    <div class="col-6 col-md"><div class="card text-center"><div class="card-body py-3"><div class="fs-4 fw-700">{{ $stats['total'] }}</div><div class="small text-muted">Total</div></div></div></div>
    <div class="col-6 col-md"><div class="card text-center"><div class="card-body py-3"><div class="fs-4 fw-700 text-secondary">{{ $stats['draft'] }}</div><div class="small text-muted">Draft</div></div></div></div>
    <div class="col-6 col-md"><div class="card text-center"><div class="card-body py-3"><div class="fs-4 fw-700 text-warning">{{ $stats['pending'] }}</div><div class="small text-muted">Pending</div></div></div></div>
    <div class="col-6 col-md"><div class="card text-center"><div class="card-body py-3"><div class="fs-4 fw-700 text-success">{{ $stats['signed'] }}</div><div class="small text-muted">Signed</div></div></div></div>
    <div class="col-12 col-md"><div class="card text-center"><div class="card-body py-3"><div class="fs-4 fw-700 text-success">${{ number_format($stats['total_value'], 0) }}</div><div class="small text-muted">Signed Value</div></div></div></div>
</div>

<div class="card mb-4"><div class="card-body">
    <form method="GET" class="row g-2">
        <div class="col-md-5"><div class="search-box"><i class="bi bi-search search-icon"></i><input type="text" name="search" class="form-control" placeholder="Search contracts..." value="{{ request('search') }}"></div></div>
        <div class="col-md-3"><select name="status" class="form-select"><option value="">All Status</option>@foreach(['draft','pending_signature','signed','expired','cancelled'] as $s)<option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>@endforeach</select></div>
        <div class="col-md-4 d-flex gap-2"><button type="submit" class="btn btn-outline-primary flex-fill">Filter</button><a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary">Clear</a></div>
    </form>
</div></div>

<div class="card table-card">
    <div class="card-body p-0">
        @if($contracts->count())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Contract</th><th>Contact</th><th>Value</th><th>Status</th><th>End Date</th><th></th></tr></thead>
                <tbody>
                @foreach($contracts as $c)
                <tr>
                    <td>
                        <a href="{{ route('contracts.show', $c) }}" class="fw-600 text-decoration-none">{{ Str::limit($c->title, 50) }}</a>
                        <div class="text-muted" style="font-size:11px;">{{ $c->contract_number }}</div>
                    </td>
                    <td class="text-muted small">{{ $c->contact?->full_name ?? '—' }}</td>
                    <td>{{ $c->value ? '$'.number_format($c->value,0) : '—' }}</td>
                    <td><span class="badge bg-{{ $c->status_badge }}">{{ $c->status_label }}</span></td>
                    <td class="text-muted small">{{ $c->end_date?->format('M d, Y') ?? '—' }}</td>
                    <td>
                        <div class="dropdown"><button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('contracts.show', $c) }}"><i class="bi bi-eye me-2"></i>View</a></li>
                            <li><a class="dropdown-item" href="{{ route('contracts.edit', $c) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                            <li><a class="dropdown-item" href="{{ route('contracts.pdf', $c) }}"><i class="bi bi-file-pdf me-2"></i>Download PDF</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item text-danger" onclick="confirmDelete('{{ route('contracts.destroy', $c) }}','{{ $c->title }}')"><i class="bi bi-trash me-2"></i>Delete</button></li>
                        </ul></div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-top">{{ $contracts->links() }}</div>
        @else
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-file-earmark-text-fill"></i></div>
            <h5>No contracts yet</h5>
            <p class="text-muted">Create your first contract to manage agreements with contacts and deals.</p>
            <a href="{{ route('contracts.create') }}" class="btn btn-primary">New Contract</a>
        </div>
        @endif
    </div>
</div>
@endsection
