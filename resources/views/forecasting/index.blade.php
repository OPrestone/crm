@extends('layouts.app')
@section('title', 'Sales Forecasting')
@section('page-title', 'Sales Forecasting')
@section('content')
<div class="page-header">
    <div><h1>Sales Forecasting</h1></div>
    <div class="d-flex gap-2">
        <form method="GET" class="d-flex gap-2 align-items-center">
            <select name="period" class="form-select form-select-sm" onchange="this.form.submit()">
                @foreach($periods as $value => $label)
                <option value="{{ $value }}" {{ $period === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

@if(session('success'))<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>@endif

{{-- KPI Row --}}
<div class="row g-3 mb-4">
    @php $pct = $quotaAmount > 0 ? min(100, round($wonValue / $quotaAmount * 100)) : 0; @endphp
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="text-muted small mb-1">Won This Period</div>
                <div class="fs-2 fw-700 text-success">${{ number_format($wonValue, 0) }}</div>
                <div class="text-muted small">{{ $wonDeals->count() }} deals closed</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="text-muted small mb-1">Weighted Pipeline</div>
                <div class="fs-2 fw-700 text-primary">${{ number_format($weightedTotal, 0) }}</div>
                <div class="text-muted small">{{ $openDeals->count() }} open deals</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="text-muted small mb-1">Quota Attainment</div>
                <div class="fs-2 fw-700 {{ $pct >= 100 ? 'text-success' : ($pct >= 60 ? 'text-warning' : 'text-danger') }}">{{ $pct }}%</div>
                <div class="progress mt-2" style="height:6px;"><div class="progress-bar bg-{{ $pct >= 100 ? 'success' : ($pct >= 60 ? 'warning' : 'danger') }}" style="width:{{ $pct }}%"></div></div>
                <div class="text-muted small mt-1">Quota: ${{ number_format($quotaAmount, 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="text-muted small mb-1">Total Pipeline Value</div>
                <div class="fs-2 fw-700">${{ number_format($openDeals->sum('value'), 0) }}</div>
                <div class="text-muted small">unweighted open deals</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- Revenue Trend Chart --}}
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header fw-600">Revenue Trend (Last 6 Months)</div>
            <div class="card-body"><canvas id="trendChart" height="120"></canvas></div>
        </div>
    </div>

    {{-- Set Quota --}}
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header fw-600">Set Quota</div>
            <div class="card-body">
                <form method="POST" action="{{ route('forecasting.quota') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-600">Rep (or leave blank for team quota)</label>
                        <select name="user_id" class="form-select">
                            <option value="">Team / All Reps</option>
                            @foreach($users as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Period</label>
                        <select name="period" class="form-select">
                            @foreach($periods as $v => $l)<option value="{{ $v }}" {{ $v === $period ? 'selected' : '' }}>{{ $l }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Quota Amount ($)</label>
                        <input type="number" name="amount" class="form-control" value="{{ $quotaAmount }}" min="0" step="100" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-bullseye me-1"></i>Set Quota</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Stage Funnel --}}
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header fw-600">Pipeline by Stage</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Stage</th><th>Deals</th><th>Value</th><th>Weighted</th></tr></thead>
                    <tbody>
                    @foreach($stages as $stage)
                    <tr>
                        <td><span class="badge" style="background:{{ $stage['color'] }}20;color:{{ $stage['color'] }};border:1px solid {{ $stage['color'] }}40;">{{ $stage['name'] }}</span></td>
                        <td>{{ $stage['count'] }}</td>
                        <td>${{ number_format($stage['value'], 0) }}</td>
                        <td class="fw-600">${{ number_format($stage['weighted'], 0) }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Rep breakdown --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header fw-600">Rep Performance</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Rep</th><th>Won</th><th>Pipeline</th><th>Quota</th><th>%</th></tr></thead>
                    <tbody>
                    @foreach($repData as $rd)
                    @if($rd['deals'] > 0 || $rd['won'] > 0 || $rd['quota'] > 0)
                    @php $repPct = $rd['quota'] > 0 ? min(100, round($rd['won'] / $rd['quota'] * 100)) : 0; @endphp
                    <tr>
                        <td class="fw-600">{{ $rd['user']->name }}</td>
                        <td class="text-success">${{ number_format($rd['won'], 0) }}</td>
                        <td>${{ number_format($rd['pipeline'], 0) }}</td>
                        <td class="text-muted">${{ number_format($rd['quota'], 0) }}</td>
                        <td><span class="badge bg-{{ $repPct >= 100 ? 'success' : ($repPct >= 50 ? 'warning' : 'danger') }}">{{ $repPct }}%</span></td>
                    </tr>
                    @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Open Deals Table --}}
<div class="card">
    <div class="card-header fw-600 d-flex justify-content-between align-items-center">
        <span>Open Pipeline Deals</span>
        <span class="badge bg-primary">{{ $openDeals->count() }}</span>
    </div>
    <div class="card-body p-0">
        @if($openDeals->count())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Deal</th><th>Contact</th><th>Stage</th><th>Probability</th><th>Value</th><th>Weighted</th><th>Assigned</th></tr></thead>
                <tbody>
                @foreach($openDeals as $d)
                <tr>
                    <td><a href="{{ route('deals.show', $d) }}" class="fw-600 text-decoration-none">{{ Str::limit($d->title, 40) }}</a></td>
                    <td class="text-muted small">{{ $d->contact?->full_name ?? '—' }}</td>
                    <td>@if($d->stage)<span class="badge" style="background:{{ $d->stage->color }}20;color:{{ $d->stage->color }};border:1px solid {{ $d->stage->color }}40;">{{ $d->stage->name }}</span>@else<span class="text-muted">—</span>@endif</td>
                    <td><span class="badge bg-secondary">{{ $d->probability }}%</span></td>
                    <td>${{ number_format($d->value, 0) }}</td>
                    <td class="fw-600 text-primary">${{ number_format($d->value * $d->probability / 100, 0) }}</td>
                    <td class="text-muted small">{{ $d->assignedTo?->name ?? '—' }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state py-4"><p class="text-muted">No open deals in pipeline.</p></div>
        @endif
    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('assets/vendor/chartjs/chart.min.js') }}"></script>
<script>
const trendData = @json($trend);
new Chart(document.getElementById('trendChart'), {
    type: 'bar',
    data: {
        labels: trendData.map(d => d.label),
        datasets: [{
            label: 'Won Revenue ($)',
            data: trendData.map(d => d.value),
            backgroundColor: 'rgba(25, 135, 84, 0.7)',
            borderColor: '#198754',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { callback: v => '$' + v.toLocaleString() },
                grid: { color: 'rgba(0,0,0,.05)' }
            },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush
