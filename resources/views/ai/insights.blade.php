@extends('layouts.app')
@section('title', 'Pipeline Intelligence')
@section('page-title', 'Pipeline Intelligence')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Pipeline Intelligence</h1>
        <p class="text-muted mb-0">AI-powered analysis of your CRM health and sales pipeline</p>
    </div>
    <a href="{{ route('ai.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>AI Tools</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-soft"><i class="bi bi-trophy"></i></div>
            <div><div class="stat-value text-success">${{ number_format($wonDeals) }}</div><div class="stat-label">Won This Month</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-danger-soft"><i class="bi bi-x-circle"></i></div>
            <div><div class="stat-value text-danger">${{ number_format($lostDeals) }}</div><div class="stat-label">Lost This Month</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning-soft"><i class="bi bi-hourglass-split"></i></div>
            <div><div class="stat-value text-warning">{{ $stalledLeads->count() }}</div><div class="stat-label">Stalled Leads</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-danger-soft"><i class="bi bi-exclamation-circle"></i></div>
            <div><div class="stat-value text-danger">{{ $overdueTasks->count() }}</div><div class="stat-label">Overdue Tasks</div></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    @foreach($insights as $insight)
    <div class="col-md-6">
        <div class="card border-0 shadow-sm border-start border-{{ $insight['type'] }} border-4">
            <div class="card-body p-4">
                <div class="d-flex gap-3 align-items-start">
                    <div class="rounded-3 bg-{{ $insight['type'] }}-subtle p-2 flex-shrink-0">
                        <i class="bi {{ $insight['icon'] }} text-{{ $insight['type'] }} fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-700 mb-1">{{ $insight['title'] }}</div>
                        <p class="text-muted mb-2" style="font-size:14px;">{{ $insight['text'] }}</p>
                        <a href="{{ $insight['action'] }}" class="btn btn-sm btn-{{ $insight['type'] }}">{{ $insight['action_text'] }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">
    @if($stalledLeads->count())
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-700 mb-0"><i class="bi bi-hourglass-split text-warning me-2"></i>Stalled Leads</h5>
                <span class="badge bg-warning text-dark">{{ $stalledLeads->count() }} need attention</span>
            </div>
            <div class="card-body p-0">
                @foreach($stalledLeads as $lead)
                <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                    <div class="avatar-circle avatar-sm flex-shrink-0">{{ strtoupper(substr($lead->title,0,1)) }}</div>
                    <div class="flex-1">
                        <div class="fw-600" style="font-size:13px;">{{ $lead->title }}</div>
                        <div class="text-muted" style="font-size:11px;">{{ $lead->contact?->first_name }} · Last updated {{ $lead->updated_at->diffForHumans() }}</div>
                    </div>
                    <a href="{{ route('ai.lead-score', $lead) }}" class="btn btn-sm btn-warning">AI Score</a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    @if($topDeals->count())
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent pt-4 px-4">
                <h5 class="fw-700 mb-0"><i class="bi bi-trophy text-primary me-2"></i>Top Open Deals</h5>
            </div>
            <div class="card-body p-0">
                @foreach($topDeals as $deal)
                <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                    <div class="flex-1">
                        <div class="fw-600" style="font-size:13px;">{{ $deal->title }}</div>
                        <div class="text-muted" style="font-size:11px;">{{ $deal->stage?->name }} · {{ $deal->contact?->first_name }}</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-700 text-success">${{ number_format($deal->value) }}</div>
                    </div>
                    <a href="{{ route('ai.deal-insight', $deal) }}" class="btn btn-sm btn-outline-primary">Analyse</a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    @if($overdueTasks->count())
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent pt-4 px-4">
                <h5 class="fw-700 mb-0"><i class="bi bi-exclamation-circle text-danger me-2"></i>Overdue Tasks</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Task</th><th>Priority</th><th>Due Date</th><th>Days Overdue</th><th></th></tr></thead>
                        <tbody>
                        @foreach($overdueTasks as $task)
                        <tr>
                            <td class="fw-600">{{ $task->title }}</td>
                            <td><span class="badge bg-{{ $task->priority_badge }}-subtle text-{{ $task->priority_badge }}">{{ ucfirst($task->priority ?? 'normal') }}</span></td>
                            <td class="text-danger">{{ $task->due_date?->format('M d, Y') ?? '—' }}</td>
                            <td><span class="badge bg-danger">{{ $task->due_date ? now()->diffInDays($task->due_date) : '?' }} days</span></td>
                            <td><a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-danger">View</a></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
