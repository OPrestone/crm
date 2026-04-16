@extends('layouts.app')
@section('title', 'Developer Portal')
@section('page-title', 'Developer Portal')

@push('styles')
<style>
.dev-stat { background:#fff;border:1px solid var(--color-border);border-radius:12px;padding:20px 24px;display:flex;align-items:center;gap:16px; }
.dev-stat-icon { width:44px;height:44px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0; }
.dev-stat-val { font-size:1.6rem;font-weight:800;line-height:1; }
.dev-stat-label { font-size:12px;color:var(--color-muted);margin-top:2px; }
.endpoint-row { font-family:monospace;font-size:13px; }
.app-card { background:#fff;border:1px solid var(--color-border);border-radius:12px;padding:20px;transition:box-shadow .15s;cursor:pointer; }
.app-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.08); }
.log-method { font-family:monospace;font-size:11px;font-weight:700;padding:2px 7px;border-radius:4px; }
</style>
@endpush

@section('content')

{{-- Stats row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="dev-stat">
            <div class="dev-stat-icon bg-primary-subtle"><i class="bi bi-app-indicator text-primary"></i></div>
            <div>
                <div class="dev-stat-val">{{ $stats['apps'] }}</div>
                <div class="dev-stat-label">API Applications</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="dev-stat">
            <div class="dev-stat-icon bg-success-subtle"><i class="bi bi-lightning-charge-fill text-success"></i></div>
            <div>
                <div class="dev-stat-val">{{ number_format($stats['calls_today']) }}</div>
                <div class="dev-stat-label">API Calls Today</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="dev-stat">
            <div class="dev-stat-icon bg-info-subtle"><i class="bi bi-send-fill text-info"></i></div>
            <div>
                <div class="dev-stat-val">{{ number_format($stats['webhooks_sent']) }}</div>
                <div class="dev-stat-label">Webhooks Delivered</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="dev-stat">
            <div class="dev-stat-icon bg-danger-subtle"><i class="bi bi-exclamation-octagon-fill text-danger"></i></div>
            <div>
                <div class="dev-stat-val">{{ number_format($stats['errors_today']) }}</div>
                <div class="dev-stat-label">Errors Today</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Left: Chart + quick actions --}}
    <div class="col-lg-8">
        {{-- API traffic chart --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                <div class="fw-600"><i class="bi bi-activity text-primary me-2"></i>API Traffic — Last 7 Days</div>
                <div class="d-flex gap-2 text-muted" style="font-size:12px;">
                    <span><span class="badge bg-primary-subtle text-primary rounded-pill">●</span> Total</span>
                    <span><span class="badge bg-danger-subtle text-danger rounded-pill">●</span> Errors</span>
                </div>
            </div>
            <div class="card-body">
                <canvas id="apiChart" style="height:200px;"></canvas>
            </div>
        </div>

        {{-- Recent API logs --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                <div class="fw-600"><i class="bi bi-journal-code text-secondary me-2"></i>Recent API Requests</div>
                <a href="{{ route('developer.logs') }}" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Method</th>
                            <th>Endpoint</th>
                            <th>Status</th>
                            <th class="text-end">Time</th>
                            <th class="text-end">When</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLogs as $log)
                        <tr>
                            <td><span class="badge bg-{{ $log->method_color }}-subtle text-{{ $log->method_color }} log-method">{{ $log->method }}</span></td>
                            <td class="endpoint-row text-truncate" style="max-width:260px;">{{ $log->endpoint }}</td>
                            <td><span class="badge bg-{{ $log->status_color }}-subtle text-{{ $log->status_color }}">{{ $log->status_code }}</span></td>
                            <td class="text-end text-muted" style="font-size:12px;">{{ $log->response_time_ms }}ms</td>
                            <td class="text-end text-muted" style="font-size:12px;">{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No API requests yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Right: Apps + quick links --}}
    <div class="col-lg-4">
        {{-- Quick actions --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-600 py-3"><i class="bi bi-grid-1x2 me-2 text-primary"></i>Quick Actions</div>
            <div class="card-body p-3">
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('developer.apps.create') }}" class="btn btn-primary w-100 text-start">
                        <i class="bi bi-plus-circle me-2"></i>Create API Application
                    </a>
                    <a href="{{ route('developer.docs') }}" class="btn btn-outline-secondary w-100 text-start">
                        <i class="bi bi-book me-2"></i>API Documentation
                    </a>
                    <a href="{{ route('developer.logs') }}" class="btn btn-outline-secondary w-100 text-start">
                        <i class="bi bi-journal-code me-2"></i>View Request Logs
                    </a>
                    <a href="{{ route('developer.webhooks') }}" class="btn btn-outline-secondary w-100 text-start">
                        <i class="bi bi-broadcast me-2"></i>Webhook Deliveries
                    </a>
                </div>
            </div>
        </div>

        {{-- Your apps --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                <div class="fw-600"><i class="bi bi-app-indicator me-2 text-success"></i>Your Applications</div>
                <a href="{{ route('developer.apps') }}" class="btn btn-sm btn-outline-secondary">All Apps</a>
            </div>
            <div class="card-body p-3">
                @forelse($apps as $app)
                <a href="{{ route('developer.apps.show', $app) }}" class="text-decoration-none">
                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3 hover-bg mb-1">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center {{ $app->is_active ? 'bg-success' : 'bg-secondary' }}" style="width:8px;height:8px;flex-shrink:0;"></div>
                            <div>
                                <div class="fw-600" style="font-size:13px;">{{ $app->name }}</div>
                                <div class="text-muted" style="font-size:11px;font-family:monospace;">{{ substr($app->client_id, 0, 16) }}…</div>
                            </div>
                        </div>
                        <span class="text-muted" style="font-size:11px;">{{ number_format($app->total_requests) }} calls</span>
                    </div>
                </a>
                @empty
                <div class="text-center text-muted py-3" style="font-size:13px;">
                    <i class="bi bi-code-slash d-block mb-2" style="font-size:24px;"></i>No apps yet — create one!
                </div>
                @endforelse
            </div>
        </div>

        {{-- Recent webhooks --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-600 py-3"><i class="bi bi-broadcast me-2 text-info"></i>Recent Webhooks</div>
            <div class="card-body p-0">
                @forelse($recentWebhooks as $wh)
                <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                    <div>
                        <div style="font-size:12px;font-family:monospace;font-weight:600;">{{ $wh->event }}</div>
                        <div class="text-muted" style="font-size:11px;">{{ $wh->created_at->diffForHumans() }}</div>
                    </div>
                    {!! $wh->status_badge !!}
                </div>
                @empty
                <div class="text-center text-muted py-3" style="font-size:13px;">No webhooks fired yet</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('apiChart'), {
    type: 'bar',
    data: {
        labels: @json($chartDays),
        datasets: [
            {
                label: 'Total Calls',
                data: @json($chartCalls),
                backgroundColor: 'rgba(13,110,253,.15)',
                borderColor: '#0d6efd',
                borderWidth: 2,
                borderRadius: 4,
            },
            {
                label: 'Errors',
                data: @json($chartErrors),
                backgroundColor: 'rgba(220,53,69,.15)',
                borderColor: '#dc3545',
                borderWidth: 2,
                borderRadius: 4,
            }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
});
</script>
@endpush
