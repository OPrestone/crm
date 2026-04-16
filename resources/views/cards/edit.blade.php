@extends('layouts.app')
@section('title', 'Edit Card')
@section('page-title', 'Edit Card')
@section('content')
<div class="page-header">
    <div><h1><i class="bi bi-pencil me-2"></i>Edit Card</h1><p class="text-muted mb-0">{{ $card->name }}</p></div>
    <a href="{{ route('cards.show', $card) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<form method="POST" action="{{ route('cards.update', $card) }}" enctype="multipart/form-data">
@csrf @method('PUT')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Card Details</h5></div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12"><label class="form-label fw-600">Card Name <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name',$card->name) }}" required></div>
                    <div class="col-md-6"><label class="form-label fw-600">Template</label><select name="template_id" class="form-select"><option value="">— No Template —</option>@foreach($templates as $t)<option value="{{ $t->id }}" {{ old('template_id',$card->template_id)==$t->id?'selected':'' }}>{{ $t->name }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label fw-600">Link to Contact</label><select name="contact_id" class="form-select"><option value="">— None —</option>@foreach($contacts as $c)<option value="{{ $c->id }}" {{ old('contact_id',$card->contact_id)==$c->id?'selected':'' }}>{{ $c->first_name }} {{ $c->last_name }}</option>@endforeach</select></div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Card Fields</h5></div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-600">Full Name</label><input type="text" name="data[name]" class="form-control" value="{{ old('data.name',$card->data['name']??'') }}"></div>
                    <div class="col-md-6"><label class="form-label fw-600">Job Title</label><input type="text" name="data[title]" class="form-control" value="{{ old('data.title',$card->data['title']??'') }}"></div>
                    <div class="col-md-6"><label class="form-label fw-600">Company</label><input type="text" name="data[company]" class="form-control" value="{{ old('data.company',$card->data['company']??'') }}"></div>
                    <div class="col-md-6"><label class="form-label fw-600">Email</label><input type="email" name="data[email]" class="form-control" value="{{ old('data.email',$card->data['email']??'') }}"></div>
                    <div class="col-md-6"><label class="form-label fw-600">Phone</label><input type="text" name="data[phone]" class="form-control" value="{{ old('data.phone',$card->data['phone']??'') }}"></div>
                    <div class="col-md-6"><label class="form-label fw-600">Website</label><input type="text" name="data[website]" class="form-control" value="{{ old('data.website',$card->data['website']??'') }}"></div>
                    <div class="col-md-6"><label class="form-label fw-600">LinkedIn</label><input type="text" name="data[linkedin]" class="form-control" value="{{ old('data.linkedin',$card->data['linkedin']??'') }}"></div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0"><i class="bi bi-qr-code me-2"></i>QR Code Data</h5></div>
            <div class="card-body p-4">
                <textarea name="qr_data" class="form-control font-monospace" rows="5" placeholder="vCard, URL, or custom text">{{ old('qr_data',$card->qr_data) }}</textarea>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Photo</h5></div>
            <div class="card-body p-4">
                @if($card->photo)
                <div class="text-center mb-3">
                    <img src="{{ Storage::url($card->photo) }}" style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid #e5e7eb;">
                    <div class="text-muted mt-1" style="font-size:12px;">Current photo</div>
                </div>
                @endif
                <div class="upload-zone" onclick="document.getElementById('photoInput').click()" id="photoZone">
                    <i class="bi bi-arrow-repeat fs-4 text-muted mb-1 d-block"></i>
                    <div style="font-size:13px;" class="text-muted">{{ $card->photo?'Replace photo':'Upload photo' }}</div>
                </div>
                <input type="file" name="photo" id="photoInput" accept=".jpg,.jpeg,.png" class="d-none" onchange="previewPhoto(this)">
            </div>
        </div>
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save me-2"></i>Save Changes</button>
            <a href="{{ route('cards.show', $card) }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</div>
</form>
<style>
.upload-zone { border:2px dashed #dee2e6; border-radius:12px; padding:20px 16px; text-align:center; cursor:pointer; transition:all .2s; }
.upload-zone:hover { border-color:#4f46e5; background:#f5f5ff; }
</style>
@push('scripts')
<script>
function previewPhoto(input) {
    const zone = document.getElementById('photoZone');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            zone.innerHTML = '<img src="'+e.target.result+'" style="width:64px;height:64px;border-radius:50%;object-fit:cover;margin-bottom:6px;"><div style="font-size:12px;" class="text-success fw-600">'+input.files[0].name+'</div>';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
