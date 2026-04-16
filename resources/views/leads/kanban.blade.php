@extends('layouts.app')
@section('title', 'Leads Pipeline')
@section('page-title', 'Leads Pipeline')
@section('content')
<div class="page-header">
    <div><h1>Leads Pipeline</h1></div>
    <div class="d-flex gap-2">
        <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary"><i class="bi bi-list-ul me-1"></i>List View</a>
        <a href="{{ route('leads.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Lead</a>
    </div>
</div>
<div class="kanban-board">
@foreach($stages as $stage)
<div class="kanban-column">
    <div class="kanban-col-header">
        <div class="kanban-col-title">
            <div class="kanban-col-dot" style="background:{{ $stage->color }};"></div>
            {{ $stage->name }}
        </div>
        <span class="badge bg-light text-dark">{{ ($leads[$stage->id] ?? collect())->count() }}</span>
    </div>
    <div class="kanban-cards" data-stage-id="{{ $stage->id }}">
        @foreach($leads[$stage->id] ?? [] as $lead)
        <div class="kanban-card" draggable="true" data-id="{{ $lead->id }}" data-type="lead">
            <div class="kanban-card-title">{{ Str::limit($lead->title, 35) }}</div>
            <div class="kanban-card-meta d-flex align-items-center justify-content-between">
                <span>{{ $lead->contact?->full_name ?? '—' }}</span>
                <span class="badge bg-{{ $lead->status_badge }}" style="font-size:10px;">{{ ucfirst($lead->status) }}</span>
            </div>
            @if($lead->value)<div class="kanban-card-meta mt-1 fw-600 text-primary">${{ number_format($lead->value, 0) }}</div>@endif
            <div class="mt-2 d-flex gap-1">
                <a href="{{ route('leads.show', $lead) }}" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size:11px;">View</a>
                <a href="{{ route('leads.edit', $lead) }}" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size:11px;">Edit</a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endforeach
</div>
@endsection
