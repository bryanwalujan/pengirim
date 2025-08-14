<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = User::find(Auth::id());

        // Filter berdasarkan status
        $filter = $request->get('filter', 'all'); // all, read, unread

        $query = $user->notifications()->latest();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->paginate(10);
        $unreadCount = $user->unreadNotifications()->count();
        $totalCount = $user->notifications()->count();

        return view('admin.notifications.index', compact(
            'notifications',
            'unreadCount',
            'totalCount',
            'filter'
        ));
    }

    public function read($notificationId)
    {
        $user = User::find(Auth::id());
        $notification = $user?->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();

            // Redirect ke URL yang ditentukan atau kembali ke halaman notifikasi
            $redirectUrl = $notification->data['url'] ?? route('admin.notifications.index');
            return redirect($redirectUrl)->with('success', 'Notifikasi telah ditandai sebagai dibaca.');
        }

        return redirect()->route('admin.notifications.index')
            ->with('error', 'Notifikasi tidak ditemukan.');
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }

    public function delete($notificationId)
    {
        $user = User::find(Auth::id());
        $notification = $user?->notifications()->find($notificationId);

        if ($notification) {
            $notification->delete();
            return redirect()->route('admin.notifications.index')
                ->with('success', 'Notifikasi berhasil dihapus.');
        }

        return redirect()->route('admin.notifications.index')
            ->with('error', 'Notifikasi tidak ditemukan.');
    }
}