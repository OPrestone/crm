@extends('layouts.app')
@section('title', 'Web Forms')
@section('page-title', 'Web Forms')
@section('content')
<div class="page-header">
    <div><h1>Web Forms &amp; Lead Capture</h1></div>
    <a href="{{ route('web_forms.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Form</a>
</div>

@if(session('success'))<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>@endif

<div class="card mb-4"><div class="card-body">
    <form method="GET" class="row g-2">
        <div class="col-md-9"><div class="search-box"><i class="bi bi-search search-icon"></i><input type="text" name="search" class="form-control" placeholder="Search forms..." value="{{ request('search') }}"></div></div>
        <div class="col-md-3 d-flex gap-2"><button type="submit" class="btn btn-outline-primary flex-fill">Filter</button><a href="{{ route('web_forms.index') }}" class="btn btn-outline-secondary">Clear</a></div>
    </form>
</div></div>

<div class="card table-card">
    <div class="card-body p-0">
        @if($forms->count())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Form Name</th><th>Action</th><th>Submissions</th><th>Status</th><th>Created</th><th></th></tr></thead>
                <tbody>
                @foreach($forms as $f)
                <tr>
                    <td><a href="{{ route('web_forms.show', $f) }}" class="fw-600 text-decoration-none">{{ $f->name }}</a><div class="text-muted small">{{ Str::limit($f->description, 50) }}</div></td>
                    <td><span class="badge bg-info-subtle text-info">{{ ucfirst($f->submit_action) }}</span></td>
                    <td><span class="fw-600">{{ number_format($f->submissions_count) }}</span></td>
                    <td>@if($f->is_active)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif</td>
                    <td class="text-muted small">{{ $f->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="dropdown"><button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('web_forms.show', $f) }}"><i class="bi bi-eye me-2"></i>View</a></li>
                            <li><a class="dropdown-item" href="{{ route('web_forms.edit', $f) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                            <li><a class="dropdown-item" href="{{ route('web_forms.public', $f) }}" target="_blank"><i class="bi bi-box-arrow-up-right me-2"></i>Preview</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item text-danger" onclick="confirmDelete('{{ route('web_forms.destroy', $f) }}','{{ $f->name }}')"><i class="bi bi-trash me-2"></i>Delete</button></li>
                        </ul></div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-top">{{ $forms->links() }}</div>
        @else
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-ui-checks-grid"></i></div>
            <h5>No web forms yet</h5>
            <p class="text-muted">Build embeddable forms that auto-create contacts and leads.</p>
            <a href="{{ route('web_forms.create') }}" class="btn btn-primary">Create Form</a>
        </div>
        @endif
    </div>
</div>
@endsection
