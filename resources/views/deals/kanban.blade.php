@extends('layouts.app')
@section('title', 'Deals Pipeline')
@section('page-title', 'Deals Pipeline')
@section('content')
<div class="page-header">
    <div><h1>Deals Pipeline</h1></div>
    <div class="d-flex gap-2">
        <a href="{{ route('deals.index') }}" class="btn btn-outline-secondary"><i class="bi bi-list-ul me-1"></i>List View</a>
        <a href="{{ route('deals.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Deal</a>
    </div>
</div>
<div class="kanban-board">
@foreach($stages as $stage)
<div class="kanban-column">
    <div class="kanban-col-header">
        <div class="kanban-col-title"><div class="kanban-col-dot" style="background:{{ $stage->color }};"></div>{{ $stage->name }}</div>
        <div>
            <span class="badge bg-light text-dark me-1">{{ ($deals[$stage->id] ?? collect())->count() }}</span>
            <small class="text-muted">${{ number_format(($deals[$stage->id] ?? collect())->sum('value'), 0) }}</small>
        </div>
    </div>
    <div class="kanban-cards" data-stage-id="{{ $stage->id }}">
        @foreach($deals[$stage->id] ?? [] as $deal)
        <div class="kanban-card" draggable="true" data-id="{{ $deal->id }}" data-type="deal">
            <div class="kanban-card-title">{{ Str::limit($deal->title, 35) }}</div>
            <div class="fw-700 text-primary mb-1">${{ number_format($deal->value, 0) }}</div>
            <div class="kanban-card-meta d-flex align-items-center justify-content-between">
                <span>{{ $deal->contact?->full_name ?? $deal->company?->name ?? '—' }}</span>
                <span class="badge bg-{{ $deal->priority_badge }}-subtle text-{{ $deal->priority_badge }}" style="font-size:10px;">{{ ucfirst($deal->priority) }}</span>
            </div>
            @if($deal->expected_close_date)
            <div class="kanban-card-meta mt-1"><i class="bi bi-calendar3 text-muted"></i> {{ $deal->expected_close_date->format('M j') }}</div>
            @endif
            <div class="mt-2 d-flex gap-1">
                <a href="{{ route('deals.show', $deal) }}" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size:11px;">View</a>
                <a href="{{ route('deals.edit', $deal) }}" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size:11px;">Edit</a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endforeach
</div>
@endsection
