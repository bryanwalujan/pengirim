<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        // Ambil notifikasi pengguna (termasuk yang sudah dibaca dan belum)
        $notifications = User::find(Auth::id())->notifications()->latest()->paginate(15);
        $unreadCount = User::find(Auth::id())->unreadNotifications()->count();

        return view('admin.notifications.index', compact('notifications', 'unreadCount'));
    }

    public function read($notificationId)
    {
        $notification = User::find(Auth::id())->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            return redirect($notification->data['url'] ?? route('admin.notifications.index'));
        }
        return redirect()->route('admin.notifications.index')->with('error', 'Notifikasi tidak ditemukan.');
    }
}