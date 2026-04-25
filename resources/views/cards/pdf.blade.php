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
    $initial  = strtoupper(substr($name, 0, 1));
@endphp
<style>
@page { margin: 0; }
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: DejaVu Sans, Arial, sans-serif;
    background: {{ $bgColor }};
    color: {{ $txtColor }};
    width: 241.89pt;
    height: 153.07pt;
}

/* ── Decorative blobs (fully inside so no overflow issues) ── */
.blob1 {
    position: absolute; top: -10pt; right: -10pt;
    width: 65pt; height: 65pt; border-radius: 50%;
    background: {{ $accent }}; opacity: 0.18;
}
.blob2 {
    position: absolute; bottom: -22pt; left: -10pt;
    width: 85pt; height: 85pt; border-radius: 50%;
    background: {{ $accent }}; opacity: 0.10;
}

/* ── Outer wrapper ── */
.wrap {
    width: 241.89pt;
    height: 153.07pt;
    position: relative;
    padding: 15pt 16pt 14pt 16pt;
}

/* ── Header: avatar | name block — table cell layout ── */
.header-table { width: 100%; border-collapse: collapse; margin-bottom: 9pt; }
.avatar-cell  { width: 50pt; vertical-align: middle; }
.info-cell    { vertical-align: middle; padding-left: 11pt; }

.avatar-circle {
    width: 44pt; height: 44pt; border-radius: 22pt;
    background: {{ $accent }};
    text-align: center;
    font-size: 18pt; font-weight: 700; color: #ffffff;
    line-height: 44pt;
}
.avatar-photo {
    width: 44pt; height: 44pt; border-radius: 22pt;
    border: 2pt solid {{ $accent }};
}

.c-name    { font-size: 13.5pt; font-weight: 700; line-height: 1.25; }
.c-title   { font-size: 8pt; opacity: 0.78; margin-top: 2pt; }
.c-company { font-size: 7.5pt; font-weight: 700; margin-top: 2.5pt; color: {{ $accent }}; }

/* ── Divider ── */
.divider { height: 0.5pt; background: rgba(255,255,255,0.22); margin-bottom: 8pt; }

/* ── Contact rows ── */
.ct { width: 75%; border-collapse: collapse; font-size: 7.5pt; }
.ct td { padding: 1.5pt 0; vertical-align: middle; }
.ct .ico { width: 13pt; font-size: 8pt; }
.ct .val { padding-right: 12pt; max-width: 90pt; }

/* ── QR code ── */
.qr-box {
    position: absolute;
    bottom: 11pt; right: 13pt;
    background: #ffffff;
    border-radius: 4pt;
    padding: 3pt;
    width: 50pt; height: 50pt;
}
</style>
</head>
<body>
<div class="wrap">

    <div class="blob1"></div>
    <div class="blob2"></div>

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td class="avatar-cell">
                @if($photoBase64)
                    <img src="{{ $photoBase64 }}" class="avatar-photo">
                @else
                    <div class="avatar-circle">{{ $initial }}</div>
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

    {{-- Contact info --}}
    <table class="ct">
        @if($email || $phone)
        <tr>
            @if($email)
            <td class="ico">&#9993;</td>
            <td class="val">{{ $email }}</td>
            @endif
            @if($phone)
            <td class="ico" style="padding-left:6pt;">&#9743;</td>
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
            <td class="ico" style="padding-left:{{ $website ? '6pt' : '0' }};">in</td>
            <td class="val">{{ $linkedin }}</td>
            @endif
        </tr>
        @endif
    </table>

    {{-- QR code --}}
    @if($qrCode)
    <div class="qr-box">
        <div style="width:44pt;height:44pt;">{!! $qrCode !!}</div>
    </div>
    @endif

</div>
</body>
</html>
