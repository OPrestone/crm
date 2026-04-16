@extends('layouts.app')
@section('title', 'API Applications')
@section('page-title', 'API Applications')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <p class="text-muted mb-0">Manage your API applications and credentials.</p>
    </div>
    <a href="{{ route('developer.apps.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>New Application
    </a>
</div>

@if($apps->isEmpty())
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <div style="font-size:48px;margin-bottom:12px;">🔌</div>
        <h5 class="fw-700">No API applications yet</h5>
        <p class="text-muted mb-4">Create your first app to get API credentials and start integrating.</p>
        <a href="{{ route('developer.apps.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Create Application</a>
    </div>
</div>
@else
<div class="row g-4">
    @foreach($apps as $app)
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center {{ $app->is_active ? 'bg-primary-subtle' : 'bg-secondary-subtle' }}" style="width:44px;height:44px;font-size:20px;">
                            <i class="bi bi-app-indicator {{ $app->is_active ? 'text-primary' : 'text-secondary' }}"></i>
                        </div>
                        <div>
                            <div class="fw-700">{{ $app->name }}</div>
                            {!! $app->status_badge !!}
                        </div>
                    </div>
                </div>
                @if($app->description)
                <p class="text-muted mb-3" style="font-size:13px;">{{ Str::limit($app->description, 80) }}</p>
                @endif
                <div class="d-flex flex-column gap-1 mb-3">
                    <div class="d-flex justify-content-between" style="font-size:12px;">
                        <span class="text-muted">Client ID</span>
                        <span class="font-monospace text-truncate ms-2" style="max-width:160px;">{{ $app->client_id }}</span>
                    </div>
                    <div class="d-flex justify-content-between" style="font-size:12px;">
                        <span class="text-muted">Total Requests</span>
                        <span class="fw-600">{{ number_format($app->total_requests) }}</span>
                    </div>
                    <div class="d-flex justify-content-between" style="font-size:12px;">
                        <span class="text-muted">Rate Limit</span>
                        <span>{{ number_format($app->rate_limit) }}/hr</span>
                    </div>
                    <div class="d-flex justify-content-between" style="font-size:12px;">
                        <span class="text-muted">Last Used</span>
                        <span>{{ $app->last_used_at ? $app->last_used_at->diffForHumans() : 'Never' }}</span>
                    </div>
                    <div class="d-flex justify-content-between" style="font-size:12px;">
                        <span class="text-muted">Webhook</span>
                        <span>{{ $app->webhook_url ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<span class="text-muted">None</span>' }}</span>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top d-flex gap-2">
                <a href="{{ route('developer.apps.show', $app) }}" class="btn btn-sm btn-primary flex-fill">
                    <i class="bi bi-eye me-1"></i>View & Manage
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
