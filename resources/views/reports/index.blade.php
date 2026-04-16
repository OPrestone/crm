@extends('layouts.app')
@section('title', 'Reports')
@section('page-title', 'Reports & Analytics')
@section('content')
<div class="page-header">
    <div><h1>Reports & Analytics</h1></div>
    <a href="{{ route('reports.pdf') }}" target="_blank" class="btn btn-outline-danger"><i class="bi bi-file-pdf me-1"></i>Export PDF Report</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="stat-value text-primary">${{ number_format($dealStats['won_value'], 0) }}</div>
            <div class="text-muted">Revenue Won</div>
            <div class="text-success" style="font-size:12px;">${{ number_format($dealStats['total_value'], 0) }} total pipeline</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="stat-value text-warning">{{ $leadConversion['rate'] }}%</div>
            <div class="text-muted">Lead Conversion</div>
            <div class="text-muted" style="font-size:12px;">{{ $leadConversion['converted'] }}/{{ $leadConversion['total'] }} leads</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="stat-value text-info">${{ number_format($dealStats['avg_value'], 0) }}</div>
            <div class="text-muted">Avg Deal Value</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="stat-value text-success">{{ $taskCompletion['total'] > 0 ? round($taskCompletion['completed'] / $taskCompletion['total'] * 100) : 0 }}%</div>
            <div class="text-muted">Task Completion</div>
            <div class="text-muted" style="font-size:12px;">{{ $taskCompletion['overdue'] }} overdue</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">12-Month Revenue</h5></div>
            <div class="card-body px-4"><div class="chart-container" style="height:260px;"><canvas id="annualChart"></canvas></div></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Lead Sources</h5></div>
            <div class="card-body px-4">
                @forelse($contactSources as $src)
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span style="font-size:13px;">{{ $src->source ?? 'Unknown' }}</span>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress" style="width:80px;height:6px;"><div class="progress-bar bg-primary" style="width:{{ $contactSources->max('count') > 0 ? ($src->count / $contactSources->max('count') * 100) : 0 }}%"></div></div>
                        <span class="badge bg-light text-dark">{{ $src->count }}</span>
                    </div>
                </div>
                @empty<div class="text-center text-muted py-4" style="font-size:13px;">No data yet</div>@endforelse
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Lead Conversion</h5></div>
            <div class="card-body px-4">
                <div class="chart-container" style="height:200px;"><canvas id="leadChart"></canvas></div>
                <div class="row g-3 text-center mt-2">
                    <div class="col-4"><div class="fw-700 text-primary">{{ $leadConversion['total'] }}</div><div class="text-muted" style="font-size:12px;">Total</div></div>
                    <div class="col-4"><div class="fw-700 text-success">{{ $leadConversion['converted'] }}</div><div class="text-muted" style="font-size:12px;">Converted</div></div>
                    <div class="col-4"><div class="fw-700 text-danger">{{ $leadConversion['lost'] }}</div><div class="text-muted" style="font-size:12px;">Lost</div></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Task Performance</h5></div>
            <div class="card-body px-4">
                @php $rate = $taskCompletion['total'] > 0 ? round($taskCompletion['completed'] / $taskCompletion['total'] * 100) : 0; @endphp
                <div class="text-center my-4">
                    <div style="font-size:48px;font-weight:700;color:#198754;">{{ $rate }}%</div>
                    <div class="text-muted">Completion Rate</div>
                </div>
                <div class="progress mb-3" style="height:12px;"><div class="progress-bar bg-success" style="width:{{ $rate }}%"></div></div>
                <div class="row g-3 text-center">
                    <div class="col-4"><div class="fw-700">{{ $taskCompletion['total'] }}</div><div class="text-muted" style="font-size:12px;">Total</div></div>
                    <div class="col-4"><div class="fw-700 text-success">{{ $taskCompletion['completed'] }}</div><div class="text-muted" style="font-size:12px;">Done</div></div>
                    <div class="col-4"><div class="fw-700 text-danger">{{ $taskCompletion['overdue'] }}</div><div class="text-muted" style="font-size:12px;">Overdue</div></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('annualChart'), {
    type: 'bar',
    data: {
        labels: @json($months),
        datasets: [{
            label: 'Revenue',
            data: @json($salesData),
            backgroundColor: 'rgba(13,110,253,.15)',
            borderColor: '#0d6efd',
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { color: 'rgba(0,0,0,.04)' }, ticks: { callback: v => '$' + v.toLocaleString(), font: { size: 11 } } },
            x: { grid: { display: false }, ticks: { font: { size: 10 } } }
        }
    }
});

new Chart(document.getElementById('leadChart'), {
    type: 'doughnut',
    data: {
        labels: ['Converted', 'Lost', 'Active'],
        datasets: [{
            data: [{{ $leadConversion['converted'] }}, {{ $leadConversion['lost'] }}, {{ max(0, $leadConversion['total'] - $leadConversion['converted'] - $leadConversion['lost']) }}],
            backgroundColor: ['#198754', '#dc3545', '#0d6efd'],
            borderWidth: 0,
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } } }
});
</script>
@endpush
