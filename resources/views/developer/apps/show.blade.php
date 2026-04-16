@extends('layouts.app')
@section('title', $app->name . ' — Developer App')
@section('page-title', $app->name)

@push('styles')
<style>
.secret-field { font-family:monospace;font-size:13px;background:#1e293b;color:#94a3b8;border:1px solid #334155;border-radius:8px;padding:10px 14px;word-break:break-all; }
.copy-btn { cursor:pointer;color:#64748b;transition:color .15s; }
.copy-btn:hover { color:#0d6efd; }
.key-row { display:flex;align-items:center;gap:10px;margin-bottom:12px; }
.key-label { font-size:11px;text-transform:uppercase;font-weight:700;letter-spacing:1px;color:#94a3b8;width:90px;flex-shrink:0; }
</style>
@endpush

@section('content')
{{-- New secret banner --}}
@if(session('new_secret'))
<div class="alert alert-warning border-warning d-flex align-items-start gap-3 mb-4" role="alert">
    <i class="bi bi-shield-exclamation" style="font-size:24px;flex-shrink:0;"></i>
    <div class="flex-fill">
        <div class="fw-700 mb-1">🔐 Save your secret key — shown only once!</div>
        <div class="secret-field d-flex align-items-center justify-content-between">
            <span id="newSecretVal">{{ session('new_secret') }}</span>
            <i class="bi bi-clipboard copy-btn ms-3 fs-5" onclick="copyText('newSecretVal', this)" title="Copy"></i>
        </div>
    </div>
</div>
@endif

<div class="row g-4">
    {{-- Left column: Credentials + settings --}}
    <div class="col-lg-7">

        {{-- Credentials card --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                <div class="fw-600"><i class="bi bi-key-fill text-warning me-2"></i>API Credentials</div>
                {!! $app->status_badge !!}
            </div>
            <div class="card-body p-4" style="background:#0f172a;border-radius:0 0 12px 12px;">
                <div class="key-row">
                    <span class="key-label">Client ID</span>
                    <div class="secret-field flex-fill d-flex align-items-center justify-content-between" style="font-size:12px;">
                        <span id="clientIdVal">{{ $app->client_id }}</span>
                        <i class="bi bi-clipboard copy-btn ms-2" onclick="copyText('clientIdVal', this)" title="Copy"></i>
                    </div>
                </div>
                <div class="key-row">
                    <span class="key-label">API Secret</span>
                    <div class="secret-field flex-fill d-flex align-items-center justify-content-between" style="font-size:12px;">
                        <span id="secretVal">{{ $app->masked_secret }}</span>
                        <div class="d-flex gap-2 ms-2 flex-shrink-0">
                            <form method="POST" action="{{ route('developer.apps.regenerate', $app) }}" onsubmit="return confirm('Regenerate secret? The old secret will stop working immediately.')">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-outline-warning border-0 py-0" title="Regenerate"><i class="bi bi-arrow-clockwise"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="mt-3 p-3 rounded-3" style="background:rgba(255,255,255,.04);font-size:12px;color:#64748b;">
                    <div class="fw-600 mb-2" style="color:#94a3b8;">Authentication header:</div>
                    <code style="color:#7dd3fc;">Authorization: Bearer {{ substr($app->client_id, 0, 16) }}…</code>
                </div>
            </div>
        </div>

        {{-- Edit app settings --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-600 py-3"><i class="bi bi-gear me-2 text-secondary"></i>Application Settings</div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('developer.apps.update', $app) }}">
                    @csrf @method('PATCH')
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-600">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $app->name) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-600">Status</label>
                            <select name="is_active" class="form-select">
                                <option value="1" {{ $app->is_active ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !$app->is_active ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-600">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $app->description) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Rate Limit (req/hr)</label>
                            <input type="number" name="rate_limit" class="form-control" value="{{ old('rate_limit', $app->rate_limit) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-600">Webhook URL</label>
                            <input type="url" name="webhook_url" class="form-control" value="{{ old('webhook_url', $app->webhook_url) }}" placeholder="https://…">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-600">Webhook Events</label>
                            <div class="row g-2">
                                @foreach($events as $slug => $label)
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="webhook_events[]" value="{{ $slug }}" id="uev_{{ $slug }}"
                                            {{ in_array($slug, $app->webhook_events ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="uev_{{ $slug }}">{{ $label }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-600">IP Whitelist</label>
                            <textarea name="allowed_ips" rows="2" class="form-control font-monospace" placeholder="One IP per line">{{ old('allowed_ips', implode("\n", $app->allowed_ips ?? [])) }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Save Changes</button>
                        @if($app->webhook_url)
                        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#testWebhookModal">
                            <i class="bi bi-broadcast me-1"></i>Test Webhook
                        </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Danger zone --}}
        <div class="card border-danger border-opacity-25 shadow-sm">
            <div class="card-header bg-danger-subtle fw-600 py-3 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Danger Zone</div>
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <div class="fw-600">Delete Application</div>
                    <div class="text-muted small">Permanently delete this app and all its logs. This cannot be undone.</div>
                </div>
                <form method="POST" action="{{ route('developer.apps.destroy', $app) }}"
                      onsubmit="return confirm('Delete {{ $app->name }}? All logs and webhook deliveries will be permanently removed.')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm"><i class="bi bi-trash me-1"></i>Delete App</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Right column: Stats + recent logs --}}
    <div class="col-lg-5">

        {{-- Stats --}}
        <div class="row g-3 mb-4">
            @php $statItems = [['Total Requests','primary','total','bi-lightning-charge'],['Today','info','today','bi-calendar-day'],['Errors','danger','errors','bi-x-octagon'],['Webhooks Sent','success','wh_sent','bi-broadcast']]; @endphp
            @foreach($statItems as $s)
            <div class="col-6">
                <div class="card border-0 shadow-sm text-center p-3">
                    <i class="bi {{ $s[3] }} text-{{ $s[1] }} mb-1" style="font-size:20px;"></i>
                    <div class="fw-700 fs-5">{{ number_format($stats[$s[2]]) }}</div>
                    <div class="text-muted" style="font-size:11px;">{{ $s[0] }}</div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Recent logs --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-600 py-3"><i class="bi bi-journal-code me-2 text-secondary"></i>Recent Requests</div>
            <div class="table-responsive" style="max-height:280px;overflow-y:auto;">
                <table class="table table-sm table-hover align-middle mb-0">
                    <tbody>
                        @forelse($recentLogs as $log)
                        <tr>
                            <td><span class="badge bg-{{ $log->method_color }}-subtle text-{{ $log->method_color }}" style="font-size:10px;font-family:monospace;">{{ $log->method }}</span></td>
                            <td class="text-truncate" style="max-width:160px;font-size:11px;font-family:monospace;">{{ $log->endpoint }}</td>
                            <td><span class="badge bg-{{ $log->status_color }}-subtle text-{{ $log->status_color }}" style="font-size:10px;">{{ $log->status_code }}</span></td>
                            <td class="text-muted" style="font-size:10px;">{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3 small">No requests yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent webhook deliveries --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-600 py-3"><i class="bi bi-broadcast me-2 text-info"></i>Recent Webhook Deliveries</div>
            <div class="card-body p-0">
                @forelse($recentWebhooks as $wh)
                <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                    <div>
                        <div style="font-size:11px;font-family:monospace;font-weight:600;">{{ $wh->event }}</div>
                        <div class="text-muted" style="font-size:10px;">{{ $wh->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        @if($wh->response_code)<span class="text-muted" style="font-size:10px;">{{ $wh->response_code }}</span>@endif
                        {!! $wh->status_badge !!}
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-3 small">No webhook deliveries yet</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Test Webhook Modal --}}
<div class="modal fade" id="testWebhookModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-700"><i class="bi bi-broadcast text-info me-2"></i>Send Test Webhook</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('developer.apps.test-webhook', $app) }}">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">Select an event to send a test payload to <strong>{{ $app->webhook_url }}</strong></p>
                    <select name="event" class="form-select" required>
                        @foreach($events as $slug => $label)
                        <option value="{{ $slug }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white"><i class="bi bi-send me-1"></i>Send Test</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyText(id, btn) {
    const text = document.getElementById(id).innerText;
    navigator.clipboard.writeText(text).then(() => {
        btn.classList.replace('bi-clipboard', 'bi-clipboard-check');
        btn.style.color = '#22c55e';
        setTimeout(() => { btn.classList.replace('bi-clipboard-check', 'bi-clipboard'); btn.style.color = ''; }, 2000);
    });
}
</script>
@endpush
