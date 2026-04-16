@extends('layouts.app')
@section('title', 'AI Lead Score')
@section('page-title', 'AI Lead Score')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-star-fill text-warning me-2"></i>AI Lead Analysis</h1>
        <p class="text-muted mb-0">{{ $lead->title }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('leads.show', $lead) }}" class="btn btn-outline-secondary"><i class="bi bi-eye me-1"></i>View Lead</a>
        <a href="{{ route('ai.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>AI Tools</a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center p-4">
            <div class="mb-3">
                <div style="width:140px;height:140px;margin:0 auto;position:relative;">
                    <svg viewBox="0 0 36 36" style="width:140px;height:140px;transform:rotate(-90deg)">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e5e7eb" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15.9" fill="none"
                            stroke="{{ $score>=70?'#10b981':($score>=40?'#f59e0b':'#ef4444') }}"
                            stroke-width="3"
                            stroke-dasharray="{{ round($score * 100 / 100, 1) }} 100"
                            stroke-linecap="round"/>
                    </svg>
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;">
                        <div class="fw-700" style="font-size:2rem;color:{{ $score>=70?'#10b981':($score>=40?'#f59e0b':'#ef4444') }}">{{ $score }}</div>
                        <div class="text-muted" style="font-size:11px;">/ 100</div>
                    </div>
                </div>
            </div>
            <h5 class="fw-700">Lead Score</h5>
            <span class="badge bg-{{ $score>=70?'success':($score>=40?'warning':'danger') }}-subtle text-{{ $score>=70?'success':($score>=40?'warning':'danger') }} fs-6 px-3 py-2">
                {{ $score>=70?'Hot Lead':($score>=40?'Warm Lead':'Cold Lead') }}
            </span>
            <hr>
            <div class="text-start">
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Source</span><span class="fw-600">{{ ucfirst($lead->source ?? 'Unknown') }}</span></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Budget</span><span class="fw-600">{{ $lead->budget ? '$'.number_format($lead->budget) : 'Unknown' }}</span></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Activities</span><span class="fw-600">{{ $lead->activities->count() }}</span></div>
                <div class="d-flex justify-content-between"><span class="text-muted">Stage</span><span class="fw-600">{{ $lead->stage?->name ?? 'Unassigned' }}</span></div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-4 px-4">
                <h5 class="fw-700 mb-0"><i class="bi bi-lightbulb text-warning me-2"></i>Score Reasoning</h5>
            </div>
            <div class="card-body p-4">
                @foreach($reasoning as $r)
                <div class="d-flex gap-3 align-items-start mb-3">
                    <div class="rounded-3 bg-{{ $r['color'] }}-subtle p-2 flex-shrink-0">
                        <i class="bi {{ $r['icon'] }} text-{{ $r['color'] }}"></i>
                    </div>
                    <p class="mb-0 pt-1" style="font-size:14px;">{{ $r['text'] }}</p>
                </div>
                @endforeach
                @if(empty($reasoning))
                <p class="text-muted mb-0">Insufficient data for detailed reasoning. Add more activity to improve insights.</p>
                @endif
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent pt-4 px-4">
                <h5 class="fw-700 mb-0"><i class="bi bi-list-task text-primary me-2"></i>Recommended Next Actions</h5>
            </div>
            <div class="card-body p-4">
                @foreach($actions as $action)
                <div class="d-flex gap-3 align-items-center mb-3">
                    <span class="badge bg-{{ $action['priority']==='high'?'danger':($action['priority']==='medium'?'warning':'secondary') }}-subtle text-{{ $action['priority']==='high'?'danger':($action['priority']==='medium'?'warning':'secondary') }}" style="font-size:10px;width:55px;text-align:center;">{{ strtoupper($action['priority']) }}</span>
                    <i class="bi {{ $action['icon'] }} text-muted"></i>
                    <span style="font-size:14px;">{{ $action['text'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent pt-4 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-700 mb-0"><i class="bi bi-envelope-paper text-info me-2"></i>AI-Generated Follow-Up Email</h5>
        <button class="btn btn-sm btn-primary" onclick="copyEmail()"><i class="bi bi-clipboard me-1"></i>Copy</button>
    </div>
    <div class="card-body p-4">
        <div class="mb-3">
            <label class="form-label fw-600">Subject</label>
            <input type="text" class="form-control" id="emailSubject" value="{{ $emailDraft['subject'] }}" readonly>
        </div>
        <div>
            <label class="form-label fw-600">Body</label>
            <textarea class="form-control font-monospace" id="emailBody" rows="12" readonly>{{ $emailDraft['body'] }}</textarea>
        </div>
        <div class="mt-3 d-flex gap-2">
            @if($lead->contact?->email)
            <a href="mailto:{{ $lead->contact->email }}?subject={{ urlencode($emailDraft['subject']) }}&body={{ urlencode($emailDraft['body']) }}" class="btn btn-success"><i class="bi bi-send me-1"></i>Open in Mail</a>
            @endif
            <a href="{{ route('ai.email', ['lead_id'=>$lead->id]) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Customize</a>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function copyEmail() {
    const subject = document.getElementById('emailSubject').value;
    const body = document.getElementById('emailBody').value;
    navigator.clipboard.writeText('Subject: ' + subject + '\n\n' + body);
    const btn = event.target.closest('button');
    btn.innerHTML = '<i class="bi bi-check me-1"></i>Copied!';
    setTimeout(() => btn.innerHTML = '<i class="bi bi-clipboard me-1"></i>Copy', 2000);
}
</script>
@endpush
