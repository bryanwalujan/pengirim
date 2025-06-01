<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\StatusSurat;
use App\Models\TrackingSurat;
use App\Models\SuratAktifKuliah;
use App\Models\SuratIjinSurvey;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('view dashboard');

        // Get user and their role
        $user = User::find(Auth::id());
        $isStaff = $user->hasRole('staff');
        $isKaprodi = $user->hasRole('dosen') && str_contains(strtolower($user->jabatan), 'koordinator program studi');
        $isPimpinan = $user->hasRole('dosen') && (str_contains(strtolower($user->jabatan), 'pimpinan jurusan') || str_contains(strtolower($user->jabatan), 'ptik'));

        // Summary of letter statuses
        $statusCounts = [
            'diajukan' => StatusSurat::where('status', 'diajukan')->count(),
            'diproses' => StatusSurat::where('status', 'diproses')->count(),
            'disetujui_kaprodi' => StatusSurat::where('status', 'disetujui_kaprodi')->count(),
            'disetujui' => StatusSurat::where('status', 'disetujui')->count(),
            'ditolak' => StatusSurat::where('status', 'ditolak')->count(),
            'siap_diambil' => StatusSurat::where('status', 'siap_diambil')->count(),
        ];

        // Unread notifications
        $unreadNotifications = $user->unreadNotifications()
            ->whereIn('type', ['App\\Notifications\\SuratNeedApprovalNotification', 'App\\Notifications\\SuratTakenNotification'])
            ->count();

        // Recent activities (limited to 10)
        $recentActivities = TrackingSurat::with(['mahasiswa', 'surat'])
            ->latest()
            ->take(10)
            ->get();

        // Data for chart (submissions per month for each service)
        $months = [];
        $aktifKuliahCounts = [];
        $ijinSurveyCounts = [];
        $cutiAkademikCounts = [];
        $pindahCounts = [];

        // Fetch data for the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months[] = $month->format('M Y');
            $aktifKuliahCounts[] = SuratAktifKuliah::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $ijinSurveyCounts[] = SuratIjinSurvey::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $cutiAkademikCounts[] = 0; // Replace with actual query when model is available
            $pindahCounts[] = 0; // Replace with actual query when model is available
        }

        return view('admin.dashboard.index', compact(
            'statusCounts',
            'unreadNotifications',
            'recentActivities',
            'months',
            'aktifKuliahCounts',
            'ijinSurveyCounts',
            'cutiAkademikCounts',
            'pindahCounts'
        ));
    }
}