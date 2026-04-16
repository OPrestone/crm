@extends('layouts.app')
@section('title', 'New Commission Plan')
@section('page-title', 'New Commission Plan')
@section('content')
<div class="page-header">
    <div><h1>New Commission Plan</h1></div>
    <a href="{{ route('commissions.plans') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header fw-600">Plan Details</div>
            <div class="card-body">
                <form method="POST" action="{{ route('commissions.plans.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-600">Plan Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Standard 10% Commission">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" id="planType" onchange="toggleType()">
                            <option value="percentage" {{ old('type')==='percentage'?'selected':'' }}>Percentage of deal value</option>
                            <option value="flat" {{ old('type')==='flat'?'selected':'' }}>Flat rate per deal</option>
                            <option value="tiered" {{ old('type')==='tiered'?'selected':'' }}>Tiered percentage</option>
                        </select>
                    </div>
                    <div class="mb-3" id="rateField">
                        <label class="form-label fw-600">Rate <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="rate" class="form-control" value="{{ old('rate', 10) }}" step="0.01" min="0" required>
                            <span class="input-group-text" id="rateSymbol">%</span>
                        </div>
                        <div class="form-text">For percentage type, enter e.g. <code>10</code> for 10%. For flat, enter dollar amount.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Minimum Deal Value ($)</label>
                        <input type="number" name="min_deal_value" class="form-control" value="{{ old('min_deal_value', 0) }}" step="100" min="0">
                        <div class="form-text">Commission is only calculated for deals at or above this value.</div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Create Plan</button>
                        <a href="{{ route('commissions.plans') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function toggleType() {
    const type = document.getElementById('planType').value;
    document.getElementById('rateSymbol').textContent = type === 'flat' ? '$' : '%';
}
toggleType();
</script>
@endpush
