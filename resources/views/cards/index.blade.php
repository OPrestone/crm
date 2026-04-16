@extends('layouts.app')
@section('title', 'Card Generator')
@section('page-title', 'Card Generator')
@section('content')
<div class="page-header">
    <div><h1>Card Generator</h1></div>
    <div class="d-flex gap-2">
        <a href="{{ route('cards.templates.create') }}" class="btn btn-outline-primary"><i class="bi bi-layout-text-sidebar me-1"></i>New Template</a>
        <a href="{{ route('cards.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Create Card</a>
    </div>
</div>

@if($templates->count())
<h5 class="fw-700 mb-3">Templates</h5>
<div class="row g-3 mb-5">
@foreach($templates as $template)
<div class="col-md-4 col-lg-3">
    <div class="card h-100">
        <div class="card-body text-center">
            <div class="avatar-circle bg-primary text-white mx-auto mb-3" style="width:48px;height:48px;font-size:18px;border-radius:10px;"><i class="bi bi-credit-card-2-front"></i></div>
            <h6 class="fw-700 mb-1">{{ $template->name }}</h6>
            <span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($template->category) }}</span>
            <div class="mt-2 text-muted" style="font-size:12px;">{{ $template->cards_count }} card(s)</div>
        </div>
    </div>
</div>
@endforeach
</div>
@endif

<h5 class="fw-700 mb-3">Generated Cards</h5>
@if($cards->count())
<div class="row g-3">
@foreach($cards as $card)
<div class="col-md-6 col-lg-4">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-start gap-3">
                <div class="avatar-circle bg-success text-white" style="width:44px;height:44px;font-size:16px;flex-shrink:0;border-radius:10px;"><i class="bi bi-credit-card-fill"></i></div>
                <div class="flex-1">
                    <h6 class="fw-700 mb-1">{{ $card->name }}</h6>
                    <div class="text-muted" style="font-size:12px;">{{ $card->contact?->full_name ?? 'No contact' }}</div>
                    <div class="text-muted" style="font-size:12px;">{{ $card->template?->name ?? 'Custom' }}</div>
                </div>
                <div class="dropdown"><button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('cards.show', $card) }}"><i class="bi bi-eye"></i> View</a></li>
                        <li><a class="dropdown-item" href="{{ route('cards.pdf', $card) }}" target="_blank"><i class="bi bi-file-pdf"></i> PDF</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><button class="dropdown-item text-danger" onclick="confirmDelete('{{ route('cards.destroy', $card) }}', '{{ $card->name }}')"><i class="bi bi-trash"></i> Delete</button></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach
</div>
@else
<div class="empty-state"><div class="empty-icon"><i class="bi bi-credit-card-2-front"></i></div><h5>No cards created yet</h5><p class="text-muted">Create beautiful ID cards, business cards, and membership cards.</p><a href="{{ route('cards.create') }}" class="btn btn-primary">Create Card</a></div>
@endif
@endsection
