<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $contract->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11pt; color: #222; line-height: 1.7; }
        .page { padding: 50px 60px; }
        .header { border-bottom: 2px solid #1a1a2e; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { font-size: 22pt; color: #1a1a2e; margin-bottom: 4px; }
        .meta-table { width: 100%; margin-bottom: 30px; }
        .meta-table td { padding: 4px 0; font-size: 10pt; }
        .meta-table .label { color: #666; font-weight: bold; width: 140px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 9pt; font-weight: bold; }
        .badge-success { background: #d1f0e0; color: #0d6832; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-secondary { background: #e9ecef; color: #495057; }
        .content-box { border: 1px solid #ddd; padding: 30px; border-radius: 6px; background: #fafafa; margin-bottom: 30px; white-space: pre-wrap; font-size: 10.5pt; line-height: 1.9; }
        .footer { border-top: 1px solid #ddd; padding-top: 20px; margin-top: 30px; font-size: 9pt; color: #888; display: flex; justify-content: space-between; }
        .signature-block { display: flex; gap: 60px; margin-top: 40px; }
        .sig-line { flex: 1; border-top: 1px solid #444; padding-top: 8px; font-size: 9pt; color: #555; }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <h1>{{ $contract->title }}</h1>
        <div style="color:#666;font-size:10pt;">{{ $contract->contract_number }}</div>
    </div>

    <table class="meta-table">
        <tr>
            <td class="label">Status</td>
            <td>
                <span class="badge {{ $contract->status === 'signed' ? 'badge-success' : ($contract->status === 'pending_signature' ? 'badge-warning' : 'badge-secondary') }}">
                    {{ $contract->status_label }}
                </span>
            </td>
        </tr>
        @if($contract->contact)
        <tr><td class="label">Contact</td><td>{{ $contract->contact->full_name }} &lt;{{ $contract->contact->email }}&gt;</td></tr>
        @endif
        @if($contract->deal)
        <tr><td class="label">Deal</td><td>{{ $contract->deal->title }}</td></tr>
        @endif
        @if($contract->value)
        <tr><td class="label">Contract Value</td><td><strong>${{ number_format($contract->value, 2) }}</strong></td></tr>
        @endif
        @if($contract->start_date)
        <tr><td class="label">Start Date</td><td>{{ $contract->start_date->format('F j, Y') }}</td></tr>
        @endif
        @if($contract->end_date)
        <tr><td class="label">End Date</td><td>{{ $contract->end_date->format('F j, Y') }}</td></tr>
        @endif
        @if($contract->signed_by)
        <tr><td class="label">Signed By</td><td>{{ $contract->signed_by }}</td></tr>
        @endif
        @if($contract->signed_at)
        <tr><td class="label">Signed At</td><td>{{ $contract->signed_at->format('F j, Y') }}</td></tr>
        @endif
    </table>

    <div class="content-box">{{ $contract->content }}</div>

    <div class="signature-block">
        <div class="sig-line">
            <strong>{{ $contract->contact?->full_name ?? 'Client' }}</strong><br>
            Signature &amp; Date
        </div>
        <div class="sig-line">
            <strong>{{ $contract->creator?->name ?? 'Authorised Signatory' }}</strong><br>
            Signature &amp; Date
        </div>
    </div>

    <div class="footer">
        <span>Generated: {{ now()->format('F j, Y') }}</span>
        <span>{{ $contract->contract_number }}</span>
    </div>
</div>
</body>
</html>
