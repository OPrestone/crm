<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
h1 { color: #0d6efd; }
table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
th { background: #f8f9fa; padding: 8px; border: 1px solid #dee2e6; }
td { padding: 8px; border: 1px solid #dee2e6; }
</style>
</head>
<body>
<h1>{{ $tenant->name }} — CRM Report</h1>
<p>Generated: {{ now()->format('M j, Y H:i') }}</p>
<h2>Summary Statistics</h2>
<table>
    <tr><th>Metric</th><th>Value</th></tr>
    <tr><td>Total Contacts</td><td>{{ number_format($data['contacts']) }}</td></tr>
    <tr><td>Total Leads</td><td>{{ number_format($data['leads']) }}</td></tr>
    <tr><td>Total Deals</td><td>{{ number_format($data['deals']) }}</td></tr>
    <tr><td>Total Revenue (Paid Invoices)</td><td>${{ number_format($data['revenue'], 2) }}</td></tr>
</table>
</body>
</html>
