@extends('layouts.app')
@section('title', 'Edit Ticket')
@section('page-title', 'Edit Ticket')

@section('content')
<div class="page-header">
    <div>
        <h1>Edit: {{ $ticket->ticket_number }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tickets.index') }}">Tickets</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol></nav>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header fw-600"><i class="bi bi-pencil me-2 text-warning"></i>Edit Ticket</div>
            <div class="card-body">
                <form method="POST" action="{{ route('tickets.update', $ticket) }}">
                    @csrf @method('PUT')
                    @include('tickets._form', ['ticket' => $ticket])
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Update Ticket</button>
                        <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
