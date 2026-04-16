@extends('layouts.app')
@section('title', 'Create Card')
@section('page-title', 'Create Card')
@section('content')
<div class="page-header">
    <div><h1><i class="bi bi-credit-card-2-front me-2"></i>Create Card</h1><p class="text-muted mb-0">Generate a professional card with photo and QR code</p></div>
    <a href="{{ route('cards.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<form method="POST" action="{{ route('cards.store') }}" enctype="multipart/form-data">
@csrf
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Card Details</h5></div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12"><label class="form-label fw-600">Card Name <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="e.g. John Smith Business Card"></div>
                    <div class="col-md-6"><label class="form-label fw-600">Template</label><select name="template_id" class="form-select"><option value="">— No Template —</option>@foreach($templates as $t)<option value="{{ $t->id }}" {{ old('template_id')==$t->id?'selected':'' }}>{{ $t->name }} ({{ ucfirst($t->category) }})</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label fw-600">Link to Contact</label><select name="contact_id" class="form-select" id="contactSelect" onchange="autoFillQr()"><option value="">— No Contact —</option>@foreach($contacts as $c)<option value="{{ $c->id }}" data-email="{{ $c->email }}" data-phone="{{ $c->phone }}" data-name="{{ $c->first_name }} {{ $c->last_name }}" {{ old('contact_id')==$c->id?'selected':'' }}>{{ $c->first_name }} {{ $c->last_name }}</option>@endforeach</select></div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Card Fields <span class="text-muted fw-400" style="font-size:13px;">(override contact data)</span></h5></div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-600">Full Name</label><input type="text" name="data[name]" class="form-control" value="{{ old('data.name') }}" placeholder="Display name on card"></div>
                    <div class="col-md-6"><label class="form-label fw-600">Job Title</label><input type="text" name="data[title]" class="form-control" value="{{ old('data.title') }}" placeholder="e.g. Senior Sales Manager"></div>
                    <div class="col-md-6"><label class="form-label fw-600">Company</label><input type="text" name="data[company]" class="form-control" value="{{ old('data.company') }}"></div>
                    <div class="col-md-6"><label class="form-label fw-600">Email</label><input type="email" name="data[email]" class="form-control" value="{{ old('data.email') }}"></div>
                    <div class="col-md-6"><label class="form-label fw-600">Phone</label><input type="text" name="data[phone]" class="form-control" value="{{ old('data.phone') }}"></div>
                    <div class="col-md-6"><label class="form-label fw-600">Website</label><input type="text" name="data[website]" class="form-control" value="{{ old('data.website') }}" placeholder="e.g. www.company.com"></div>
                    <div class="col-md-6"><label class="form-label fw-600">LinkedIn</label><input type="text" name="data[linkedin]" class="form-control" value="{{ old('data.linkedin') }}" placeholder="e.g. linkedin.com/in/john"></div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0"><i class="bi bi-qr-code me-2"></i>QR Code Data <span class="text-muted fw-400" style="font-size:13px;">(auto-generated from contact)</span></h5></div>
            <div class="card-body p-4">
                <label class="form-label fw-600">QR Encode (vCard, URL, or any text)</label>
                <textarea name="qr_data" id="qrData" class="form-control font-monospace" rows="5" placeholder="Leave blank to auto-generate from contact, or enter a URL like https://yoursite.com">{{ old('qr_data') }}</textarea>
                <div class="form-text">Auto-generates a vCard QR code from the linked contact. Or enter a custom URL/text.</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0"><i class="bi bi-person-circle me-2"></i>Photo</h5></div>
            <div class="card-body p-4">
                <div class="upload-zone" onclick="document.getElementById('photoInput').click()" id="photoZone">
                    <i class="bi bi-person-bounding-box fs-2 text-muted mb-2 d-block"></i>
                    <div class="fw-600 text-muted" style="font-size:14px;">Upload Photo</div>
                    <div class="text-muted" style="font-size:12px;">JPG, PNG · max 3MB</div>
                </div>
                <input type="file" name="photo" id="photoInput" accept=".jpg,.jpeg,.png" class="d-none" onchange="previewPhoto(this)">
                <div class="form-text mt-2">Photo appears on the card and in PDF export.</div>
            </div>
        </div>
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-credit-card-2-front me-2"></i>Create Card</button>
            <a href="{{ route('cards.index') }}" class="btn btn-outline-secondary">Cancel</a>
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
function previewPhoto(input) {
    const zone = document.getElementById('photoZone');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            zone.innerHTML = '<img src="'+e.target.result+'" style="width:80px;height:80px;border-radius:50%;object-fit:cover;margin-bottom:8px;"><div style="font-size:13px;" class="text-success fw-600">'+input.files[0].name+'</div>';
            zone.classList.add('uploaded');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function autoFillQr() {
    const sel = document.getElementById('contactSelect');
    const opt = sel.options[sel.selectedIndex];
    const qr  = document.getElementById('qrData');
    if (opt.value && !qr.value) {
        const name  = opt.dataset.name || '';
        const email = opt.dataset.email || '';
        const phone = opt.dataset.phone || '';
        qr.value = 'BEGIN:VCARD\nVERSION:3.0\nFN:'+name+(email?'\nEMAIL:'+email:'')+(phone?'\nTEL:'+phone:'')+'\nEND:VCARD';
    }
}
</script>
@endpush
@endsection
