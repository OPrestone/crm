@extends('layouts.app')
@section('title', 'API Request Logs')
@section('page-title', 'API Request Logs')

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
            <div class="col-md-2">
                <label class="form-label small fw-600">Method</label>
                <select name="method" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach(['GET','POST','PUT','PATCH','DELETE'] as $m)
                    <option value="{{ $m }}" {{ request('method') === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-600">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="2xx" {{ request('status') === '2xx' ? 'selected' : '' }}>2xx Success</option>
                    <option value="4xx" {{ request('status') === '4xx' ? 'selected' : '' }}>4xx Client Error</option>
                    <option value="5xx" {{ request('status') === '5xx' ? 'selected' : '' }}>5xx Server Error</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-600">Date</label>
                <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm me-1"><i class="bi bi-funnel me-1"></i>Filter</button>
                <a href="{{ route('developer.logs') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
        <div class="fw-600"><i class="bi bi-journal-code me-2 text-secondary"></i>Request Log <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $logs->total() }}</span></div>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Method</th>
                    <th>Endpoint</th>
                    <th>App</th>
                    <th>Status</th>
                    <th>IP</th>
                    <th class="text-end">Time (ms)</th>
                    <th class="text-end">When</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td><span class="badge bg-{{ $log->method_color }}-subtle text-{{ $log->method_color }}" style="font-family:monospace;font-size:10px;">{{ $log->method }}</span></td>
                    <td class="text-truncate" style="max-width:240px;font-family:monospace;font-size:12px;">{{ $log->endpoint }}</td>
                    <td style="font-size:12px;">{{ optional($log->app)->name ?? '—' }}</td>
                    <td><span class="badge bg-{{ $log->status_color }}-subtle text-{{ $log->status_color }}">{{ $log->status_code }}</span></td>
                    <td style="font-size:11px;font-family:monospace;">{{ $log->ip_address ?? '—' }}</td>
                    <td class="text-end" style="font-size:12px;">{{ number_format($log->response_time_ms) }}</td>
                    <td class="text-end text-muted" style="font-size:11px;" title="{{ $log->created_at }}">{{ $log->created_at->diffForHumans() }}</td>
                    <td>
                        @if($log->request_body || $log->response_body)
                        <button class="btn btn-xs btn-outline-secondary border-0 py-0 px-1" data-bs-toggle="collapse" data-bs-target="#log{{ $log->id }}">
                            <i class="bi bi-chevron-down" style="font-size:10px;"></i>
                        </button>
                        @endif
                    </td>
                </tr>
                @if($log->request_body || $log->response_body)
                <tr id="log{{ $log->id }}" class="collapse">
                    <td colspan="8" class="bg-dark-subtle p-3">
                        <div class="row g-3">
                            @if($log->request_body)
                            <div class="col-md-6">
                                <div class="small fw-600 mb-1 text-muted">Request Body</div>
                                <pre class="bg-dark text-light rounded p-2 mb-0" style="font-size:11px;max-height:150px;overflow:auto;">{{ json_encode(json_decode($log->request_body), JSON_PRETTY_PRINT) ?: $log->request_body }}</pre>
                            </div>
                            @endif
                            @if($log->response_body)
                            <div class="col-md-6">
                                <div class="small fw-600 mb-1 text-muted">Response Body</div>
                                <pre class="bg-dark text-light rounded p-2 mb-0" style="font-size:11px;max-height:150px;overflow:auto;">{{ json_encode(json_decode($log->response_body), JSON_PRETTY_PRINT) ?: $log->response_body }}</pre>
                            </div>
                            @endif
                        </div>
                        @if($log->error_message)
                        <div class="mt-2 text-danger small"><i class="bi bi-exclamation-triangle me-1"></i>{{ $log->error_message }}</div>
                        @endif
                    </td>
                </tr>
                @endif
                @empty
                <tr><td colspan="8" class="text-center text-muted py-5">No API request logs found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="card-footer bg-white">{{ $logs->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
