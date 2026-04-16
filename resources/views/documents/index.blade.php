@extends('layouts.app')
@section('title', 'Documents')
@section('page-title', 'Documents')

@section('content')
<div class="page-header">
    <div>
        <h1>Documents</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Documents</li>
        </ol></nav>
    </div>
    <a href="{{ route('documents.create') }}" class="btn btn-primary"><i class="bi bi-upload me-1"></i>Upload</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-4"><div class="stat-card">
        <div class="stat-icon bg-primary-subtle"><i class="bi bi-files text-primary"></i></div>
        <div class="stat-value">{{ $stats['total'] }}</div><div class="stat-label">Total Files</div>
    </div></div>
    <div class="col-6 col-md-4"><div class="stat-card">
        <div class="stat-icon bg-info-subtle"><i class="bi bi-hdd text-info"></i></div>
        <div class="stat-value">{{ round($stats['size'] / 1048576, 1) }} MB</div><div class="stat-label">Total Size</div>
    </div></div>
    <div class="col-6 col-md-4"><div class="stat-card">
        <div class="stat-icon bg-success-subtle"><i class="bi bi-calendar3 text-success"></i></div>
        <div class="stat-value">{{ $stats['this_month'] }}</div><div class="stat-label">This Month</div>
    </div></div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4"><div class="search-box"><i class="bi bi-search search-icon"></i>
                <input type="text" name="search" class="form-control" placeholder="Search documents..." value="{{ request('search') }}">
            </div></div>
            <div class="col-md-2">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)<option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="pdf" {{ request('type') === 'pdf' ? 'selected' : '' }}>PDF</option>
                    <option value="word" {{ request('type') === 'word' ? 'selected' : '' }}>Word</option>
                    <option value="excel" {{ request('type') === 'excel' ? 'selected' : '' }}>Excel</option>
                    <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Image</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary me-2"><i class="bi bi-filter"></i> Filter</button>
                <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card table-card">
    <div class="card-body p-0">
        @if($documents->count())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>File</th><th>Category</th><th>Size</th><th>Uploaded By</th><th>Date</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($documents as $doc)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi {{ $doc->file_icon }}" style="font-size:1.5rem;"></i>
                                <div>
                                    <a href="{{ route('documents.show', $doc) }}" class="fw-600 text-decoration-none">{{ $doc->title }}</a>
                                    <div class="text-muted small">{{ $doc->file_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $doc->category ?? '—' }}</td>
                        <td>{{ $doc->file_size_human }}</td>
                        <td>{{ $doc->uploader?->name ?? '—' }}</td>
                        <td class="text-muted small">{{ $doc->created_at->format('d M Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('documents.download', $doc) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i></a>
                            <form method="POST" action="{{ route('documents.destroy', $doc) }}" class="d-inline" onsubmit="return confirm('Delete this document?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3">{{ $documents->links() }}</div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-folder2-open text-muted" style="font-size:3rem;"></i>
            <h5 class="mt-3 text-muted">No documents yet</h5>
            <a href="{{ route('documents.create') }}" class="btn btn-primary mt-2"><i class="bi bi-upload me-1"></i>Upload First Document</a>
        </div>
        @endif
    </div>
</div>
@endsection
