@extends('layouts.app')
@section('title', 'New Contract')
@section('page-title', 'New Contract')
@section('content')
<div class="page-header">
    <div><h1>New Contract</h1></div>
    <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<form method="POST" action="{{ route('contracts.store') }}">
    @csrf
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header fw-600">Contract Details</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-600">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-600">Contact</label>
                            <select name="contact_id" class="form-select">
                                <option value="">Select contact...</option>
                                @foreach($contacts as $contact)<option value="{{ $contact->id }}" {{ old('contact_id')==$contact->id?'selected':'' }}>{{ $contact->full_name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Deal</label>
                            <select name="deal_id" class="form-select">
                                <option value="">Link to deal...</option>
                                @foreach($deals as $deal)<option value="{{ $deal->id }}" {{ old('deal_id')==$deal->id?'selected':'' }}>{{ $deal->title }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Template (optional)</label>
                        <select name="template_id" class="form-select" id="templateSelect">
                            <option value="">Start from scratch</option>
                            @foreach($templates as $t)<option value="{{ $t->id }}" {{ old('template_id')==$t->id?'selected':'' }}>{{ $t->name }}</option>@endforeach
                        </select>
                        <div class="form-text">Selecting a template will populate the content below.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Contract Content <span class="text-danger">*</span></label>
                        <textarea name="content" id="contractContent" class="form-control @error('content') is-invalid @enderror" rows="16" required placeholder="Enter the full contract text here...">{{ old('content') }}</textarea>
                        @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                        <div class="input-group"><span class="input-group-text">$</span><input type="number" name="value" class="form-control" value="{{ old('value') }}" step="0.01" min="0"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Status</label>
                        <select name="status" class="form-select">
                            @foreach(['draft','pending_signature','signed','expired','cancelled'] as $s)
                            <option value="{{ $s }}" {{ old('status','draft')===$s?'selected':'' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Signed By</label>
                        <input type="text" name="signed_by" class="form-control" value="{{ old('signed_by') }}" placeholder="Signatory name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Signed At</label>
                        <input type="date" name="signed_at" class="form-control" value="{{ old('signed_at') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Create Contract</button>
                        <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
@push('scripts')
<script>
document.getElementById('templateSelect').addEventListener('change', function() {
    if (!this.value) return;
    fetch('{{ route('contracts.template.content', ':id') }}'.replace(':id', this.value))
        .then(r => r.json())
        .then(d => { document.getElementById('contractContent').value = d.content; });
});
</script>
@endpush
