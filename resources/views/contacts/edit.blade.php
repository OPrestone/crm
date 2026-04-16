@extends('layouts.app')
@section('title', 'Edit Contact')
@section('page-title', 'Edit Contact')

@section('content')
<div class="page-header">
    <div>
        <h1>Edit: {{ $contact->full_name }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('contacts.index') }}">Contacts</a></li>
            <li class="breadcrumb-item"><a href="{{ route('contacts.show', $contact) }}">{{ $contact->full_name }}</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol></nav>
    </div>
</div>
<form method="POST" action="{{ route('contacts.update', $contact) }}">
@csrf @method('PATCH')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Basic Information</h5></div>
            <div class="card-body px-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-600">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $contact->first_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $contact->last_name) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $contact->email) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $contact->phone) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Mobile</label>
                        <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $contact->mobile) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Job Title</label>
                        <input type="text" name="job_title" class="form-control" value="{{ old('job_title', $contact->job_title) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Company</label>
                        <select name="company_id" class="form-select">
                            <option value="">— No Company —</option>
                            @foreach($companies as $co)
                            <option value="{{ $co->id }}" {{ old('company_id', $contact->company_id) == $co->id ? 'selected' : '' }}>{{ $co->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Source</label>
                        <select name="source" class="form-select">
                            <option value="">— Select Source —</option>
                            @foreach(['Website','Referral','Social Media','Direct','Email Campaign','Event','Cold Call','Other'] as $src)
                            <option value="{{ $src }}" {{ old('source', $contact->source) == $src ? 'selected' : '' }}>{{ $src }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Country</label>
                        <input type="text" name="country" class="form-control" value="{{ old('country', $contact->country) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $contact->city) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-600">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $contact->notes) }}</textarea>
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
                        <option value="active" {{ old('status', $contact->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $contact->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="blocked" {{ old('status', $contact->status) === 'blocked' ? 'selected' : '' }}>Blocked</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-600">Lead Score: <span id="scoreDisplay">{{ old('lead_score', $contact->lead_score) }}</span></label>
                    <input type="range" id="lead_score" name="lead_score" class="form-range" min="0" max="100" value="{{ old('lead_score', $contact->lead_score) }}">
                </div>
            </div>
        </div>
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update Contact</button>
            <a href="{{ route('contacts.show', $contact) }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</div>
</form>
@endsection
