@extends('layouts.app')
@section('title', 'Deals')
@section('page-title', 'Deals')
@section('content')
<div class="page-header">
    <div><h1>Deals</h1><p class="text-muted mb-0">Pipeline value: <strong>${{ number_format($totalValue, 0) }}</strong></p></div>
    <div class="d-flex gap-2">
        <a href="{{ route('deals.index', ['view' => 'kanban']) }}" class="btn btn-outline-secondary {{ request('view') === 'kanban' ? 'active' : '' }}"><i class="bi bi-kanban me-1"></i>Kanban</a>
        <a href="{{ route('deals.index') }}" class="btn btn-outline-secondary {{ !request('view') || request('view') === 'list' ? 'active' : '' }}"><i class="bi bi-list-ul me-1"></i>List</a>
        <a href="{{ route('deals.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Deal</a>
    </div>
</div>
<div class="card mb-4"><div class="card-body">
    <form method="GET" class="row g-2">
        <input type="hidden" name="view" value="{{ request('view', 'list') }}">
        <div class="col-md-5"><div class="search-box"><i class="bi bi-search search-icon"></i><input type="text" name="search" class="form-control" placeholder="Search deals..." value="{{ request('search') }}"></div></div>
        <div class="col-md-2"><select name="status" class="form-select"><option value="">All Status</option><option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option><option value="won" {{ request('status') === 'won' ? 'selected' : '' }}>Won</option><option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Lost</option></select></div>
        <div class="col-md-3"><button type="submit" class="btn btn-outline-primary me-2"><i class="bi bi-filter"></i> Filter</button><a href="{{ route('deals.index') }}" class="btn btn-outline-secondary">Clear</a></div>
    </form>
</div></div>
<div class="card table-card">
    <div class="card-body p-0">
        @if($deals->count())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Deal</th><th>Contact / Company</th><th>Stage</th><th>Value</th><th>Probability</th><th>Close Date</th><th>Priority</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @foreach($deals as $deal)
                <tr>
                    <td><a href="{{ route('deals.show', $deal) }}" class="fw-600 text-decoration-none">{{ Str::limit($deal->title, 35) }}</a></td>
                    <td>
                        @if($deal->contact)<div style="font-size:13px;">{{ $deal->contact->full_name }}</div>@endif
                        @if($deal->company)<div class="text-muted" style="font-size:12px;">{{ $deal->company->name }}</div>@endif
                    </td>
                    <td>@if($deal->stage)<span class="badge" style="background:{{ $deal->stage->color }}20;color:{{ $deal->stage->color }};border:1px solid {{ $deal->stage->color }}40;">{{ $deal->stage->name }}</span>@else<span class="text-muted">—</span>@endif</td>
                    <td class="fw-600">${{ number_format($deal->value, 0) }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-1" style="height:4px;min-width:50px;"><div class="progress-bar bg-primary" style="width:{{ $deal->probability }}%"></div></div>
                            <span style="font-size:12px;">{{ $deal->probability }}%</span>
                        </div>
                    </td>
                    <td class="text-muted" style="font-size:12px;">{{ $deal->expected_close_date?->format('M j, Y') ?? '—' }}</td>
                    <td><span class="badge bg-{{ $deal->priority_badge }}-subtle text-{{ $deal->priority_badge }}" style="font-size:10px;">{{ ucfirst($deal->priority) }}</span></td>
                    <td><span class="badge bg-{{ $deal->status_badge }}-subtle text-{{ $deal->status_badge }}">{{ ucfirst($deal->status) }}</span></td>
                    <td><div class="dropdown"><button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                        <ul class="dropdown-menu"><li><a class="dropdown-item" href="{{ route('deals.show', $deal) }}"><i class="bi bi-eye"></i> View</a></li><li><a class="dropdown-item" href="{{ route('deals.edit', $deal) }}"><i class="bi bi-pencil"></i> Edit</a></li><li><hr class="dropdown-divider"></li><li><button class="dropdown-item text-danger" onclick="confirmDelete('{{ route('deals.destroy', $deal) }}', '{{ $deal->title }}')"><i class="bi bi-trash"></i> Delete</button></li></ul>
                    </div></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-top">{{ $deals->links() }}</div>
        @else
        <div class="empty-state"><div class="empty-icon"><i class="bi bi-briefcase-fill"></i></div><h5>No deals found</h5><a href="{{ route('deals.create') }}" class="btn btn-primary">Add Deal</a></div>
        @endif
    </div>
</div>
@endsection
