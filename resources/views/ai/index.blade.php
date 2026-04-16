@extends('layouts.app')
@section('title', 'AI Assistant')
@section('page-title', 'AI Assistant')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-robot me-2 text-primary"></i>AI Assistant</h1>
        <p class="text-muted mb-0">Intelligent insights and tools to supercharge your sales</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <a href="{{ route('ai.insights') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm ai-tool-card" style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                <div class="card-body text-white p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-3 bg-white bg-opacity-25 p-3 me-3"><i class="bi bi-graph-up-arrow fs-4"></i></div>
                        <div>
                            <div class="fw-700 fs-5">Pipeline Intelligence</div>
                            <div style="opacity:.85;font-size:13px;">Real-time CRM health analysis</div>
                        </div>
                    </div>
                    <p style="opacity:.9;font-size:14px;" class="mb-3">AI-powered analysis of your pipeline, highlighting risks, opportunities, and recommended actions.</p>
                    <span class="badge bg-white text-primary fw-600">View Insights <i class="bi bi-arrow-right ms-1"></i></span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('ai.email') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm ai-tool-card" style="background:linear-gradient(135deg,#0ea5e9,#0284c7)">
                <div class="card-body text-white p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-3 bg-white bg-opacity-25 p-3 me-3"><i class="bi bi-envelope-paper fs-4"></i></div>
                        <div>
                            <div class="fw-700 fs-5">Email Composer</div>
                            <div style="opacity:.85;font-size:13px;">Context-aware email drafts</div>
                        </div>
                    </div>
                    <p style="opacity:.9;font-size:14px;" class="mb-3">Generate professional, personalized emails for any contact, lead, or deal in seconds.</p>
                    <span class="badge bg-white text-info fw-600">Compose Email <i class="bi bi-arrow-right ms-1"></i></span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm" style="background:linear-gradient(135deg,#10b981,#059669)">
            <div class="card-body text-white p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-3 bg-white bg-opacity-25 p-3 me-3"><i class="bi bi-shield-check fs-4"></i></div>
                    <div>
                        <div class="fw-700 fs-5">Lead Scoring</div>
                        <div style="opacity:.85;font-size:13px;">Prioritize your hottest leads</div>
                    </div>
                </div>
                <p style="opacity:.9;font-size:14px;" class="mb-3">AI scores each lead based on engagement, budget, source, and activity. Focus on what matters.</p>
                <a href="{{ route('leads.index') }}" class="badge bg-white text-success fw-600 text-decoration-none">View Leads <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
</div>

@if(count($insights))
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent pt-4 px-4">
        <h5 class="fw-700 mb-0"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Live AI Insights</h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            @foreach($insights as $insight)
            <div class="col-md-6">
                <div class="alert alert-{{ $insight['type'] }} border-0 mb-0 d-flex gap-3 align-items-start">
                    <i class="bi {{ $insight['icon'] }} fs-5 mt-1 flex-shrink-0"></i>
                    <div>
                        <div class="fw-600 mb-1">{{ $insight['title'] }}</div>
                        <div style="font-size:13px;">{{ $insight['text'] }}</div>
                        <a href="{{ $insight['action'] }}" class="btn btn-sm btn-{{ $insight['type'] }} mt-2">{{ $insight['action_text'] }}</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent pt-4 px-4">
                <h5 class="fw-700 mb-0"><i class="bi bi-star-fill text-warning me-2"></i>AI Lead Scoring</h5>
            </div>
            <div class="card-body p-0">
                @php $leads = \App\Models\Lead::where('tenant_id', auth()->user()->tenant_id)->with(['contact','activities'])->latest()->take(8)->get(); @endphp
                @foreach($leads as $lead)
                @php
                    $activities = $lead->activities->count();
                    $base = match($lead->source ?? '') { 'referral'=>30,'website'=>20,'linkedin'=>18,default=>10 };
                    $score = min($base + min($activities*5,20) + (($lead->budget??0)>=10000?15:5), 100);
                @endphp
                <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                    <div style="width:42px;text-align:center;flex-shrink:0;">
                        <div class="fw-700 fs-5 {{ $score>=70?'text-success':($score>=40?'text-warning':'text-danger') }}">{{ $score }}</div>
                        <div style="font-size:10px;" class="text-muted">score</div>
                    </div>
                    <div class="flex-1">
                        <div class="fw-600" style="font-size:13px;">{{ $lead->title }}</div>
                        <div class="text-muted" style="font-size:11px;">{{ $lead->contact?->first_name }} {{ $lead->contact?->last_name }}</div>
                        <div class="progress mt-1" style="height:4px;width:120px;">
                            <div class="progress-bar bg-{{ $score>=70?'success':($score>=40?'warning':'danger') }}" style="width:{{ $score }}%"></div>
                        </div>
                    </div>
                    <a href="{{ route('ai.lead-score', $lead) }}" class="btn btn-sm btn-outline-primary">Analyse</a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent pt-4 px-4">
                <h5 class="fw-700 mb-0"><i class="bi bi-trophy text-primary me-2"></i>AI Deal Insights</h5>
            </div>
            <div class="card-body p-0">
                @php $deals = \App\Models\Deal::where('tenant_id', auth()->user()->tenant_id)->with(['contact','stage','activities'])->where('status','open')->latest()->take(8)->get(); @endphp
                @foreach($deals as $deal)
                @php
                    $pos = $deal->stage?->position ?? 0;
                    $prob = min(10 + $pos*12 + min($deal->activities->count()*5,15), 95);
                @endphp
                <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                    <div style="width:42px;text-align:center;flex-shrink:0;">
                        <div class="fw-700 fs-5 {{ $prob>=70?'text-success':($prob>=40?'text-warning':'text-danger') }}">{{ $prob }}%</div>
                        <div style="font-size:10px;" class="text-muted">win</div>
                    </div>
                    <div class="flex-1">
                        <div class="fw-600" style="font-size:13px;">{{ $deal->title }}</div>
                        <div class="text-muted" style="font-size:11px;">${{ number_format($deal->value) }} · {{ $deal->stage?->name }}</div>
                        <div class="progress mt-1" style="height:4px;width:120px;">
                            <div class="progress-bar bg-{{ $prob>=70?'success':($prob>=40?'warning':'danger') }}" style="width:{{ $prob }}%"></div>
                        </div>
                    </div>
                    <a href="{{ route('ai.deal-insight', $deal) }}" class="btn btn-sm btn-outline-primary">Analyse</a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
.ai-tool-card { transition:transform .2s,box-shadow .2s; cursor:pointer; }
.ai-tool-card:hover { transform:translateY(-4px); box-shadow:0 12px 30px rgba(0,0,0,.15)!important; }
</style>
@endsection
