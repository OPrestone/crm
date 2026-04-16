@extends('layouts.app')
@section('title', 'Edit Verification')
@section('page-title', 'Edit Verification')
@section('content')
<div class="page-header">
    <div><h1><i class="bi bi-pencil me-2"></i>Edit Verification</h1><p class="text-muted mb-0">{{ $idVerification->full_name }}</p></div>
    <a href="{{ route('id-verification.show', $idVerification) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<form method="POST" action="{{ route('id-verification.update', $idVerification) }}" enctype="multipart/form-data">
@csrf @method('PUT')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Personal Information</h5></div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-600">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control" value="{{ old('full_name',$idVerification->full_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth',$idVerification->date_of_birth?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Nationality</label>
                        <input type="text" name="nationality" class="form-control" value="{{ old('nationality',$idVerification->nationality) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">— Select —</option>
                            <option value="male" {{ old('gender',$idVerification->gender)==='male'?'selected':'' }}>Male</option>
                            <option value="female" {{ old('gender',$idVerification->gender)==='female'?'selected':'' }}>Female</option>
                            <option value="other" {{ old('gender',$idVerification->gender)==='other'?'selected':'' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-600">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address',$idVerification->address) }}</textarea>
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
                        <select name="id_type" class="form-select" required>
                            <option value="passport" {{ old('id_type',$idVerification->id_type)==='passport'?'selected':'' }}>Passport</option>
                            <option value="national_id" {{ old('id_type',$idVerification->id_type)==='national_id'?'selected':'' }}>National ID</option>
                            <option value="driver_license" {{ old('id_type',$idVerification->id_type)==='driver_license'?'selected':'' }}>Driver's License</option>
                            <option value="residence_permit" {{ old('id_type',$idVerification->id_type)==='residence_permit'?'selected':'' }}>Residence Permit</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">ID Number</label>
                        <input type="text" name="id_number" class="form-control font-monospace" value="{{ old('id_number',$idVerification->id_number) }}">
                    </div>
                    <div class="col-md-4"><label class="form-label fw-600">Issue Date</label><input type="date" name="issue_date" class="form-control" value="{{ old('issue_date',$idVerification->issue_date?->format('Y-m-d')) }}"></div>
                    <div class="col-md-4"><label class="form-label fw-600">Expiry Date</label><input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date',$idVerification->expiry_date?->format('Y-m-d')) }}"></div>
                    <div class="col-md-4"><label class="form-label fw-600">Issuing Country</label><input type="text" name="issuing_country" class="form-control" value="{{ old('issuing_country',$idVerification->issuing_country) }}"></div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Replace Documents <span class="text-muted fw-400" style="font-size:13px;">(leave blank to keep existing)</span></h5></div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-600">Document Front</label>
                        @if($idVerification->document_front)
                        <div class="mb-2"><img src="{{ Storage::url($idVerification->document_front) }}" class="img-fluid rounded border" style="max-height:80px;" onerror="this.style.display='none'"></div>
                        @endif
                        <input type="file" name="document_front" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600">Document Back</label>
                        @if($idVerification->document_back)
                        <div class="mb-2"><img src="{{ Storage::url($idVerification->document_back) }}" class="img-fluid rounded border" style="max-height:80px;" onerror="this.style.display='none'"></div>
                        @endif
                        <input type="file" name="document_back" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600">Selfie</label>
                        @if($idVerification->selfie)
                        <div class="mb-2"><img src="{{ Storage::url($idVerification->selfie) }}" class="img-fluid rounded border" style="max-height:80px;"></div>
                        @endif
                        <input type="file" name="selfie" class="form-control" accept=".jpg,.jpeg,.png">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Link to Contact</h5></div>
            <div class="card-body p-4">
                <select name="contact_id" class="form-select">
                    <option value="">— None —</option>
                    @foreach($contacts as $c)
                    <option value="{{ $c->id }}" {{ old('contact_id',$idVerification->contact_id)==$c->id?'selected':'' }}>{{ $c->first_name }} {{ $c->last_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Notes</h5></div>
            <div class="card-body p-4">
                <textarea name="notes" class="form-control" rows="5">{{ old('notes',$idVerification->notes) }}</textarea>
            </div>
        </div>
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save me-2"></i>Save Changes</button>
            <a href="{{ route('id-verification.show', $idVerification) }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</div>
</form>
@endsection
