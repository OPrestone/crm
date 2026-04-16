@extends('layouts.app')
@section('title', $ticket->ticket_number)
@section('page-title', 'Ticket')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $ticket->ticket_number }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tickets.index') }}">Tickets</a></li>
            <li class="breadcrumb-item active">{{ $ticket->ticket_number }}</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" onsubmit="return confirm('Delete this ticket?')">
            @csrf @method('DELETE')<button class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>Delete</button>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between fw-600">
                <span>{{ $ticket->subject }}</span>
                <div class="d-flex gap-2">
                    <span class="badge bg-{{ $ticket->priority_badge }}-subtle text-{{ $ticket->priority_badge }}">{{ ucfirst($ticket->priority) }}</span>
                    <span class="badge bg-{{ $ticket->status_badge }}-subtle text-{{ $ticket->status_badge }}">{{ ucwords(str_replace('_',' ',$ticket->status)) }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex gap-2 mb-3">
                    <div class="avatar-circle bg-primary text-white" style="width:38px;height:38px;font-size:14px;flex-shrink:0;">{{ $ticket->creator?->initials ?? '?' }}</div>
                    <div>
                        <div class="fw-600">{{ $ticket->creator?->name ?? 'System' }}</div>
                        <div class="text-muted small">{{ $ticket->created_at->format('d M Y H:i') }} · via {{ ucfirst($ticket->channel) }}</div>
                    </div>
                </div>
                <div class="border rounded p-3 bg-light">{{ $ticket->description }}</div>
            </div>
        </div>

        {{-- Replies --}}
        @foreach($ticket->replies as $reply)
        <div class="card mb-3 {{ $reply->is_internal ? 'border-warning' : '' }}">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="avatar-circle bg-{{ $reply->is_internal ? 'warning' : 'success' }} text-white" style="width:32px;height:32px;font-size:12px;flex-shrink:0;">{{ $reply->user?->initials ?? '?' }}</div>
                    <div class="flex-1">
                        <span class="fw-600">{{ $reply->user?->name ?? 'Agent' }}</span>
                        @if($reply->is_internal)<span class="badge bg-warning-subtle text-warning ms-2 small">Internal Note</span>@endif
                        <div class="text-muted small">{{ $reply->created_at->format('d M Y H:i') }}</div>
                    </div>
                </div>
                <div>{{ $reply->body }}</div>
            </div>
        </div>
        @endforeach

        {{-- Reply form --}}
        @if(!in_array($ticket->status, ['closed']))
        <div class="card">
            <div class="card-header fw-600">Add Reply</div>
            <div class="card-body">
                <form method="POST" action="{{ route('tickets.reply', $ticket) }}">
                    @csrf
                    <textarea name="body" rows="4" class="form-control mb-3" placeholder="Write your reply..." required></textarea>
                    <div class="row g-2 align-items-center">
                        <div class="col-md-4">
                            <select name="status" class="form-select form-select-sm">
                                <option value="">Keep current status</option>
                                @foreach(['open','pending','in_progress','resolved','closed'] as $s)
                                <option value="{{ $s }}" {{ $ticket->status === $s ? 'selected' : '' }}>→ Mark as {{ ucwords(str_replace('_',' ',$s)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_internal" id="isInternal" value="1">
                                <label class="form-check-label small" for="isInternal">Internal note (not visible to client)</label>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-reply-fill me-1"></i>Send Reply</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header fw-600">Ticket Info</div>
            <div class="card-body">
                @if($ticket->contact)
                <div class="mb-3"><div class="text-muted small">Contact</div>
                    <a href="{{ route('contacts.show', $ticket->contact) }}" class="fw-600 text-decoration-none">{{ $ticket->contact->full_name }}</a></div>
                @endif
                <div class="mb-2"><div class="text-muted small">Assigned To</div>
                    <div class="fw-600">{{ $ticket->assignedTo?->name ?? 'Unassigned' }}</div></div>
                <div class="mb-2"><div class="text-muted small">Category</div><div>{{ $ticket->category ?? '—' }}</div></div>
                <div class="mb-2"><div class="text-muted small">Channel</div><div>{{ ucfirst($ticket->channel) }}</div></div>
                <div class="mb-2"><div class="text-muted small">Created</div><div class="small">{{ $ticket->created_at->format('d M Y H:i') }}</div></div>
                @if($ticket->resolved_at)
                <div class="mb-2"><div class="text-muted small">Resolved</div><div class="small">{{ $ticket->resolved_at->format('d M Y H:i') }}</div></div>
                @endif
                <div class="mb-2"><div class="text-muted small">Replies</div><div class="fw-600">{{ $ticket->replies->count() }}</div></div>
            </div>
        </div>
    </div>
</div>
@endsection
