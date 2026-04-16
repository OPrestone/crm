@extends('layouts.app')
@section('title', 'Commission Tracking')
@section('page-title', 'Commission Tracking')
@section('content')
<div class="page-header">
    <div><h1>Commission Tracking</h1></div>
    <div class="d-flex gap-2">
        <a href="{{ route('commissions.plans') }}" class="btn btn-outline-secondary"><i class="bi bi-list-check me-1"></i>Plans</a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#calculateModal"><i class="bi bi-calculator me-1"></i>Calculate Commission</button>
    </div>
</div>

@if(session('success'))<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}</div>@endif

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card text-center"><div class="card-body py-3"><div class="fs-4 fw-700 text-warning">${{ number_format($stats['total_pending'],2) }}</div><div class="small text-muted">Pending</div></div></div></div>
    <div class="col-md-3"><div class="card text-center"><div class="card-body py-3"><div class="fs-4 fw-700 text-info">${{ number_format($stats['total_approved'],2) }}</div><div class="small text-muted">Approved</div></div></div></div>
    <div class="col-md-3"><div class="card text-center"><div class="card-body py-3"><div class="fs-4 fw-700 text-success">${{ number_format($stats['total_paid'],2) }}</div><div class="small text-muted">Paid Out</div></div></div></div>
    <div class="col-md-3"><div class="card text-center"><div class="card-body py-3"><div class="fs-4 fw-700">{{ $stats['count'] }}</div><div class="small text-muted">Total Records</div></div></div></div>
</div>

<div class="row g-4 mb-4">
    {{-- Rep earnings summary --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header fw-600">Rep Earnings</div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                @foreach($repSummary as $rd)
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fw-600">{{ $rd['user']->name }}</div>
                        <div class="text-success fw-700">${{ number_format($rd['paid'],2) }}</div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-warning">Pending: ${{ number_format($rd['pending'],2) }}</small>
                        <small class="text-muted">{{ $rd['count'] }} records</small>
                    </div>
                </div>
                @endforeach
                @if($repSummary->isEmpty())
                <div class="list-group-item text-center text-muted py-4">No commission records yet.</div>
                @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Commission table --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header fw-600 d-flex justify-content-between align-items-center">
                <span>All Commissions</span>
                <form method="GET" class="d-flex gap-2">
                    <select name="user_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Reps</option>
                        @foreach($users as $u)<option value="{{ $u->id }}" {{ request('user_id')==$u->id?'selected':'' }}>{{ $u->name }}</option>@endforeach
                    </select>
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        @foreach(['pending','approved','paid'] as $s)<option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach
                    </select>
                </form>
            </div>
            <div class="card-body p-0">
                @if($commissions->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Rep</th><th>Deal</th><th>Plan</th><th>Deal Value</th><th>Commission</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                        @foreach($commissions as $c)
                        <tr>
                            <td class="fw-600">{{ $c->user?->name ?? '—' }}</td>
                            <td class="text-muted small"><a href="{{ route('deals.show', $c->deal_id) }}" class="text-decoration-none">{{ Str::limit($c->deal?->title, 30) }}</a></td>
                            <td class="text-muted small">{{ $c->plan?->name ?? '—' }}</td>
                            <td>${{ number_format($c->deal_value,2) }}</td>
                            <td class="fw-700 text-success">${{ number_format($c->amount,2) }}</td>
                            <td><span class="badge bg-{{ $c->status_badge }}">{{ ucfirst($c->status) }}</span></td>
                            <td>
                                <div class="dropdown"><button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                                <ul class="dropdown-menu">
                                    @if($c->status === 'pending')
                                    <li><form method="POST" action="{{ route('commissions.approve', $c) }}">@csrf <button type="submit" class="dropdown-item text-info"><i class="bi bi-check2-circle me-2"></i>Approve</button></form></li>
                                    @endif
                                    @if($c->status === 'approved')
                                    <li><form method="POST" action="{{ route('commissions.pay', $c) }}">@csrf <button type="submit" class="dropdown-item text-success"><i class="bi bi-currency-dollar me-2"></i>Mark Paid</button></form></li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    <li><form method="POST" action="{{ route('commissions.destroy', $c) }}">@csrf @method('DELETE') <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Delete</button></form></li>
                                </ul></div>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-top">{{ $commissions->links() }}</div>
                @else
                <div class="empty-state py-5">
                    <div class="empty-icon"><i class="bi bi-cash-coin"></i></div>
                    <h6>No commission records</h6>
                    <p class="text-muted small">Use "Calculate Commission" to add records from won deals.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Calculate Modal --}}
<div class="modal fade" id="calculateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title fw-700">Calculate Commission</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('commissions.calculate') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-600">Sales Rep <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select" required>
                            <option value="">Select rep...</option>
                            @foreach($users as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Won Deal <span class="text-danger">*</span></label>
                        <select name="deal_id" class="form-select" required>
                            <option value="">Select deal...</option>
                            @foreach(\App\Models\Deal::where('tenant_id', auth()->user()->tenant_id)->where('status','won')->orderByDesc('updated_at')->get() as $d)
                            <option value="{{ $d->id }}">${{ number_format($d->value,0) }} — {{ $d->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Commission Plan <span class="text-danger">*</span></label>
                        <select name="plan_id" class="form-select" required>
                            <option value="">Select plan...</option>
                            @foreach($plans as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ ucfirst($p->type) }}: {{ $p->type === 'flat' ? '$'.$p->rate : $p->rate.'%' }})</option>@endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-calculator me-1"></i>Calculate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
