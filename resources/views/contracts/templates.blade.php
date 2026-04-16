@extends('layouts.app')
@section('title', 'Contract Templates')
@section('page-title', 'Contract Templates')
@section('content')
<div class="page-header">
    <div><h1>Contract Templates</h1></div>
    <div class="d-flex gap-2">
        <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Contracts</a>
        <a href="{{ route('contracts.templates.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Template</a>
    </div>
</div>

@if(session('success'))<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>@endif

<div class="card table-card">
    <div class="card-body p-0">
        @if($templates->count())
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Template Name</th><th>Preview</th><th>Created</th><th></th></tr></thead>
                <tbody>
                @foreach($templates as $t)
                <tr>
                    <td class="fw-600">{{ $t->name }}</td>
                    <td class="text-muted small">{{ Str::limit(strip_tags($t->content), 80) }}</td>
                    <td class="text-muted small">{{ $t->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="dropdown"><button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('contracts.templates.edit', $t) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                            <li><a class="dropdown-item" href="{{ route('contracts.create', ['template_id' => $t->id]) }}"><i class="bi bi-plus-circle me-2"></i>Use Template</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item text-danger" onclick="confirmDelete('{{ route('contracts.templates.destroy', $t) }}','{{ $t->name }}')"><i class="bi bi-trash me-2"></i>Delete</button></li>
                        </ul></div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-top">{{ $templates->links() }}</div>
        @else
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-file-text"></i></div>
            <h5>No templates yet</h5>
            <p class="text-muted">Create reusable contract templates to speed up contract creation.</p>
            <a href="{{ route('contracts.templates.create') }}" class="btn btn-primary">Create Template</a>
        </div>
        @endif
    </div>
</div>
@endsection
