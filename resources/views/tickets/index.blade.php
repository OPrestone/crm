@extends('layouts.app')
@section('title', 'Help Desk')
@section('page-title', 'Help Desk')

@section('content')
<div class="page-header">
    <div>
        <h1>Help Desk</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Tickets</li>
        </ol></nav>
    </div>
    <a href="{{ route('tickets.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Ticket</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="stat-icon bg-primary-subtle"><i class="bi bi-envelope-open text-primary"></i></div>
            <div class="stat-value">{{ $stats['open'] }}</div><div class="stat-label">Open</div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="stat-icon bg-info-subtle"><i class="bi bi-arrow-repeat text-info"></i></div>
            <div class="stat-value">{{ $stats['in_progress'] }}</div><div class="stat-label">In Progress</div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="stat-icon bg-success-subtle"><i class="bi bi-check2-all text-success"></i></div>
            <div class="stat-value">{{ $stats['resolved'] }}</div><div class="stat-label">Resolved</div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card"><div class="stat-icon bg-danger-subtle"><i class="bi bi-exclamation-triangle text-danger"></i></div>
            <div class="stat-value">{{ $stats['urgent'] }}</div><div class="stat-label">Urgent</div></div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4"><div class="search-box"><i class="bi bi-search search-icon"></i>
                <input type="text" name="search" class="form-control" placeholder="Search tickets..." value="{{ request('search') }}">
            </div></div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    @foreach(['open','pending','in_progress','resolved','closed'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="priority" class="form-select">
                    <option value="">All Priority</option>
                    @foreach(['urgent','high','medium','low'] as $p)
                    <option value="{{ $p }}" {{ request('priority') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary me-2"><i class="bi bi-filter"></i> Filter</button>
                <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card table-card">
    <div class="card-body p-0">
        @if($tickets->count())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Ticket</th><th>Subject</th><th>Contact</th><th>Priority</th><th>Status</th><th>Assigned To</th><th>Created</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                    <tr>
                        <td><a href="{{ route('tickets.show', $ticket) }}" class="fw-600 text-decoration-none text-primary">{{ $ticket->ticket_number }}</a></td>
                        <td>{{ Str::limit($ticket->subject, 50) }}</td>
                        <td>{{ $ticket->contact?->full_name ?? '—' }}</td>
                        <td><span class="badge bg-{{ $ticket->priority_badge }}-subtle text-{{ $ticket->priority_badge }}">{{ ucfirst($ticket->priority) }}</span></td>
                        <td><span class="badge bg-{{ $ticket->status_badge }}-subtle text-{{ $ticket->status_badge }}">{{ ucwords(str_replace('_',' ',$ticket->status)) }}</span></td>
                        <td>{{ $ticket->assignedTo?->name ?? 'Unassigned' }}</td>
                        <td class="text-muted small">{{ $ticket->created_at->diffForHumans() }}</td>
                        <td class="text-end">
                            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3">{{ $tickets->links() }}</div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-headset text-muted" style="font-size:3rem;"></i>
            <h5 class="mt-3 text-muted">No tickets yet</h5>
            <a href="{{ route('tickets.create') }}" class="btn btn-primary mt-2"><i class="bi bi-plus-lg me-1"></i>Create First Ticket</a>
        </div>
        @endif
    </div>
</div>
@endsection
