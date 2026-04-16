@extends('layouts.app')
@section('title', 'Leads')
@section('page-title', 'Leads')
@section('content')
<div class="page-header">
    <div><h1>Leads</h1></div>
    <div class="d-flex gap-2">
        <a href="{{ route('leads.index', ['view' => 'kanban']) }}" class="btn btn-outline-secondary {{ request('view') === 'kanban' ? 'active' : '' }}"><i class="bi bi-kanban me-1"></i>Kanban</a>
        <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary {{ !request('view') || request('view') === 'list' ? 'active' : '' }}"><i class="bi bi-list-ul me-1"></i>List</a>
        <a href="{{ route('leads.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Lead</a>
    </div>
</div>
<div class="card mb-4"><div class="card-body">
    <form method="GET" class="row g-2">
        <input type="hidden" name="view" value="{{ request('view', 'list') }}">
        <div class="col-md-5"><div class="search-box"><i class="bi bi-search search-icon"></i><input type="text" name="search" class="form-control" placeholder="Search leads..." value="{{ request('search') }}"></div></div>
        <div class="col-md-2"><select name="status" class="form-select"><option value="">All Status</option>@foreach(['new','contacted','qualified','lost','converted'] as $s)<option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach</select></div>
        <div class="col-md-2"><select name="source" class="form-select"><option value="">All Sources</option>@foreach(['Website','Referral','Social Media','Direct','Event','Other'] as $src)<option value="{{ $src }}" {{ request('source') === $src ? 'selected' : '' }}>{{ $src }}</option>@endforeach</select></div>
        <div class="col-md-3"><button type="submit" class="btn btn-outline-primary me-2"><i class="bi bi-filter"></i> Filter</button><a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">Clear</a></div>
    </form>
</div></div>
<div class="card table-card">
    <div class="card-body p-0">
        @if($leads->count())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Title</th><th>Contact</th><th>Source</th><th>Stage</th><th>Score</th><th>Value</th><th>Assigned</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @foreach($leads as $lead)
                <tr>
                    <td><a href="{{ route('leads.show', $lead) }}" class="fw-600 text-decoration-none">{{ Str::limit($lead->title, 40) }}</a></td>
                    <td>{{ $lead->contact?->full_name ?? '—' }}</td>
                    <td class="text-muted" style="font-size:13px;">{{ $lead->source ?? '—' }}</td>
                    <td>@if($lead->stage)<span class="badge" style="background:{{ $lead->stage->color }}20;color:{{ $lead->stage->color }};border:1px solid {{ $lead->stage->color }}40;">{{ $lead->stage->name }}</span>@else<span class="text-muted">—</span>@endif</td>
                    <td><span class="badge bg-secondary">{{ $lead->score }}</span></td>
                    <td>{{ $lead->value ? '$'.number_format($lead->value, 0) : '—' }}</td>
                    <td style="font-size:13px;">{{ $lead->assignedTo?->name ?? '—' }}</td>
                    <td><span class="badge bg-{{ $lead->status_badge }}">{{ ucfirst($lead->status) }}</span></td>
                    <td><div class="dropdown"><button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                        <ul class="dropdown-menu"><li><a class="dropdown-item" href="{{ route('leads.show', $lead) }}"><i class="bi bi-eye"></i> View</a></li><li><a class="dropdown-item" href="{{ route('leads.edit', $lead) }}"><i class="bi bi-pencil"></i> Edit</a></li><li><hr class="dropdown-divider"></li><li><button class="dropdown-item text-danger" onclick="confirmDelete('{{ route('leads.destroy', $lead) }}', '{{ $lead->title }}')"><i class="bi bi-trash"></i> Delete</button></li></ul>
                    </div></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-top">{{ $leads->links() }}</div>
        @else
        <div class="empty-state"><div class="empty-icon"><i class="bi bi-funnel-fill"></i></div><h5>No leads found</h5><p class="text-muted">Start tracking your leads.</p><a href="{{ route('leads.create') }}" class="btn btn-primary">Add Lead</a></div>
        @endif
    </div>
</div>
@endsection
