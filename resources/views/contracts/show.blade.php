@extends('layouts.app')
@section('title', $contract->title)
@section('page-title', 'Contract')
@section('content')
<div class="page-header">
    <div>
        <h1>{{ Str::limit($contract->title, 60) }}</h1>
        <div class="d-flex align-items-center gap-2 mt-1">
            <span class="badge bg-{{ $contract->status_badge }}">{{ $contract->status_label }}</span>
            <span class="text-muted small">{{ $contract->contract_number }}</span>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('contracts.pdf', $contract) }}" class="btn btn-outline-danger"><i class="bi bi-file-pdf me-1"></i>PDF</a>
        <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

@if(session('success'))<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header fw-600">Contract Content</div>
            <div class="card-body">
                <div class="border rounded p-4" style="font-family:'Georgia',serif;line-height:1.8;white-space:pre-wrap;min-height:400px;">{{ $contract->content }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header fw-600">Details</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted fw-600">Contact</td><td>{{ $contract->contact?->full_name ?? '—' }}</td></tr>
                    <tr><td class="text-muted fw-600">Deal</td><td>@if($contract->deal)<a href="{{ route('deals.show', $contract->deal) }}">{{ $contract->deal->title }}</a>@else—@endif</td></tr>
                    <tr><td class="text-muted fw-600">Value</td><td class="fw-600 text-success">{{ $contract->value ? '$'.number_format($contract->value, 2) : '—' }}</td></tr>
                    <tr><td class="text-muted fw-600">Start</td><td>{{ $contract->start_date?->format('M d, Y') ?? '—' }}</td></tr>
                    <tr><td class="text-muted fw-600">End</td><td>{{ $contract->end_date?->format('M d, Y') ?? '—' }}</td></tr>
                    <tr><td class="text-muted fw-600">Signed By</td><td>{{ $contract->signed_by ?? '—' }}</td></tr>
                    <tr><td class="text-muted fw-600">Signed At</td><td>{{ $contract->signed_at?->format('M d, Y') ?? '—' }}</td></tr>
                    <tr><td class="text-muted fw-600">Created by</td><td>{{ $contract->creator?->name ?? '—' }}</td></tr>
                    <tr><td class="text-muted fw-600">Created</td><td>{{ $contract->created_at->format('M d, Y') }}</td></tr>
                </table>
            </div>
        </div>
        @if($contract->notes)
        <div class="card">
            <div class="card-header fw-600">Notes</div>
            <div class="card-body"><p class="mb-0 text-muted">{{ $contract->notes }}</p></div>
        </div>
        @endif
    </div>
</div>
@endsection
