@extends('layouts.app')
@section('title', $quote->quote_number)
@section('page-title', 'Quote Details')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $quote->quote_number }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('quotes.index') }}">Quotes</a></li>
            <li class="breadcrumb-item active">{{ $quote->quote_number }}</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('quotes.pdf', $quote) }}" class="btn btn-outline-danger" target="_blank"><i class="bi bi-file-pdf me-1"></i>PDF</a>
        <a href="{{ route('quotes.edit', $quote) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <form method="POST" action="{{ route('quotes.destroy', $quote) }}" onsubmit="return confirm('Delete this quote?')">
            @csrf @method('DELETE')<button class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>Delete</button>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between fw-600">
                <span><i class="bi bi-file-earmark-ruled-fill me-2 text-warning"></i>{{ $quote->title }}</span>
                <span class="badge bg-{{ $quote->status_badge }}-subtle text-{{ $quote->status_badge }}">{{ ucfirst($quote->status) }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="text-muted small">Issue Date</div>
                        <div class="fw-600">{{ $quote->issue_date->format('d M Y') }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Valid Until</div>
                        <div class="fw-600 {{ $quote->isExpired() ? 'text-danger' : '' }}">{{ $quote->valid_until ? $quote->valid_until->format('d M Y') : '—' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Currency</div>
                        <div class="fw-600">{{ $quote->currency }}</div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr><th>Description</th><th class="text-end">Qty</th><th class="text-end">Unit Price</th><th class="text-end">Disc%</th><th class="text-end">Total</th></tr>
                        </thead>
                        <tbody>
                            @foreach($quote->items as $item)
                            <tr>
                                <td>
                                    {{ $item->description }}
                                    @if($item->product)<div class="text-muted small">{{ $item->product->name }}</div>@endif
                                </td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">{{ $quote->currency }} {{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end">{{ $item->discount > 0 ? $item->discount.'%' : '—' }}</td>
                                <td class="text-end fw-600">{{ $quote->currency }} {{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr><td colspan="4" class="text-end fw-600">Subtotal</td><td class="text-end">{{ $quote->currency }} {{ number_format($quote->subtotal, 2) }}</td></tr>
                            @if($quote->discount > 0)<tr><td colspan="4" class="text-end text-success">Discount</td><td class="text-end text-success">— {{ $quote->currency }} {{ number_format($quote->discount, 2) }}</td></tr>@endif
                            @if($quote->tax_rate > 0)<tr><td colspan="4" class="text-end">Tax ({{ $quote->tax_rate }}%)</td><td class="text-end">{{ $quote->currency }} {{ number_format($quote->tax_amount, 2) }}</td></tr>@endif
                            <tr class="table-light"><td colspan="4" class="text-end fw-700 fs-6">Total</td><td class="text-end fw-700 fs-6 text-primary">{{ $quote->currency }} {{ number_format($quote->total, 2) }}</td></tr>
                        </tfoot>
                    </table>
                </div>

                @if($quote->notes)
                <div class="mt-3"><strong>Notes:</strong><p class="text-muted mb-0">{{ $quote->notes }}</p></div>
                @endif
                @if($quote->terms)
                <div class="mt-2"><strong>Terms:</strong><p class="text-muted mb-0">{{ $quote->terms }}</p></div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header fw-600">Update Status</div>
            <div class="card-body">
                <form method="POST" action="{{ route('quotes.status', $quote) }}">
                    @csrf @method('PATCH')
                    <select name="status" class="form-select mb-2">
                        @foreach(['draft','sent','accepted','rejected','expired'] as $s)
                        <option value="{{ $s }}" {{ $quote->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Update Status</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header fw-600">Info</div>
            <div class="card-body">
                @if($quote->contact)
                <div class="mb-2"><div class="text-muted small">Contact</div>
                    <a href="{{ route('contacts.show', $quote->contact) }}" class="fw-600 text-decoration-none">{{ $quote->contact->full_name }}</a></div>
                @endif
                @if($quote->company)
                <div class="mb-2"><div class="text-muted small">Company</div>
                    <a href="{{ route('companies.show', $quote->company) }}" class="fw-600 text-decoration-none">{{ $quote->company->name }}</a></div>
                @endif
                @if($quote->deal)
                <div class="mb-2"><div class="text-muted small">Deal</div>
                    <a href="{{ route('deals.show', $quote->deal) }}" class="fw-600 text-decoration-none">{{ $quote->deal->title }}</a></div>
                @endif
                <div class="mb-2"><div class="text-muted small">Created by</div><div>{{ $quote->creator?->name ?? '—' }}</div></div>
                <div><div class="text-muted small">Created</div><div>{{ $quote->created_at->format('d M Y H:i') }}</div></div>
            </div>
        </div>
    </div>
</div>
@endsection
