@extends('layouts.app')
@section('title', 'Create API Application')
@section('page-title', 'Create API Application')

@section('content')
<div class="row justify-content-center">
<div class="col-lg-8">

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-600 py-3"><i class="bi bi-app-indicator text-primary me-2"></i>Application Details</div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('developer.apps.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-600">Application Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="My Integration" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">A human-readable name to identify this app.</div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-600">Description</label>
                <textarea name="description" rows="2" class="form-control" placeholder="What does this application do?">{{ old('description') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-600">Rate Limit (requests per hour)</label>
                <input type="number" name="rate_limit" class="form-control" value="{{ old('rate_limit', 1000) }}" min="100" max="100000">
                <div class="form-text">Max API requests this app can make per hour.</div>
            </div>

            <hr class="my-4">
            <h6 class="fw-700 mb-3"><i class="bi bi-broadcast text-info me-2"></i>Webhook Configuration</h6>

            <div class="mb-3">
                <label class="form-label fw-600">Webhook URL</label>
                <input type="url" name="webhook_url" class="form-control @error('webhook_url') is-invalid @enderror" value="{{ old('webhook_url') }}" placeholder="https://your-server.com/webhook">
                @error('webhook_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">We'll POST a JSON payload to this URL when events occur.</div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-600">Subscribe to Events</label>
                <div class="row g-2">
                    @foreach($events as $slug => $label)
                    <div class="col-md-6 col-lg-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="webhook_events[]" value="{{ $slug }}" id="ev_{{ $slug }}"
                                {{ in_array($slug, old('webhook_events', [])) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="ev_{{ $slug }}">{{ $label }}</label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-700 mb-3"><i class="bi bi-shield-check text-success me-2"></i>Security</h6>
            <div class="mb-3">
                <label class="form-label fw-600">IP Whitelist</label>
                <textarea name="allowed_ips" rows="3" class="form-control font-monospace" placeholder="192.168.1.1&#10;10.0.0.0/24&#10;(leave empty to allow all)">{{ old('allowed_ips') }}</textarea>
                <div class="form-text">One IP address or CIDR range per line. Leave empty to allow all IPs.</div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Create Application</button>
                <a href="{{ route('developer.apps') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-warning border-0 shadow-sm">
    <div class="card-body bg-warning-subtle rounded-3 p-3" style="font-size:13px;">
        <i class="bi bi-info-circle-fill text-warning me-2"></i>
        Your <strong>API Secret</strong> will be shown <strong>once</strong> immediately after creation. Store it securely — it cannot be retrieved again.
    </div>
</div>

</div>
</div>
@endsection
