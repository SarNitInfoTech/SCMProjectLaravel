<?php

namespace App\Http\Controllers;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
   public function index()
{
    // Get all notifications ordered by latest
    $notifications = Notification::latest()->paginate(15); // Adjust per page as needed

    // Count unread notifications for badge or header info
    $unreadCount = Notification::where('is_read', false)->count();

    return view('pages.user.notification.notification', compact('notifications', 'unreadCount'));
}
public function markAllRead()
{
    Notification::where('is_read', false)->update(['is_read' => true]);

    return redirect()->back()->with('success', 'All notifications marked as read.');
}

}
