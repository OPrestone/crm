@extends('layouts.app')
@section('title', 'Notifications')
@section('page-title', 'Notifications')
@section('content')
<div class="page-header">
    <div><h1>Notifications</h1></div>
    <form method="POST" action="{{ route('notifications.readAll') }}">
        @csrf
        <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-check-all me-1"></i>Mark All Read</button>
    </form>
</div>
<div class="card">
    <div class="card-body p-0">
        @forelse($notifications as $notif)
        <div class="d-flex align-items-start gap-3 px-4 py-3 {{ !$notif->isRead() ? 'bg-primary-soft' : '' }} {{ !$loop->last ? 'border-bottom' : '' }}">
            <div class="avatar-circle bg-{{ $notif->color }} text-white" style="width:38px;height:38px;font-size:14px;flex-shrink:0;"><i class="bi bi-{{ $notif->icon }}"></i></div>
            <div class="flex-1">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="fw-600">{{ $notif->title }}</div>
                    @if(!$notif->isRead())<span class="badge bg-primary ms-2">New</span>@endif
                </div>
                @if($notif->body)<div class="text-muted mt-1" style="font-size:13px;">{{ $notif->body }}</div>@endif
                <div class="text-muted mt-1" style="font-size:11px;"><i class="bi bi-clock"></i> {{ $notif->created_at->diffForHumans() }}</div>
            </div>
            @if(!$notif->isRead())
            <form method="POST" action="{{ route('notifications.read', $notif) }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-check"></i></button>
            </form>
            @endif
        </div>
        @empty
        <div class="empty-state"><div class="empty-icon"><i class="bi bi-bell"></i></div><h5>No notifications</h5><p class="text-muted">You're all caught up!</p></div>
        @endforelse
    </div>
    @if($notifications->hasPages())<div class="card-footer">{{ $notifications->links() }}</div>@endif
</div>
@endsection
