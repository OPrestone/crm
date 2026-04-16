@extends('layouts.app')
@section('title', 'Add Task')
@section('page-title', 'Add Task')
@section('content')
<div class="page-header"><div><h1>Add Task</h1></div></div>
<form method="POST" action="{{ route('tasks.store') }}">
@csrf
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card"><div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Task Details</h5></div>
        <div class="card-body px-4"><div class="row g-3">
            <div class="col-12"><label class="form-label fw-600">Title <span class="text-danger">*</span></label><input type="text" name="title" class="form-control" value="{{ old('title') }}" required></div>
            <div class="col-12"><label class="form-label fw-600">Description</label><textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea></div>
            <div class="col-md-6"><label class="form-label fw-600">Type</label><select name="type" class="form-select"><option value="task">Task</option><option value="call">Call</option><option value="email">Email</option><option value="meeting">Meeting</option></select></div>
            <div class="col-md-6"><label class="form-label fw-600">Due Date</label><input type="datetime-local" name="due_date" class="form-control" value="{{ old('due_date') }}"></div>
            <div class="col-md-6"><label class="form-label fw-600">Assign To</label><select name="assigned_to" class="form-select"><option value="">— Unassigned —</option>@foreach($users as $u)<option value="{{ $u->id }}" {{ old('assigned_to') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>@endforeach</select></div>
        </div></div></div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4"><div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Priority & Status</h5></div>
        <div class="card-body px-4">
            <div class="mb-3"><label class="form-label fw-600">Priority</label><select name="priority" class="form-select"><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option><option value="urgent">Urgent</option></select></div>
            <div class="mb-3"><label class="form-label fw-600">Status</label><select name="status" class="form-select"><option value="pending" selected>Pending</option><option value="in_progress">In Progress</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select></div>
        </div></div>
        <div class="d-grid gap-2"><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Task</button><a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Cancel</a></div>
    </div>
</div>
</form>
@endsection
