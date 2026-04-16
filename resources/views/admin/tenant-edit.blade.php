@extends('layouts.app')
@section('title', 'Edit Tenant')
@section('page-title', 'Edit Tenant')
@section('content')
<div class="page-header"><div><h1>Edit: {{ $tenant->name }}</h1></div></div>
<form method="POST" action="{{ route('admin.tenants.update', $tenant) }}">
@csrf @method('PATCH')
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card"><div class="card-body px-4 py-4"><div class="row g-3">
            <div class="col-12"><label class="form-label fw-600">Company Name</label><input type="text" name="name" class="form-control" value="{{ old('name', $tenant->name) }}" required></div>
            <div class="col-md-6"><label class="form-label fw-600">Plan</label><select name="plan" class="form-select">@foreach(['free','starter','pro','enterprise'] as $p)<option value="{{ $p }}" {{ old('plan', $tenant->plan) === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label fw-600">Status</label><select name="status" class="form-select">@foreach(['active','suspended','cancelled'] as $s)<option value="{{ $s }}" {{ old('status', $tenant->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label fw-600">Max Users</label><input type="number" name="max_users" class="form-control" value="{{ old('max_users', $tenant->max_users) }}" min="1" required></div>
            <div class="col-md-6"><label class="form-label fw-600">Max Contacts</label><input type="number" name="max_contacts" class="form-control" value="{{ old('max_contacts', $tenant->max_contacts) }}" min="1" required></div>
        </div></div></div>
    </div>
    <div class="col-lg-5"><div class="d-grid gap-2 mt-4">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update Tenant</button>
        <a href="{{ route('admin.tenants') }}" class="btn btn-outline-secondary">Cancel</a>
    </div></div>
</div>
</form>
@endsection
