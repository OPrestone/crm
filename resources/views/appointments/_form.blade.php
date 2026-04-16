<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-600">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $appointment->title ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-600">Type</label>
        <select name="type" class="form-select">
            @foreach(['call','meeting','demo','follow_up','other'] as $t)
            <option value="{{ $t }}" {{ old('type', $appointment->type ?? 'meeting') === $t ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$t)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-600">Status</label>
        <select name="status" class="form-select">
            @foreach(['scheduled','completed','cancelled','no_show'] as $s)
            <option value="{{ $s }}" {{ old('status', $appointment->status ?? 'scheduled') === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-600">Start <span class="text-danger">*</span></label>
        <input type="datetime-local" name="start_at" class="form-control" value="{{ old('start_at', isset($appointment) ? $appointment->start_at->format('Y-m-d\TH:i') : now()->addHour()->format('Y-m-d\TH:i')) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-600">End <span class="text-danger">*</span></label>
        <input type="datetime-local" name="end_at" class="form-control" value="{{ old('end_at', isset($appointment) ? $appointment->end_at->format('Y-m-d\TH:i') : now()->addHours(2)->format('Y-m-d\TH:i')) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-600">Contact</label>
        <select name="contact_id" class="form-select">
            <option value="">— Select Contact —</option>
            @foreach($contacts as $c)
            <option value="{{ $c->id }}" {{ old('contact_id', $appointment->contact_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->full_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-600">Location</label>
        <input type="text" name="location" class="form-control" value="{{ old('location', $appointment->location ?? '') }}" placeholder="e.g. Zoom, Office, Phone">
    </div>
    <div class="col-md-10">
        <label class="form-label fw-600">Description</label>
        <textarea name="description" rows="3" class="form-control">{{ old('description', $appointment->description ?? '') }}</textarea>
    </div>
    <div class="col-md-2">
        <label class="form-label fw-600">Color</label>
        <input type="color" name="color" class="form-control form-control-color w-100" value="{{ old('color', $appointment->color ?? '#0d6efd') }}">
    </div>
</div>
