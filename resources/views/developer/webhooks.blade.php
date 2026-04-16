@extends('layouts.app')
@section('title', 'Webhook Deliveries')
@section('page-title', 'Webhook Deliveries')

@section('content')
{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-600">Application</label>
                <select name="app_id" class="form-select form-select-sm">
                    <option value="">All Apps</option>
                    @foreach($apps as $app)
                    <option value="{{ $app->id }}" {{ request('app_id') == $app->id ? 'selected' : '' }}>{{ $app->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-600">Event</label>
                <select name="event" class="form-select form-select-sm">
                    <option value="">All Events</option>
                    @foreach($events as $slug => $label)
                    <option value="{{ $slug }}" {{ request('event') === $slug ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-600">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="failed"    {{ request('status') === 'failed'    ? 'selected' : '' }}>Failed</option>
                    <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary btn-sm me-1"><i class="bi bi-funnel me-1"></i>Filter</button>
                <a href="{{ route('developer.webhooks') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
        <div class="fw-600"><i class="bi bi-broadcast me-2 text-info"></i>Webhook Deliveries <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $deliveries->total() }}</span></div>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Event</th>
                    <th>App</th>
                    <th>Endpoint</th>
                    <th>Status</th>
                    <th class="text-center">Code</th>
                    <th class="text-center">Attempts</th>
                    <th class="text-end">When</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($deliveries as $delivery)
                <tr>
                    <td style="font-family:monospace;font-size:11px;font-weight:600;">{{ $delivery->event }}</td>
                    <td style="font-size:12px;">{{ optional($delivery->app)->name ?? '—' }}</td>
                    <td class="text-truncate text-muted" style="max-width:180px;font-size:11px;">{{ $delivery->endpoint_url }}</td>
                    <td>{!! $delivery->status_badge !!}</td>
                    <td class="text-center">
                        @if($delivery->response_code)
                        <span class="badge bg-{{ $delivery->response_code < 300 ? 'success' : ($delivery->response_code < 500 ? 'warning' : 'danger') }}-subtle text-{{ $delivery->response_code < 300 ? 'success' : ($delivery->response_code < 500 ? 'warning' : 'danger') }}">{{ $delivery->response_code }}</span>
                        @else —
                        @endif
                    </td>
                    <td class="text-center" style="font-size:12px;">{{ $delivery->attempts }}</td>
                    <td class="text-end text-muted" style="font-size:11px;" title="{{ $delivery->created_at }}">{{ $delivery->created_at->diffForHumans() }}</td>
                    <td>
                        <button class="btn btn-xs btn-outline-secondary border-0 py-0 px-1" data-bs-toggle="collapse" data-bs-target="#wh{{ $delivery->id }}">
                            <i class="bi bi-chevron-down" style="font-size:10px;"></i>
                        </button>
                    </td>
                </tr>
                <tr id="wh{{ $delivery->id }}" class="collapse">
                    <td colspan="8" class="bg-dark-subtle p-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="small fw-600 mb-1 text-muted">Payload Sent</div>
                                <pre class="bg-dark text-light rounded p-2 mb-0" style="font-size:11px;max-height:180px;overflow:auto;">{{ json_encode($delivery->payload, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                            <div class="col-md-6">
                                <div class="small fw-600 mb-1 text-muted">Response Received</div>
                                <pre class="bg-dark text-light rounded p-2 mb-0" style="font-size:11px;max-height:180px;overflow:auto;">{{ $delivery->response_body ?: '(no response body)' }}</pre>
                            </div>
                        </div>
                        @if($delivery->error_message)
                        <div class="mt-2 text-danger small"><i class="bi bi-exclamation-triangle me-1"></i>{{ $delivery->error_message }}</div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-5">No webhook deliveries found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($deliveries->hasPages())
    <div class="card-footer bg-white">{{ $deliveries->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
