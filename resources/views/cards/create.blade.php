@extends('layouts.app')
@section('title', 'Create Card')
@section('page-title', 'Create Card')
@section('content')
<div class="page-header"><div><h1>Create Card</h1></div></div>
<form method="POST" action="{{ route('cards.store') }}">
@csrf
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card"><div class="card-body px-4 py-4"><div class="row g-3">
            <div class="col-12"><label class="form-label fw-600">Card Name <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
            <div class="col-md-6"><label class="form-label fw-600">Template</label><select name="template_id" class="form-select"><option value="">— No Template —</option>@foreach($templates as $t)<option value="{{ $t->id }}" {{ old('template_id') == $t->id ? 'selected' : '' }}>{{ $t->name }} ({{ ucfirst($t->category) }})</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label fw-600">Assign to Contact</label><select name="contact_id" class="form-select"><option value="">— No Contact —</option>@foreach($contacts as $c)<option value="{{ $c->id }}" {{ old('contact_id') == $c->id ? 'selected' : '' }}>{{ $c->full_name }}</option>@endforeach</select></div>
        </div></div></div>
    </div>
    <div class="col-lg-4"><div class="d-grid gap-2 mt-4">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Create Card</button>
        <a href="{{ route('cards.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div></div>
</div>
</form>
@endsection
