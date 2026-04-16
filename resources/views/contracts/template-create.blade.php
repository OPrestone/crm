@extends('layouts.app')
@section('title', 'New Template')
@section('page-title', 'New Contract Template')
@section('content')
<div class="page-header">
    <div><h1>New Contract Template</h1></div>
    <a href="{{ route('contracts.templates') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<form method="POST" action="{{ route('contracts.templates.store') }}">
    @csrf
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header fw-600">Template</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-600">Template Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Content <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="18" required placeholder="Enter contract template content...">{{ old('content') }}</textarea>
                        <div class="form-text">Use placeholders like <code>{{contact_name}}</code>, <code>{{company_name}}</code>, <code>{{date}}</code> that will be replaced when the contract is created.</div>
                        @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Create Template</button>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header fw-600">Available Placeholders</div>
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
