@extends('layouts.app')
@section('title', 'Add Contact')
@section('page-title', 'Add Contact')

@section('content')
<div class="page-header">
    <div>
        <h1>Add Contact</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('contacts.index') }}">Contacts</a></li>
            <li class="breadcrumb-item active">New</li>
        </ol></nav>
    </div>
</div>
<form method="POST" action="{{ route('contacts.store') }}">
@csrf
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Basic Information</h5></div>
            <div class="card-body px-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-600">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Mobile</label>
                        <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Job Title</label>
                        <input type="text" name="job_title" class="form-control" value="{{ old('job_title') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Company</label>
                        <select name="company_id" class="form-select">
                            <option value="">— No Company —</option>
                            @foreach($companies as $co)
                            <option value="{{ $co->id }}" {{ old('company_id') == $co->id ? 'selected' : '' }}>{{ $co->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Source</label>
                        <select name="source" class="form-select">
                            <option value="">— Select Source —</option>
                            @foreach(['Website','Referral','Social Media','Direct','Email Campaign','Event','Cold Call','Other'] as $src)
                            <option value="{{ $src }}" {{ old('source') == $src ? 'selected' : '' }}>{{ $src }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Country</label>
                        <input type="text" name="country" class="form-control" value="{{ old('country') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-600">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">CRM Details</h5></div>
            <div class="card-body px-4">
                <div class="mb-3">
                    <label class="form-label fw-600">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="blocked" {{ old('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-600">Lead Score: <span id="scoreDisplay">{{ old('lead_score', 0) }}</span></label>
                    <input type="range" id="lead_score" name="lead_score" class="form-range" min="0" max="100" value="{{ old('lead_score', 0) }}">
                    <div class="d-flex justify-content-between text-muted" style="font-size:11px;"><span>Cold (0)</span><span>Hot (100)</span></div>
                </div>
            </div>
        </div>
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Contact</button>
            <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</div>
</form>
@endsection
