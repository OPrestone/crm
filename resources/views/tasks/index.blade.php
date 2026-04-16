@extends('layouts.app')
@section('title', 'Tasks')
@section('page-title', 'Tasks')
@section('content')
<div class="page-header">
    <div>
        <h1>Tasks</h1>
        @if($overdueTasks > 0)<span class="badge bg-danger ms-2">{{ $overdueTasks }} overdue</span>@endif
    </div>
    <a href="{{ route('tasks.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Task</a>
</div>
<div class="card mb-4"><div class="card-body">
    <form method="GET" class="row g-2">
        <div class="col-md-4"><div class="search-box"><i class="bi bi-search search-icon"></i><input type="text" name="search" class="form-control" placeholder="Search tasks..." value="{{ request('search') }}"></div></div>
        <div class="col-md-2"><select name="status" class="form-select"><option value="">All Status</option>@foreach(['pending','in_progress','completed','cancelled'] as $s)<option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>@endforeach</select></div>
        <div class="col-md-2"><select name="priority" class="form-select"><option value="">All Priority</option>@foreach(['low','medium','high','urgent'] as $p)<option value="{{ $p }}" {{ request('priority') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>@endforeach</select></div>
        <div class="col-md-2"><select name="type" class="form-select"><option value="">All Types</option>@foreach(['task','call','email','meeting'] as $t)<option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>@endforeach</select></div>
        <div class="col-md-2"><button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-filter"></i> Filter</button></div>
    </form>
</div></div>
<div class="card table-card">
    <div class="card-body p-0">
        @if($tasks->count())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Task</th><th>Type</th><th>Assigned</th><th>Due Date</th><th>Priority</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @foreach($tasks as $task)
                <tr class="{{ $task->isOverdue() ? 'table-danger' : '' }}">
                    <td>
                        <div class="fw-600">{{ Str::limit($task->title, 50) }}</div>
                        @if($task->description)<div class="text-muted" style="font-size:12px;">{{ Str::limit($task->description, 60) }}</div>@endif
                    </td>
                    <td><span class="badge bg-secondary-subtle text-secondary"><i class="bi bi-{{ $task->type_icon }} me-1"></i>{{ ucfirst($task->type) }}</span></td>
                    <td style="font-size:13px;">{{ $task->assignedTo?->name ?? '—' }}</td>
                    <td>
                        @if($task->due_date)
                        <span class="{{ $task->isOverdue() ? 'text-danger fw-600' : 'text-muted' }}" style="font-size:12px;">
                            @if($task->isOverdue())<i class="bi bi-exclamation-triangle me-1"></i>@endif
                            {{ $task->due_date->format('M j, Y') }}
                        </span>
                        @else<span class="text-muted">—</span>@endif
                    </td>
                    <td><span class="badge bg-{{ $task->priority_badge }}-subtle text-{{ $task->priority_badge }}">{{ ucfirst($task->priority) }}</span></td>
                    <td><span class="badge bg-{{ $task->status_badge }}-subtle text-{{ $task->status_badge }}">{{ ucfirst(str_replace('_',' ',$task->status)) }}</span></td>
                    <td><div class="dropdown"><button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                        <ul class="dropdown-menu"><li><a class="dropdown-item" href="{{ route('tasks.edit', $task) }}"><i class="bi bi-pencil"></i> Edit</a></li><li><hr class="dropdown-divider"></li><li><button class="dropdown-item text-danger" onclick="confirmDelete('{{ route('tasks.destroy', $task) }}', '{{ $task->title }}')"><i class="bi bi-trash"></i> Delete</button></li></ul>
                    </div></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-top">{{ $tasks->links() }}</div>
        @else
        <div class="empty-state"><div class="empty-icon"><i class="bi bi-check2-square"></i></div><h5>No tasks found</h5><a href="{{ route('tasks.create') }}" class="btn btn-primary">Add Task</a></div>
        @endif
    </div>
</div>
@endsection
