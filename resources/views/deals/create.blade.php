@extends('layouts.app')
@section('title', 'Add Deal')
@section('page-title', 'Add Deal')
@section('content')
<div class="page-header"><div><h1>Add Deal</h1></div></div>
<form method="POST" action="{{ route('deals.store') }}">
@csrf
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card"><div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Deal Details</h5></div>
        <div class="card-body px-4"><div class="row g-3">
            <div class="col-12"><label class="form-label fw-600">Deal Title <span class="text-danger">*</span></label><input type="text" name="title" class="form-control" value="{{ old('title') }}" required></div>
            <div class="col-md-6"><label class="form-label fw-600">Contact</label><select name="contact_id" class="form-select"><option value="">— None —</option>@foreach($contacts as $c)<option value="{{ $c->id }}" {{ old('contact_id') == $c->id ? 'selected' : '' }}>{{ $c->full_name }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label fw-600">Company</label><select name="company_id" class="form-select"><option value="">— None —</option>@foreach($companies as $co)<option value="{{ $co->id }}" {{ old('company_id') == $co->id ? 'selected' : '' }}>{{ $co->name }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label fw-600">Pipeline Stage</label><select name="stage_id" class="form-select"><option value="">— None —</option>@foreach($stages as $stage)<option value="{{ $stage->id }}" {{ old('stage_id') == $stage->id ? 'selected' : '' }}>{{ $stage->name }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label fw-600">Deal Value ($)</label><input type="number" name="value" class="form-control" value="{{ old('value', 0) }}" min="0" step="0.01"></div>
            <div class="col-md-6"><label class="form-label fw-600">Probability (%)</label><input type="number" name="probability" class="form-control" value="{{ old('probability', 50) }}" min="0" max="100"></div>
            <div class="col-md-6"><label class="form-label fw-600">Expected Close Date</label><input type="date" name="expected_close_date" class="form-control" value="{{ old('expected_close_date') }}"></div>
            <div class="col-md-6"><label class="form-label fw-600">Assign To</label><select name="assigned_to" class="form-select"><option value="">— Unassigned —</option>@foreach($users as $u)<option value="{{ $u->id }}" {{ old('assigned_to') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>@endforeach</select></div>
            <div class="col-12"><label class="form-label fw-600">Notes</label><textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea></div>
        </div></div></div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4"><div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Status</h5></div>
        <div class="card-body px-4">
            <div class="mb-3"><label class="form-label fw-600">Status</label><select name="status" class="form-select"><option value="open" {{ old('status','open') === 'open' ? 'selected' : '' }}>Open</option><option value="won" {{ old('status') === 'won' ? 'selected' : '' }}>Won</option><option value="lost" {{ old('status') === 'lost' ? 'selected' : '' }}>Lost</option></select></div>
            <div class="mb-3"><label class="form-label fw-600">Priority</label><select name="priority" class="form-select"><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option><option value="urgent">Urgent</option></select></div>
        </div></div>
        <div class="d-grid gap-2"><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Deal</button><a href="{{ route('deals.index') }}" class="btn btn-outline-secondary">Cancel</a></div>
    </div>
</div>
</form>
@endsection
