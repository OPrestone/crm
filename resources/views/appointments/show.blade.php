@extends('layouts.app')
@section('title', $appointment->title)
@section('page-title', 'Appointment')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $appointment->title }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('appointments.index') }}">Calendar</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($appointment->title, 30) }}</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <form method="POST" action="{{ route('appointments.destroy', $appointment) }}" onsubmit="return confirm('Delete this appointment?')">
            @csrf @method('DELETE')<button class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>Delete</button>
        </form>
    </div>
</div>

<div class="row g-4 justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header fw-600 d-flex align-items-center gap-2">
                <div class="rounded-circle" style="width:14px;height:14px;background:{{ $appointment->color }};flex-shrink:0;"></div>
                <span>{{ $appointment->title }}</span>
                <span class="badge bg-{{ $appointment->status_badge }}-subtle text-{{ $appointment->status_badge }} ms-auto">{{ ucwords(str_replace('_',' ',$appointment->status)) }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Start</div>
                        <div class="fw-600">{{ $appointment->start_at->format('d M Y, H:i') }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">End</div>
                        <div class="fw-600">{{ $appointment->end_at->format('d M Y, H:i') }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Duration</div>
                        <div class="fw-600">{{ $appointment->duration_minutes }} min</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Type</div>
                        <div class="fw-600">{{ ucwords(str_replace('_',' ',$appointment->type)) }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Location</div>
                        <div>{{ $appointment->location ?? '—' }}</div>
                    </div>
                    @if($appointment->contact)
                    <div class="col-md-6">
                        <div class="text-muted small">Contact</div>
                        <a href="{{ route('contacts.show', $appointment->contact) }}" class="fw-600 text-decoration-none">{{ $appointment->contact->full_name }}</a>
                    </div>
                    @endif
                    @if($appointment->description)
                    <div class="col-12">
                        <div class="text-muted small">Notes</div>
                        <div class="border rounded p-3 bg-light">{{ $appointment->description }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
