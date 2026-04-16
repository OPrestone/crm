@extends('layouts.app')
@section('title', $deal->title)
@section('page-title', 'Deal Details')
@section('content')
<div class="page-header">
    <div><h1>{{ $deal->title }}</h1></div>
    <div class="d-flex gap-2">
        <a href="{{ route('deals.edit', $deal) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <button class="btn btn-outline-danger" onclick="confirmDelete('{{ route('deals.destroy', $deal) }}', '{{ $deal->title }}')"><i class="bi bi-trash me-1"></i>Delete</button>
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-transparent pt-3 px-4"><h6 class="fw-700 mb-0">Deal Summary</h6></div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="stat-value text-primary">${{ number_format($deal->value, 0) }}</div>
                    <div class="text-muted" style="font-size:13px;">Deal Value</div>
                    <div class="mt-1"><span class="badge bg-{{ $deal->status_badge }}-subtle text-{{ $deal->status_badge }}">{{ ucfirst($deal->status) }}</span></div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1" style="font-size:12px;"><span>Win Probability</span><span class="fw-600">{{ $deal->probability }}%</span></div>
                    <div class="progress" style="height:6px;"><div class="progress-bar bg-success" style="width:{{ $deal->probability }}%"></div></div>
                </div>
                <dl class="row mb-0" style="font-size:13px;">
                    <dt class="col-5 text-muted">Stage</dt><dd class="col-7">{{ $deal->stage?->name ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Priority</dt><dd class="col-7"><span class="badge bg-{{ $deal->priority_badge }}-subtle text-{{ $deal->priority_badge }}">{{ ucfirst($deal->priority) }}</span></dd>
                    <dt class="col-5 text-muted">Close Date</dt><dd class="col-7">{{ $deal->expected_close_date?->format('M j, Y') ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Contact</dt><dd class="col-7">{{ $deal->contact?->full_name ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Company</dt><dd class="col-7">{{ $deal->company?->name ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Assigned</dt><dd class="col-7">{{ $deal->assignedTo?->name ?? 'Unassigned' }}</dd>
                </dl>
            </div>
        </div>
        @if($deal->notes)
        <div class="card"><div class="card-header bg-transparent pt-3 px-4"><h6 class="fw-700 mb-0">Notes</h6></div><div class="card-body">{{ $deal->notes }}</div></div>
        @endif
    </div>
    <div class="col-lg-8">
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#d-tasks">Tasks ({{ $deal->tasks->count() }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#d-activity">Activity</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="d-tasks">
                <div class="card"><div class="card-body p-0">
                @forelse($deal->tasks as $task)
                <div class="d-flex align-items-center justify-content-between px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="d-flex align-items-center gap-2"><i class="bi bi-{{ $task->type_icon }} text-muted"></i><div><div class="fw-600" style="font-size:13px;">{{ $task->title }}</div>@if($task->due_date)<div class="text-muted" style="font-size:11px;">Due: {{ $task->due_date->format('M j, Y') }}</div>@endif</div></div>
                    <span class="badge bg-{{ $task->status_badge }}-subtle text-{{ $task->status_badge }}" style="font-size:10px;">{{ ucfirst($task->status) }}</span>
                </div>
                @empty<div class="text-center text-muted py-4" style="font-size:13px;">No tasks</div>@endforelse
                </div></div>
            </div>
            <div class="tab-pane fade" id="d-activity">
                <div class="card"><div class="card-body">
                @forelse($deal->activities as $activity)
                <div class="d-flex gap-2 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="avatar-circle bg-{{ $activity->type_color }} text-white" style="width:28px;height:28px;font-size:11px;flex-shrink:0;"><i class="bi bi-{{ $activity->type_icon }}"></i></div>
                    <div><div class="fw-600" style="font-size:13px;">{{ $activity->subject }}</div><div class="text-muted" style="font-size:11px;">{{ $activity->user?->name ?? 'System' }} • {{ $activity->created_at->diffForHumans() }}</div></div>
                </div>
                @empty<div class="text-center text-muted py-3" style="font-size:13px;">No activity</div>@endforelse
                </div></div>
            </div>
        </div>
    </div>
</div>
@endsection
