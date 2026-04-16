<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: DejaVu Sans, sans-serif; margin: 0; padding: 10mm; background: white; }
.card-container { width: 85.6mm; height: 53.98mm; background: linear-gradient(135deg, #0d6efd, #6f42c1); border-radius: 8px; padding: 8mm; color: white; display: flex; flex-direction: column; justify-content: space-between; }
.card-org { font-size: 7px; letter-spacing: 2px; text-transform: uppercase; opacity: 0.7; }
.card-name { font-size: 16px; font-weight: bold; margin: 8px 0 4px; }
.card-title { font-size: 10px; opacity: 0.8; }
.card-contact { font-size: 9px; opacity: 0.7; }
</style>
</head>
<body>
<div class="card-container">
    <div class="card-org">{{ $card->contact?->company?->name ?? 'Organization' }}</div>
    <div>
        <div class="card-name">{{ $card->contact?->full_name ?? $card->name }}</div>
        <div class="card-title">{{ $card->contact?->job_title ?? '' }}</div>
    </div>
    <div>
        @if($card->contact?->email)<div class="card-contact">{{ $card->contact->email }}</div>@endif
        @if($card->contact?->phone)<div class="card-contact">{{ $card->contact->phone }}</div>@endif
    </div>
</div>
</body>
</html>
