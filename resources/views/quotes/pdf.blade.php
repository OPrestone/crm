<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quote {{ $quote->quote_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 13px; color: #1a1a2e; background: #fff; }
        .header { background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%); color: white; padding: 36px 40px; display: table; width: 100%; }
        .header-left { display: table-cell; vertical-align: top; }
        .header-right { display: table-cell; vertical-align: top; text-align: right; }
        .company-name { font-size: 26px; font-weight: 700; letter-spacing: 1px; }
        .quote-label { font-size: 32px; font-weight: 700; opacity: .9; }
        .quote-num { font-size: 15px; opacity: .8; margin-top: 4px; }
        .body { padding: 36px 40px; }
        .meta-grid { display: table; width: 100%; margin-bottom: 28px; }
        .meta-cell { display: table-cell; width: 33%; vertical-align: top; }
        .meta-label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #888; margin-bottom: 4px; }
        .meta-value { font-size: 14px; font-weight: 600; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; background: #e8f4fd; color: #0d6efd; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        table.items thead { background: #f8f9ff; }
        table.items th { padding: 10px 12px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .5px; color: #555; border-bottom: 2px solid #e5e7eb; }
        table.items td { padding: 12px; border-bottom: 1px solid #f0f0f0; }
        .text-right { text-align: right; }
        .totals { width: 300px; float: right; margin-bottom: 30px; }
        .totals tr td { padding: 6px 12px; }
        .totals tr.total-row td { font-size: 16px; font-weight: 700; color: #0d6efd; border-top: 2px solid #0d6efd; padding-top: 10px; }
        .notes { background: #f8f9ff; border-left: 4px solid #0d6efd; padding: 14px 18px; border-radius: 4px; margin-top: 20px; }
        .clearfix::after { content: ''; display: table; clear: both; }
        .footer { margin-top: 40px; padding-top: 16px; border-top: 1px solid #eee; text-align: center; color: #999; font-size: 11px; }
    </style>
</head>
<body>
<div class="header">
    <div class="header-left">
        <div class="company-name">{{ $tenant->name }}</div>
        @if($tenant->email)<div style="opacity:.8;font-size:13px;margin-top:4px;">{{ $tenant->email }}</div>@endif
    </div>
    <div class="header-right">
        <div class="quote-label">QUOTE</div>
        <div class="quote-num">{{ $quote->quote_number }}</div>
        <div style="margin-top:8px;font-size:13px;opacity:.8;">
            <span class="status-badge" style="background:rgba(255,255,255,.2);color:white;">{{ strtoupper($quote->status) }}</span>
        </div>
    </div>
</div>

<div class="body">
    <div class="meta-grid">
        <div class="meta-cell">
            <div class="meta-label">Prepared For</div>
            @if($quote->contact)<div class="meta-value">{{ $quote->contact->full_name }}</div>@endif
            @if($quote->company)<div style="color:#555;">{{ $quote->company->name }}</div>@endif
        </div>
        <div class="meta-cell">
            <div class="meta-label">Issue Date</div>
            <div class="meta-value">{{ $quote->issue_date->format('d F Y') }}</div>
            @if($quote->valid_until)
            <div class="meta-label" style="margin-top:12px;">Valid Until</div>
            <div class="meta-value">{{ $quote->valid_until->format('d F Y') }}</div>
            @endif
        </div>
        <div class="meta-cell" style="text-align:right;">
            <div class="meta-label">Total Amount</div>
            <div style="font-size:24px;font-weight:700;color:#0d6efd;">{{ $quote->currency }} {{ number_format($quote->total, 2) }}</div>
        </div>
    </div>

    <div style="font-size:16px;font-weight:700;color:#1a1a2e;margin-bottom:14px;">{{ $quote->title }}</div>

    <table class="items">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Discount</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quote->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">{{ $quote->currency }} {{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">{{ $item->discount > 0 ? $item->discount.'%' : '—' }}</td>
                <td class="text-right" style="font-weight:600;">{{ $quote->currency }} {{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="clearfix">
        <table class="totals">
            <tr><td>Subtotal</td><td class="text-right">{{ $quote->currency }} {{ number_format($quote->subtotal, 2) }}</td></tr>
            @if($quote->discount > 0)<tr><td style="color:green;">Discount</td><td class="text-right" style="color:green;">— {{ $quote->currency }} {{ number_format($quote->discount, 2) }}</td></tr>@endif
            @if($quote->tax_rate > 0)<tr><td>Tax ({{ $quote->tax_rate }}%)</td><td class="text-right">{{ $quote->currency }} {{ number_format($quote->tax_amount, 2) }}</td></tr>@endif
            <tr class="total-row"><td>TOTAL</td><td class="text-right">{{ $quote->currency }} {{ number_format($quote->total, 2) }}</td></tr>
        </table>
    </div>

    @if($quote->notes)
    <div class="notes"><strong>Notes:</strong><br>{{ $quote->notes }}</div>
    @endif
    @if($quote->terms)
    <div class="notes" style="margin-top:10px;border-left-color:#6c757d;background:#f8f8f8;"><strong>Terms &amp; Conditions:</strong><br>{{ $quote->terms }}</div>
    @endif

    <div class="footer">
        Generated by {{ $tenant->name }} CRM &bull; {{ now()->format('d M Y') }}
    </div>
</div>
</body>
</html>
