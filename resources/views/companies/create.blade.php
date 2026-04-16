@extends('layouts.app')
@section('title', 'Add Company')
@section('page-title', 'Add Company')
@section('content')
<div class="page-header"><div><h1>Add Company</h1></div></div>
<form method="POST" action="{{ route('companies.store') }}">
@csrf
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card"><div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Company Details</h5></div>
        <div class="card-body px-4"><div class="row g-3">
            <div class="col-12"><label class="form-label fw-600">Company Name <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
            <div class="col-md-6"><label class="form-label fw-600">Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}"></div>
            <div class="col-md-6"><label class="form-label fw-600">Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone') }}"></div>
            <div class="col-md-6"><label class="form-label fw-600">Website</label><input type="url" name="website" class="form-control" value="{{ old('website') }}" placeholder="https://"></div>
            <div class="col-md-6"><label class="form-label fw-600">Industry</label>
                <select name="industry" class="form-select"><option value="">— Select —</option>@foreach(['Technology','Finance','Healthcare','Retail','Manufacturing','Services','Education','Real Estate','Other'] as $ind)<option value="{{ $ind }}" {{ old('industry') == $ind ? 'selected' : '' }}>{{ $ind }}</option>@endforeach</select>
            </div>
            <div class="col-md-6"><label class="form-label fw-600">Company Size</label>
                <select name="size" class="form-select"><option value="">— Select —</option>@foreach(['1-10','11-50','51-200','201-500','500+'] as $s)<option value="{{ $s }}" {{ old('size') == $s ? 'selected' : '' }}>{{ $s }} employees</option>@endforeach</select>
            </div>
            <div class="col-md-6"><label class="form-label fw-600">Annual Revenue ($)</label><input type="number" name="annual_revenue" class="form-control" value="{{ old('annual_revenue') }}" min="0"></div>
            <div class="col-md-6"><label class="form-label fw-600">Country</label><input type="text" name="country" class="form-control" value="{{ old('country') }}"></div>
            <div class="col-md-6"><label class="form-label fw-600">City</label><input type="text" name="city" class="form-control" value="{{ old('city') }}"></div>
            <div class="col-12"><label class="form-label fw-600">Address</label><textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea></div>
            <div class="col-12"><label class="form-label fw-600">Notes</label><textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea></div>
        </div></div></div>
    </div>
    <div class="col-lg-4"><div class="d-grid gap-2 mt-4">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Company</button>
        <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div></div>
</div>
</form>
@endsection
