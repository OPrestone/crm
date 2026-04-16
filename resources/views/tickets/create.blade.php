@extends('layouts.app')
@section('title', 'New Ticket')
@section('page-title', 'New Ticket')

@section('content')
<div class="page-header">
    <div>
        <h1>New Ticket</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('tickets.index') }}">Tickets</a></li>
            <li class="breadcrumb-item active">New</li>
        </ol></nav>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header fw-600"><i class="bi bi-headset me-2 text-info"></i>Ticket Details</div>
            <div class="card-body">
                <form method="POST" action="{{ route('tickets.store') }}">
                    @csrf
                    @include('tickets._form', ['ticket' => null])
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Create Ticket</button>
                        <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
