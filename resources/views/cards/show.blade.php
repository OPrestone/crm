@extends('layouts.app')
@section('title', $card->name)
@section('page-title', 'Card Preview')
@section('content')
<div class="page-header">
    <div><h1>{{ $card->name }}</h1></div>
    <div class="d-flex gap-2">
        <a href="{{ route('cards.pdf', $card) }}" target="_blank" class="btn btn-outline-danger"><i class="bi bi-file-pdf me-1"></i>Export PDF</a>
        <button class="btn btn-outline-danger" onclick="confirmDelete('{{ route('cards.destroy', $card) }}', '{{ $card->name }}')"><i class="bi bi-trash me-1"></i>Delete</button>
    </div>
</div>
<div class="row g-4 justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body p-5 text-center">
                <div style="background:linear-gradient(135deg,#0d6efd,#6f42c1);border-radius:16px;padding:32px;color:white;max-width:360px;margin:0 auto;">
                    <div style="font-size:11px;letter-spacing:2px;text-transform:uppercase;opacity:.7;margin-bottom:24px;">{{ auth()->user()->tenant->name }}</div>
                    @if($card->contact)
                    <div style="font-size:24px;font-weight:700;margin-bottom:8px;">{{ $card->contact->full_name }}</div>
                    <div style="opacity:.8;margin-bottom:4px;">{{ $card->contact->job_title ?? '' }}</div>
                    @if($card->contact->email)<div style="font-size:13px;opacity:.7;">{{ $card->contact->email }}</div>@endif
                    @if($card->contact->phone)<div style="font-size:13px;opacity:.7;">{{ $card->contact->phone }}</div>@endif
                    @else
                    <div style="font-size:24px;font-weight:700;">{{ $card->name }}</div>
                    @endif
                    <div style="margin-top:24px;font-size:11px;opacity:.5;">{{ $card->template?->name ?? 'Custom Card' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
