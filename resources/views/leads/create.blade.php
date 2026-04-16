@extends('layouts.app')
@section('title', 'Add Lead')
@section('page-title', 'Add Lead')
@section('content')
<div class="page-header"><div><h1>Add Lead</h1></div></div>
<form method="POST" action="{{ route('leads.store') }}">
@csrf
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card"><div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Lead Details</h5></div>
        <div class="card-body px-4"><div class="row g-3">
            <div class="col-12"><label class="form-label fw-600">Lead Title <span class="text-danger">*</span></label><input type="text" name="title" class="form-control" value="{{ old('title') }}" required></div>
            <div class="col-md-6"><label class="form-label fw-600">Contact</label><select name="contact_id" class="form-select"><option value="">— None —</option>@foreach($contacts as $c)<option value="{{ $c->id }}" {{ old('contact_id') == $c->id ? 'selected' : '' }}>{{ $c->full_name }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label fw-600">Company</label><select name="company_id" class="form-select"><option value="">— None —</option>@foreach($companies as $co)<option value="{{ $co->id }}" {{ old('company_id') == $co->id ? 'selected' : '' }}>{{ $co->name }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label fw-600">Pipeline Stage</label><select name="stage_id" class="form-select"><option value="">— None —</option>@foreach($stages as $stage)<option value="{{ $stage->id }}" {{ old('stage_id') == $stage->id ? 'selected' : '' }}>{{ $stage->name }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label fw-600">Source</label><select name="source" class="form-select"><option value="">— Select —</option>@foreach(['Website','Referral','Social Media','Direct','Email Campaign','Event','Cold Call','Other'] as $src)<option value="{{ $src }}" {{ old('source') == $src ? 'selected' : '' }}>{{ $src }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label fw-600">Estimated Value ($)</label><input type="number" name="value" class="form-control" value="{{ old('value') }}" min="0" step="0.01"></div>
            <div class="col-md-6"><label class="form-label fw-600">Assign To</label><select name="assigned_to" class="form-select"><option value="">— Unassigned —</option>@foreach($users as $u)<option value="{{ $u->id }}" {{ old('assigned_to') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>@endforeach</select></div>
            <div class="col-12"><label class="form-label fw-600">Notes</label><textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea></div>
        </div></div></div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4"><div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Status</h5></div>
        <div class="card-body px-4">
            <div class="mb-3"><label class="form-label fw-600">Status</label><select name="status" class="form-select"><option value="new" {{ old('status','new') === 'new' ? 'selected' : '' }}>New</option><option value="contacted" {{ old('status') === 'contacted' ? 'selected' : '' }}>Contacted</option><option value="qualified" {{ old('status') === 'qualified' ? 'selected' : '' }}>Qualified</option><option value="lost" {{ old('status') === 'lost' ? 'selected' : '' }}>Lost</option><option value="converted" {{ old('status') === 'converted' ? 'selected' : '' }}>Converted</option></select></div>
            <div class="mb-3"><label class="form-label fw-600">Lead Score: <span id="scoreDisplay">{{ old('score', 0) }}</span></label><input type="range" id="lead_score" name="score" class="form-range" min="0" max="100" value="{{ old('score', 0) }}"></div>
        </div></div>
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Lead</button>
            <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</div>
</form>
@endsection
