@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- ── Quick Actions ──────────────────────────────────────────────────── --}}
<div class="d-flex align-items-center gap-2 mb-4 flex-wrap">
    <span class="text-muted me-1" style="font-size:13px;">Quick add:</span>
    <a href="{{ route('contacts.create') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-person-plus me-1"></i>Contact
    </a>
    <a href="{{ route('leads.create') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-funnel me-1"></i>Lead
    </a>
    <a href="{{ route('deals.create') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-briefcase me-1"></i>Deal
    </a>
    <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-check2-square me-1"></i>Task
    </a>
    @if($stats['overdueTasks'] > 0)
    <a href="{{ route('tasks.index') }}?filter=overdue" class="btn btn-sm btn-danger ms-auto">
        <i class="bi bi-exclamation-circle me-1"></i>{{ $stats['overdueTasks'] }} overdue {{ Str::plural('task', $stats['overdueTasks']) }}
    </a>
    @endif
</div>

{{-- ── KPI Stats ─────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    <div class="col-6 col-lg">
        <div class="stat-card">
            <div class="stat-icon bg-primary-soft"><i class="bi bi-person-lines-fill text-primary"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['totalContacts']) }}</div>
                <div class="stat-label">Contacts</div>
                <div class="stat-change {{ $stats['newContacts'] > 0 ? 'text-success' : 'text-muted' }}">
                    @if($stats['newContacts'] > 0)<i class="bi bi-arrow-up-short"></i>@endif
                    {{ $stats['newContacts'] }} this month
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg">
        <div class="stat-card">
            <div class="stat-icon bg-warning-soft"><i class="bi bi-funnel-fill text-warning"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['activeLeads']) }}</div>
                <div class="stat-label">Active Leads</div>
                <div class="stat-change {{ $stats['newLeads'] > 0 ? 'text-success' : 'text-muted' }}">
                    @if($stats['newLeads'] > 0)<i class="bi bi-arrow-up-short"></i>@endif
                    {{ $stats['newLeads'] }} this month
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg">
        <div class="stat-card">
            <div class="stat-icon bg-success-soft"><i class="bi bi-briefcase-fill text-success"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['openDeals']) }}</div>
                <div class="stat-label">Open Deals</div>
                <div class="stat-change text-muted">
                    ${{ number_format($stats['pipelineValue'], 0) }} pipeline
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg">
        <div class="stat-card">
            <div class="stat-icon bg-info-soft"><i class="bi bi-currency-dollar text-info"></i></div>
            <div>
                <div class="stat-value">${{ number_format($stats['revenueTotal'], 0) }}</div>
                <div class="stat-label">Revenue Collected</div>
                <div class="stat-change {{ $stats['revenueMonth'] > 0 ? 'text-success' : 'text-muted' }}">
                    @if($stats['revenueMonth'] > 0)<i class="bi bi-arrow-up-short"></i>@endif
                    ${{ number_format($stats['revenueMonth'], 0) }} this month
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg">
        <div class="stat-card">
            <div class="stat-icon bg-purple-soft" style="background:rgba(124,58,237,.1);">
                <i class="bi bi-trophy-fill" style="color:#7c3aed;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['winRate'] }}%</div>
                <div class="stat-label">Win Rate</div>
                <div class="stat-change text-muted">{{ $stats['wonDeals'] }} won deals</div>
            </div>
        </div>
    </div>

</div>

{{-- ── Charts row ────────────────────────────────────────────────────── --}}
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="fw-700 mb-0">Revenue — Last 6 Months</h6>
                    <a href="{{ route('invoices.index') }}" class="text-muted" style="font-size:12px;">View invoices <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div style="height:220px;"><canvas id="revenueChart"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="fw-700 mb-0">Deal Pipeline</h6>
                    <a href="{{ route('deals.index') }}" class="text-muted" style="font-size:12px;">View all <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                @if($pipelineData->isNotEmpty())
                <div style="height:160px;"><canvas id="pipelineChart"></canvas></div>
                <div class="mt-3">
                    @foreach($pipelineData as $stage)
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:8px;height:8px;border-radius:50%;background:{{ $stage['color'] }};flex-shrink:0;"></div>
                            <span style="font-size:12px;">{{ $stage['name'] }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted" style="font-size:11px;">${{ number_format($stage['value'], 0) }}</span>
                            <span class="badge bg-light text-dark" style="font-size:10px;">{{ $stage['count'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center text-muted py-4" style="font-size:13px;">
                    <i class="bi bi-diagram-3 d-block mb-2" style="font-size:28px;opacity:.3;"></i>
                    No deals in pipeline yet.<br>
                    <a href="{{ route('deals.create') }}" class="btn btn-sm btn-primary mt-2">Add first deal</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── Bottom row ────────────────────────────────────────────────────── --}}
<div class="row g-4">

    {{-- Tasks --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pb-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                <h6 class="fw-700 mb-0">Open Tasks</h6>
                <a href="{{ route('tasks.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
            </div>
            <div class="card-body px-4">
                @forelse($tasks as $task)
                @php
                    $isOverdue = $task->due_date && $task->due_date->isPast();
                    $isToday   = $task->due_date && $task->due_date->isToday();
                @endphp
                <div class="d-flex align-items-start gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="mt-1">
                        <div class="rounded-circle border d-flex align-items-center justify-content-center flex-shrink-0
                            {{ $isOverdue ? 'border-danger bg-danger-subtle' : ($isToday ? 'border-warning bg-warning-subtle' : 'border-secondary') }}"
                            style="width:20px;height:20px;">
                            @if($isOverdue)<i class="bi bi-exclamation" style="font-size:11px;color:#dc3545;"></i>
                            @elseif($isToday)<i class="bi bi-clock" style="font-size:9px;color:#fd7e14;"></i>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="fw-600 text-truncate" style="font-size:13px;">{{ $task->title }}</div>
                        <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                            <span class="badge bg-{{ $task->priority_badge }}-subtle text-{{ $task->priority_badge }}" style="font-size:10px;">{{ ucfirst($task->priority) }}</span>
                            @if($task->due_date)
                            <span class="{{ $isOverdue ? 'text-danger fw-600' : ($isToday ? 'text-warning fw-600' : 'text-muted') }}" style="font-size:11px;">
                                <i class="bi bi-calendar3"></i>
                                {{ $isOverdue ? 'Overdue · ' : ($isToday ? 'Today · ' : '') }}{{ $task->due_date->format('M j') }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4" style="font-size:13px;">
                    <i class="bi bi-check2-all d-block mb-2" style="font-size:28px;opacity:.3;"></i>
                    All caught up!<br>
                    <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-primary mt-2">Create a task</a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Top Deals --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pb-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                <h6 class="fw-700 mb-0">Top Open Deals</h6>
                <a href="{{ route('deals.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
            </div>
            <div class="card-body px-4">
                @forelse($topDeals as $deal)
                <div class="d-flex align-items-start gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="avatar-circle bg-success text-white flex-shrink-0" style="width:34px;height:34px;font-size:12px;">
                        {{ strtoupper(substr($deal->title, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="fw-600 text-truncate" style="font-size:13px;">
                            <a href="{{ route('deals.show', $deal) }}" class="text-dark text-decoration-none">{{ Str::limit($deal->title, 30) }}</a>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            @if($deal->contact)
                            <span class="text-muted text-truncate" style="font-size:11px;">{{ $deal->contact->full_name }}</span>
                            @endif
                            @if($deal->stage)
                            <span class="badge" style="font-size:10px;background:{{ $deal->stage->color }}20;color:{{ $deal->stage->color }};">{{ $deal->stage->name }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-end flex-shrink-0">
                        <div class="fw-700 text-success" style="font-size:13px;">${{ number_format($deal->value ?? 0, 0) }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4" style="font-size:13px;">
                    <i class="bi bi-briefcase d-block mb-2" style="font-size:28px;opacity:.3;"></i>
                    No open deals yet.<br>
                    <a href="{{ route('deals.create') }}" class="btn btn-sm btn-primary mt-2">Create a deal</a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Activity Feed --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pb-0 pt-4 px-4">
                <h6 class="fw-700 mb-0">Recent Activity</h6>
            </div>
            <div class="card-body px-4">
                @forelse($recentActivities as $activity)
                <div class="d-flex align-items-start gap-3 mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                    <div class="avatar-circle bg-{{ $activity->type_color }}-subtle flex-shrink-0" style="width:28px;height:28px;font-size:11px;">
                        <i class="bi bi-{{ $activity->type_icon }} text-{{ $activity->type_color }}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-truncate" style="font-size:13px;">{{ $activity->subject }}</div>
                        <div class="text-muted" style="font-size:11px;">
                            {{ $activity->user?->name ?? 'System' }} · {{ $activity->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4" style="font-size:13px;">
                    <i class="bi bi-activity d-block mb-2" style="font-size:28px;opacity:.3;"></i>
                    No activity yet
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Revenue Chart
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: @json($chartData['months']),
        datasets: [{
            label: 'Revenue',
            data: @json($chartData['revenues']),
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13,110,253,.08)',
            fill: true,
            tension: 0.4,
            borderWidth: 2,
            pointBackgroundColor: '#0d6efd',
            pointRadius: 3,
            pointHoverRadius: 5,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: {
            callbacks: { label: ctx => ' $' + ctx.parsed.y.toLocaleString() }
        }},
        scales: {
            y: { grid: { color: 'rgba(0,0,0,.04)' }, ticks: { font: { size: 11 }, callback: v => '$' + (v >= 1000 ? (v/1000).toFixed(0)+'k' : v) } },
            x: { grid: { display: false }, ticks: { font: { size: 11 } } }
        }
    }
});

// Pipeline Doughnut
@if($pipelineData->isNotEmpty())
new Chart(document.getElementById('pipelineChart'), {
    type: 'doughnut',
    data: {
        labels: @json($pipelineData->pluck('name')),
        datasets: [{ data: @json($pipelineData->pluck('count')), backgroundColor: @json($pipelineData->pluck('color')), borderWidth: 0 }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: {
            callbacks: { label: ctx => ' ' + ctx.label + ': ' + ctx.parsed + ' deals' }
        }},
        cutout: '68%',
    }
});
@endif
</script>
@endpush

@if($showOnboarding ?? false)
@include('onboarding.walkthrough')
@endif
