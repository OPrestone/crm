@extends('layouts.app')
@section('title', 'New Tenant')
@section('page-title', 'New Tenant')
@section('content')
<div class="page-header"><div><h1>Create Tenant</h1></div></div>
<form method="POST" action="{{ route('admin.tenants.store') }}">
@csrf
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card"><div class="card-body px-4 py-4"><div class="row g-3">
            <div class="col-12"><label class="form-label fw-600">Company Name <span class="text-danger">*</span></label><input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}" required></div>
            <div class="col-md-6"><label class="form-label fw-600">Admin Name <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
            <div class="col-md-6"><label class="form-label fw-600">Admin Email <span class="text-danger">*</span></label><input type="email" name="email" class="form-control" value="{{ old('email') }}" required></div>
            <div class="col-md-6"><label class="form-label fw-600">Password</label><input type="password" name="password" class="form-control" minlength="8" required></div>
            <div class="col-md-6"><label class="form-label fw-600">Plan</label><select name="plan" class="form-select"><option value="free">Free</option><option value="starter">Starter</option><option value="pro">Pro</option><option value="enterprise">Enterprise</option></select></div>
        </div></div></div>
    </div>
    <div class="col-lg-5"><div class="d-grid gap-2 mt-4">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Create Tenant</button>
        <a href="{{ route('admin.tenants') }}" class="btn btn-outline-secondary">Cancel</a>
    </div></div>
</div>
</form>
@endsection
