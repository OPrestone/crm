@extends('layouts.app')
@section('title', 'Companies')
@section('page-title', 'Companies')

@section('content')
<div class="page-header">
    <div>
        <h1>Companies</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active">Companies</li></ol></nav>
    </div>
    <a href="{{ route('companies.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Company</a>
</div>
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-6"><div class="search-box"><i class="bi bi-search search-icon"></i><input type="text" name="search" class="form-control" placeholder="Search companies..." value="{{ request('search') }}"></div></div>
            <div class="col-md-3"><select name="industry" class="form-select"><option value="">All Industries</option>@foreach(['Technology','Finance','Healthcare','Retail','Manufacturing','Services','Education','Other'] as $ind)<option value="{{ $ind }}" {{ request('industry') === $ind ? 'selected' : '' }}>{{ $ind }}</option>@endforeach</select></div>
            <div class="col-md-3"><button type="submit" class="btn btn-outline-primary me-2"><i class="bi bi-filter"></i> Filter</button><a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">Clear</a></div>
        </form>
    </div>
</div>
<div class="row g-4">
@forelse($companies as $company)
<div class="col-md-6 col-lg-4">
    <div class="card h-100">
        <div class="card-body">
            <div class="d-flex align-items-start gap-3 mb-3">
                <div class="avatar-circle bg-primary text-white" style="width:44px;height:44px;font-size:16px;flex-shrink:0;border-radius:10px;">{{ strtoupper(substr($company->name, 0, 2)) }}</div>
                <div class="flex-1">
                    <h6 class="fw-700 mb-0"><a href="{{ route('companies.show', $company) }}" class="text-decoration-none">{{ $company->name }}</a></h6>
                    <span class="text-muted" style="font-size:12px;">{{ $company->industry ?? 'General' }}</span>
                </div>
                <div class="dropdown"><button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('companies.show', $company) }}"><i class="bi bi-eye"></i> View</a></li>
                        <li><a class="dropdown-item" href="{{ route('companies.edit', $company) }}"><i class="bi bi-pencil"></i> Edit</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><button class="dropdown-item text-danger" onclick="confirmDelete('{{ route('companies.destroy', $company) }}', '{{ $company->name }}')"><i class="bi bi-trash"></i> Delete</button></li>
                    </ul>
                </div>
            </div>
            <div class="row g-2 text-center">
                <div class="col-4"><div class="fw-700">{{ $company->contacts_count }}</div><div class="text-muted" style="font-size:11px;">Contacts</div></div>
                <div class="col-4"><div class="fw-700">{{ $company->deals_count }}</div><div class="text-muted" style="font-size:11px;">Deals</div></div>
                <div class="col-4"><div class="fw-700">{{ $company->size ?? '—' }}</div><div class="text-muted" style="font-size:11px;">Size</div></div>
            </div>
            @if($company->email || $company->website)
            <hr class="my-2">
            <div style="font-size:12px;">
                @if($company->website)<div><i class="bi bi-globe text-muted"></i> <a href="{{ $company->website }}" target="_blank" class="text-decoration-none">{{ parse_url($company->website, PHP_URL_HOST) }}</a></div>@endif
                @if($company->email)<div><i class="bi bi-envelope text-muted"></i> {{ $company->email }}</div>@endif
            </div>
            @endif
        </div>
    </div>
</div>
@empty
<div class="col-12"><div class="empty-state"><div class="empty-icon"><i class="bi bi-building"></i></div><h5>No companies found</h5><p class="text-muted">Add your first company to get started.</p><a href="{{ route('companies.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Company</a></div></div>
@endforelse
</div>
@if($companies->hasPages())
<div class="mt-4">{{ $companies->links() }}</div>
@endif
@endsection
