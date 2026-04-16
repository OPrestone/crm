@extends('layouts.app')
@section('title', 'Email Campaigns')
@section('page-title', 'Email Campaigns')
@section('content')
<div class="page-header">
    <div><h1>Email Campaigns</h1></div>
    <div><a href="{{ route('email_campaigns.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Campaign</a></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="card text-center"><div class="card-body py-3"><div class="fs-4 fw-700 text-primary">{{ $stats['total'] }}</div><div class="small text-muted">Total</div></div></div></div>
    <div class="col-6 col-md-3"><div class="card text-center"><div class="card-body py-3"><div class="fs-4 fw-700 text-success">{{ $stats['sent'] }}</div><div class="small text-muted">Sent</div></div></div></div>
    <div class="col-6 col-md-3"><div class="card text-center"><div class="card-body py-3"><div class="fs-4 fw-700 text-secondary">{{ $stats['draft'] }}</div><div class="small text-muted">Drafts</div></div></div></div>
    <div class="col-6 col-md-3"><div class="card text-center"><div class="card-body py-3"><div class="fs-4 fw-700 text-info">{{ $stats['scheduled'] }}</div><div class="small text-muted">Scheduled</div></div></div></div>
</div>

<div class="card mb-4"><div class="card-body">
    <form method="GET" class="row g-2">
        <div class="col-md-6"><div class="search-box"><i class="bi bi-search search-icon"></i><input type="text" name="search" class="form-control" placeholder="Search campaigns..." value="{{ request('search') }}"></div></div>
        <div class="col-md-3"><select name="status" class="form-select"><option value="">All Status</option>@foreach(['draft','scheduled','sending','sent'] as $s)<option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach</select></div>
        <div class="col-md-3 d-flex gap-2"><button type="submit" class="btn btn-outline-primary flex-fill"><i class="bi bi-filter"></i> Filter</button><a href="{{ route('email_campaigns.index') }}" class="btn btn-outline-secondary">Clear</a></div>
    </form>
</div></div>

<div class="card table-card">
    <div class="card-body p-0">
        @if($campaigns->count())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Campaign</th><th>Subject</th><th>Status</th><th>Recipients</th><th>Open Rate</th><th>Click Rate</th><th>Sent At</th><th></th></tr>
                </thead>
                <tbody>
                @foreach($campaigns as $c)
                <tr>
                    <td><a href="{{ route('email_campaigns.show', $c) }}" class="fw-600 text-decoration-none">{{ $c->name }}</a></td>
                    <td class="text-muted small">{{ Str::limit($c->subject, 50) }}</td>
                    <td><span class="badge bg-{{ $c->status_badge }}">{{ ucfirst($c->status) }}</span></td>
                    <td>{{ number_format($c->recipients_count) }}</td>
                    <td>@if($c->status==='sent')<span class="text-success fw-600">{{ $c->open_rate }}%</span>@else<span class="text-muted">—</span>@endif</td>
                    <td>@if($c->status==='sent')<span class="text-primary fw-600">{{ $c->click_rate }}%</span>@else<span class="text-muted">—</span>@endif</td>
                    <td class="text-muted small">{{ $c->sent_at?->format('M d, Y') ?? '—' }}</td>
                    <td>
                        <div class="dropdown"><button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('email_campaigns.show', $c) }}"><i class="bi bi-eye me-2"></i>View</a></li>
                            @if($c->status !== 'sent')
                            <li><a class="dropdown-item" href="{{ route('email_campaigns.edit', $c) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                            <li>
                                <form method="POST" action="{{ route('email_campaigns.send', $c) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-success" onclick="return confirm('Send this campaign now?')"><i class="bi bi-send me-2"></i>Send Now</button>
                                </form>
                            </li>
                            @endif
                            <li>
                                <form method="POST" action="{{ route('email_campaigns.duplicate', $c) }}" class="d-inline">@csrf<button type="submit" class="dropdown-item"><i class="bi bi-copy me-2"></i>Duplicate</button></form>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item text-danger" onclick="confirmDelete('{{ route('email_campaigns.destroy', $c) }}','{{ $c->name }}')"><i class="bi bi-trash me-2"></i>Delete</button></li>
                        </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-top">{{ $campaigns->links() }}</div>
        @else
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-envelope-paper-fill"></i></div>
            <h5>No campaigns yet</h5>
            <p class="text-muted">Create your first email campaign to engage your contacts.</p>
            <a href="{{ route('email_campaigns.create') }}" class="btn btn-primary">Create Campaign</a>
        </div>
        @endif
    </div>
</div>
@endsection
