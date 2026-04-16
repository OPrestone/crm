@extends('layouts.app')
@section('title', 'Calendar')
@section('page-title', 'Calendar')

@section('content')
<div class="page-header">
    <div>
        <h1>Calendar &amp; Appointments</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Calendar</li>
        </ol></nav>
    </div>
    <a href="{{ route('appointments.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Schedule</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="stat-card">
        <div class="stat-icon bg-primary-subtle"><i class="bi bi-calendar-day text-primary"></i></div>
        <div class="stat-value">{{ $stats['today'] }}</div><div class="stat-label">Today</div>
    </div></div>
    <div class="col-6 col-md-3"><div class="stat-card">
        <div class="stat-icon bg-info-subtle"><i class="bi bi-calendar-week text-info"></i></div>
        <div class="stat-value">{{ $stats['this_week'] }}</div><div class="stat-label">This Week</div>
    </div></div>
    <div class="col-6 col-md-3"><div class="stat-card">
        <div class="stat-icon bg-warning-subtle"><i class="bi bi-clock text-warning"></i></div>
        <div class="stat-value">{{ $stats['scheduled'] }}</div><div class="stat-label">Upcoming</div>
    </div></div>
    <div class="col-6 col-md-3"><div class="stat-card">
        <div class="stat-icon bg-success-subtle"><i class="bi bi-check-circle text-success"></i></div>
        <div class="stat-value">{{ $stats['completed'] }}</div><div class="stat-label">Completed (month)</div>
    </div></div>
</div>

<div class="row g-4">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between fw-600">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('appointments.index', ['month' => $month == 1 ? 12 : $month - 1, 'year' => $month == 1 ? $year - 1 : $year]) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-chevron-left"></i></a>
                    <span style="min-width:160px;text-align:center;">{{ $start->format('F Y') }}</span>
                    <a href="{{ route('appointments.index', ['month' => $month == 12 ? 1 : $month + 1, 'year' => $month == 12 ? $year + 1 : $year]) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-chevron-right"></i></a>
                </div>
                <a href="{{ route('appointments.index', ['month' => now()->month, 'year' => now()->year]) }}" class="btn btn-sm btn-outline-primary">Today</a>
            </div>
            <div class="card-body p-0">
                @php
                    $firstDow = (int)$start->copy()->startOfMonth()->dayOfWeek;
                    $daysInMonth = $start->copy()->daysInMonth;
                    $byDay = $appointments->groupBy(fn($a) => $a->start_at->day);
                @endphp
                <div class="table-responsive">
                    <table class="table table-bordered mb-0" style="table-layout:fixed;">
                        <thead class="table-light">
                            <tr>
                                @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)
                                <th class="text-center py-2" style="font-size:12px;">{{ $d }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                        @php $day = 1; $started = false; @endphp
                        @for($row = 0; $row < 6; $row++)
                        @if($day > $daysInMonth) @break @endif
                        <tr>
                            @for($col = 0; $col < 7; $col++)
                            @php
                                if($row === 0 && $col < $firstDow) { echo '<td class="bg-light"></td>'; continue; }
                                if($day > $daysInMonth) { echo '<td class="bg-light"></td>'; continue; }
                                $isToday = now()->day === $day && now()->month === $month && now()->year === $year;
                                $dayAppts = $byDay->get($day, collect());
                            @endphp
                            <td style="height:80px;vertical-align:top;padding:4px;">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <span class="small fw-600 {{ $isToday ? 'bg-primary text-white rounded-circle d-flex align-items-center justify-content-center' : '' }}" style="{{ $isToday ? 'width:22px;height:22px;font-size:11px;' : '' }}">{{ $day }}</span>
                                </div>
                                @foreach($dayAppts->take(3) as $appt)
                                <a href="{{ route('appointments.show', $appt) }}" class="d-block text-decoration-none text-white mb-1 px-1 rounded small text-truncate" style="background:{{ $appt->color }};font-size:11px;" title="{{ $appt->title }}">
                                    {{ $appt->start_at->format('H:i') }} {{ Str::limit($appt->title, 20) }}
                                </a>
                                @endforeach
                                @if($dayAppts->count() > 3)
                                <div class="text-muted" style="font-size:10px;">+{{ $dayAppts->count() - 3 }} more</div>
                                @endif
                            </td>
                            @php $day++; @endphp
                            @endfor
                        </tr>
                        @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card">
            <div class="card-header fw-600">Upcoming</div>
            <div class="card-body p-0">
                @forelse($upcoming as $appt)
                <a href="{{ route('appointments.show', $appt) }}" class="d-block text-decoration-none p-3 border-bottom hover-bg">
                    <div class="d-flex gap-2 align-items-start">
                        <div class="rounded" style="width:4px;height:40px;background:{{ $appt->color }};flex-shrink:0;"></div>
                        <div>
                            <div class="fw-600 small">{{ Str::limit($appt->title, 30) }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ $appt->start_at->format('d M, H:i') }}</div>
                            @if($appt->contact)<div class="text-muted" style="font-size:11px;">{{ $appt->contact->full_name }}</div>@endif
                        </div>
                    </div>
                </a>
                @empty
                <div class="text-center text-muted py-4 small">No upcoming appointments</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
