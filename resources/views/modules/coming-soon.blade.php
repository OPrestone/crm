@extends('layouts.app')
@section('title', $info['title'])
@section('page-title', $info['title'])

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $info['title'] }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">{{ $info['title'] }}</li>
        </ol></nav>
    </div>
    <span class="badge bg-{{ $info['plan'] === 'Enterprise' ? 'dark' : 'primary' }} px-3 py-2" style="font-size:13px;">
        <i class="bi bi-stars me-1"></i>{{ $info['plan'] }} Plan
    </span>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card text-center py-5 px-4">
            <div class="mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-{{ $info['color'] }}-subtle mb-3" style="width:80px;height:80px;">
                    <i class="bi {{ $info['icon'] }} text-{{ $info['color'] }}" style="font-size:2.5rem;"></i>
                </div>
                <h2 class="fw-700">{{ $info['title'] }}</h2>
                <p class="text-muted fs-6 mx-auto" style="max-width:500px;">{{ $info['description'] }}</p>
            </div>

            <div class="row g-3 mb-5 text-start">
                @foreach($info['features'] as $feature)
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2">
                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-{{ $info['color'] }}-subtle flex-shrink-0" style="width:28px;height:28px;">
                            <i class="bi bi-check-lg text-{{ $info['color'] }}" style="font-size:13px;"></i>
                        </div>
                        <span>{{ $feature }}</span>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="d-flex flex-column align-items-center gap-3">
                <div class="alert alert-{{ $info['color'] }} d-inline-flex align-items-center gap-2 mb-0 px-4 py-3">
                    <i class="bi bi-rocket-takeoff-fill fs-5"></i>
                    <div class="text-start">
                        <div class="fw-600">Coming Soon — Available on {{ $info['plan'] }} plan</div>
                        <div class="small">This module is available on {{ $info['plan'] }} plan and above. Ask your admin to enable it.</div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back to Dashboard</a>
                    @if(auth()->user()->isTenantAdmin())
                    <a href="{{ route('settings.index') }}" class="btn btn-primary"><i class="bi bi-gear me-1"></i>Manage Plan</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
