@extends('layouts.app')
@section('title', 'Contacts')
@section('page-title', 'Contacts')

@section('content')
<div class="page-header">
    <div>
        <h1>Contacts</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Contacts</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('contacts.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add Contact
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-5">
                <div class="search-box">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" name="search" class="form-control" placeholder="Search contacts..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="source" class="form-select">
                    <option value="">All Sources</option>
                    <option value="Website" {{ request('source') === 'Website' ? 'selected' : '' }}>Website</option>
                    <option value="Referral" {{ request('source') === 'Referral' ? 'selected' : '' }}>Referral</option>
                    <option value="Social Media" {{ request('source') === 'Social Media' ? 'selected' : '' }}>Social Media</option>
                    <option value="Direct" {{ request('source') === 'Direct' ? 'selected' : '' }}>Direct</option>
                    <option value="Event" {{ request('source') === 'Event' ? 'selected' : '' }}>Event</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary me-2"><i class="bi bi-filter"></i> Filter</button>
                <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card table-card">
    <div class="card-body p-0">
        @if($contacts->count())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Contact</th>
                        <th>Company</th>
                        <th>Email / Phone</th>
                        <th>Source</th>
                        <th>Score</th>
                        <th>Status</th>
                        <th>Added</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contacts as $contact)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle" style="width:36px;height:36px;font-size:13px;flex-shrink:0;">{{ $contact->initials }}</div>
                                <div>
                                    <a href="{{ route('contacts.show', $contact) }}" class="fw-600 text-decoration-none">{{ $contact->full_name }}</a>
                                    @if($contact->job_title)<div class="text-muted" style="font-size:12px;">{{ $contact->job_title }}</div>@endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $contact->company?->name ?? '—' }}</td>
                        <td>
                            @if($contact->email)<div style="font-size:13px;">{{ $contact->email }}</div>@endif
                            @if($contact->phone)<div class="text-muted" style="font-size:12px;">{{ $contact->phone }}</div>@endif
                        </td>
                        <td><span class="text-muted" style="font-size:13px;">{{ $contact->source ?? '—' }}</span></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="score-bar flex-1" style="min-width:60px;">
                                    <div class="score-fill bg-{{ $contact->score_color }}" style="width:{{ $contact->lead_score }}%;"></div>
                                </div>
                                <span style="font-size:12px;font-weight:600;">{{ $contact->lead_score }}</span>
                            </div>
                        </td>
                        <td><span class="badge bg-{{ $contact->status_badge }}-subtle text-{{ $contact->status_badge }}">{{ ucfirst($contact->status) }}</span></td>
                        <td class="text-muted" style="font-size:12px;">{{ $contact->created_at->format('M j, Y') }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('contacts.show', $contact) }}"><i class="bi bi-eye"></i> View</a></li>
                                    <li><a class="dropdown-item" href="{{ route('contacts.edit', $contact) }}"><i class="bi bi-pencil"></i> Edit</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><button class="dropdown-item text-danger" onclick="confirmDelete('{{ route('contacts.destroy', $contact) }}', '{{ $contact->full_name }}')"><i class="bi bi-trash"></i> Delete</button></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-top">{{ $contacts->links() }}</div>
        @else
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-person-lines-fill"></i></div>
            <h5>No contacts found</h5>
            <p class="text-muted">Start by adding your first contact.</p>
            <a href="{{ route('contacts.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Contact</a>
        </div>
        @endif
    </div>
</div>
@endsection
