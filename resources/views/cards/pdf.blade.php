<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
@php
    $fields   = $card->data ?? [];
    $contact  = $card->contact;
    $design   = $card->template?->design ?? [];

    /* Support both 'accent' and 'accent_color' key names across templates */
    $accent   = $design['accent']      ?? $design['accent_color']  ?? '#f59e0b';
    $bgColor  = $design['bg_color']    ?? '#1e3a5f';
    $txtColor = $design['text_color']  ?? '#ffffff';

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
/* ─── Page = exact business card (ISO ID-1 / CR80) ─────────────────────── */
@page { margin: 0; size: 241.89pt 153.07pt; }
html, body {
    margin: 0; padding: 0;
    width: 241.89pt; height: 153.07pt;
    font-family: DejaVu Sans, Arial, sans-serif;
    background: {{ $bgColor }};
    color: {{ $txtColor }};
}

/* ─── Outer wrapper ─────────────────────────────────────────────────────── */
.wrap {
    width: 241.89pt;
    height: 153.07pt;
    position: relative;
    padding: 14pt 16pt 13pt 16pt;
}

/* ─── Decorative circles (match the show view blobs) ───────────────────── */
.blob1 {
    position: absolute; top: -18pt; right: -18pt;
    width: 78pt; height: 78pt; border-radius: 39pt;
    background: {{ $accent }}; opacity: 0.20;
}
.blob2 {
    position: absolute; bottom: -30pt; left: -14pt;
    width: 108pt; height: 108pt; border-radius: 54pt;
    background: {{ $accent }}; opacity: 0.12;
}

/* ─── Header: avatar | name block (table, not flex) ────────────────────── */
.hdr { border-collapse: collapse; width: 100%; margin-bottom: 7pt; }
.av-cell   { width: 48pt; vertical-align: middle; }
.info-cell { vertical-align: middle; padding-left: 10pt; }

/* Avatar initials circle */
.av-circle {
    width: 42pt; height: 42pt; border-radius: 21pt;
    background: {{ $accent }};
    text-align: center; line-height: 42pt;
    font-size: 17pt; font-weight: 700; color: #ffffff;
    border: 2pt solid {{ $accent }};
}
/* Avatar photo */
.av-photo {
    width: 42pt; height: 42pt; border-radius: 21pt;
    border: 2.5pt solid {{ $accent }};
}

.c-name    { font-size: 13pt; font-weight: 700; line-height: 1.2; }
.c-title   { font-size: 7.5pt; opacity: 0.80; margin-top: 1.5pt; }
.c-company { font-size: 7pt; font-weight: 700; margin-top: 2pt; color: {{ $accent }}; }

/* ─── Divider ───────────────────────────────────────────────────────────── */
.divider { height: 0.5pt; background: rgba(255,255,255,0.22); margin-bottom: 6pt; }

/* ─── Contact info — 2-column table matching the show grid ─────────────── */
/*   Stops short of the QR area (last ~54pt of width)                       */
.ct {
    border-collapse: collapse;
    width: 172pt; /* leaves 54pt for QR box on the right */
}
.ct td { padding: 1.2pt 0; vertical-align: middle; font-size: 7pt; opacity: 0.90; }
.ct .ico { width: 11pt; color: {{ $accent }}; }
.ct .val { padding-right: 10pt; }

/* ─── QR code ───────────────────────────────────────────────────────────── */
.qr-box {
    position: absolute; bottom: 11pt; right: 13pt;
    background: #ffffff;
    border-radius: 5pt;
    padding: 3pt;
    width: 44pt; height: 44pt;
}
</style>
</head>
<body>
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

    {{-- Contact info — 2 columns matching the on-screen grid --}}
    <table class="ct">
        @if($email || $phone)
        <tr>
            @if($email)
            <td class="ico">&#9993;</td>
            <td class="val">{{ $email }}</td>
            @endif
            @if($phone)
            <td class="ico" style="padding-left:8pt;">&#9743;</td>
            <td class="val">{{ $phone }}</td>
            @endif
        </tr>
        @endif
        @if($website || $linkedin)
        <tr>
            @if($website)
            <td class="ico">&#127760;</td>
            <td class="val">{{ $website }}</td>
            @endif
            @if($linkedin)
            <td class="ico" style="padding-left:8pt;font-weight:700;">in</td>
            <td class="val">{{ $linkedin }}</td>
            @endif
        </tr>
        @endif
    </table>

    {{-- QR code — embedded as base64 SVG data URI (works in DomPDF) --}}
    @if($qrCode)
    <div class="qr-box">
        <img src="data:image/svg+xml;base64,{{ base64_encode($qrCode) }}"
             width="38" height="38">
    </div>
    @endif

</div>
</body>
</html>
