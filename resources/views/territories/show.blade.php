@extends('layouts.app')
@section('title', $territory->name)
@section('page-title', 'Territory')
@section('content')
<div class="page-header">
    <div>
        <div class="d-flex align-items-center gap-2">
            <div style="width:18px;height:18px;border-radius:50%;background:{{ $territory->color }};"></div>
            <h1 class="mb-0">{{ $territory->name }}</h1>
            <span class="badge bg-{{ $territory->type_badge }}">{{ ucfirst($territory->type) }}</span>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('territories.edit', $territory) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('territories.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

@if(session('success'))<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>@endif

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header fw-600">Details</div>
            <div class="card-body">
                @if($territory->description)<p class="text-muted mb-3">{{ $territory->description }}</p>@endif
                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted fw-600">Type</td><td>{{ ucfirst($territory->type) }}</td></tr>
                    <tr><td class="text-muted fw-600">Created by</td><td>{{ $territory->creator?->name ?? '—' }}</td></tr>
                    <tr><td class="text-muted fw-600">Created</td><td>{{ $territory->created_at->format('M d, Y') }}</td></tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header fw-600">Assigned Reps ({{ $territory->users->count() }})</div>
            <div class="card-body p-0">
                @if($territory->users->count())
                <ul class="list-group list-group-flush">
                    @foreach($territory->users as $u)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-600 small">{{ $u->name }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ $u->email }}</div>
                        </div>
                        <span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($u->roles->first()?->name ?? 'user') }}</span>
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="p-3 text-center text-muted small">No reps assigned. <a href="{{ route('territories.edit', $territory) }}">Edit territory</a> to add reps.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header fw-600 d-flex justify-content-between">
                <span>Contacts in Territory</span>
                <span class="badge bg-secondary">{{ $contacts->total() }}</span>
            </div>
            <div class="card-body p-0">
                @if($contacts->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Name</th><th>Company</th><th>Assigned To</th><th>Status</th></tr></thead>
                        <tbody>
                        @foreach($contacts as $c)
                        <tr>
                            <td><a href="{{ route('contacts.show', $c) }}" class="fw-600 text-decoration-none">{{ $c->full_name }}</a><div class="text-muted small">{{ $c->email }}</div></td>
                            <td class="text-muted small">{{ $c->company?->name ?? '—' }}</td>
                            <td class="text-muted small">{{ $c->assignedTo?->name ?? '—' }}</td>
                            <td><span class="badge bg-{{ $c->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($c->status ?? 'active') }}</span></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-top">{{ $contacts->links() }}</div>
                @else
                <div class="empty-state py-5">
                    <div class="empty-icon"><i class="bi bi-people"></i></div>
                    <h6>No contacts in this territory</h6>
                    <p class="text-muted small">Contacts assigned to the reps in this territory will appear here.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
