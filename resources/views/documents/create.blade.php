@extends('layouts.app')
@section('title', 'Upload Document')
@section('page-title', 'Upload Document')

@section('content')
<div class="page-header">
    <div>
        <h1>Upload Document</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Documents</a></li>
            <li class="breadcrumb-item active">Upload</li>
        </ol></nav>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header fw-600"><i class="bi bi-upload me-2 text-primary"></i>Upload File</div>
            <div class="card-body">
                <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-600">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Category</label>
                            <input type="text" name="category" class="form-control" value="{{ old('category') }}" placeholder="e.g. Contract, Proposal">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-600">Description</label>
                            <textarea name="description" rows="2" class="form-control">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-600">File <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" required>
                            <div class="form-text">Max 20 MB. Supported: PDF, Word, Excel, images, ZIP and more.</div>
                            @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Link to</label>
                            <select name="documentable_type" class="form-select">
                                <option value="">— None —</option>
                                <option value="contact" {{ old('documentable_type') === 'contact' ? 'selected' : '' }}>Contact</option>
                                <option value="deal" {{ old('documentable_type') === 'deal' ? 'selected' : '' }}>Deal</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="documentableIdWrap" style="{{ old('documentable_type') ? '' : 'display:none' }}">
                            <label class="form-label fw-600">Select Record</label>
                            <select name="documentable_id" class="form-select" id="documentableIdSelect">
                                <option value="">— Choose —</option>
                                @foreach($contacts as $c)<option value="{{ $c->id }}" data-type="contact">{{ $c->full_name }}</option>@endforeach
                                @foreach($deals as $d)<option value="{{ $d->id }}" data-type="deal">{{ $d->title }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-upload me-1"></i>Upload</button>
                        <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.querySelector('[name="documentable_type"]').addEventListener('change', function() {
    const wrap = document.getElementById('documentableIdWrap');
    const sel = document.getElementById('documentableIdSelect');
    wrap.style.display = this.value ? '' : 'none';
    Array.from(sel.options).forEach(o => {
        if (!o.value) return;
        o.style.display = (o.dataset.type === this.value) ? '' : 'none';
    });
    sel.value = '';
});
</script>
@endpush
