<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications
     */
    public function index(Request $request)
    {
        $user = User::find(Auth::id());

        $filter = $request->get('filter', 'all');
        $type = $request->get('type', 'all');

        $query = $user->notifications()->latest();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        // PERBAIKAN: Filter berdasarkan type notification yang benar
        if ($type === 'surat') {
            $query->whereIn('type', [
                'App\Notifications\SuratTakenNotification',
                'App\Notifications\SuratNeedApprovalNotification',
            ]);
        } elseif ($type === 'komisi') {
            // PERBAIKAN: Gunakan namespace lengkap yang benar
            $query->whereIn('type', [
                'App\Notifications\KomisiProposalNeedApprovalNotification',
                'App\Notifications\KomisiHasilNeedApprovalNotification', // ✅ PERBAIKI INI
            ]);
        }

        $notifications = $query->paginate(15);

        $unreadCount = $user->unreadNotifications()->count();
        $totalCount = $user->notifications()->count();

        $unreadSuratCount = $user->unreadNotifications()
            ->whereIn('type', [
                'App\Notifications\SuratTakenNotification',
                'App\Notifications\SuratNeedApprovalNotification',
            ])
            ->count();

        // PERBAIKAN: Hitung Komisi Proposal DAN Komisi Hasil dengan namespace yang benar
        $unreadKomisiCount = $user->unreadNotifications()
            ->whereIn('type', [
                'App\Notifications\KomisiProposalNeedApprovalNotification',
                'App\Notifications\KomisiHasilNeedApprovalNotification', // ✅ PERBAIKI INI
            ])
            ->count();

        return view('admin.notifications.index', compact(
            'notifications',
            'unreadCount',
            'totalCount',
            'unreadSuratCount',
            'unreadKomisiCount',
            'filter',
            'type'
        ));
    }

    /**
     * Mark as read and stay on page (untuk button "Baca")
     */
    public function markAsReadAndStay($notificationId)
    {
        try {
            $user = User::find(Auth::id());
            $notification = $user->notifications()->find($notificationId);

            if ($notification) {
                $notification->markAsRead();

                return redirect()->back()->with('success', 'Notifikasi ditandai sebagai dibaca.');
            }

            return redirect()->back()->with('error', 'Notifikasi tidak ditemukan.');

        } catch (\Exception $e) {
            Log::error('Error marking notification as read', [
                'notification_id' => $notificationId,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan.');
        }
    }

    /**
     * Mark specific notification as read and redirect
     */
    public function readAndRedirect($notificationId)
    {
        try {
            $user = User::find(Auth::id());
            $notification = $user->notifications()->find($notificationId);

            if (!$notification) {
                return redirect()->route('admin.notifications.index')
                    ->with('error', 'Notifikasi tidak ditemukan.');
            }

            // Mark as read FIRST
            $notification->markAsRead();

            // Get notification data
            $data = $notification->data;
            $notificationType = $notification->type;

            Log::info('Notification redirect', [
                'notification_id' => $notificationId,
                'type' => $notificationType,
                'data' => $data,
            ]);

            // Default URL
            $url = route('admin.dashboard.index');

            // Determine URL based on notification type - PERBAIKAN
            switch ($notificationType) {
                case 'App\Notifications\KomisiProposalNeedApprovalNotification':
                    $komisiId = $data['komisi_proposal_id'] ?? null;
                    if ($komisiId) {
                        $url = route('admin.komisi-proposal.show', $komisiId);
                    }
                    break;

                case 'App\Notifications\KomisiHasilNeedApprovalNotification':
                    // PERBAIKAN: Handler untuk Komisi Hasil
                    $komisiHasilId = $data['komisi_hasil_id'] ?? null;
                    if ($komisiHasilId) {
                        $url = route('admin.komisi-hasil.show', $komisiHasilId);
                    }
                    break;

                case 'App\Notifications\SuratNeedApprovalNotification':
                case 'App\Notifications\SuratTakenNotification':
                    $url = $data['url'] ?? route('admin.dashboard.index');
                    break;

                default:
                    Log::warning('Unknown notification type for redirect', [
                        'type' => $notificationType
                    ]);
                    break;
            }

            Log::info('Redirecting to', ['url' => $url]);

            return redirect($url);

        } catch (\Exception $e) {
            Log::error('Error in readAndRedirect', [
                'notification_id' => $notificationId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('admin.notifications.index')
                ->with('error', 'Terjadi kesalahan saat memproses notifikasi.');
        }
    }

    /**
     * Mark specific notification as read (AJAX)
     */
    public function markAsRead($notificationId)
    {
        try {
            $user = User::find(Auth::id());
            $notification = $user?->notifications()->find($notificationId);

            if ($notification) {
                $notification->markAsRead();

                return response()->json([
                    'success' => true,
                    'message' => 'Notifikasi ditandai sebagai dibaca.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Notifikasi tidak ditemukan.'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error marking notification as read', [
                'notification_id' => $notificationId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan.'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead()
    {
        try {
            $user = Auth::user();
            $user->unreadNotifications->markAsRead();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Semua notifikasi telah ditandai sebagai dibaca.'
                ]);
            }

            return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menandai notifikasi sebagai dibaca.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal menandai notifikasi sebagai dibaca.');
        }
    }

    /**
     * Delete specific notification
     */
    public function delete($notificationId)
    {
        try {
            $user = User::find(Auth::id());
            $notification = $user?->notifications()->find($notificationId);

            if ($notification) {
                $notification->delete();

                if (request()->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Notifikasi berhasil dihapus.'
                    ]);
                }

                return redirect()->route('admin.notifications.index')
                    ->with('success', 'Notifikasi berhasil dihapus.');
            }

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notifikasi tidak ditemukan.'
                ], 404);
            }

            return redirect()->route('admin.notifications.index')
                ->with('error', 'Notifikasi tidak ditemukan.');

        } catch (\Exception $e) {
            Log::error('Error deleting notification', [
                'notification_id' => $notificationId,
                'error' => $e->getMessage(),
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan.'
                ], 500);
            }

            return redirect()->route('admin.notifications.index')
                ->with('error', 'Terjadi kesalahan.');
        }
    }

    /**
     * Get unread notification count (AJAX)
     */
    public function getUnreadCount()
    {
        try {
            $user = User::find(Auth::id());

            if (!$user) {
                return response()->json([
                    'total' => 0,
                    'surat' => 0,
                    'komisi' => 0,
                ]);
            }

            $totalUnread = $user->unreadNotifications()->count();
            $suratUnread = $user->unreadNotifications()
                ->whereIn('type', [
                    'App\Notifications\SuratTakenNotification',
                    'App\Notifications\SuratNeedApprovalNotification',
                ])
                ->count();

            // PERBAIKI INI - Hitung kedua tipe komisi
            $komisiUnread = $user->unreadNotifications()
                ->whereIn('type', [
                    'App\Notifications\KomisiProposalNeedApprovalNotification',
                    'App\Notifications\KomisiHasilNeedApprovalNotification',
                ])
                ->count();

            return response()->json([
                'total' => $totalUnread,
                'surat' => $suratUnread,
                'komisi' => $komisiUnread,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting unread notification count: ' . $e->getMessage());

            return response()->json([
                'total' => 0,
                'surat' => 0,
                'komisi' => 0,
            ]);
        }
    }
}