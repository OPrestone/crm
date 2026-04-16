@extends('layouts.app')
@section('title', 'My Profile')
@section('page-title', 'My Profile')
@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card text-center">
            <div class="card-body pt-4">
                <div class="avatar-circle mx-auto mb-3" style="width:72px;height:72px;font-size:24px;">{{ auth()->user()->initials }}</div>
                <h5 class="fw-700 mb-1">{{ auth()->user()->name }}</h5>
                <p class="text-muted mb-2">{{ auth()->user()->email }}</p>
                <span class="badge bg-primary">{{ auth()->user()->roles->first()?->name ?? 'staff' }}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Profile Information</h5></div>
            <div class="card-body px-4">
                <form method="POST" action="{{ route('profile.update') }}">
                @csrf @method('patch')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-600">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update Profile</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-transparent pt-4 px-4"><h5 class="fw-700 mb-0">Change Password</h5></div>
            <div class="card-body px-4">
                <form method="POST" action="{{ route('password.update') }}">
                @csrf @method('put')
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-600">Current Password</label><input type="password" name="current_password" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label fw-600">New Password</label><input type="password" name="password" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label fw-600">Confirm</label><input type="password" name="password_confirmation" class="form-control" required></div>
                    <div class="col-12"><button type="submit" class="btn btn-warning"><i class="bi bi-key me-1"></i>Update Password</button></div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
