@extends('layouts.app')
@section('title', 'Territory Management')
@section('page-title', 'Territory Management')
@section('content')
<div class="page-header">
    <div><h1>Territory Management</h1></div>
    <a href="{{ route('territories.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Territory</a>
</div>

@if(session('success'))<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>@endif

<div class="card mb-4"><div class="card-body">
    <form method="GET" class="row g-2">
        <div class="col-md-5"><div class="search-box"><i class="bi bi-search search-icon"></i><input type="text" name="search" class="form-control" placeholder="Search territories..." value="{{ request('search') }}"></div></div>
        <div class="col-md-3"><select name="type" class="form-select"><option value="">All Types</option>@foreach(['geographic','account','industry','custom'] as $t)<option value="{{ $t }}" {{ request('type')===$t?'selected':'' }}>{{ ucfirst($t) }}</option>@endforeach</select></div>
        <div class="col-md-4 d-flex gap-2"><button type="submit" class="btn btn-outline-primary flex-fill">Filter</button><a href="{{ route('territories.index') }}" class="btn btn-outline-secondary">Clear</a></div>
    </form>
</div></div>

<div class="row g-4">
    @forelse($territories as $t)
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div style="width:14px;height:14px;border-radius:50%;background:{{ $t->color }};flex-shrink:0;"></div>
                            <h5 class="mb-0 fw-700"><a href="{{ route('territories.show', $t) }}" class="text-decoration-none text-dark">{{ $t->name }}</a></h5>
                        </div>
                        <span class="badge bg-{{ $t->type_badge }}">{{ ucfirst($t->type) }}</span>
                    </div>
                    <div class="dropdown"><button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('territories.show', $t) }}"><i class="bi bi-eye me-2"></i>View</a></li>
                        <li><a class="dropdown-item" href="{{ route('territories.edit', $t) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                        <li><button class="dropdown-item text-danger" onclick="confirmDelete('{{ route('territories.destroy', $t) }}','{{ $t->name }}')"><i class="bi bi-trash me-2"></i>Delete</button></li>
                    </ul></div>
                </div>
                @if($t->description)<p class="text-muted small mb-3">{{ Str::limit($t->description, 80) }}</p>@endif
                <div class="border-top pt-3">
                    <div class="small text-muted fw-600 mb-2">Assigned Reps ({{ $t->users->count() }})</div>
                    @if($t->users->count())
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($t->users->take(5) as $u)
                        <span class="badge bg-secondary-subtle text-secondary border">{{ $u->name }}</span>
                        @endforeach
                        @if($t->users->count() > 5)<span class="badge bg-secondary">+{{ $t->users->count() - 5 }} more</span>@endif
                    </div>
                    @else
                    <span class="text-muted small">No reps assigned</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card"><div class="card-body empty-state">
            <div class="empty-icon"><i class="bi bi-map-fill"></i></div>
            <h5>No territories yet</h5>
            <p class="text-muted">Define sales territories to organise your team and route leads effectively.</p>
            <a href="{{ route('territories.create') }}" class="btn btn-primary">Create Territory</a>
        </div></div>
    </div>
    @endforelse
</div>

@if($territories->count())
<div class="mt-4">{{ $territories->links() }}</div>
@endif
@endsection
