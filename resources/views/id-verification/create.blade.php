@extends('layouts.app')
@section('title', 'New ID Verification')
@section('page-title', 'New ID Verification')
@section('content')
<div class="page-header">
    <div><h1><i class="bi bi-shield-plus me-2 text-primary"></i>New ID Verification</h1><p class="text-muted mb-0">Submit a new KYC document for verification</p></div>
    <a href="{{ route('id-verification.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<form method="POST" action="{{ route('id-verification.store') }}" enctype="multipart/form-data">
@csrf
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Personal Information</h5></div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-600">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name') }}" required>
                        @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Nationality</label>
                        <input type="text" name="nationality" class="form-control" value="{{ old('nationality') }}" placeholder="e.g. British, American">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">— Select —</option>
                            <option value="male" {{ old('gender')==='male'?'selected':'' }}>Male</option>
                            <option value="female" {{ old('gender')==='female'?'selected':'' }}>Female</option>
                            <option value="other" {{ old('gender')==='other'?'selected':'' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-600">Address</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Full residential address">{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Document Details</h5></div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-600">ID Type <span class="text-danger">*</span></label>
                        <select name="id_type" class="form-select @error('id_type') is-invalid @enderror" required>
                            <option value="">— Select —</option>
                            <option value="passport" {{ old('id_type')==='passport'?'selected':'' }}>Passport</option>
                            <option value="national_id" {{ old('id_type')==='national_id'?'selected':'' }}>National ID</option>
                            <option value="driver_license" {{ old('id_type')==='driver_license'?'selected':'' }}>Driver's License</option>
                            <option value="residence_permit" {{ old('id_type')==='residence_permit'?'selected':'' }}>Residence Permit</option>
                        </select>
                        @error('id_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">ID / Document Number</label>
                        <input type="text" name="id_number" class="form-control font-monospace" value="{{ old('id_number') }}" placeholder="e.g. A1234567">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600">Issue Date</label>
                        <input type="date" name="issue_date" class="form-control" value="{{ old('issue_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600">Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600">Issuing Country</label>
                        <input type="text" name="issuing_country" class="form-control" value="{{ old('issuing_country') }}" placeholder="e.g. United Kingdom">
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Document Uploads</h5></div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-600">Document Front</label>
                        <div class="upload-zone" onclick="document.getElementById('doc_front').click()" id="zone_front">
                            <i class="bi bi-cloud-upload fs-3 text-muted mb-2 d-block"></i>
                            <div style="font-size:13px;" class="text-muted">Click to upload front</div>
                            <div style="font-size:11px;" class="text-muted">JPG, PNG, PDF · max 5MB</div>
                        </div>
                        <input type="file" name="document_front" id="doc_front" accept=".jpg,.jpeg,.png,.pdf" class="d-none" onchange="previewUpload(this,'zone_front')">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600">Document Back</label>
                        <div class="upload-zone" onclick="document.getElementById('doc_back').click()" id="zone_back">
                            <i class="bi bi-cloud-upload fs-3 text-muted mb-2 d-block"></i>
                            <div style="font-size:13px;" class="text-muted">Click to upload back</div>
                            <div style="font-size:11px;" class="text-muted">JPG, PNG, PDF · max 5MB</div>
                        </div>
                        <input type="file" name="document_back" id="doc_back" accept=".jpg,.jpeg,.png,.pdf" class="d-none" onchange="previewUpload(this,'zone_back')">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600">Selfie / Liveness</label>
                        <div class="upload-zone" onclick="document.getElementById('selfie').click()" id="zone_selfie">
                            <i class="bi bi-person-video3 fs-3 text-muted mb-2 d-block"></i>
                            <div style="font-size:13px;" class="text-muted">Click to upload selfie</div>
                            <div style="font-size:11px;" class="text-muted">JPG, PNG · max 5MB</div>
                        </div>
                        <input type="file" name="selfie" id="selfie" accept=".jpg,.jpeg,.png" class="d-none" onchange="previewUpload(this,'zone_selfie')">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Link to Contact</h5></div>
            <div class="card-body p-4">
                <label class="form-label fw-600">CRM Contact</label>
                <select name="contact_id" class="form-select">
                    <option value="">— Optional —</option>
                    @foreach($contacts as $c)
                    <option value="{{ $c->id }}" {{ old('contact_id')==$c->id?'selected':'' }}>{{ $c->first_name }} {{ $c->last_name }}</option>
                    @endforeach
                </select>
                <div class="form-text">Link this verification to a CRM contact for complete KYC records.</div>
            </div>
        </div>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Notes</h5></div>
            <div class="card-body p-4">
                <textarea name="notes" class="form-control" rows="5" placeholder="Internal notes about this verification…">{{ old('notes') }}</textarea>
            </div>
        </div>
        <div class="card border-0 shadow-sm bg-primary-soft">
            <div class="card-body p-4">
                <h6 class="fw-700 mb-3"><i class="bi bi-info-circle me-2"></i>AI Confidence Scoring</h6>
                <p class="text-muted mb-0" style="font-size:13px;">The system automatically computes a confidence score based on the completeness and quality of the submitted data. More documents = higher confidence.</p>
            </div>
        </div>
        <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-shield-check me-2"></i>Submit for Verification</button>
            <a href="{{ route('id-verification.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</div>
</form>

<style>
.upload-zone { border:2px dashed #dee2e6; border-radius:12px; padding:24px 16px; text-align:center; cursor:pointer; transition:all .2s; }
.upload-zone:hover { border-color:#4f46e5; background:#f5f5ff; }
.upload-zone.uploaded { border-color:#10b981; background:#f0fdf4; }
</style>
@push('scripts')
<script>
function previewUpload(input, zoneId) {
    const zone = document.getElementById(zoneId);
    if (input.files && input.files[0]) {
        const name = input.files[0].name;
        zone.classList.add('uploaded');
        zone.innerHTML = '<i class="bi bi-check-circle-fill fs-3 text-success mb-2 d-block"></i><div style="font-size:13px;font-weight:600;" class="text-success">'+name+'</div><div style="font-size:11px;" class="text-muted">Click to change</div>';
    }
}
</script>
@endpush
@endsection
