@extends('layouts.app')
@section('title', 'Edit Plan')
@section('page-title', 'Edit Commission Plan')
@section('content')
<div class="page-header">
    <div><h1>Edit: {{ $plan->name }}</h1></div>
    <a href="{{ route('commissions.plans') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header fw-600">Plan Details</div>
            <div class="card-body">
                <form method="POST" action="{{ route('commissions.plans.update', $plan) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-600">Plan Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $plan->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Type</label>
                        <select name="type" class="form-select" id="planType" onchange="toggleType()">
                            @foreach(['percentage','flat','tiered'] as $t)
                            <option value="{{ $t }}" {{ old('type',$plan->type)===$t?'selected':'' }}>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Rate</label>
                        <div class="input-group">
                            <input type="number" name="rate" class="form-control" value="{{ old('rate', $plan->rate) }}" step="0.01" min="0" required>
                            <span class="input-group-text" id="rateSymbol">%</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Minimum Deal Value ($)</label>
                        <input type="number" name="min_deal_value" class="form-control" value="{{ old('min_deal_value', $plan->min_deal_value) }}" step="100" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Status</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $plan->is_active ? 'checked' : '' }}>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Update Plan</button>
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
