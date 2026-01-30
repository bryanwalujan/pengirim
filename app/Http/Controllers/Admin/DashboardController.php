<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Service;
use App\Models\StatusSurat;
use App\Models\SuratPindah;
use Illuminate\Http\Request;
use App\Models\TrackingSurat;
use App\Models\SuratIjinSurvey;
use App\Models\PembayaranUkt;
use App\Models\SuratAktifKuliah;
use App\Models\SuratCutiAkademik;
use App\Models\TahunAjaran;
use App\Models\PendaftaranSeminarProposal;
use App\Models\PendaftaranUjianHasil;
use App\Models\PeminjamanProyektor;
use App\Models\PeminjamanLaboratorium;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Permission\Models\Role;

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

        // E-Service Features Statistics - FITUR UTAMA
        $featureStats = $this->getFeatureStats();

        // Quick Actions Data
        $quickStats = $this->getQuickStats();

        return array_merge([
            'statusCounts' => $statusCounts,
            'unreadNotifications' => $unreadNotifications,
            'recentActivities' => $recentActivities,
            'analytics' => $analytics,
            'featureStats' => $featureStats,
            'quickStats' => $quickStats
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

    /**
     * Get statistics for main e-service features
     */
    private function getFeatureStats()
    {
        $currentMonth = now()->startOfMonth();

        return [
            // Layanan Surat Mahasiswa
            'surat_mahasiswa' => [
                'aktif_kuliah' => [
                    'total' => SuratAktifKuliah::count(),
                    'bulan_ini' => SuratAktifKuliah::whereMonth('created_at', now()->month)->count(),
                    'pending' => StatusSurat::where('surat_type', SuratAktifKuliah::class)->whereIn('status', ['diajukan', 'diproses'])->count(),
                ],
                'ijin_survey' => [
                    'total' => SuratIjinSurvey::count(),
                    'bulan_ini' => SuratIjinSurvey::whereMonth('created_at', now()->month)->count(),
                    'pending' => StatusSurat::where('surat_type', SuratIjinSurvey::class)->whereIn('status', ['diajukan', 'diproses'])->count(),
                ],
                'cuti_akademik' => [
                    'total' => SuratCutiAkademik::count(),
                    'bulan_ini' => SuratCutiAkademik::whereMonth('created_at', now()->month)->count(),
                    'pending' => StatusSurat::where('surat_type', SuratCutiAkademik::class)->whereIn('status', ['diajukan', 'diproses'])->count(),
                ],
                'pindah' => [
                    'total' => SuratPindah::count(),
                    'bulan_ini' => SuratPindah::whereMonth('created_at', now()->month)->count(),
                    'pending' => StatusSurat::where('surat_type', SuratPindah::class)->whereIn('status', ['diajukan', 'diproses'])->count(),
                ],
            ],

            // Layanan Skripsi
            'skripsi' => [
                'pendaftaran_sempro' => [
                    'total' => PendaftaranSeminarProposal::count(),
                    'bulan_ini' => PendaftaranSeminarProposal::whereMonth('created_at', now()->month)->count(),
                    'pending' => PendaftaranSeminarProposal::where('status', 'pending')->count(),
                ],
                'pendaftaran_ujian' => [
                    'total' => PendaftaranUjianHasil::count(),
                    'bulan_ini' => PendaftaranUjianHasil::whereMonth('created_at', now()->month)->count(),
                    'pending' => PendaftaranUjianHasil::where('status', 'pending')->count(),
                ],
            ],

            // Layanan Peminjaman
            'peminjaman' => [
                'proyektor' => [
                    'total' => PeminjamanProyektor::count(),
                    'aktif' => PeminjamanProyektor::where('status', 'disetujui')->where('tanggal_kembali', '>=', now())->count(),
                    'pending' => PeminjamanProyektor::where('status', 'pending')->count(),
                ],
                'laboratorium' => [
                    'total' => PeminjamanLaboratorium::count(),
                    'aktif' => PeminjamanLaboratorium::where('status', 'disetujui')->whereDate('tanggal_peminjaman', '>=', now()->toDateString())->count(),
                    'pending' => PeminjamanLaboratorium::where('status', 'pending')->count(),
                ],
            ],

            // Manajemen Akademik
            'akademik' => [
                'mahasiswa' => User::role('mahasiswa')->count(),
                'dosen' => User::role('dosen')->count(),
                'staff' => User::role('staff')->count(),
                'tahun_ajaran_aktif' => TahunAjaran::aktif()->first()?->tahun ?? 'Tidak ada',
                'ukt_belum_lunas' => PembayaranUkt::where('status', '!=', 'lunas')->count(),
            ],

            // Total perlu perhatian
            'need_attention' => [
                'surat_pending' => StatusSurat::whereIn('status', ['diajukan', 'diproses'])->count(),
                'sempro_pending' => PendaftaranSeminarProposal::where('status', 'pending')->count(),
                'ujian_pending' => PendaftaranUjianHasil::where('status', 'pending')->count(),
                'peminjaman_pending' => PeminjamanProyektor::where('status', 'pending')->count() + PeminjamanLaboratorium::where('status', 'pending')->count(),
            ]
        ];
    }

    /**
     * Get quick statistics for dashboard cards
     */
    private function getQuickStats()
    {
        return [
            'total_mahasiswa' => User::role('mahasiswa')->count(),
            'total_dosen' => User::role('dosen')->count(),
            'total_staff' => User::role('staff')->count(),
            'total_layanan' => Service::where('is_active', true)->count(),
            'total_surat_bulan_ini' => StatusSurat::whereMonth('created_at', now()->month)->count(),
            'total_pendaftaran_skripsi' => PendaftaranSeminarProposal::count() + PendaftaranUjianHasil::count(),
        ];
    }
}