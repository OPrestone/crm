@extends('layouts.app')
@section('title', 'Edit Contract')
@section('page-title', 'Edit Contract')
@section('content')
<div class="page-header">
    <div><h1>Edit Contract</h1></div>
    <a href="{{ route('contracts.show', $contract) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<form method="POST" action="{{ route('contracts.update', $contract) }}">
    @csrf @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header fw-600">Contract Details</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-600">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $contract->title) }}" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-600">Contact</label>
                            <select name="contact_id" class="form-select"><option value="">—</option>@foreach($contacts as $c)<option value="{{ $c->id }}" {{ old('contact_id',$contract->contact_id)==$c->id?'selected':'' }}>{{ $c->full_name }}</option>@endforeach</select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Deal</label>
                            <select name="deal_id" class="form-select"><option value="">—</option>@foreach($deals as $d)<option value="{{ $d->id }}" {{ old('deal_id',$contract->deal_id)==$d->id?'selected':'' }}>{{ $d->title }}</option>@endforeach</select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Content <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="16" required>{{ old('content', $contract->content) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header fw-600">Settings</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-600">Value</label>
                        <div class="input-group"><span class="input-group-text">$</span><input type="number" name="value" class="form-control" value="{{ old('value', $contract->value) }}" step="0.01"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Status</label>
                        <select name="status" class="form-select">
                            @foreach(['draft','pending_signature','signed','expired','cancelled'] as $s)
                            <option value="{{ $s }}" {{ old('status',$contract->status)===$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $contract->start_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $contract->end_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Signed By</label>
                        <input type="text" name="signed_by" class="form-control" value="{{ old('signed_by', $contract->signed_by) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Signed At</label>
                        <input type="date" name="signed_at" class="form-control" value="{{ old('signed_at', $contract->signed_at?->format('Y-m-d')) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $contract->notes) }}</textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Update Contract</button>
                        <a href="{{ route('contracts.show', $contract) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
