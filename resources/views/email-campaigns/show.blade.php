@extends('layouts.app')
@section('title', $emailCampaign->name)
@section('page-title', $emailCampaign->name)
@section('content')
<div class="page-header">
    <div>
        <h1>{{ $emailCampaign->name }}</h1>
        <span class="badge bg-{{ $emailCampaign->status_badge }} fs-6">{{ ucfirst($emailCampaign->status) }}</span>
    </div>
    <div class="d-flex gap-2">
        @if($emailCampaign->status !== 'sent')
        <a href="{{ route('email_campaigns.edit', $emailCampaign) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <form method="POST" action="{{ route('email_campaigns.send', $emailCampaign) }}">
            @csrf
            <button type="submit" class="btn btn-success" onclick="return confirm('Send this campaign to all contacts now?')"><i class="bi bi-send me-1"></i>Send Now</button>
        </form>
        @endif
        <a href="{{ route('email_campaigns.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

@if(session('success'))<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}</div>@endif

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="card text-center"><div class="card-body"><div class="fs-3 fw-700 text-primary">{{ number_format($analytics['total_sent']) }}</div><div class="small text-muted">Sent</div></div></div></div>
    <div class="col-md-3"><div class="card text-center"><div class="card-body"><div class="fs-3 fw-700 text-success">{{ $analytics['total_opened'] }}</div><div class="small text-muted">Opened</div><div class="small text-muted">{{ $emailCampaign->open_rate }}%</div></div></div></div>
    <div class="col-md-3"><div class="card text-center"><div class="card-body"><div class="fs-3 fw-700 text-info">{{ $analytics['total_clicked'] }}</div><div class="small text-muted">Clicked</div><div class="small text-muted">{{ $emailCampaign->click_rate }}%</div></div></div></div>
    <div class="col-md-3"><div class="card text-center"><div class="card-body"><div class="fs-3 fw-700 text-secondary">{{ $analytics['total_sent'] > 0 ? number_format(($analytics['total_sent'] - $analytics['total_opened']) / $analytics['total_sent'] * 100, 1) : 0 }}%</div><div class="small text-muted">Unread Rate</div></div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header fw-600">Email Preview</div>
            <div class="card-body">
                <div class="mb-3 pb-3 border-bottom">
                    <div class="small text-muted mb-1">Subject</div>
                    <div class="fw-600">{{ $emailCampaign->subject }}</div>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="small text-muted mb-1">From</div>
                    <div>{{ $emailCampaign->from_name ?? auth()->user()->name }} &lt;{{ $emailCampaign->from_email ?? auth()->user()->email }}&gt;</div>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="small text-muted mb-1">Segment</div>
                    <div>{{ ucfirst($emailCampaign->segment) }} contacts</div>
                </div>
                <div>
                    <div class="small text-muted mb-2">Body</div>
                    <div class="p-3 bg-light rounded" style="font-family:monospace;white-space:pre-wrap;font-size:13px;max-height:300px;overflow-y:auto;">{{ $emailCampaign->body }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header fw-600">Campaign Info</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted fw-600">Created by</td><td>{{ $emailCampaign->creator?->name ?? '—' }}</td></tr>
                    <tr><td class="text-muted fw-600">Created</td><td>{{ $emailCampaign->created_at->format('M d, Y g:i A') }}</td></tr>
                    @if($emailCampaign->scheduled_at)
                    <tr><td class="text-muted fw-600">Scheduled</td><td>{{ $emailCampaign->scheduled_at->format('M d, Y g:i A') }}</td></tr>
                    @endif
                    @if($emailCampaign->sent_at)
                    <tr><td class="text-muted fw-600">Sent at</td><td>{{ $emailCampaign->sent_at->format('M d, Y g:i A') }}</td></tr>
                    @endif
                </table>
            </div>
        </div>

        @if($recipients->count())
        <div class="card">
            <div class="card-header fw-600">Recipients <span class="badge bg-secondary ms-1">{{ $recipients->total() }}</span></div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" style="max-height:280px;overflow-y:auto;">
                @foreach($recipients as $r)
                <div class="list-group-item py-2 px-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="small fw-600">{{ $r->contact?->full_name ?? '—' }}</div>
                        <div class="d-flex gap-1">
                            @if($r->opened_at)<span class="badge bg-success-subtle text-success" title="Opened"><i class="bi bi-envelope-open"></i></span>@endif
                            @if($r->clicked_at)<span class="badge bg-info-subtle text-info" title="Clicked"><i class="bi bi-cursor"></i></span>@endif
                            @if(!$r->opened_at && $r->sent_at)<span class="badge bg-secondary-subtle text-secondary" title="Delivered"><i class="bi bi-envelope"></i></span>@endif
                        </div>
                    </div>
                    <div class="small text-muted">{{ $r->contact?->email ?? '—' }}</div>
                </div>
                @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
