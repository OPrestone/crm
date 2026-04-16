@extends('layouts.app')
@section('title', 'New Template')
@section('page-title', 'New Card Template')
@section('content')
<div class="page-header"><div><h1>New Card Template</h1></div></div>
<form method="POST" action="{{ route('cards.templates.store') }}">
@csrf
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card"><div class="card-body px-4 py-4"><div class="row g-3">
            <div class="col-12"><label class="form-label fw-600">Template Name <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
            <div class="col-md-6"><label class="form-label fw-600">Category</label><select name="category" class="form-select"><option value="business">Business Card</option><option value="id">ID Card</option><option value="membership">Membership Card</option><option value="event">Event Badge</option></select></div>
        </div></div></div>
    </div>
    <div class="col-lg-4"><div class="d-grid gap-2 mt-4">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Template</button>
        <a href="{{ route('cards.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div></div>
</div>
</form>
@endsection
