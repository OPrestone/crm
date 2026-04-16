@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-soft"><i class="bi bi-person-lines-fill"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['contacts']) }}</div>
                <div class="stat-label">Total Contacts</div>
                <div class="stat-change text-success"><i class="bi bi-arrow-up-short"></i>{{ $stats['new_contacts'] }} this month</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning-soft"><i class="bi bi-funnel-fill"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['leads']) }}</div>
                <div class="stat-label">Active Leads</div>
                <div class="stat-change text-info"><i class="bi bi-arrow-up-short"></i>{{ $stats['new_leads'] }} this month</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-soft"><i class="bi bi-briefcase-fill"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['deals']) }}</div>
                <div class="stat-label">Open Deals</div>
                <div class="stat-change text-muted">{{ number_format($stats['won_deals']) }} won</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon bg-info-soft"><i class="bi bi-currency-dollar"></i></div>
            <div>
                <div class="stat-value">${{ number_format($stats['revenue'], 0) }}</div>
                <div class="stat-label">Total Revenue</div>
                <div class="stat-change text-success"><i class="bi bi-arrow-up-short"></i>${{ number_format($stats['monthly_revenue'], 0) }} this month</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-transparent border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="fw-700 mb-0">Revenue Overview</h5>
                    <select class="form-select form-select-sm w-auto">
                        <option>Last 6 months</option>
                        <option>Last 12 months</option>
                    </select>
                </div>
            </div>
            <div class="card-body px-4">
                <div class="chart-container" style="height:240px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-transparent border-0 pb-0 pt-4 px-4">
                <h5 class="fw-700 mb-0">Deal Pipeline</h5>
            </div>
            <div class="card-body px-4">
                <div class="chart-container" style="height:200px;">
                    <canvas id="pipelineChart"></canvas>
                </div>
                <div class="mt-3">
                    @foreach($pipelineData as $stage)
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:10px;height:10px;border-radius:50%;background:{{ $stage['color'] }};flex-shrink:0;"></div>
                            <span style="font-size:13px;">{{ $stage['name'] }}</span>
                        </div>
                        <span class="badge bg-light text-dark">{{ $stage['count'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-transparent border-0 pb-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-700 mb-0">Recent Tasks</h5>
                <a href="{{ route('tasks.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
            </div>
            <div class="card-body px-4">
                @forelse($recentTasks as $task)
                <div class="d-flex align-items-start gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="form-check mt-1">
                        <input class="form-check-input" type="checkbox" {{ $task->status === 'completed' ? 'checked' : '' }} disabled>
                    </div>
                    <div class="flex-1">
                        <div class="fw-600" style="font-size:13px;">{{ Str::limit($task->title, 40) }}</div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="badge bg-{{ $task->priority_badge }}-subtle text-{{ $task->priority_badge }}" style="font-size:10px;">{{ ucfirst($task->priority) }}</span>
                            @if($task->due_date)
                            <span class="text-muted" style="font-size:11px;"><i class="bi bi-calendar3"></i> {{ $task->due_date->format('M j') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-3" style="font-size:13px;">No tasks yet</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-transparent border-0 pb-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-700 mb-0">Recent Leads</h5>
                <a href="{{ route('leads.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
            </div>
            <div class="card-body px-4">
                @forelse($recentLeads as $lead)
                <div class="d-flex align-items-center gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="avatar-circle bg-{{ $lead->status_badge }} text-white" style="width:36px;height:36px;font-size:13px;flex-shrink:0;">
                        {{ strtoupper(substr($lead->title, 0, 2)) }}
                    </div>
                    <div class="flex-1">
                        <div class="fw-600" style="font-size:13px;">{{ Str::limit($lead->title, 30) }}</div>
                        <div class="text-muted" style="font-size:11px;">{{ $lead->source ?? 'Direct' }} • {{ $lead->created_at->diffForHumans() }}</div>
                    </div>
                    <span class="badge bg-{{ $lead->status_badge }}">{{ ucfirst($lead->status) }}</span>
                </div>
                @empty
                <div class="text-center text-muted py-3" style="font-size:13px;">No leads yet</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-transparent border-0 pb-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-700 mb-0">Recent Activity</h5>
            </div>
            <div class="card-body px-4">
                @forelse($recentActivities as $activity)
                <div class="timeline-item mb-3">
                    <div class="d-flex align-items-start gap-2">
                        <div class="avatar-circle bg-{{ $activity->type_color }} text-white" style="width:28px;height:28px;font-size:11px;flex-shrink:0;">
                            <i class="bi bi-{{ $activity->type_icon }}"></i>
                        </div>
                        <div class="flex-1">
                            <div style="font-size:13px;">{{ $activity->subject }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ $activity->user?->name ?? 'System' }} • {{ $activity->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-3" style="font-size:13px;">No activity yet</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const months = @json($chartData['months']);
const revenues = @json($chartData['revenues']);

new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: months,
        datasets: [{
            label: 'Revenue',
            data: revenues,
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13,110,253,.1)',
            fill: true,
            tension: 0.4,
            borderWidth: 2.5,
            pointBackgroundColor: '#0d6efd',
            pointRadius: 4,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { grid: { color: 'rgba(0,0,0,.04)' }, ticks: { font: { size: 11 }, callback: v => '$' + v.toLocaleString() } },
            x: { grid: { display: false }, ticks: { font: { size: 11 } } }
        }
    }
});

const pipelineLabels = @json($pipelineData->pluck('name'));
const pipelineCounts = @json($pipelineData->pluck('count'));
const pipelineColors = @json($pipelineData->pluck('color'));

new Chart(document.getElementById('pipelineChart'), {
    type: 'doughnut',
    data: {
        labels: pipelineLabels,
        datasets: [{ data: pipelineCounts, backgroundColor: pipelineColors, borderWidth: 0 }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        cutout: '70%',
    }
});
</script>
@endpush

@if($showOnboarding ?? false)
@include('onboarding.walkthrough')
@endif
