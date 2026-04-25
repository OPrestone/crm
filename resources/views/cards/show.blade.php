@extends('layouts.app')
@section('title', $card->name)
@section('page-title', 'Card Preview')
@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-credit-card me-2"></i>{{ $card->name }}</h1>
        <p class="text-muted mb-0">{{ $card->template?->name ?? 'Custom Card' }} · Created {{ $card->created_at->format('M d, Y') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('cards.pdf', $card) }}" target="_blank" class="btn btn-primary"><i class="bi bi-file-earmark-pdf me-1"></i>Export PDF</a>
        <a href="{{ route('cards.edit', $card) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('cards.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Card Preview</h5></div>
            <div class="card-body p-4 d-flex justify-content-center">
                @php
                    $design   = $card->template?->design ?? [];
                    $fields   = $card->data ?? [];
                    $bgColor  = $design['bg_color']               ?? '#1e3a5f';
                    $txtColor = $design['text_color']             ?? '#ffffff';
                    $accent   = $design['accent_color'] ?? $design['accent'] ?? '#f59e0b';
                    $divColor = $design['divider_color'] ?? $accent;
                @endphp
                <div style="width:380px;min-height:220px;border-radius:16px;background:{{ $bgColor }};color:{{ $txtColor }};padding:28px 28px 24px;position:relative;box-shadow:0 20px 60px rgba(0,0,0,.3);overflow:hidden;">
                    <div style="position:absolute;top:-30px;right:-30px;width:120px;height:120px;border-radius:50%;background:{{ $accent }};opacity:.2;"></div>
                    <div style="position:absolute;bottom:-40px;left:-20px;width:160px;height:160px;border-radius:50%;background:{{ $accent }};opacity:.1;"></div>
                    <div class="d-flex align-items-center gap-3 mb-3" style="position:relative;">
                        @if($card->photo)
                        <img src="{{ Storage::url($card->photo) }}" style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:3px solid {{ $accent }};flex-shrink:0;">
                        @else
                        <div style="width:64px;height:64px;border-radius:50%;background:{{ $accent }};display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;flex-shrink:0;color:#fff;">{{ strtoupper(substr($card->name,0,1)) }}</div>
                        @endif
                        <div>
                            <div style="font-size:20px;font-weight:700;line-height:1.2;">{{ $fields['name'] ?? $card->contact?->full_name ?? $card->name }}</div>
                            @if($fields['title'] ?? $card->contact?->job_title)<div style="font-size:13px;opacity:.8;">{{ $fields['title'] ?? $card->contact?->job_title }}</div>@endif
                            @if($fields['company'] ?? $card->contact?->company?->name)<div style="font-size:12px;color:{{ $accent }};font-weight:600;">{{ $fields['company'] ?? $card->contact?->company?->name }}</div>@endif
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;position:relative;margin-bottom:{{ $qrCode?'36px':'0' }};">
                        @if($fields['email'] ?? $card->contact?->email)
                        <div style="display:flex;align-items:center;gap:6px;font-size:12px;opacity:.9;"><i class="bi bi-envelope-fill" style="color:{{ $accent }};font-size:11px;flex-shrink:0;"></i>{{ $fields['email'] ?? $card->contact?->email }}</div>
                        @endif
                        @if($fields['phone'] ?? $card->contact?->phone)
                        <div style="display:flex;align-items:center;gap:6px;font-size:12px;opacity:.9;"><i class="bi bi-telephone-fill" style="color:{{ $accent }};font-size:11px;flex-shrink:0;"></i>{{ $fields['phone'] ?? $card->contact?->phone }}</div>
                        @endif
                        @if($fields['website'] ?? null)
                        <div style="display:flex;align-items:center;gap:6px;font-size:12px;opacity:.9;"><i class="bi bi-globe" style="color:{{ $accent }};font-size:11px;flex-shrink:0;"></i>{{ $fields['website'] }}</div>
                        @endif
                        @if($fields['linkedin'] ?? null)
                        <div style="display:flex;align-items:center;gap:6px;font-size:12px;opacity:.9;"><i class="bi bi-linkedin" style="color:{{ $accent }};font-size:11px;flex-shrink:0;"></i>{{ $fields['linkedin'] }}</div>
                        @endif
                    </div>
                    @if($qrCode)
                    <div style="position:absolute;bottom:16px;right:20px;" title="Scan to save contact">
                        <div style="width:56px;height:56px;border-radius:6px;background:white;padding:4px;overflow:hidden;display:flex;align-items:center;justify-content:center;">
                            <div style="width:48px;height:48px;">{!! $qrCode !!}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($qrCode)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0"><i class="bi bi-qr-code me-2"></i>QR Code — Scan to Save Contact</h5></div>
            <div class="card-body p-4 d-flex gap-4 align-items-start">
                <div style="width:160px;height:160px;border-radius:12px;border:1px solid #e5e7eb;padding:8px;background:white;flex-shrink:0;overflow:hidden;display:flex;align-items:center;justify-content:center;">
                    <div style="width:144px;height:144px;">{!! $qrCode !!}</div>
                </div>
                <div>
                    <p class="text-muted mb-3" style="font-size:14px;">Scan with any phone camera to save contact details. Encoded as vCard 3.0 standard (compatible with all address books).</p>
                    <pre class="bg-light rounded p-3 mb-0" style="font-size:11px;max-height:120px;overflow:auto;white-space:pre-wrap;">{{ $card->qr_data }}</pre>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Card Information</h5></div>
            <div class="card-body p-4">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted fw-600" style="font-size:12px;">Card Name</dt><dd class="col-7 fw-600">{{ $card->name }}</dd>
                    <dt class="col-5 text-muted fw-600" style="font-size:12px;">Template</dt><dd class="col-7">{{ $card->template?->name ?? '—' }}</dd>
                    <dt class="col-5 text-muted fw-600" style="font-size:12px;">Linked Contact</dt>
                    <dd class="col-7">@if($card->contact)<a href="{{ route('contacts.show', $card->contact) }}">{{ $card->contact->first_name }} {{ $card->contact->last_name }}</a>@else—@endif</dd>
                    <dt class="col-5 text-muted fw-600" style="font-size:12px;">Photo</dt>
                    <dd class="col-7"><span class="badge bg-{{ $card->photo?'success':'secondary' }}-subtle text-{{ $card->photo?'success':'secondary' }}">{{ $card->photo?'Uploaded':'None' }}</span></dd>
                    <dt class="col-5 text-muted fw-600" style="font-size:12px;">QR Code</dt>
                    <dd class="col-7"><span class="badge bg-{{ $card->qr_data?'success':'secondary' }}-subtle text-{{ $card->qr_data?'success':'secondary' }}">{{ $card->qr_data?'Enabled':'Not set' }}</span></dd>
                    <dt class="col-5 text-muted fw-600" style="font-size:12px;">Created</dt><dd class="col-7 text-muted">{{ $card->created_at->format('M d, Y') }}</dd>
                </dl>
            </div>
        </div>
        @if($card->data)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Card Fields</h5></div>
            <div class="card-body p-4">
                @foreach($card->data as $key => $value)@if($value)<div class="d-flex justify-content-between mb-2 pb-2 border-bottom"><span class="text-muted" style="font-size:13px;">{{ ucfirst($key) }}</span><span class="fw-600" style="font-size:13px;">{{ $value }}</span></div>@endif
                @endforeach
            </div>
        </div>
        @endif
        <form method="POST" action="{{ route('cards.destroy', $card) }}" onsubmit="return confirm('Delete this card?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger w-100"><i class="bi bi-trash me-1"></i>Delete Card</button>
        </form>
    </div>
</div>
@endsection
