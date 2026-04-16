@extends('layouts.app')
@section('title', $task->title)
@section('page-title', 'Task Details')
@section('content')
<div class="page-header">
    <div>
        <h1>{{ $task->title }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('tasks.index') }}">Tasks</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($task->title, 40) }}</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <button class="btn btn-outline-danger" onclick="confirmDelete('{{ route('tasks.destroy', $task) }}', '{{ addslashes($task->title) }}')"><i class="bi bi-trash me-1"></i>Delete</button>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-transparent pt-3 px-4">
                <h6 class="fw-700 mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Task Details</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="avatar-circle bg-{{ $task->priority_badge }} text-white" style="width:52px;height:52px;font-size:22px;">
                        <i class="bi bi-{{ $task->type_icon }}"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">{{ $task->title }}</h5>
                        <span class="badge bg-{{ $task->priority_badge }}-subtle text-{{ $task->priority_badge }}">{{ ucfirst($task->priority) }} Priority</span>
                    </div>
                </div>
                <dl class="row mb-0" style="font-size:13px;">
                    <dt class="col-5 text-muted">Status</dt>
                    <dd class="col-7">
                        <span class="badge bg-{{ $task->status_badge }}-subtle text-{{ $task->status_badge }}">{{ ucfirst(str_replace('_',' ',$task->status)) }}</span>
                    </dd>
                    <dt class="col-5 text-muted">Type</dt>
                    <dd class="col-7"><i class="bi bi-{{ $task->type_icon }} me-1"></i>{{ ucfirst($task->type) }}</dd>
                    <dt class="col-5 text-muted">Priority</dt>
                    <dd class="col-7"><span class="badge bg-{{ $task->priority_badge }}">{{ ucfirst($task->priority) }}</span></dd>
                    <dt class="col-5 text-muted">Due Date</dt>
                    <dd class="col-7">
                        @if($task->due_date)
                            <span class="{{ $task->isOverdue() ? 'text-danger fw-600' : '' }}">
                                <i class="bi bi-calendar3 me-1"></i>{{ $task->due_date->format('M j, Y') }}
                                @if($task->isOverdue()) <span class="badge bg-danger ms-1">Overdue</span>@endif
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </dd>
                    @if($task->completed_at)
                    <dt class="col-5 text-muted">Completed</dt>
                    <dd class="col-7"><i class="bi bi-check-circle text-success me-1"></i>{{ $task->completed_at->format('M j, Y') }}</dd>
                    @endif
                    <dt class="col-5 text-muted">Assigned To</dt>
                    <dd class="col-7">
                        @if($task->assignedTo)
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle bg-primary text-white" style="width:22px;height:22px;font-size:10px;">{{ strtoupper(substr($task->assignedTo->name,0,1)) }}</div>
                                <span>{{ $task->assignedTo->name }}</span>
                            </div>
                        @else
                            <span class="text-muted">Unassigned</span>
                        @endif
                    </dd>
                    <dt class="col-5 text-muted">Created By</dt>
                    <dd class="col-7">{{ $task->creator?->name ?? 'System' }}</dd>
                    <dt class="col-5 text-muted">Created</dt>
                    <dd class="col-7">{{ $task->created_at->format('M j, Y') }}</dd>
                </dl>
            </div>
        </div>

        @if($task->status !== 'completed')
        <div class="card">
            <div class="card-body">
                <h6 class="fw-700 mb-3">Quick Actions</h6>
                <form method="POST" action="{{ route('tasks.update', $task) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="title" value="{{ $task->title }}">
                    <input type="hidden" name="type" value="{{ $task->type }}">
                    <input type="hidden" name="priority" value="{{ $task->priority }}">
                    <input type="hidden" name="description" value="{{ $task->description }}">
                    <input type="hidden" name="due_date" value="{{ $task->due_date?->format('Y-m-d') }}">
                    <input type="hidden" name="assigned_to" value="{{ $task->assigned_to }}">
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check-circle me-2"></i>Mark as Completed
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-8">
        @if($task->description)
        <div class="card mb-4">
            <div class="card-header bg-transparent pt-3 px-4">
                <h6 class="fw-700 mb-0"><i class="bi bi-text-paragraph me-2 text-primary"></i>Description</h6>
            </div>
            <div class="card-body">
                <p class="mb-0" style="font-size:14px;line-height:1.7;">{{ $task->description }}</p>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header bg-transparent pt-3 px-4">
                <h6 class="fw-700 mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Timeline</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item d-flex gap-3 mb-3">
                        <div class="avatar-circle bg-primary text-white" style="width:32px;height:32px;font-size:13px;flex-shrink:0;">
                            <i class="bi bi-plus-lg"></i>
                        </div>
                        <div class="pt-1">
                            <div class="fw-600" style="font-size:13px;">Task created</div>
                            <div class="text-muted" style="font-size:11px;">{{ $task->created_at->format('M j, Y g:i A') }} · {{ $task->creator?->name ?? 'System' }}</div>
                        </div>
                    </div>
                    @if($task->status === 'in_progress')
                    <div class="timeline-item d-flex gap-3 mb-3">
                        <div class="avatar-circle bg-info text-white" style="width:32px;height:32px;font-size:13px;flex-shrink:0;">
                            <i class="bi bi-play-fill"></i>
                        </div>
                        <div class="pt-1">
                            <div class="fw-600" style="font-size:13px;">In Progress</div>
                            <div class="text-muted" style="font-size:11px;">Task is currently being worked on</div>
                        </div>
                    </div>
                    @endif
                    @if($task->completed_at)
                    <div class="timeline-item d-flex gap-3">
                        <div class="avatar-circle bg-success text-white" style="width:32px;height:32px;font-size:13px;flex-shrink:0;">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <div class="pt-1">
                            <div class="fw-600" style="font-size:13px;">Task completed</div>
                            <div class="text-muted" style="font-size:11px;">{{ $task->completed_at->format('M j, Y g:i A') }}</div>
                        </div>
                    </div>
                    @endif
                    @if($task->isOverdue())
                    <div class="timeline-item d-flex gap-3">
                        <div class="avatar-circle bg-danger text-white" style="width:32px;height:32px;font-size:13px;flex-shrink:0;">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <div class="pt-1">
                            <div class="fw-600 text-danger" style="font-size:13px;">Task overdue</div>
                            <div class="text-muted" style="font-size:11px;">Was due {{ $task->due_date->format('M j, Y') }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
