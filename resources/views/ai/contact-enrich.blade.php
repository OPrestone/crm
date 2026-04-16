@extends('layouts.app')
@section('title', 'Contact Intelligence')
@section('page-title', 'Contact Intelligence')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-person-badge text-primary me-2"></i>Contact Intelligence</h1>
        <p class="text-muted mb-0">{{ $contact->first_name }} {{ $contact->last_name }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('contacts.show', $contact) }}" class="btn btn-outline-secondary"><i class="bi bi-eye me-1"></i>View Contact</a>
        <a href="{{ route('ai.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>AI Tools</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center p-4 mb-4">
            <div class="avatar-circle mx-auto mb-3" style="width:72px;height:72px;font-size:28px;">{{ strtoupper(substr($contact->first_name,0,1)) }}</div>
            <h5 class="fw-700">{{ $contact->first_name }} {{ $contact->last_name }}</h5>
            <p class="text-muted mb-3">{{ $contact->title }} {{ $contact->company?->name ? '@ '.$contact->company->name : '' }}</p>
            <div class="mb-3">
                <span class="badge bg-primary-subtle text-primary fs-6 px-3 py-2">{{ $profile['persona'] }}</span>
            </div>
            <div style="width:120px;margin:0 auto;">
                <div class="d-flex justify-content-between mb-1"><span style="font-size:12px;" class="text-muted">Engagement</span><span class="fw-700 {{ $score>=70?'text-success':($score>=40?'text-warning':'text-danger') }}">{{ $score }}%</span></div>
                <div class="progress" style="height:6px;"><div class="progress-bar bg-{{ $score>=70?'success':($score>=40?'warning':'danger') }}" style="width:{{ $score }}%"></div></div>
            </div>
            <hr>
            <div class="text-start">
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Open Deals</span><span class="fw-600">{{ $profile['open_deals'] }}</span></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Won Deals</span><span class="fw-600 text-success">{{ $profile['won_deals'] }}</span></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Total Value</span><span class="fw-600">${{ number_format($profile['total_value']) }}</span></div>
                <div class="d-flex justify-content-between"><span class="text-muted">Activities</span><span class="fw-600">{{ $profile['activity_count'] }}</span></div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent pt-3 px-4">
                <h6 class="fw-700 mb-0"><i class="bi bi-check2-circle text-success me-2"></i>Recommendations</h6>
            </div>
            <div class="card-body p-3">
                @foreach($profile['recommendations'] as $rec)
                <div class="d-flex gap-2 align-items-start mb-2">
                    <i class="bi bi-arrow-right-circle text-primary mt-1 flex-shrink-0"></i>
                    <span style="font-size:13px;">{{ $rec }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-700 mb-0"><i class="bi bi-envelope-paper text-info me-2"></i>AI-Generated Email</h5>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" onchange="changeType(this.value)" style="width:160px;">
                        <option value="follow_up">Follow-up</option>
                        <option value="introduction">Introduction</option>
                        <option value="proposal">Proposal</option>
                    </select>
                    <button class="btn btn-sm btn-primary" onclick="copyEmail()"><i class="bi bi-clipboard me-1"></i>Copy</button>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-600">Subject</label>
                    <input type="text" class="form-control" id="emailSubject" value="{{ $emailDraft['subject'] }}">
                </div>
                <textarea class="form-control font-monospace" id="emailBody" rows="15">{{ $emailDraft['body'] }}</textarea>
                <div class="mt-3 d-flex gap-2">
                    @if($contact->email)
                    <a href="mailto:{{ $contact->email }}?subject={{ urlencode($emailDraft['subject']) }}&body={{ urlencode($emailDraft['body']) }}" class="btn btn-success"><i class="bi bi-send me-1"></i>Open in Mail</a>
                    @endif
                    <a href="{{ route('ai.email', ['contact_id'=>$contact->id]) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Full Composer</a>
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
function changeType(type) {
    window.location.href='{{ route('ai.email') }}?contact_id={{ $contact->id }}&type='+type;
}
</script>
@endpush
