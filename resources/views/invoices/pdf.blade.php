<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
.header { display: flex; justify-content: space-between; margin-bottom: 40px; }
.company-name { font-size: 20px; font-weight: bold; color: #0d6efd; }
.invoice-title { font-size: 28px; font-weight: bold; color: #0d6efd; }
.invoice-number { font-size: 16px; font-weight: bold; }
table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
th { background: #f8f9fa; padding: 8px; text-align: left; border-bottom: 2px solid #dee2e6; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
td { padding: 8px; border-bottom: 1px solid #dee2e6; }
.text-right { text-align: right; }
.total-table td { border: none; padding: 4px 8px; }
.grand-total { font-size: 16px; font-weight: bold; color: #0d6efd; }
.badge { padding: 3px 8px; border-radius: 4px; font-size: 11px; }
.badge-paid { background: #d1e7dd; color: #0f5132; }
.badge-sent { background: #cff4fc; color: #055160; }
.badge-draft { background: #e2e3e5; color: #41464b; }
.badge-overdue { background: #f8d7da; color: #842029; }
</style>
</head>
<body>
<div class="header">
    <div>
        <div class="company-name">{{ $tenant->name }}</div>
        <div>{{ $tenant->email }}</div>
        @if($tenant->phone)<div>{{ $tenant->phone }}</div>@endif
    </div>
    <div style="text-align:right;">
        <div class="invoice-title">INVOICE</div>
        <div class="invoice-number">{{ $invoice->invoice_number }}</div>
        <div class="badge badge-{{ $invoice->status }}">{{ strtoupper($invoice->status) }}</div>
    </div>
</div>
<div style="display:flex; justify-content:space-between; margin-bottom:30px;">
    <div>
        <div style="font-size:10px;text-transform:uppercase;letter-spacing:1px;color:#6c757d;margin-bottom:5px;">Bill To</div>
        @if($invoice->contact)
        <div style="font-weight:bold;">{{ $invoice->contact->full_name }}</div>
        @if($invoice->contact->email)<div>{{ $invoice->contact->email }}</div>@endif
        @endif
        @if($invoice->company && !$invoice->contact)<div style="font-weight:bold;">{{ $invoice->company->name }}</div>@endif
    </div>
    <div style="text-align:right;">
        <div>Issue Date: <strong>{{ $invoice->issue_date->format('M j, Y') }}</strong></div>
        <div>Due Date: <strong>{{ $invoice->due_date->format('M j, Y') }}</strong></div>
    </div>
</div>
<table>
    <thead><tr><th>Description</th><th class="text-right">Qty</th><th class="text-right">Unit Price</th><th class="text-right">Total</th></tr></thead>
    <tbody>
    @foreach($invoice->items as $item)
    <tr><td>{{ $item->description }}</td><td class="text-right">{{ $item->quantity }}</td><td class="text-right">${{ number_format($item->unit_price, 2) }}</td><td class="text-right"><strong>${{ number_format($item->total, 2) }}</strong></td></tr>
    @endforeach
    </tbody>
</table>
<div style="display:flex; justify-content:flex-end;">
    <table style="width:280px;">
        <tr class="total-table"><td>Subtotal</td><td class="text-right">${{ number_format($invoice->subtotal, 2) }}</td></tr>
        <tr class="total-table"><td>Tax ({{ $invoice->tax_rate }}%)</td><td class="text-right">${{ number_format($invoice->tax_amount, 2) }}</td></tr>
        @if($invoice->discount > 0)<tr class="total-table"><td>Discount</td><td class="text-right">-${{ number_format($invoice->discount, 2) }}</td></tr>@endif
        <tr class="total-table" style="border-top:2px solid #dee2e6;"><td class="grand-total">Total</td><td class="text-right grand-total">${{ number_format($invoice->total, 2) }} {{ $invoice->currency }}</td></tr>
    </table>
</div>
@if($invoice->notes)<p style="margin-top:20px;"><strong>Notes:</strong> {{ $invoice->notes }}</p>@endif
@if($invoice->terms)<p style="color:#6c757d;font-size:11px;">{{ $invoice->terms }}</p>@endif
</body>
</html>
