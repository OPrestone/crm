@extends('layouts.app')
@section('title', $invoice->invoice_number)
@section('page-title', 'Invoice')
@section('content')
<div class="page-header">
    <div><h1>{{ $invoice->invoice_number }}</h1></div>
    <div class="d-flex gap-2">
        <a href="{{ route('invoices.pdf', $invoice) }}" target="_blank" class="btn btn-outline-danger"><i class="bi bi-file-pdf me-1"></i>PDF</a>
        <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <button class="btn btn-outline-danger" onclick="confirmDelete('{{ route('invoices.destroy', $invoice) }}', '{{ $invoice->invoice_number }}')"><i class="bi bi-trash me-1"></i>Delete</button>
    </div>
</div>
<div class="card">
    <div class="card-body p-4 p-md-5">
        <div class="row mb-5">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="avatar-circle bg-primary text-white" style="width:48px;height:48px;font-size:18px;border-radius:12px;">C</div>
                    <div>
                        <h5 class="fw-700 mb-0">{{ auth()->user()->tenant->name }}</h5>
                        <div class="text-muted" style="font-size:13px;">{{ auth()->user()->tenant->email }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <h2 class="fw-700 text-primary mb-1">INVOICE</h2>
                <h4 class="fw-600 mb-2">{{ $invoice->invoice_number }}</h4>
                <span class="badge bg-{{ $invoice->status_badge }}-subtle text-{{ $invoice->status_badge }} fs-6">{{ ucfirst($invoice->status) }}</span>
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-md-6">
                <h6 class="text-muted text-uppercase mb-3" style="font-size:11px;letter-spacing:1px;">Bill To</h6>
                @if($invoice->contact)
                <div class="fw-700">{{ $invoice->contact->full_name }}</div>
                @if($invoice->contact->email)<div class="text-muted">{{ $invoice->contact->email }}</div>@endif
                @elseif($invoice->company)
                <div class="fw-700">{{ $invoice->company->name }}</div>
                @if($invoice->company->email)<div class="text-muted">{{ $invoice->company->email }}</div>@endif
                @else<div class="text-muted">—</div>@endif
            </div>
            <div class="col-md-6 text-md-end">
                <div class="mb-2"><span class="text-muted">Issue Date:</span> <strong>{{ $invoice->issue_date->format('M j, Y') }}</strong></div>
                <div><span class="text-muted">Due Date:</span> <strong class="{{ $invoice->isOverdue() ? 'text-danger' : '' }}">{{ $invoice->due_date->format('M j, Y') }}</strong></div>
            </div>
        </div>
        <div class="table-responsive mb-4">
            <table class="table">
                <thead class="table-light"><tr><th>Description</th><th class="text-end">Qty</th><th class="text-end">Unit Price</th><th class="text-end">Total</th></tr></thead>
                <tbody>
                @foreach($invoice->items as $item)
                <tr><td>{{ $item->description }}</td><td class="text-end">{{ $item->quantity }}</td><td class="text-end">${{ number_format($item->unit_price, 2) }}</td><td class="text-end fw-600">${{ number_format($item->total, 2) }}</td></tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="row justify-content-end mb-4">
            <div class="col-md-5">
                <table class="table table-sm">
                    <tr><td class="text-muted">Subtotal</td><td class="text-end">${{ number_format($invoice->subtotal, 2) }}</td></tr>
                    <tr><td class="text-muted">Tax ({{ $invoice->tax_rate }}%)</td><td class="text-end">${{ number_format($invoice->tax_amount, 2) }}</td></tr>
                    @if($invoice->discount > 0)<tr><td class="text-muted">Discount</td><td class="text-end text-danger">-${{ number_format($invoice->discount, 2) }}</td></tr>@endif
                    <tr class="fw-700 table-light fs-5"><td>Total</td><td class="text-end text-primary">${{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</td></tr>
                </table>
            </div>
        </div>
        @if($invoice->notes)
        <div class="mb-4"><h6 class="text-muted text-uppercase mb-2" style="font-size:11px;letter-spacing:1px;">Notes</h6><p>{{ $invoice->notes }}</p></div>
        @endif
        @if($invoice->terms)
        <div><h6 class="text-muted text-uppercase mb-2" style="font-size:11px;letter-spacing:1px;">Terms</h6><p class="text-muted">{{ $invoice->terms }}</p></div>
        @endif
    </div>
</div>
@endsection
