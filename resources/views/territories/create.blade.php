@extends('layouts.app')
@section('title', 'New Territory')
@section('page-title', 'New Territory')
@section('content')
<div class="page-header">
    <div><h1>New Territory</h1></div>
    <a href="{{ route('territories.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<form method="POST" action="{{ route('territories.store') }}">
    @csrf
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header fw-600">Territory Details</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-600">Territory Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. West Coast, Enterprise Accounts">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Describe the scope of this territory...">{{ old('description') }}</textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-600">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select">
                                <option value="geographic" {{ old('type')==='geographic'?'selected':'' }}>Geographic</option>
                                <option value="account" {{ old('type')==='account'?'selected':'' }}>Account-based</option>
                                <option value="industry" {{ old('type')==='industry'?'selected':'' }}>Industry</option>
                                <option value="custom" {{ old('type','custom')==='custom'?'selected':'' }}>Custom</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Colour</label>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="color" name="color" class="form-control form-control-color" value="{{ old('color','#4361ee') }}" style="width:60px;">
                                <span class="text-muted small">Used for map and badge display.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-600">Assign Sales Reps</div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach($users as $u)
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="user_ids[]" value="{{ $u->id }}" id="user_{{ $u->id }}" {{ in_array($u->id, old('user_ids',[]))?'checked':'' }}>
                                <label class="form-check-label" for="user_{{ $u->id }}">{{ $u->name }}<span class="text-muted small ms-1">({{ $u->email }})</span></label>
                            </div>
                        </div>
                        @endforeach
                        @if($users->isEmpty())
                        <div class="col-12 text-muted small">No users available.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header fw-600">About Territory Types</div>
                <div class="card-body">
                    <dl class="small mb-0">
                        <dt class="text-primary mb-1">Geographic</dt>
                        <dd class="text-muted mb-3">Based on regions, states, cities, or countries. Leads are routed by contact location.</dd>
                        <dt class="text-success mb-1">Account-based</dt>
                        <dd class="text-muted mb-3">Based on named accounts or company size. Best for enterprise sales teams.</dd>
                        <dt class="text-warning mb-1">Industry</dt>
                        <dd class="text-muted mb-3">Based on vertical markets or sectors (e.g. Healthcare, Finance, Retail).</dd>
                        <dt class="text-secondary mb-1">Custom</dt>
                        <dd class="text-muted mb-0">Fully custom criteria defined by your team.</dd>
                    </dl>
                </div>
            </div>
            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save me-1"></i>Create Territory</button>
                <a href="{{ route('territories.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </div>
</form>
@endsection
