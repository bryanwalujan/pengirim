<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\StatusSurat;
use App\Models\SuratPindah;
use Illuminate\Http\Request;
use App\Models\TrackingSurat;
use App\Models\SuratIjinSurvey;
use App\Models\SuratAktifKuliah;
use App\Models\SuratCutiAkademik;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('view dashboard');

        // Cache data untuk performa yang lebih baik
        $data = Cache::remember('dashboard_data_' . Auth::id(), now()->addMinutes(5), function () {
            return $this->getDashboardData();
        });

        return view('admin.dashboard.index', $data);
    }

    private function getDashboardData()
    {
        $user = User::find(Auth::id());

        // Summary of letter statuses dengan query yang lebih efisien
        $statusCounts = StatusSurat::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Ensure all status counts exist
        $defaultCounts = [
            'diajukan' => 0,
            'diproses' => 0,
            'disetujui_kaprodi' => 0,
            'disetujui' => 0,
            'ditolak' => 0,
            'siap_diambil' => 0,
            'diambil' => 0
        ];
        $statusCounts = array_merge($defaultCounts, $statusCounts);

        // Unread notifications
        $unreadNotifications = $user->unreadNotifications()
            ->whereIn('type', [
                'App\\Notifications\\SuratNeedApprovalNotification',
                'App\\Notifications\\SuratTakenNotification'
            ])
            ->count();

        // Recent activities - DEFAULT: Minggu ini, maksimal 12 data
        $weekStart = now()->startOfWeek(); // Senin minggu ini
        $recentActivities = TrackingSurat::with(['mahasiswa:id,name,nim'])
            ->where('created_at', '>=', $weekStart)
            ->latest()
            ->take(8) // Maksimal 12 aktivitas
            ->get();

        // Data untuk chart dengan query yang lebih efisien
        $chartData = $this->getChartData();

        // Statistics untuk analytic cards
        $analytics = $this->getAnalytics();

        return array_merge([
            'statusCounts' => $statusCounts,
            'unreadNotifications' => $unreadNotifications,
            'recentActivities' => $recentActivities,
            'analytics' => $analytics
        ], $chartData);
    }

    private function getChartData()
    {
        $months = [];
        $aktifKuliahCounts = [];
        $ijinSurveyCounts = [];
        $cutiAkademikCounts = [];
        $pindahCounts = [];

        // Data untuk 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months[] = $month->format('M Y');

            $startOfMonth = $month->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();

            $aktifKuliahCounts[] = SuratAktifKuliah::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            $ijinSurveyCounts[] = SuratIjinSurvey::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            $cutiAkademikCounts[] = SuratCutiAkademik::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            $pindahCounts[] = SuratPindah::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        }

        return [
            'months' => $months,
            'aktifKuliahCounts' => $aktifKuliahCounts,
            'ijinSurveyCounts' => $ijinSurveyCounts,
            'cutiAkademikCounts' => $cutiAkademikCounts,
            'pindahCounts' => $pindahCounts
        ];
    }

    private function getAnalytics()
    {
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        return [
            'todaySubmissions' => TrackingSurat::whereDate('created_at', $today)->count(),
            'yesterdaySubmissions' => TrackingSurat::whereDate('created_at', $yesterday)->count(),
            'thisMonthSubmissions' => TrackingSurat::whereDate('created_at', '>=', $thisMonth)->count(),
            'lastMonthSubmissions' => TrackingSurat::whereBetween('created_at', [$lastMonth, $thisMonth])->count(),
            'averageProcessingTime' => $this->getAverageProcessingTime(),
            'completionRate' => $this->getCompletionRate()
        ];
    }

    private function getAverageProcessingTime()
    {
        // Hitung rata-rata waktu pemrosesan dari diajukan ke disetujui
        $completedSurats = StatusSurat::where('status', 'disetujui')
            ->whereMonth('updated_at', now()->month)
            ->get();

        if ($completedSurats->isEmpty()) {
            return 0;
        }

        $totalDays = 0;
        foreach ($completedSurats as $surat) {
            $created = TrackingSurat::where('surat_id', $surat->surat_id)
                ->where('surat_type', $surat->surat_type)
                ->where('aksi', 'diajukan')
                ->first();

            if ($created) {
                $totalDays += $created->created_at->diffInDays($surat->updated_at);
            }
        }

        return round($totalDays / $completedSurats->count(), 1);
    }

    private function getCompletionRate()
    {
        $totalSurats = StatusSurat::whereMonth('created_at', now()->month)->count();
        $completedSurats = StatusSurat::whereIn('status', ['disetujui', 'siap_diambil', 'diambil'])
            ->whereMonth('updated_at', now()->month)
            ->count();

        return $totalSurats > 0 ? round(($completedSurats / $totalSurats) * 100, 1) : 0;
    }
}