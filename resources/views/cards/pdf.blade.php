<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
@php
    $fields   = $card->data ?? [];
    $contact  = $card->contact;
    $design   = $card->template?->design ?? [];
    $accent   = $design['accent']     ?? '#f59e0b';
    $bgColor  = $design['bg_color']   ?? '#1e3a5f';
    $txtColor = $design['text_color'] ?? '#ffffff';

    $name     = $fields['name']    ?? $contact?->full_name ?? $card->name;
    $title    = $fields['title']   ?? $contact?->job_title ?? '';
    $company  = $fields['company'] ?? $contact?->company?->name ?? '';
    $email    = $fields['email']   ?? $contact?->email ?? '';
    $phone    = $fields['phone']   ?? $contact?->phone ?? '';
    $website  = $fields['website']  ?? '';
    $linkedin = $fields['linkedin'] ?? '';
    $address  = $fields['address']  ?? '';
    $initial  = strtoupper(substr($name, 0, 1));

    // Hex → RGB helper for rgba fallback
    $hex = ltrim($accent, '#');
    $ar  = hexdec(substr($hex,0,2));
    $ag  = hexdec(substr($hex,2,2));
    $ab  = hexdec(substr($hex,4,2));
@endphp
<style>
@page  { margin: 0; size: A4 portrait; }
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: DejaVu Sans, Arial, sans-serif; background: #ffffff; }

/* ══════════════════════════════════════════
   HEADER BAND  (coloured card section)
══════════════════════════════════════════ */
.header-band {
    width: 595pt;
    background: {{ $bgColor }};
    color: {{ $txtColor }};
    padding: 36pt 40pt 32pt 40pt;
    position: relative;
}

/* Decorative blobs — kept within band height */
.blob1 {
    position: absolute; top: -18pt; right: -18pt;
    width: 110pt; height: 110pt; border-radius: 55pt;
    background: {{ $accent }}; opacity: 0.18;
}
.blob2 {
    position: absolute; bottom: -28pt; right: 90pt;
    width: 90pt; height: 90pt; border-radius: 45pt;
    background: {{ $accent }}; opacity: 0.12;
}
.blob3 {
    position: absolute; top: 20pt; left: -20pt;
    width: 70pt; height: 70pt; border-radius: 35pt;
    background: {{ $accent }}; opacity: 0.10;
}

/* Avatar + name table */
.hdr-table { width: 100%; border-collapse: collapse; }
.hdr-avatar-cell { width: 80pt; vertical-align: middle; }
.hdr-info-cell   { vertical-align: middle; padding-left: 20pt; }

.avatar-circle {
    width: 72pt; height: 72pt; border-radius: 36pt;
    background: {{ $accent }};
    text-align: center; line-height: 72pt;
    font-size: 28pt; font-weight: 700; color: #ffffff;
}
.avatar-photo {
    width: 72pt; height: 72pt; border-radius: 36pt;
    border: 3pt solid {{ $accent }};
}

.c-name    { font-size: 22pt; font-weight: 700; line-height: 1.2; }
.c-title   { font-size: 11pt; opacity: 0.80; margin-top: 4pt; }
.c-company { font-size: 10pt; font-weight: 700; margin-top: 5pt; color: {{ $accent }}; }

/* Accent rule under header */
.accent-rule {
    width: 595pt; height: 4pt;
    background: {{ $accent }};
}

/* ══════════════════════════════════════════
   CONTENT AREA  (white section)
══════════════════════════════════════════ */
.content {
    padding: 36pt 40pt 30pt 40pt;
    position: relative;
}

.section-title {
    font-size: 8.5pt; font-weight: 700; letter-spacing: 1.5pt;
    text-transform: uppercase; color: #94a3b8;
    margin-bottom: 12pt;
}

.contact-table { width: 100%; border-collapse: collapse; }
.contact-table td { padding: 7pt 0; vertical-align: top; }
.contact-table tr { border-bottom: 0.5pt solid #f1f5f9; }

.ct-icon {
    width: 24pt;
    font-size: 12pt;
    color: {{ $accent }};
    vertical-align: middle;
}
.ct-label {
    width: 70pt;
    font-size: 8.5pt; font-weight: 700;
    color: #64748b; text-transform: uppercase;
    letter-spacing: 0.5pt; vertical-align: middle;
}
.ct-value {
    font-size: 11pt;
    color: #1e293b;
    vertical-align: middle;
}

/* QR code block — bottom right corner of content */
.qr-block {
    position: absolute;
    bottom: 30pt; right: 40pt;
    text-align: center;
}
.qr-label {
    font-size: 7pt; color: #94a3b8; letter-spacing: 0.8pt;
    text-transform: uppercase; margin-top: 5pt;
}

/* Footer bar */
.footer-bar {
    width: 595pt; height: 10pt;
    background: {{ $bgColor }};
    position: absolute; bottom: 0; left: 0;
}
</style>
</head>
<body>

{{-- ── COLOURED HEADER BAND ── --}}
<div class="header-band">
    <div class="blob1"></div>
    <div class="blob2"></div>
    <div class="blob3"></div>

    <table class="hdr-table">
        <tr>
            <td class="hdr-avatar-cell">
                @if($photoBase64)
                    <img src="{{ $photoBase64 }}" class="avatar-photo">
                @else
                    <div class="avatar-circle">{{ $initial }}</div>
                @endif
            </td>
            <td class="hdr-info-cell">
                <div class="c-name">{{ $name }}</div>
                @if($title)   <div class="c-title">{{ $title }}</div>   @endif
                @if($company) <div class="c-company">{{ $company }}</div> @endif
            </td>
        </tr>
    </table>
</div>
<div class="accent-rule"></div>

{{-- ── CONTACT DETAILS ── --}}
<div class="content">

    <div class="section-title">Contact Information</div>

    <table class="contact-table" style="width:{{ $qrCode ? '68%' : '100%' }};">
        @if($email)
        <tr>
            <td class="ct-icon">&#9993;</td>
            <td class="ct-label">Email</td>
            <td class="ct-value">{{ $email }}</td>
        </tr>
        @endif
        @if($phone)
        <tr>
            <td class="ct-icon">&#9743;</td>
            <td class="ct-label">Phone</td>
            <td class="ct-value">{{ $phone }}</td>
        </tr>
        @endif
        @if($website)
        <tr>
            <td class="ct-icon">&#127760;</td>
            <td class="ct-label">Website</td>
            <td class="ct-value">{{ $website }}</td>
        </tr>
        @endif
        @if($linkedin)
        <tr>
            <td class="ct-icon" style="font-weight:700;">in</td>
            <td class="ct-label">LinkedIn</td>
            <td class="ct-value">{{ $linkedin }}</td>
        </tr>
        @endif
        @if($address)
        <tr>
            <td class="ct-icon">&#9679;</td>
            <td class="ct-label">Address</td>
            <td class="ct-value">{{ $address }}</td>
        </tr>
        @endif
    </table>

    {{-- QR code — base64 SVG data URI, the only reliable way in DomPDF --}}
    @if($qrCode)
    <div class="qr-block">
        <img src="data:image/svg+xml;base64,{{ base64_encode($qrCode) }}"
             width="110" height="110"
             style="border:6pt solid #ffffff;border-radius:8pt;box-shadow:0 0 0 1pt #e2e8f0;">
        <div class="qr-label">Scan to save contact</div>
    </div>
    @endif

    <div class="footer-bar"></div>
</div>

</body>
</html>
