@extends('layouts.app')
@section('title', 'Edit Campaign')
@section('page-title', 'Edit Campaign')
@section('content')
<div class="page-header">
    <div><h1>Edit: {{ $emailCampaign->name }}</h1></div>
    <a href="{{ route('email_campaigns.show', $emailCampaign) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<form method="POST" action="{{ route('email_campaigns.update', $emailCampaign) }}">
    @csrf @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header fw-600">Campaign Details</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-600">Campaign Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $emailCampaign->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Email Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject', $emailCampaign->subject) }}" required>
                        @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-600">From Name</label>
                            <input type="text" name="from_name" class="form-control" value="{{ old('from_name', $emailCampaign->from_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">From Email</label>
                            <input type="email" name="from_email" class="form-control" value="{{ old('from_email', $emailCampaign->from_email) }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Email Body <span class="text-danger">*</span></label>
                        <textarea name="body" class="form-control @error('body') is-invalid @enderror" rows="14">{{ old('body', $emailCampaign->body) }}</textarea>
                        @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header fw-600">Settings</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-600">Recipient Segment</label>
                        <select name="segment" class="form-select">
                            <option value="all" {{ old('segment',$emailCampaign->segment)==='all'?'selected':'' }}>All Contacts</option>
                            <option value="active" {{ old('segment',$emailCampaign->segment)==='active'?'selected':'' }}>Active Contacts Only</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Schedule Send</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at', $emailCampaign->scheduled_at?->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Update Campaign</button>
                        <a href="{{ route('email_campaigns.show', $emailCampaign) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
