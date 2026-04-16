@extends('layouts.app')
@section('title', 'Edit Template')
@section('page-title', 'Edit Contract Template')
@section('content')
<div class="page-header">
    <div><h1>Edit: {{ $template->name }}</h1></div>
    <a href="{{ route('contracts.templates') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<form method="POST" action="{{ route('contracts.templates.update', $template) }}">
    @csrf @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header fw-600">Template</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-600">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $template->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Content <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="18" required>{{ old('content', $template->content) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Update Template</button>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header fw-600">Placeholders</div>
                <div class="card-body">
                    <ul class="list-unstyled small text-muted mb-0">
                        @foreach(['contact_name','contact_email','company_name','deal_title','deal_value','date','year','tenant_name'] as $p)
                        <li class="mb-2"><code class="text-primary">{{ '{{' }}{{ $p }}{{ '}}' }}</code></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
