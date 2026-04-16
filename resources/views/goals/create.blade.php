@extends('layouts.app')
@section('title', 'New Goal')
@section('page-title', 'New Goal')

@section('content')
<div class="page-header">
    <div>
        <h1>New Goal</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('goals.index') }}">Goals</a></li>
            <li class="breadcrumb-item active">New</li>
        </ol></nav>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header fw-600"><i class="bi bi-bullseye me-2 text-success"></i>Goal Details</div>
            <div class="card-body">
                <form method="POST" action="{{ route('goals.store') }}">
                    @csrf
                    @include('goals._form', ['goal' => null])
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Create Goal</button>
                        <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
