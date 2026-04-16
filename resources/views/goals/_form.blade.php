<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-600">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $goal->title ?? '') }}" required>
    </div>
    <div class="col-12">
        <label class="form-label fw-600">Description</label>
        <textarea name="description" rows="2" class="form-control">{{ old('description', $goal->description ?? '') }}</textarea>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-600">Goal Type <span class="text-danger">*</span></label>
        <select name="type" class="form-select">
            @foreach(['revenue','deals_won','leads_created','contacts_added','calls_made','demos_scheduled'] as $t)
            <option value="{{ $t }}" {{ old('type', $goal->type ?? 'revenue') === $t ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$t)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-600">Period</label>
        <select name="period" class="form-select">
            @foreach(['monthly','quarterly','yearly','custom'] as $p)
            <option value="{{ $p }}" {{ old('period', $goal->period ?? 'monthly') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-600">Status</label>
        <select name="status" class="form-select">
            @foreach(['active','completed','failed','paused'] as $s)
            <option value="{{ $s }}" {{ old('status', $goal->status ?? 'active') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-600">Target Value <span class="text-danger">*</span></label>
        <input type="number" name="target_value" class="form-control" value="{{ old('target_value', $goal->target_value ?? 0) }}" min="0" step="0.01" required>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-600">Assigned To (optional)</label>
        <select name="user_id" class="form-select">
            <option value="">— Whole Team —</option>
            @foreach($users as $u)
            <option value="{{ $u->id }}" {{ old('user_id', $goal->user_id ?? '') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-600">Start Date <span class="text-danger">*</span></label>
        <input type="date" name="start_date" class="form-control" value="{{ old('start_date', isset($goal) ? $goal->start_date->format('Y-m-d') : now()->startOfMonth()->format('Y-m-d')) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-600">End Date <span class="text-danger">*</span></label>
        <input type="date" name="end_date" class="form-control" value="{{ old('end_date', isset($goal) ? $goal->end_date->format('Y-m-d') : now()->endOfMonth()->format('Y-m-d')) }}" required>
    </div>
</div>
