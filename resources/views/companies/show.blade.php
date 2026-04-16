@extends('layouts.app')
@section('title', $company->name)
@section('page-title', $company->name)
@section('content')
<div class="page-header">
    <div><h1>{{ $company->name }}</h1><nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('companies.index') }}">Companies</a></li><li class="breadcrumb-item active">{{ $company->name }}</li></ol></nav></div>
    <div class="d-flex gap-2">
        <a href="{{ route('companies.edit', $company) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <button class="btn btn-outline-danger" onclick="confirmDelete('{{ route('companies.destroy', $company) }}', '{{ $company->name }}')"><i class="bi bi-trash me-1"></i>Delete</button>
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center pt-4">
                <div class="avatar-circle bg-primary text-white mx-auto mb-3" style="width:60px;height:60px;font-size:20px;border-radius:14px;">{{ strtoupper(substr($company->name, 0, 2)) }}</div>
                <h5 class="fw-700 mb-1">{{ $company->name }}</h5>
                <p class="text-muted mb-0">{{ $company->industry ?? 'General' }}</p>
            </div>
            <hr class="my-0">
            <div class="card-body">
                @if($company->email)<div class="d-flex gap-2 mb-2"><i class="bi bi-envelope text-muted"></i><a href="mailto:{{ $company->email }}" class="text-decoration-none">{{ $company->email }}</a></div>@endif
                @if($company->phone)<div class="d-flex gap-2 mb-2"><i class="bi bi-telephone text-muted"></i><span>{{ $company->phone }}</span></div>@endif
                @if($company->website)<div class="d-flex gap-2 mb-2"><i class="bi bi-globe text-muted"></i><a href="{{ $company->website }}" target="_blank" class="text-decoration-none">{{ $company->website }}</a></div>@endif
                @if($company->city || $company->country)<div class="d-flex gap-2 mb-2"><i class="bi bi-geo-alt text-muted"></i><span>{{ collect([$company->city, $company->country])->filter()->join(', ') }}</span></div>@endif
                @if($company->size)<div class="d-flex gap-2"><i class="bi bi-people text-muted"></i><span>{{ $company->size }} employees</span></div>@endif
                @if($company->annual_revenue)<div class="d-flex gap-2 mt-2"><i class="bi bi-currency-dollar text-muted"></i><span>${{ number_format($company->annual_revenue) }} revenue</span></div>@endif
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row g-3 text-center">
                    <div class="col-4"><div class="fw-700 fs-5">{{ $company->contacts->count() }}</div><div class="text-muted" style="font-size:12px;">Contacts</div></div>
                    <div class="col-4"><div class="fw-700 fs-5">{{ $company->deals->count() }}</div><div class="text-muted" style="font-size:12px;">Deals</div></div>
                    <div class="col-4"><div class="fw-700 fs-5">{{ $company->leads->count() }}</div><div class="text-muted" style="font-size:12px;">Leads</div></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#c-contacts">Contacts ({{ $company->contacts->count() }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#c-deals">Deals ({{ $company->deals->count() }})</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#c-invoices">Invoices ({{ $company->invoices->count() }})</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="c-contacts">
                <div class="card"><div class="card-body p-0">
                @forelse($company->contacts as $contact)
                <div class="d-flex align-items-center justify-content-between px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar-circle" style="width:34px;height:34px;font-size:12px;">{{ $contact->initials }}</div>
                        <div>
                            <a href="{{ route('contacts.show', $contact) }}" class="fw-600 text-decoration-none">{{ $contact->full_name }}</a>
                            <div class="text-muted" style="font-size:12px;">{{ $contact->job_title ?? '—' }}</div>
                        </div>
                    </div>
                    <span class="badge bg-{{ $contact->status_badge }}-subtle text-{{ $contact->status_badge }}">{{ ucfirst($contact->status) }}</span>
                </div>
                @empty<div class="text-center text-muted py-4" style="font-size:13px;">No contacts</div>@endforelse
                </div></div>
            </div>
            <div class="tab-pane fade" id="c-deals">
                <div class="card"><div class="card-body p-0">
                @forelse($company->deals as $deal)
                <div class="d-flex align-items-center justify-content-between px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div><a href="{{ route('deals.show', $deal) }}" class="fw-600 text-decoration-none">{{ $deal->title }}</a><div class="text-muted" style="font-size:12px;">{{ $deal->stage?->name ?? 'No stage' }}</div></div>
                    <div class="text-end"><div class="fw-600">${{ number_format($deal->value, 0) }}</div><span class="badge bg-{{ $deal->status_badge }}-subtle text-{{ $deal->status_badge }}" style="font-size:10px;">{{ ucfirst($deal->status) }}</span></div>
                </div>
                @empty<div class="text-center text-muted py-4" style="font-size:13px;">No deals</div>@endforelse
                </div></div>
            </div>
            <div class="tab-pane fade" id="c-invoices">
                <div class="card"><div class="card-body p-0">
                @forelse($company->invoices as $invoice)
                <div class="d-flex align-items-center justify-content-between px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div><a href="{{ route('invoices.show', $invoice) }}" class="fw-600 text-decoration-none">{{ $invoice->invoice_number }}</a><div class="text-muted" style="font-size:12px;">{{ $invoice->due_date->format('M j, Y') }}</div></div>
                    <div class="text-end"><div class="fw-600">${{ number_format($invoice->total, 2) }}</div><span class="badge bg-{{ $invoice->status_badge }}-subtle text-{{ $invoice->status_badge }}" style="font-size:10px;">{{ ucfirst($invoice->status) }}</span></div>
                </div>
                @empty<div class="text-center text-muted py-4" style="font-size:13px;">No invoices</div>@endforelse
                </div></div>
            </div>
        </div>
        @if($company->notes)
        <div class="card mt-4"><div class="card-header bg-transparent pt-3 px-4"><h6 class="fw-700 mb-0">Notes</h6></div><div class="card-body">{{ $company->notes }}</div></div>
        @endif
    </div>
</div>
@endsection
