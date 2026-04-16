@extends('layouts.app')
@section('title', $lead->title)
@section('page-title', 'Lead Details')
@section('content')
<div class="page-header">
    <div><h1>{{ $lead->title }}</h1></div>
    <div class="d-flex gap-2">
        <a href="{{ route('leads.edit', $lead) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <button class="btn btn-outline-danger" onclick="confirmDelete('{{ route('leads.destroy', $lead) }}', '{{ $lead->title }}')"><i class="bi bi-trash me-1"></i>Delete</button>
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body">
                <dl class="row mb-0" style="font-size:13px;">
                    <dt class="col-5 text-muted">Status</dt><dd class="col-7"><span class="badge bg-{{ $lead->status_badge }}">{{ ucfirst($lead->status) }}</span></dd>
                    <dt class="col-5 text-muted">Stage</dt><dd class="col-7">{{ $lead->stage?->name ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Score</dt><dd class="col-7"><span class="badge bg-info">{{ $lead->score }}/100</span></dd>
                    <dt class="col-5 text-muted">Value</dt><dd class="col-7 fw-600">{{ $lead->value ? '$'.number_format($lead->value, 2) : '—' }}</dd>
                    <dt class="col-5 text-muted">Source</dt><dd class="col-7">{{ $lead->source ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Contact</dt><dd class="col-7">{{ $lead->contact?->full_name ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Company</dt><dd class="col-7">{{ $lead->company?->name ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Assigned</dt><dd class="col-7">{{ $lead->assignedTo?->name ?? 'Unassigned' }}</dd>
                    <dt class="col-5 text-muted">Created</dt><dd class="col-7">{{ $lead->created_at->format('M j, Y') }}</dd>
                </dl>
            </div>
        </div>
        @if($lead->notes)
        <div class="card"><div class="card-header bg-transparent pt-3 px-4"><h6 class="fw-700 mb-0">Notes</h6></div><div class="card-body">{{ $lead->notes }}</div></div>
        @endif
    </div>
    <div class="col-lg-8">
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#l-tasks">Tasks ({{ $lead->tasks->count() }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#l-activity">Activity</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="l-tasks">
                <div class="card"><div class="card-body p-0">
                @forelse($lead->tasks as $task)
                <div class="d-flex align-items-center justify-content-between px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="d-flex align-items-center gap-2"><i class="bi bi-{{ $task->type_icon }} text-muted"></i>
                        <div><div class="fw-600" style="font-size:13px;">{{ $task->title }}</div>@if($task->due_date)<div class="text-muted" style="font-size:11px;">Due: {{ $task->due_date->format('M j, Y') }}</div>@endif</div>
                    </div>
                    <span class="badge bg-{{ $task->status_badge }}-subtle text-{{ $task->status_badge }}" style="font-size:10px;">{{ ucfirst($task->status) }}</span>
                </div>
                @empty<div class="text-center text-muted py-4" style="font-size:13px;">No tasks</div>@endforelse
                </div></div>
            </div>
            <div class="tab-pane fade" id="l-activity">
                <div class="card"><div class="card-body">
                @forelse($lead->activities as $activity)
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
