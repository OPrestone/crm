@extends('layouts.app')
@section('title', 'Edit Form')
@section('page-title', 'Edit Form')
@section('content')
<div class="page-header">
    <div><h1>Edit: {{ $webForm->name }}</h1></div>
    <a href="{{ route('web_forms.show', $webForm) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<form method="POST" action="{{ route('web_forms.update', $webForm) }}">
    @csrf @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header fw-600">Form Details</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-600">Form Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $webForm->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Description</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $webForm->description) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header fw-600 d-flex justify-content-between">
                    <span>Fields</span>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addFieldBtn"><i class="bi bi-plus-lg me-1"></i>Add Field</button>
                </div>
                <div class="card-body">
                    <div id="fieldsContainer">
                    @php $fields = old('fields', $webForm->fields ?? []); @endphp
                    @foreach($fields as $i => $field)
                    <div class="field-row row g-2 align-items-center mb-2 border rounded p-2">
                        <div class="col-md-3"><input type="text" name="fields[{{ $i }}][label]" class="form-control form-control-sm" value="{{ $field['label'] }}" placeholder="Label" required></div>
                        <div class="col-md-3"><input type="text" name="fields[{{ $i }}][name]" class="form-control form-control-sm" value="{{ $field['name'] }}" placeholder="name_key" required></div>
                        <div class="col-md-2"><select name="fields[{{ $i }}][type]" class="form-select form-select-sm">@foreach(['text','email','phone','textarea','number'] as $t)<option value="{{ $t }}" {{ ($field['type']??'text')===$t?'selected':'' }}>{{ ucfirst($t) }}</option>@endforeach</select></div>
                        <div class="col-md-2 text-center"><div class="form-check form-switch d-inline-flex gap-2"><input class="form-check-input" type="checkbox" name="fields[{{ $i }}][required]" value="1" {{ ($field['required']??false)?'checked':'' }}><label class="form-check-label small">Req</label></div></div>
                        <div class="col-md-2 text-end"><button type="button" class="btn btn-sm btn-outline-danger remove-field"><i class="bi bi-trash"></i></button></div>
                    </div>
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header fw-600">Settings</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-600">On Submit, Create</label>
                        <select name="submit_action" class="form-select">
                            <option value="contact" {{ old('submit_action',$webForm->submit_action)==='contact'?'selected':'' }}>Contact Only</option>
                            <option value="lead" {{ old('submit_action',$webForm->submit_action)==='lead'?'selected':'' }}>Lead Only</option>
                            <option value="both" {{ old('submit_action',$webForm->submit_action)==='both'?'selected':'' }}>Contact + Lead</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Success Message</label>
                        <textarea name="success_message" class="form-control" rows="2">{{ old('success_message', $webForm->success_message) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Redirect URL</label>
                        <input type="url" name="redirect_url" class="form-control" value="{{ old('redirect_url', $webForm->redirect_url) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">Status</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $webForm->is_active ? 'checked' : '' }}>
                            <label class="form-check-label">Active (accepting submissions)</label>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Update Form</button>
                        <a href="{{ route('web_forms.show', $webForm) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
@push('scripts')
<script>
let fieldIndex = {{ count($webForm->fields ?? []) }};
document.getElementById('addFieldBtn').addEventListener('click', function() {
    const container = document.getElementById('fieldsContainer');
    const div = document.createElement('div');
    div.className = 'field-row row g-2 align-items-center mb-2 border rounded p-2';
    div.innerHTML = `<div class="col-md-3"><input type="text" name="fields[${fieldIndex}][label]" class="form-control form-control-sm" placeholder="Label" required></div><div class="col-md-3"><input type="text" name="fields[${fieldIndex}][name]" class="form-control form-control-sm" placeholder="name_key" required></div><div class="col-md-2"><select name="fields[${fieldIndex}][type]" class="form-select form-select-sm"><option value="text">Text</option><option value="email">Email</option><option value="textarea">Textarea</option></select></div><div class="col-md-2 text-center"><div class="form-check form-switch d-inline-flex gap-2"><input class="form-check-input" type="checkbox" name="fields[${fieldIndex}][required]" value="1"><label class="form-check-label small">Req</label></div></div><div class="col-md-2 text-end"><button type="button" class="btn btn-sm btn-outline-danger remove-field"><i class="bi bi-trash"></i></button></div>`;
    container.appendChild(div);
    fieldIndex++;
});
document.addEventListener('click', function(e) { if(e.target.closest('.remove-field')) e.target.closest('.field-row').remove(); });
</script>
@endpush
