@extends('layouts.app')
@section('title', 'Edit Goal')
@section('page-title', 'Edit Goal')

@section('content')
<div class="page-header">
    <div>
        <h1>Edit: {{ $goal->title }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('goals.index') }}">Goals</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol></nav>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header fw-600"><i class="bi bi-pencil me-2 text-warning"></i>Edit Goal</div>
            <div class="card-body">
                <form method="POST" action="{{ route('goals.update', $goal) }}">
                    @csrf @method('PUT')
                    @include('goals._form', ['goal' => $goal])
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Update Goal</button>
                        <a href="{{ route('goals.show', $goal) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
