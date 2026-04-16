<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: DejaVu Sans, Arial, sans-serif; background:#f0f0f0; padding:8mm; }
.page { width:241.89pt; height:153.07pt; }
.card-front {
    width:241.89pt; height:153.07pt;
    border-radius:10pt;
    position:relative; overflow:hidden;
    background:{{ $card->template?->design['bg_color'] ?? '#1e3a5f' }};
    color:{{ $card->template?->design['text_color'] ?? '#ffffff' }};
    padding:18pt 18pt 14pt;
}
.accent { color: {{ $card->template?->design['accent'] ?? '#f59e0b' }}; }
.circle1 { position:absolute; top:-20pt; right:-20pt; width:80pt; height:80pt; border-radius:50%; background:{{ $card->template?->design['accent'] ?? '#f59e0b' }}; opacity:.2; }
.circle2 { position:absolute; bottom:-30pt; left:-15pt; width:110pt; height:110pt; border-radius:50%; background:{{ $card->template?->design['accent'] ?? '#f59e0b' }}; opacity:.1; }
.header { display:flex; align-items:center; gap:12pt; margin-bottom:12pt; position:relative; }
.photo-circle { width:50pt; height:50pt; border-radius:50%; overflow:hidden; border:2.5pt solid {{ $card->template?->design['accent'] ?? '#f59e0b' }}; flex-shrink:0; }
.photo-circle img { width:100%; height:100%; }
.initials-circle { width:50pt; height:50pt; border-radius:50%; background:{{ $card->template?->design['accent'] ?? '#f59e0b' }}; display:flex; align-items:center; justify-content:center; font-size:20pt; font-weight:700; color:#fff; flex-shrink:0; }
.name { font-size:15pt; font-weight:700; line-height:1.2; }
.title { font-size:9pt; opacity:.8; margin-top:2pt; }
.company { font-size:8pt; font-weight:600; margin-top:2pt; }
.contacts { position:relative; display:table; width:100%; }
.contact-row { display:table-row; font-size:8pt; opacity:.9; }
.contact-row td { display:table-cell; padding:2pt 8pt 2pt 0; }
.contact-row .icon { font-size:7pt; width:12pt; }
.qr-box { position:absolute; bottom:12pt; right:14pt; background:white; border-radius:5pt; padding:3pt; }
.qr-box img { display:block; }
.divider { width:100%; height:0.5pt; background:rgba(255,255,255,.2); margin:8pt 0; position:relative; }
</style>
</head>
<body>
@php
    $fields   = $card->data ?? [];
    $contact  = $card->contact;
    $design   = $card->template?->design ?? [];
    $accent   = $design['accent'] ?? '#f59e0b';
    $name     = $fields['name']    ?? $contact?->full_name ?? $card->name;
    $title    = $fields['title']   ?? $contact?->job_title ?? '';
    $company  = $fields['company'] ?? $contact?->company?->name ?? '';
    $email    = $fields['email']   ?? $contact?->email ?? '';
    $phone    = $fields['phone']   ?? $contact?->phone ?? '';
    $website  = $fields['website'] ?? '';
    $linkedin = $fields['linkedin'] ?? '';
@endphp
<div class="card-front">
    <div class="circle1"></div>
    <div class="circle2"></div>

    <div class="header">
        @if($photoBase64)
        <div class="photo-circle"><img src="{{ $photoBase64 }}"></div>
        @else
        <div class="initials-circle">{{ strtoupper(substr($name,0,1)) }}</div>
        @endif
        <div>
            <div class="name">{{ $name }}</div>
            @if($title)<div class="title">{{ $title }}</div>@endif
            @if($company)<div class="company accent">{{ $company }}</div>@endif
        </div>
    </div>

    <div class="divider"></div>

    <table style="width:100%;font-size:8pt;opacity:.9;position:relative;">
        @if($email)
        <tr><td style="width:12pt;padding:1.5pt 6pt 1.5pt 0;">&#9993;</td><td style="padding:1.5pt 0;">{{ $email }}</td>
        @if($phone)<td style="width:12pt;padding:1.5pt 6pt 1.5pt 12pt;">&#9743;</td><td style="padding:1.5pt 0;">{{ $phone }}</td>@endif
        </tr>
        @endif
        @if($website || $linkedin)
        <tr>
            @if($website)<td style="padding:1.5pt 6pt 1.5pt 0;">&#9729;</td><td style="padding:1.5pt 0;">{{ $website }}</td>@endif
            @if($linkedin)<td style="padding:1.5pt 6pt 1.5pt 12pt;">in</td><td style="padding:1.5pt 0;">{{ $linkedin }}</td>@endif
        </tr>
        @endif
    </table>

    @if($qrCode)
    <div class="qr-box">
        <img src="data:image/png;base64,{{ $qrCode }}" width="46" height="46">
    </div>
    @endif
</div>
</body>
</html>
