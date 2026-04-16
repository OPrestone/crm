@extends('layouts.app')
@section('title', 'Audit Log')
@section('page-title', 'Audit Log')
@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div class="fw-700 fs-4 text-primary">{{ number_format($stats['today']) }}</div>
            <div class="text-muted small">Events Today</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div class="fw-700 fs-4 text-info">{{ number_format($stats['this_week']) }}</div>
            <div class="text-muted small">This Week</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div class="fw-700 fs-4">{{ number_format($stats['total']) }}</div>
            <div class="text-muted small">All Time</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center py-3">
            <div class="fw-700 fs-4 text-success">{{ $stats['users_active'] }}</div>
            <div class="text-muted small">Active Users Today</div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">All Users</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="event" class="form-control form-control-sm" placeholder="Event (e.g. contact.created)" value="{{ request('event') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary btn-sm flex-fill"><i class="bi bi-search"></i></button>
                <a href="{{ route('audit_log.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:160px">Time</th>
                    <th>Event</th>
                    <th>Resource</th>
                    <th>User</th>
                    <th>IP Address</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
            <tr>
                <td class="text-muted small">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                <td>
                    <span class="badge bg-{{ $log->event_color }}">{{ $log->event_label }}</span>
                    <code class="small ms-1 text-muted">{{ $log->event }}</code>
                </td>
                <td>
                    @if($log->auditable_type)
                        <span class="badge bg-light text-dark border">{{ $log->resource_type }}</span>
                        @if($log->auditable_id)
                            <span class="text-muted small">#{{ $log->auditable_id }}</span>
                        @endif
                    @else
                        <span class="text-muted">–</span>
                    @endif
                </td>
                <td>
                    @if($log->user)
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-700"
                                 style="width:28px;height:28px;font-size:11px">
                                {{ substr($log->user->name, 0, 1) }}
                            </div>
                            <span class="small">{{ $log->user->name }}</span>
                        </div>
                    @else
                        <span class="text-muted small">System</span>
                    @endif
                </td>
                <td class="text-muted small">{{ $log->ip_address ?? '–' }}</td>
                <td>
                    @if($log->old_values || $log->new_values)
                        <button class="btn btn-sm btn-outline-secondary py-0" data-bs-toggle="collapse" data-bs-target="#log-detail-{{ $log->id }}">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    @endif
                </td>
            </tr>
            @if($log->old_values || $log->new_values)
            <tr class="collapse" id="log-detail-{{ $log->id }}">
                <td colspan="6" class="bg-light">
                    <div class="row g-3 p-2">
                        @if($log->old_values)
                        <div class="col-md-6">
                            <div class="fw-700 small text-muted mb-1"><i class="bi bi-dash-circle text-danger me-1"></i>Before</div>
                            <pre class="small mb-0 p-2 rounded bg-white border" style="max-height:200px;overflow:auto">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                        @endif
                        @if($log->new_values)
                        <div class="col-md-6">
                            <div class="fw-700 small text-muted mb-1"><i class="bi bi-plus-circle text-success me-1"></i>After</div>
                            <pre class="small mb-0 p-2 rounded bg-white border" style="max-height:200px;overflow:auto">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                        @endif
                        @if($log->url)
                        <div class="col-12">
                            <span class="badge bg-{{ $log->method === 'DELETE' ? 'danger' : ($log->method === 'POST' ? 'success' : ($log->method === 'PATCH' || $log->method === 'PUT' ? 'warning' : 'secondary')) }}">{{ $log->method }}</span>
                            <code class="small text-muted">{{ $log->url }}</code>
                        </div>
                        @endif
                    </div>
                </td>
            </tr>
            @endif
            @empty
            <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                    <i class="bi bi-shield-check display-6 d-block mb-2"></i>
                    No audit events recorded yet.
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </div>
    @if($logs->hasPages())
    <div class="card-footer bg-transparent">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
