@extends('layouts.app')
@section('title', 'New Campaign')
@section('page-title', 'New Campaign')
@section('content')
<div class="page-header">
    <div><h1>New Email Campaign</h1></div>
    <a href="{{ route('email_campaigns.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<form method="POST" action="{{ route('email_campaigns.store') }}">
    @csrf
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header fw-600">Campaign Details</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-600">Campaign Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Spring Promotion 2026" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Email Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}" placeholder="Your email subject line..." required>
                        @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-600">From Name</label>
                            <input type="text" name="from_name" class="form-control" value="{{ old('from_name', auth()->user()->name) }}" placeholder="Sender name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">From Email</label>
                            <input type="email" name="from_email" class="form-control" value="{{ old('from_email', auth()->user()->email) }}" placeholder="sender@example.com">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Email Body <span class="text-danger">*</span></label>
                        <textarea name="body" id="emailBody" class="form-control @error('body') is-invalid @enderror" rows="14" placeholder="Write your email content here...">{{ old('body') }}</textarea>
                        <div class="form-text">You can use HTML or plain text. Use <code>@{{first_name}}</code>, <code>@{{last_name}}</code>, <code>@{{email}}</code> as personalisation tokens.</div>
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
                            <option value="all" {{ old('segment')==='all'?'selected':'' }}>All Contacts</option>
                            <option value="active" {{ old('segment')==='active'?'selected':'' }}>Active Contacts Only</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Schedule Send (optional)</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at') }}">
                        <div class="form-text">Leave blank to save as draft.</div>
                    </div>
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Save Campaign</button>
                        <a href="{{ route('email_campaigns.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header fw-600">Tips</div>
                <div class="card-body">
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-2"><i class="bi bi-lightbulb text-warning me-1"></i>Keep subject lines under 60 characters</li>
                        <li class="mb-2"><i class="bi bi-lightbulb text-warning me-1"></i>Personalise with <code>@{{first_name}}</code></li>
                        <li class="mb-2"><i class="bi bi-lightbulb text-warning me-1"></i>Include a clear call-to-action</li>
                        <li><i class="bi bi-lightbulb text-warning me-1"></i>Test your campaign before sending</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
