@extends('layouts.app')
@section('title', 'New Invoice')
@section('page-title', 'New Invoice')
@section('content')
<div class="page-header"><div><h1>New Invoice</h1></div></div>
<form method="POST" action="{{ route('invoices.store') }}">
@csrf
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4"><div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Invoice Details</h5></div>
        <div class="card-body px-4"><div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-600">Invoice Number <span class="text-danger">*</span></label><input type="text" name="invoice_number" class="form-control" value="{{ old('invoice_number', $nextNumber) }}" required></div>
            <div class="col-md-6"><label class="form-label fw-600">Currency</label><select name="currency" class="form-select"><option value="USD" selected>USD ($)</option><option value="EUR">EUR (€)</option><option value="GBP">GBP (£)</option><option value="KES">KES (Ksh)</option></select></div>
            <div class="col-md-6"><label class="form-label fw-600">Contact</label><select name="contact_id" class="form-select"><option value="">— None —</option>@foreach($contacts as $c)<option value="{{ $c->id }}" {{ old('contact_id') == $c->id ? 'selected' : '' }}>{{ $c->full_name }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label fw-600">Company</label><select name="company_id" class="form-select"><option value="">— None —</option>@foreach($companies as $co)<option value="{{ $co->id }}" {{ old('company_id') == $co->id ? 'selected' : '' }}>{{ $co->name }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label fw-600">Issue Date <span class="text-danger">*</span></label><input type="date" name="issue_date" class="form-control" value="{{ old('issue_date', date('Y-m-d')) }}" required></div>
            <div class="col-md-6"><label class="form-label fw-600">Due Date <span class="text-danger">*</span></label><input type="date" name="due_date" class="form-control" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required></div>
        </div></div></div>

        <div class="card mb-4"><div class="card-header bg-transparent pt-4 px-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-700 mb-0">Line Items</h5>
            <button type="button" class="btn btn-outline-primary btn-sm" id="addItemBtn"><i class="bi bi-plus-lg me-1"></i>Add Item</button>
        </div>
        <div class="card-body px-4">
            <div class="row g-2 mb-2 d-none d-md-flex">
                <div class="col-md-5 text-muted fw-600" style="font-size:12px;">Description</div>
                <div class="col-md-2 text-muted fw-600" style="font-size:12px;">Qty</div>
                <div class="col-md-2 text-muted fw-600" style="font-size:12px;">Unit Price</div>
                <div class="col-md-2 text-muted fw-600" style="font-size:12px;">Total</div>
            </div>
            <div id="invoiceItems">
                <div class="row g-2 mb-2 invoice-item-row">
                    <div class="col-md-5"><input type="text" name="items[0][description]" class="form-control" placeholder="Description" required></div>
                    <div class="col-md-2"><input type="number" name="items[0][quantity]" class="form-control item-qty" placeholder="Qty" value="1" min="0.01" step="0.01" required></div>
                    <div class="col-md-2"><input type="number" name="items[0][unit_price]" class="form-control item-price" placeholder="Price" min="0" step="0.01" required></div>
                    <div class="col-md-2"><input type="text" class="form-control item-total" placeholder="Total" readonly></div>
                    <div class="col-md-1"><button type="button" class="btn btn-outline-danger btn-sm w-100 remove-item-btn"><i class="bi bi-trash"></i></button></div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-transparent">
            <div class="row justify-content-end">
                <div class="col-md-5">
                    <table class="table table-sm mb-0" style="font-size:13px;">
                        <tr><td class="text-muted">Subtotal</td><td class="text-end fw-600">$<span id="subtotalDisplay">0.00</span></td></tr>
                        <tr><td class="text-muted">Tax (<input type="number" id="tax_rate" name="tax_rate" class="form-control form-control-sm d-inline" style="width:60px;" value="{{ old('tax_rate', 0) }}" min="0" max="100" step="0.1" oninput="updateInvoiceTotal()">%)</td><td class="text-end fw-600">$<span id="taxDisplay">0.00</span></td></tr>
                        <tr><td class="text-muted">Discount ($<input type="number" id="discount" name="discount" class="form-control form-control-sm d-inline" style="width:80px;" value="{{ old('discount', 0) }}" min="0" step="0.01" oninput="updateInvoiceTotal()">)</td><td class="text-end fw-600">$<span id="discountDisplay">0.00</span></td></tr>
                        <tr class="fw-700"><td>Total</td><td class="text-end text-primary fs-5">$<span id="totalDisplay">0.00</span></td></tr>
                    </table>
                </div>
            </div>
        </div>
        </div>

        <div class="card"><div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Additional Info</h5></div>
        <div class="card-body px-4"><div class="row g-3">
            <div class="col-12"><label class="form-label fw-600">Notes to Client</label><textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea></div>
            <div class="col-12"><label class="form-label fw-600">Terms & Conditions</label><textarea name="terms" class="form-control" rows="2">{{ old('terms', 'Payment due within 30 days of invoice date.') }}</textarea></div>
        </div></div></div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4"><div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Status</h5></div>
        <div class="card-body px-4">
            <select name="status" class="form-select"><option value="draft" selected>Draft</option><option value="sent">Sent</option><option value="paid">Paid</option></select>
        </div></div>
        <div class="d-grid gap-2"><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Create Invoice</button><a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">Cancel</a></div>
    </div>
</div>
</form>
@endsection
