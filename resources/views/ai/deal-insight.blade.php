@extends('layouts.app')
@section('title', 'AI Deal Insight')
@section('page-title', 'AI Deal Insight')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-trophy text-primary me-2"></i>AI Deal Analysis</h1>
        <p class="text-muted mb-0">{{ $deal->title }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('deals.show', $deal) }}" class="btn btn-outline-secondary"><i class="bi bi-eye me-1"></i>View Deal</a>
        <a href="{{ route('ai.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>AI Tools</a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center p-4 mb-4">
            <div style="width:140px;height:140px;margin:0 auto 16px;position:relative;">
                <svg viewBox="0 0 36 36" style="width:140px;height:140px;transform:rotate(-90deg)">
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e5e7eb" stroke-width="3"/>
                    <circle cx="18" cy="18" r="15.9" fill="none"
                        stroke="{{ $probability>=70?'#10b981':($probability>=40?'#f59e0b':'#ef4444') }}"
                        stroke-width="3"
                        stroke-dasharray="{{ $probability }} 100"
                        stroke-linecap="round"/>
                </svg>
                <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;">
                    <div class="fw-700" style="font-size:2rem;color:{{ $probability>=70?'#10b981':($probability>=40?'#f59e0b':'#ef4444') }}">{{ $probability }}%</div>
                    <div class="text-muted" style="font-size:11px;">win rate</div>
                </div>
            </div>
            <h5 class="fw-700">Win Probability</h5>
            <span class="badge bg-{{ $probability>=70?'success':($probability>=40?'warning':'danger') }}-subtle text-{{ $probability>=70?'success':($probability>=40?'warning':'danger') }} fs-6 px-3 py-2">
                {{ $probability>=70?'High Confidence':($probability>=40?'Moderate Chance':'Needs Attention') }}
            </span>
            <hr>
            <div class="text-start">
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Value</span><span class="fw-600 text-success">${{ number_format($deal->value) }}</span></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Stage</span><span class="fw-600">{{ $deal->stage?->name }}</span></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Activities</span><span class="fw-600">{{ $deal->activities->count() }}</span></div>
                <div class="d-flex justify-content-between"><span class="text-muted">Close Date</span>
                    <span class="fw-600 {{ $deal->expected_close_date && now()->isAfter($deal->expected_close_date)?'text-danger':'' }}">
                        {{ $deal->expected_close_date?->format('M d, Y') ?? 'Not set' }}
                    </span>
                </div>
            </div>
        </div>
        @if(count($risks))
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent pt-3 px-4">
                <h6 class="fw-700 mb-0"><i class="bi bi-shield-exclamation text-warning me-2"></i>Risk Signals</h6>
            </div>
            <div class="card-body p-3">
                @foreach($risks as $risk)
                <div class="d-flex gap-2 align-items-start mb-2">
                    <span class="badge bg-{{ $risk['level']==='high'?'danger':($risk['level']==='medium'?'warning':'success') }}-subtle text-{{ $risk['level']==='high'?'danger':($risk['level']==='medium'?'warning':'success') }}" style="font-size:10px;white-space:nowrap;">{{ strtoupper($risk['level']) }}</span>
                    <span style="font-size:13px;">{{ $risk['text'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-4 px-4">
                <h5 class="fw-700 mb-0"><i class="bi bi-lightbulb text-warning me-2"></i>AI Reasoning</h5>
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
            </div>
        </div>
        <div class="card border-0 shadow-sm mb-4">
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
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-700 mb-0"><i class="bi bi-envelope-paper text-info me-2"></i>AI-Generated Email</h5>
                <button class="btn btn-sm btn-primary" onclick="copyEmail()"><i class="bi bi-clipboard me-1"></i>Copy</button>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-600">Subject</label>
                    <input type="text" class="form-control" id="emailSubject" value="{{ $emailDraft['subject'] }}" readonly>
                </div>
                <textarea class="form-control font-monospace" id="emailBody" rows="10" readonly>{{ $emailDraft['body'] }}</textarea>
                <div class="mt-3 d-flex gap-2">
                    @if($deal->contact?->email)
                    <a href="mailto:{{ $deal->contact->email }}?subject={{ urlencode($emailDraft['subject']) }}&body={{ urlencode($emailDraft['body']) }}" class="btn btn-success"><i class="bi bi-send me-1"></i>Open in Mail</a>
                    @endif
                    <a href="{{ route('ai.email', ['deal_id'=>$deal->id]) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Customize</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function copyEmail() {
    navigator.clipboard.writeText('Subject: '+document.getElementById('emailSubject').value+'\n\n'+document.getElementById('emailBody').value);
    const btn=event.target.closest('button');btn.innerHTML='<i class="bi bi-check me-1"></i>Copied!';
    setTimeout(()=>btn.innerHTML='<i class="bi bi-clipboard me-1"></i>Copy',2000);
}
</script>
@endpush
