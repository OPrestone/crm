<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
@php
    $fields   = $card->data ?? [];
    $contact  = $card->contact;
    $design   = $card->template?->design ?? [];

    /* All seeded templates use 'accent_color' — fall back to 'accent' for custom cards */
    $accent   = $design['accent_color'] ?? $design['accent']       ?? '#f59e0b';
    $bgColor  = $design['bg_color']                                ?? '#1e3a5f';
    $txtColor = $design['text_color']                              ?? '#ffffff';
    $divColor = $design['divider_color']  ?? $accent;

    $name     = $fields['name']    ?? $contact?->full_name        ?? $card->name;
    $title    = $fields['title']   ?? $contact?->job_title        ?? '';
    $company  = $fields['company'] ?? $contact?->company?->name   ?? '';
    $email    = $fields['email']   ?? $contact?->email            ?? '';
    $phone    = $fields['phone']   ?? $contact?->phone            ?? '';
    $website  = $fields['website']  ?? '';
    $linkedin = $fields['linkedin'] ?? '';
    $initial  = strtoupper(substr($name, 0, 1));
@endphp
<style>
/* ── Page size = ISO ID-1 / CR80 business card, zero margins ── */
@page { margin: 0; size: 241.89pt 153.07pt landscape; }

/* Fill background on both html + body so no white edges show */
html {
    margin: 0; padding: 0;
    background: {{ $bgColor }};
}
body {
    margin: 0; padding: 0;
    background: {{ $bgColor }};
    color: {{ $txtColor }};
    font-family: DejaVu Sans, Arial, sans-serif;
    width: 241.89pt;
    height: 153.07pt;
}

/* ── Full-bleed background layer ── */
.bg-fill {
    position: absolute;
    top: 0; left: 0;
    width: 241.89pt;
    height: 153.07pt;
    background: {{ $bgColor }};
}

/* ── Decorative circles (match show.blade.php exactly) ── */
/* show: top:-30px right:-30px 120px blob @ .20 opacity */
/* show: bottom:-40px left:-20px 160px blob @ .10 opacity */
/* Scale factor ≈ 241.89/380 = 0.637                       */
.blob1 {
    position: absolute;
    top: -19pt; right: -19pt;
    width: 76pt; height: 76pt; border-radius: 38pt;
    background: {{ $accent }}; opacity: 0.20;
}
.blob2 {
    position: absolute;
    bottom: -25pt; left: -13pt;
    width: 102pt; height: 102pt; border-radius: 51pt;
    background: {{ $accent }}; opacity: 0.10;
}

/* ── Outer content wrapper ── */
.wrap {
    position: relative;
    width: 241.89pt;
    height: 153.07pt;
    padding: 15pt 16pt 13pt 16pt;
}

/* ── Header: avatar cell | info cell (table, no flex) ── */
.hdr       { border-collapse: collapse; width: 100%; margin-bottom: 7pt; }
.av-cell   { width: 50pt; vertical-align: middle; }
.info-cell { vertical-align: middle; padding-left: 10pt; }

/* Initials circle */
.av-circle {
    width: 42pt; height: 42pt; border-radius: 21pt;
    background: {{ $accent }};
    border: 2.5pt solid {{ $accent }};
    text-align: center; line-height: 42pt;
    font-size: 17pt; font-weight: 700; color: #ffffff;
}
/* Photo circle */
.av-photo {
    width: 42pt; height: 42pt; border-radius: 21pt;
    border: 2.5pt solid {{ $accent }};
}

.c-name    { font-size: 13.5pt; font-weight: 700; line-height: 1.2; color: {{ $txtColor }}; }
.c-title   { font-size: 7.5pt;  opacity: 0.80; margin-top: 2pt;  color: {{ $txtColor }}; }
.c-company { font-size: 7pt;    font-weight: 700; margin-top: 2pt; color: {{ $accent }}; }

/* ── Divider (uses template's divider_color) ── */
.divider {
    height: 0.5pt;
    background: {{ $divColor }};
    opacity: 0.35;
    margin-bottom: 6pt;
}

/* ── Contact info: 2-column table (mirrors the 2-col grid in show view) ── */
/* Max-width leaves ~56pt clear on the right for the QR box                  */
.ct        { border-collapse: collapse; width: 170pt; }
.ct td     { padding: 1.2pt 0; vertical-align: middle; font-size: 7pt; color: {{ $txtColor }}; opacity: 0.90; }
.ct .ico   { width: 11pt; color: {{ $accent }}; opacity: 1; }
.ct .val   { padding-right: 8pt; }
.ct .sep   { width: 4pt; }

/* ── QR code box ── */
.qr-box {
    position: absolute;
    bottom: 11pt; right: 12pt;
    background: #ffffff;
    border-radius: 5pt;
    padding: 3pt;
    width: 46pt; height: 46pt;
}
</style>
</head>
<body>

{{-- Full-bleed background (ensures edge-to-edge color, even past default DomPDF insets) --}}
<div class="bg-fill"></div>

<div class="wrap">
    <div class="blob1"></div>
    <div class="blob2"></div>

    {{-- Header --}}
    <table class="hdr">
        <tr>
            <td class="av-cell">
                @if($photoBase64)
                    <img src="{{ $photoBase64 }}" class="av-photo">
                @else
                    <div class="av-circle">{{ $initial }}</div>
                @endif
            </td>
            <td class="info-cell">
                <div class="c-name">{{ $name }}</div>
                @if($title)   <div class="c-title">{{ $title }}</div>   @endif
                @if($company) <div class="c-company">{{ $company }}</div> @endif
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    {{-- Contact: two columns matching the on-screen 2-col grid --}}
    <table class="ct">
        @if($email || $phone)
        <tr>
            @if($email)
                <td class="ico">&#9993;</td>
                <td class="val">{{ $email }}</td>
            @else
                <td class="ico"></td><td class="val"></td>
            @endif
            <td class="sep"></td>
            @if($phone)
                <td class="ico">&#9743;</td>
                <td class="val">{{ $phone }}</td>
            @else
                <td class="ico"></td><td class="val"></td>
            @endif
        </tr>
        @endif
        @if($website || $linkedin)
        <tr>
            @if($website)
                <td class="ico">&#127760;</td>
                <td class="val">{{ $website }}</td>
            @else
                <td class="ico"></td><td class="val"></td>
            @endif
            <td class="sep"></td>
            @if($linkedin)
                <td class="ico" style="font-weight:700;">in</td>
                <td class="val">{{ $linkedin }}</td>
            @else
                <td class="ico"></td><td class="val"></td>
            @endif
        </tr>
        @endif
    </table>

    {{-- QR code: base64 SVG data URI — the only reliable method in DomPDF --}}
    @if($qrCode)
    <div class="qr-box">
        <img src="data:image/svg+xml;base64,{{ base64_encode($qrCode) }}"
             width="40" height="40">
    </div>
    @endif

</div>
</body>
</html>
