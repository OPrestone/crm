@extends('layouts.app')
@section('title', 'AI Email Composer')
@section('page-title', 'AI Email Composer')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-envelope-paper text-info me-2"></i>AI Email Composer</h1>
        <p class="text-muted mb-0">Generate professional, context-aware emails in seconds</p>
    </div>
    <a href="{{ route('ai.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>AI Tools</a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent pt-4 px-4">
                <h5 class="fw-700 mb-0">Configuration</h5>
            </div>
            <div class="card-body p-4">
                <form method="GET" action="{{ route('ai.email') }}" id="configForm">
                    <div class="mb-3">
                        <label class="form-label fw-600">Email Type</label>
                        <select name="type" class="form-select" onchange="document.getElementById('configForm').submit()">
                            <option value="follow_up" {{ $type==='follow_up'?'selected':'' }}>Follow-up</option>
                            <option value="introduction" {{ $type==='introduction'?'selected':'' }}>Introduction</option>
                            <option value="proposal" {{ $type==='proposal'?'selected':'' }}>Proposal</option>
                            <option value="closing" {{ $type==='closing'?'selected':'' }}>Closing / Final Push</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">For a Lead</label>
                        <select name="lead_id" class="form-select" onchange="document.getElementById('configForm').submit()">
                            <option value="">— Select Lead —</option>
                            @foreach($leads as $lead)
                            <option value="{{ $lead->id }}" {{ (request('lead_id')==$lead->id)?'selected':'' }}>{{ $lead->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-600">For a Deal</label>
                        <select name="deal_id" class="form-select" onchange="document.getElementById('configForm').submit()">
                            <option value="">— Select Deal —</option>
                            @foreach($deals as $deal)
                            <option value="{{ $deal->id }}" {{ (request('deal_id')==$deal->id)?'selected':'' }}>{{ $deal->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-600">For a Contact</label>
                        <select name="contact_id" class="form-select" onchange="document.getElementById('configForm').submit()">
                            <option value="">— Select Contact —</option>
                            @foreach($contacts as $contact)
                            <option value="{{ $contact->id }}" {{ (request('contact_id')==$contact->id)?'selected':'' }}>{{ $contact->first_name }} {{ $contact->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
                <div class="alert alert-info border-0 p-3" style="font-size:13px;">
                    <i class="bi bi-info-circle me-2"></i>Select a context above to generate a personalised email draft.
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        @if(!empty($context))
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-700 mb-0"><i class="bi bi-magic text-primary me-2"></i>Generated Email</h5>
                <button class="btn btn-sm btn-primary" onclick="copyEmail()"><i class="bi bi-clipboard me-1"></i>Copy All</button>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-600">Subject Line</label>
                    <input type="text" class="form-control form-control-lg" id="emailSubject" value="{{ $context['subject'] }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-600">Email Body</label>
                    <textarea class="form-control font-monospace" id="emailBody" rows="18" style="font-size:14px;">{{ $context['body'] }}</textarea>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @php
                        $recipientEmail = $context['lead']?->contact?->email ?? $context['deal']?->contact?->email ?? $context['contact']?->email ?? null;
                    @endphp
                    @if($recipientEmail)
                    <a href="mailto:{{ $recipientEmail }}?subject={{ urlencode($context['subject']) }}&body={{ urlencode($context['body']) }}" class="btn btn-success">
                        <i class="bi bi-send me-1"></i>Send via Mail Client
                    </a>
                    @endif
                    <button class="btn btn-outline-secondary" onclick="resetEmail()"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset Draft</button>
                </div>
            </div>
        </div>
        @else
        <div class="card border-0 shadow-sm h-100 d-flex align-items-center justify-content-center" style="min-height:400px;">
            <div class="text-center p-5">
                <div class="rounded-circle bg-primary-subtle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width:80px;height:80px;">
                    <i class="bi bi-envelope-paper text-primary fs-3"></i>
                </div>
                <h5 class="fw-700 mb-2">Select a Context</h5>
                <p class="text-muted">Choose a lead, deal, or contact on the left to generate a personalised email draft with AI.</p>
                <div class="row g-2 mt-3 text-start">
                    <div class="col-12"><div class="d-flex gap-2 align-items-center p-3 bg-light rounded-3"><i class="bi bi-star text-warning"></i><span style="font-size:13px;">Referral follow-ups convert 4x better when personalised</span></div></div>
                    <div class="col-12"><div class="d-flex gap-2 align-items-center p-3 bg-light rounded-3"><i class="bi bi-clock text-info"></i><span style="font-size:13px;">Best time to send: Tuesday–Thursday, 9–11am</span></div></div>
                    <div class="col-12"><div class="d-flex gap-2 align-items-center p-3 bg-light rounded-3"><i class="bi bi-check2 text-success"></i><span style="font-size:13px;">Subject lines under 50 chars get 22% higher open rates</span></div></div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
@push('scripts')
<script>
const originalSubject = `{{ addslashes($context['subject'] ?? '') }}`;
const originalBody = `{{ addslashes(str_replace(["\r\n","\n","\r"], "\\n", $context['body'] ?? '')) }}`;
function copyEmail() {
    navigator.clipboard.writeText('Subject: '+document.getElementById('emailSubject').value+'\n\n'+document.getElementById('emailBody').value);
    const btn=event.target.closest('button');btn.innerHTML='<i class="bi bi-check me-1"></i>Copied!';
    setTimeout(()=>btn.innerHTML='<i class="bi bi-clipboard me-1"></i>Copy All',2000);
}
function resetEmail() {
    document.getElementById('emailSubject').value = originalSubject;
    document.getElementById('emailBody').value = originalBody.replace(/\\n/g,'\n');
}
</script>
@endpush
