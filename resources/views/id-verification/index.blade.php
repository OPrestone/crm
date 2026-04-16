@extends('layouts.app')
@section('title', 'ID Verification')
@section('page-title', 'ID Verification')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-shield-check me-2 text-primary"></i>ID Verification</h1>
        <p class="text-muted mb-0">KYC document management and verification workflow</p>
    </div>
    <a href="{{ route('id-verification.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Verification</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon bg-info-soft"><i class="bi bi-files"></i></div><div><div class="stat-value">{{ $stats['total'] }}</div><div class="stat-label">Total Records</div></div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon bg-warning-soft"><i class="bi bi-hourglass"></i></div><div><div class="stat-value text-warning">{{ $stats['pending'] }}</div><div class="stat-label">Pending Review</div></div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon bg-success-soft"><i class="bi bi-patch-check"></i></div><div><div class="stat-value text-success">{{ $stats['verified'] }}</div><div class="stat-label">Verified</div></div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card"><div class="stat-icon bg-danger-soft"><i class="bi bi-exclamation-triangle"></i></div><div><div class="stat-value text-danger">{{ $stats['high_risk'] }}</div><div class="stat-label">High Risk</div></div></div></div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent pt-4 px-4">
        <form method="GET" class="row g-2">
            <div class="col-md-4"><input type="text" name="search" class="form-control" placeholder="Search name or ID number…" value="{{ request('search') }}"></div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Pending</option>
                    <option value="under_review" {{ request('status')==='under_review'?'selected':'' }}>Under Review</option>
                    <option value="verified" {{ request('status')==='verified'?'selected':'' }}>Verified</option>
                    <option value="rejected" {{ request('status')==='rejected'?'selected':'' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="risk_level" class="form-select">
                    <option value="">All Risk Levels</option>
                    <option value="low" {{ request('risk_level')==='low'?'selected':'' }}>Low</option>
                    <option value="medium" {{ request('risk_level')==='medium'?'selected':'' }}>Medium</option>
                    <option value="high" {{ request('risk_level')==='high'?'selected':'' }}>High</option>
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-primary">Filter</button></div>
            @if(request()->hasAny(['search','status','risk_level']))<div class="col-auto"><a href="{{ route('id-verification.index') }}" class="btn btn-outline-secondary">Clear</a></div>@endif
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Person</th>
                        <th>ID Type</th>
                        <th>ID Number</th>
                        <th>Contact</th>
                        <th>Confidence</th>
                        <th>Risk</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($verifications as $v)
                <tr>
                    <td>
                        <div class="fw-600">{{ $v->full_name }}</div>
                        @if($v->nationality)<div class="text-muted" style="font-size:11px;">{{ $v->nationality }}</div>@endif
                    </td>
                    <td><span class="badge bg-secondary-subtle text-secondary">{{ $v->id_type_label }}</span></td>
                    <td class="font-monospace" style="font-size:13px;">{{ $v->id_number ?? '—' }}</td>
                    <td>
                        @if($v->contact)
                        <a href="{{ route('contacts.show', $v->contact) }}" class="text-decoration-none">{{ $v->contact->first_name }} {{ $v->contact->last_name }}</a>
                        @else<span class="text-muted">—</span>@endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-1" style="height:6px;width:60px;"><div class="progress-bar bg-{{ $v->confidence_score>=70?'success':($v->confidence_score>=40?'warning':'danger') }}" style="width:{{ $v->confidence_score }}%"></div></div>
                            <span style="font-size:12px;">{{ $v->confidence_score }}%</span>
                        </div>
                    </td>
                    <td><span class="badge bg-{{ $v->risk_badge }}-subtle text-{{ $v->risk_badge }}">{{ ucfirst($v->risk_level) }}</span></td>
                    <td><span class="badge bg-{{ $v->status_badge }}-subtle text-{{ $v->status_badge }}">{{ ucfirst(str_replace('_',' ',$v->status)) }}</span></td>
                    <td class="text-muted" style="font-size:12px;">{{ $v->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('id-verification.show', $v) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('id-verification.edit', $v) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-5">
                    <i class="bi bi-shield-check fs-1 d-block mb-2 opacity-25"></i>No verification records found.
                    <a href="{{ route('id-verification.create') }}" class="btn btn-primary btn-sm mt-2 d-block mx-auto" style="width:fit-content;">Add First Record</a>
                </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($verifications->hasPages())
    <div class="card-footer bg-transparent px-4 py-3">{{ $verifications->links() }}</div>
    @endif
</div>
@endsection
