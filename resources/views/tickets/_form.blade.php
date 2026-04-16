<div class="row g-3">
    @if(!isset($ticket))
    <div class="col-md-6">
        <label class="form-label fw-600">Ticket Number</label>
        <input type="text" name="ticket_number" class="form-control" value="{{ old('ticket_number', $nextNum ?? '') }}" required>
    </div>
    @endif
    <div class="col-md-6">
        <label class="form-label fw-600">Channel</label>
        <select name="channel" class="form-select">
            @foreach(['email','phone','chat','web','other'] as $ch)
            <option value="{{ $ch }}" {{ old('channel', $ticket->channel ?? 'email') === $ch ? 'selected' : '' }}>{{ ucfirst($ch) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label fw-600">Subject <span class="text-danger">*</span></label>
        <input type="text" name="subject" class="form-control" value="{{ old('subject', $ticket->subject ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-600">Priority</label>
        <select name="priority" class="form-select">
            @foreach(['low','medium','high','urgent'] as $p)
            <option value="{{ $p }}" {{ old('priority', $ticket->priority ?? 'medium') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-600">Status</label>
        <select name="status" class="form-select">
            @foreach(['open','pending','in_progress','resolved','closed'] as $s)
            <option value="{{ $s }}" {{ old('status', $ticket->status ?? 'open') === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-600">Category</label>
        <input type="text" name="category" class="form-control" value="{{ old('category', $ticket->category ?? '') }}" placeholder="e.g. Billing, Technical">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-600">Contact</label>
        <select name="contact_id" class="form-select">
            <option value="">— Select Contact —</option>
            @foreach($contacts as $c)
            <option value="{{ $c->id }}" {{ old('contact_id', $ticket->contact_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->full_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-600">Assigned To</label>
        <select name="assigned_to" class="form-select">
            <option value="">— Unassigned —</option>
            @foreach($agents as $agent)
            <option value="{{ $agent->id }}" {{ old('assigned_to', $ticket->assigned_to ?? '') == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label fw-600">Description <span class="text-danger">*</span></label>
        <textarea name="description" rows="6" class="form-control" required>{{ old('description', $ticket->description ?? '') }}</textarea>
    </div>
</div>
