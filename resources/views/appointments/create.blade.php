@extends('layouts.app')
@section('title', 'Schedule Appointment')
@section('page-title', 'Schedule Appointment')

@section('content')
<div class="page-header">
    <div>
        <h1>Schedule Appointment</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('appointments.index') }}">Calendar</a></li>
            <li class="breadcrumb-item active">Schedule</li>
        </ol></nav>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header fw-600"><i class="bi bi-calendar-plus me-2 text-primary"></i>Appointment Details</div>
            <div class="card-body">
                <form method="POST" action="{{ route('appointments.store') }}">
                    @csrf
                    @include('appointments._form', ['appointment' => null])
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Schedule</button>
                        <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
