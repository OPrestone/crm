<?php

namespace App\Http\Controllers;

use App\Models\CrmNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->crmNotifications()->latest()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function readAll()
    {
        Auth::user()->crmNotifications()->whereNull('read_at')->update(['read_at' => now()]);
        return back()->with('success', 'All notifications marked as read.');
    }

    public function markRead(CrmNotification $notification)
    {
        abort_if($notification->user_id !== Auth::id(), 403);
        $notification->markAsRead();
        return back();
    }
}
