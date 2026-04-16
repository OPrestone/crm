@extends('layouts.app')
@section('title', $document->title)
@section('page-title', 'Document')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $document->title }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Documents</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($document->title, 30) }}</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('documents.download', $document) }}" class="btn btn-primary"><i class="bi bi-download me-1"></i>Download</a>
        <form method="POST" action="{{ route('documents.destroy', $document) }}" onsubmit="return confirm('Delete this document?')">
            @csrf @method('DELETE')<button class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>Delete</button>
        </form>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <i class="bi {{ $document->file_icon }}" style="font-size:3rem;"></i>
                    <div>
                        <div class="fw-600 fs-6">{{ $document->file_name }}</div>
                        <div class="text-muted small">{{ $document->file_size_human }} · {{ $document->file_type ?? 'unknown' }}</div>
                    </div>
                </div>
                <hr>
                <div class="row g-3">
                    <div class="col-md-6"><div class="text-muted small">Category</div><div>{{ $document->category ?? '—' }}</div></div>
                    <div class="col-md-6"><div class="text-muted small">Uploaded By</div><div>{{ $document->uploader?->name ?? '—' }}</div></div>
                    <div class="col-md-6"><div class="text-muted small">Uploaded On</div><div>{{ $document->created_at->format('d M Y H:i') }}</div></div>
                    @if($document->documentable_type)
                    <div class="col-md-6"><div class="text-muted small">Linked To</div><div>{{ class_basename($document->documentable_type) }} #{{ $document->documentable_id }}</div></div>
                    @endif
                    @if($document->description)
                    <div class="col-12"><div class="text-muted small">Description</div><div>{{ $document->description }}</div></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
