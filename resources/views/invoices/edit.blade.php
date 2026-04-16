@extends('layouts.app')
@section('title', 'Edit Invoice')
@section('page-title', 'Edit Invoice')
@section('content')
<div class="page-header"><div><h1>Edit {{ $invoice->invoice_number }}</h1></div></div>
<form method="POST" action="{{ route('invoices.update', $invoice) }}">
@csrf @method('PATCH')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4"><div class="card-body px-4 py-4"><div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-600">Currency</label><select name="currency" class="form-select"><option value="USD" {{ $invoice->currency === 'USD' ? 'selected' : '' }}>USD ($)</option><option value="EUR" {{ $invoice->currency === 'EUR' ? 'selected' : '' }}>EUR (€)</option><option value="GBP" {{ $invoice->currency === 'GBP' ? 'selected' : '' }}>GBP (£)</option></select></div>
            <div class="col-md-6"><label class="form-label fw-600">Contact</label><select name="contact_id" class="form-select"><option value="">— None —</option>@foreach($contacts as $c)<option value="{{ $c->id }}" {{ old('contact_id', $invoice->contact_id) == $c->id ? 'selected' : '' }}>{{ $c->full_name }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label fw-600">Company</label><select name="company_id" class="form-select"><option value="">— None —</option>@foreach($companies as $co)<option value="{{ $co->id }}" {{ old('company_id', $invoice->company_id) == $co->id ? 'selected' : '' }}>{{ $co->name }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label fw-600">Issue Date</label><input type="date" name="issue_date" class="form-control" value="{{ old('issue_date', $invoice->issue_date->format('Y-m-d')) }}" required></div>
            <div class="col-md-6"><label class="form-label fw-600">Due Date</label><input type="date" name="due_date" class="form-control" value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required></div>
        </div></div></div>
        <div class="card mb-4"><div class="card-header bg-transparent pt-4 px-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-700 mb-0">Line Items</h5>
            <button type="button" class="btn btn-outline-primary btn-sm" id="addItemBtn"><i class="bi bi-plus-lg me-1"></i>Add Item</button>
        </div>
        <div class="card-body px-4">
            <div id="invoiceItems">
                @foreach($invoice->items as $idx => $item)
                <div class="row g-2 mb-2 invoice-item-row">
                    <div class="col-md-5"><input type="text" name="items[{{ $idx }}][description]" class="form-control" value="{{ $item->description }}" required></div>
                    <div class="col-md-2"><input type="number" name="items[{{ $idx }}][quantity]" class="form-control item-qty" value="{{ $item->quantity }}" min="0.01" step="0.01" required></div>
                    <div class="col-md-2"><input type="number" name="items[{{ $idx }}][unit_price]" class="form-control item-price" value="{{ $item->unit_price }}" min="0" step="0.01" required></div>
                    <div class="col-md-2"><input type="text" class="form-control item-total" value="{{ $item->total }}" readonly></div>
                    <div class="col-md-1"><button type="button" class="btn btn-outline-danger btn-sm w-100 remove-item-btn"><i class="bi bi-trash"></i></button></div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="card-footer bg-transparent">
            <div class="row justify-content-end">
                <div class="col-md-5">
                    <table class="table table-sm mb-0" style="font-size:13px;">
                        <tr><td class="text-muted">Subtotal</td><td class="text-end fw-600">$<span id="subtotalDisplay">{{ number_format($invoice->subtotal, 2) }}</span></td></tr>
                        <tr><td class="text-muted">Tax (<input type="number" id="tax_rate" name="tax_rate" class="form-control form-control-sm d-inline" style="width:60px;" value="{{ $invoice->tax_rate }}" min="0" max="100" step="0.1" oninput="updateInvoiceTotal()">%)</td><td class="text-end fw-600">$<span id="taxDisplay">{{ number_format($invoice->tax_amount, 2) }}</span></td></tr>
                        <tr><td class="text-muted">Discount ($<input type="number" id="discount" name="discount" class="form-control form-control-sm d-inline" style="width:80px;" value="{{ $invoice->discount }}" min="0" step="0.01" oninput="updateInvoiceTotal()">)</td><td class="text-end fw-600">$<span id="discountDisplay">{{ number_format($invoice->discount, 2) }}</span></td></tr>
                        <tr class="fw-700"><td>Total</td><td class="text-end text-primary fs-5">$<span id="totalDisplay">{{ number_format($invoice->total, 2) }}</span></td></tr>
                    </table>
                </div>
            </div>
        </div></div>
        <div class="card"><div class="card-body px-4 py-4"><div class="row g-3">
            <div class="col-12"><label class="form-label fw-600">Notes</label><textarea name="notes" class="form-control" rows="2">{{ old('notes', $invoice->notes) }}</textarea></div>
            <div class="col-12"><label class="form-label fw-600">Terms</label><textarea name="terms" class="form-control" rows="2">{{ old('terms', $invoice->terms) }}</textarea></div>
        </div></div></div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4"><div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Status</h5></div>
        <div class="card-body px-4"><select name="status" class="form-select">@foreach(['draft','sent','paid','overdue','cancelled'] as $s)<option value="{{ $s }}" {{ old('status', $invoice->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach</select></div></div>
        <div class="d-grid gap-2"><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update Invoice</button><a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-secondary">Cancel</a></div>
    </div>
</div>
</form>
@endsection
