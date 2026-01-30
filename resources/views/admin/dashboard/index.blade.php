{{-- filepath: /c:/laragon/www/eservice-app/resources/views/admin/dashboard/index.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Dashboard Admin')

@push('styles')
    <style>
        .stats-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .stats-card.primary {
            border-left-color: #696cff;
        }

        .stats-card.warning {
            border-left-color: #ffab00;
        }

        .stats-card.success {
            border-left-color: #71dd37;
        }

        .stats-card.danger {
            border-left-color: #ff3e1d;
        }

        .stats-card.info {
            border-left-color: #03c3ec;
        }

        .trend-indicator {
            display: flex;
            align-items: center;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        .trend-up {
            color: #71dd37;
        }

        .trend-down {
            color: #ff3e1d;
        }

        .trend-neutral {
            color: #8592a3;
        }

        .activity-timeline {
            position: relative;
            padding-left: 1.5rem;
        }

        .activity-timeline::before {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #696cff, #e7e7ff);
        }

        .activity-item {
            position: relative;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .activity-item::before {
            content: '';
            position: absolute;
            left: -1.25rem;
            top: 0.5rem;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #696cff;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #696cff;
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 1rem;
        }

        .widget-separator {
            height: 1px;
            background: linear-gradient(90deg, transparent, #ddd, transparent);
            margin: 1.5rem 0;
        }

        @media (max-width: 768px) {
            .stats-card {
                margin-bottom: 1rem;
            }

            .chart-container {
                height: 250px;
            }
        }

        /* Additional enhancements */
        .hover-shadow-sm:hover {
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
        }

        .active-scale-down:active {
            transform: scale(0.98);
        }

        .transition-base {
            transition: all 0.2s ease;
        }

        .hover-bg-light-secondary:hover {
            background-color: #e9ecef !important;
        }

        .alert-soft-warning {
            background-color: #fff2d6;
            color: #ffab00;
        }

        .alert-soft-success {
            background-color: #e8fadf;
            color: #71dd37;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header dengan Quick Actions -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Dashboard Admin</h4>
                <p class="text-muted mb-0">
                    <i class='bx bx-time-five me-1'></i>
                    Terakhir diperbarui: <span id="lastUpdated">{{ now()->format('d M Y, H:i') }}</span>
                </p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary btn-sm" onclick="refreshDashboard()">
                    <i class='bx bx-refresh me-1'></i>Refresh
                </button>
            </div>
        </div>

        <!-- Enhanced Summary Cards -->
        <div class="row mb-4">
            <!-- Total Requests Card -->
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card stats-card primary h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class='bx bx-file'></i>
                                        </span>
                                    </div>
                                    <span class="text-muted small">Total Pengajuan</span>
                                </div>
                                <h3 class="mb-0">{{ array_sum($statusCounts) }}</h3>
                                <div class="trend-indicator">
                                    @php
                                        $todayChange =
                                            $analytics['todaySubmissions'] - $analytics['yesterdaySubmissions'];
                                        $trendClass =
                                            $todayChange > 0
                                                ? 'trend-up'
                                                : ($todayChange < 0
                                                    ? 'trend-down'
                                                    : 'trend-neutral');
                                        $trendIcon =
                                            $todayChange > 0
                                                ? 'bx-trending-up'
                                                : ($todayChange < 0
                                                    ? 'bx-trending-down'
                                                    : 'bx-minus');
                                    @endphp
                                    <i class='bx {{ $trendIcon }} me-1 {{ $trendClass }}'></i>
                                    <span class="{{ $trendClass }}">
                                        {{ abs($todayChange) }} dari kemarin
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Requests Card -->
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card stats-card warning h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded bg-label-warning">
                                            <i class='bx bx-time'></i>
                                        </span>
                                    </div>
                                    <span class="text-muted small">Menunggu</span>
                                </div>
                                <h3 class="mb-0">{{ $statusCounts['diajukan'] + $statusCounts['diproses'] }}</h3>
                                <div class="trend-indicator">
                                    <i class='bx bx-info-circle me-1 text-info'></i>
                                    <span class="text-muted">{{ $statusCounts['diajukan'] }} baru</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approved Requests Card -->
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card stats-card success h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded bg-label-success">
                                            <i class='bx bx-check-circle'></i>
                                        </span>
                                    </div>
                                    <span class="text-muted small">Disetujui</span>
                                </div>
                                <h3 class="mb-0">
                                    {{ $statusCounts['disetujui_kaprodi'] + $statusCounts['disetujui'] + $statusCounts['siap_diambil'] }}
                                </h3>
                                <div class="trend-indicator">
                                    <i class='bx bx-check me-1 text-success'></i>
                                    <span class="text-muted">{{ $analytics['completionRate'] }}% selesai</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Average Processing Time Card -->
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card stats-card info h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded bg-label-info">
                                            <i class='bx bx-timer'></i>
                                        </span>
                                    </div>
                                    <span class="text-muted small">Rata-rata Proses</span>
                                </div>
                                <h3 class="mb-0">{{ $analytics['averageProcessingTime'] }}</h3>
                                <div class="trend-indicator">
                                    <i class='bx bx-calendar me-1 text-info'></i>
                                    <span class="text-muted">hari</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- E-Service Features Overview -->
        <div class="row mb-4">
            <!-- Header Section -->
            <div class="col-12 mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class='bx bx-grid-alt me-2'></i>Fitur E-Service
                    </h5>
                    <span class="text-muted small">Akses cepat ke layanan utama</span>
                </div>
            </div>

            <!-- 1. Manajemen Layanan (Surat Mahasiswa) -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm stats-card primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-md me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class='bx bx-envelope'></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0">Manajemen Layanan</h6>
                                <small class="text-muted">Administrasi Surat Mahasiswa</small>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <a href="{{ route('admin.surat-aktif-kuliah.index') }}" class="text-decoration-none">
                                    <div class="p-2 bg-light rounded text-center border active-scale-down">
                                        <h6 class="mb-0 text-primary">
                                            {{ $featureStats['surat_mahasiswa']['aktif_kuliah']['total'] }}</h6>
                                        <small class="text-muted d-block text-truncate" style="font-size: 0.7rem;">Aktif
                                            Kuliah</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('admin.surat-ijin-survey.index') }}" class="text-decoration-none">
                                    <div class="p-2 bg-light rounded text-center border active-scale-down">
                                        <h6 class="mb-0 text-info">
                                            {{ $featureStats['surat_mahasiswa']['ijin_survey']['total'] }}</h6>
                                        <small class="text-muted d-block text-truncate" style="font-size: 0.7rem;">Ijin
                                            Survey</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('admin.surat-cuti-akademik.index') }}" class="text-decoration-none">
                                    <div class="p-2 bg-light rounded text-center border active-scale-down">
                                        <h6 class="mb-0 text-warning">
                                            {{ $featureStats['surat_mahasiswa']['cuti_akademik']['total'] }}</h6>
                                        <small class="text-muted d-block text-truncate" style="font-size: 0.7rem;">Cuti
                                            Akademik</small>
                                    </div>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('admin.surat-pindah.index') }}" class="text-decoration-none">
                                    <div class="p-2 bg-light rounded text-center border active-scale-down">
                                        <h6 class="mb-0 text-success">
                                            {{ $featureStats['surat_mahasiswa']['pindah']['total'] }}</h6>
                                        <small class="text-muted d-block text-truncate"
                                            style="font-size: 0.7rem;">Pindah</small>
                                    </div>
                                </a>
                            </div>
                        </div>

                        @if ($featureStats['need_attention']['surat_pending'] > 0)
                            <div class="alert alert-soft-warning py-2 mb-0 d-flex align-items-center"
                                style="font-size: 0.8rem;">
                                <i class='bx bx-time me-2'></i>
                                <span><strong>{{ $featureStats['need_attention']['surat_pending'] }}</strong> pengajuan
                                    perlu diproses</span>
                            </div>
                        @else
                            <div class="alert alert-soft-success py-2 mb-0 d-flex align-items-center"
                                style="font-size: 0.8rem;">
                                <i class='bx bx-check-circle me-2'></i>
                                <span>Semua pengajuan telah diproses</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 2. Manajemen Skripsi -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm stats-card success">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-md me-3">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class='bx bxs-graduation'></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0">Manajemen Skripsi</h6>
                                <small class="text-muted">Sempro & Ujian Hasil</small>
                            </div>
                        </div>

                        <div class="d-flex flex-column gap-2 mb-3">
                            <!-- Sempro -->
                            <a href="{{ route('admin.pendaftaran-seminar-proposal.index') }}"
                                class="text-decoration-none text-body">
                                <div
                                    class="d-flex justify-content-between align-items-center p-2 bg-light rounded border hover-bg-light-secondary transition-base">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-xs me-2">
                                            <span class="avatar-initial rounded-circle bg-label-primary"><i
                                                    class="bx bx-book-open"></i></span>
                                        </div>
                                        <div>
                                            <small class="d-block fw-semibold">Seminar Proposal</small>
                                            <small class="text-muted"
                                                style="font-size: 0.7rem;">{{ $featureStats['skripsi']['pendaftaran_sempro']['bulan_ini'] }}
                                                bulan ini</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span
                                            class="fs-6 fw-bold">{{ $featureStats['skripsi']['pendaftaran_sempro']['total'] }}</span>
                                        @if ($featureStats['skripsi']['pendaftaran_sempro']['pending'] > 0)
                                            <span
                                                class="badge bg-danger rounded-pill ms-1 pt-1">{{ $featureStats['skripsi']['pendaftaran_sempro']['pending'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </a>

                            <!-- Ujian Hasil -->
                            <a href="{{ route('admin.pendaftaran-ujian-hasil.index') }}"
                                class="text-decoration-none text-body">
                                <div
                                    class="d-flex justify-content-between align-items-center p-2 bg-light rounded border hover-bg-light-secondary transition-base">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-xs me-2">
                                            <span class="avatar-initial rounded-circle bg-label-success"><i
                                                    class="bx bx-medal"></i></span>
                                        </div>
                                        <div>
                                            <small class="d-block fw-semibold">Ujian Hasil</small>
                                            <small class="text-muted"
                                                style="font-size: 0.7rem;">{{ $featureStats['skripsi']['pendaftaran_ujian']['bulan_ini'] }}
                                                bulan ini</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span
                                            class="fs-6 fw-bold">{{ $featureStats['skripsi']['pendaftaran_ujian']['total'] }}</span>
                                        @if ($featureStats['skripsi']['pendaftaran_ujian']['pending'] > 0)
                                            <span
                                                class="badge bg-danger rounded-pill ms-1 pt-1">{{ $featureStats['skripsi']['pendaftaran_ujian']['pending'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.pendaftaran-seminar-proposal.index') }}"
                                class="btn btn-outline-primary btn-sm flex-fill">
                                <i class='bx bx-list-ul me-1'></i>Sempro
                            </a>
                            <a href="{{ route('admin.pendaftaran-ujian-hasil.index') }}"
                                class="btn btn-outline-success btn-sm flex-fill">
                                <i class='bx bx-list-check me-1'></i>Ujian
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Layanan Akademik -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm stats-card info">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-md me-3">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class='bx bx-buildings'></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0">Layanan Akademik</h6>
                                <small class="text-muted">Fasilitas & Data</small>
                            </div>
                        </div>

                        <!-- Peminjaman Section -->
                        <div class="mb-3">
                            <small class="text-uppercase text-muted fw-bold d-block mb-2"
                                style="font-size: 0.65rem;">Peminjaman Fasilitas</small>
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="{{ route('admin.peminjaman-proyektor.index') }}"
                                        class="text-decoration-none text-body">
                                        <div
                                            class="p-2 border rounded d-flex justify-content-between align-items-center bg-white hover-shadow-sm">
                                            <div>
                                                <i class="bx bx-slideshow text-warning me-1"></i>
                                                <small>Proyektor</small>
                                            </div>
                                            <div>
                                                <span
                                                    class="fw-bold">{{ $featureStats['peminjaman']['proyektor']['total'] }}</span>
                                                @if ($featureStats['peminjaman']['proyektor']['pending'] > 0)
                                                    <span class="text-danger small">•</span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('admin.peminjaman-laboratorium.index') }}"
                                        class="text-decoration-none text-body">
                                        <div
                                            class="p-2 border rounded d-flex justify-content-between align-items-center bg-white hover-shadow-sm">
                                            <div>
                                                <i class="bx bx-desktop text-info me-1"></i>
                                                <small>Lab</small>
                                            </div>
                                            <div>
                                                <span
                                                    class="fw-bold">{{ $featureStats['peminjaman']['laboratorium']['total'] }}</span>
                                                @if ($featureStats['peminjaman']['laboratorium']['pending'] > 0)
                                                    <span class="text-danger small">•</span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Data Akademik Section -->
                        <div>
                            <small class="text-uppercase text-muted fw-bold d-block mb-2" style="font-size: 0.65rem;">Data
                                Akademik</small>
                            <div class="d-flex justify-content-between align-items-center p-2 bg-lighter rounded mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-calendar-event text-primary me-2"></i>
                                    <small class="fw-semibold">Tahun Ajaran</small>
                                </div>
                                <span
                                    class="badge bg-label-primary">{{ $featureStats['akademik']['tahun_ajaran_aktif'] }}</span>
                            </div>

                            <div class="d-flex gap-2 justify-content-between text-center">
                                <div class="flex-fill p-1 bg-light rounded">
                                    <div class="fw-bold text-dark">{{ $featureStats['akademik']['mahasiswa'] }}</div>
                                    <small class="text-muted" style="font-size: 0.65rem;">Mhs</small>
                                </div>
                                <div class="flex-fill p-1 bg-light rounded">
                                    <div class="fw-bold text-dark">{{ $featureStats['akademik']['dosen'] }}</div>
                                    <small class="text-muted" style="font-size: 0.65rem;">Dsn</small>
                                </div>
                                @if ($featureStats['akademik']['ukt_belum_lunas'] > 0)
                                    <div class="flex-fill p-1 bg-light rounded border border-danger">
                                        <div class="fw-bold text-danger">
                                            {{ $featureStats['akademik']['ukt_belum_lunas'] }}</div>
                                        <small class="text-danger" style="font-size: 0.65rem;">UKT</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Charts and Analytics -->
        <div class="row mb-4">
            <!-- Enhanced Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <i class='bx bx-line-chart me-2'></i>Tren Pengajuan Surat
                            </h5>
                            <small class="text-muted">6 Bulan Terakhir</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary btn-sm" onclick="changeChartType('line')"
                                title="Line Chart">
                                <i class='bx bx-line-chart'></i>
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="changeChartType('bar')" title="Bar Chart">
                                <i class='bx bx-bar-chart'></i>
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="changeChartType('area')"
                                title="Area Chart">
                                <i class='bx bx-chart'></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="suratTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Notifications & Quick Stats -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class='bx bx-bell me-2'></i>Notifikasi & Status
                        </h5>
                        <span class="badge bg-warning">{{ $unreadNotifications }}</span>
                    </div>
                    <div class="card-body">
                        <!-- Quick Notifications -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-center align-items-center flex-column py-3">
                                <div class="avatar avatar-lg mb-3">
                                    <span class="avatar-initial rounded bg-label-warning">
                                        <i class='bx bx-bell bx-lg'></i>
                                    </span>
                                </div>
                                <h4 class="mb-2">{{ $unreadNotifications }}</h4>
                                <p class="text-muted mb-3 text-center">Notifikasi Belum Dibaca</p>
                                <a href="{{ route('admin.notifications.index') }}" class="btn btn-primary btn-sm">
                                    <i class='bx bx-right-arrow-alt me-1'></i>Kelola Notifikasi
                                </a>
                            </div>
                        </div>

                        <div class="widget-separator"></div>

                        <!-- Quick Stats -->
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class='bx bx-calendar'></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $analytics['todaySubmissions'] }}</h6>
                                        <small class="text-muted">Hari Ini</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded bg-label-success">
                                            <i class='bx bx-calendar-week'></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $analytics['thisMonthSubmissions'] }}</h6>
                                        <small class="text-muted">Bulan Ini</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Recent Activities -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <i class='bx bx-history me-2'></i>Aktivitas Terbaru
                            </h5>
                            <small class="text-muted" id="activity-period-info">Minggu Ini
                                ({{ now()->startOfWeek()->format('d M') }} -
                                {{ now()->endOfWeek()->format('d M Y') }})</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary btn-sm" onclick="filterActivities('today')"
                                data-period="today">
                                Hari Ini
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="filterActivities('week')" data-period="week">
                                Minggu Ini
                            </button>
                            <a href="{{ route('admin.activities') }}" class="btn btn-outline-primary btn-sm">
                                <i class='bx bx-right-arrow-alt me-1'></i>Lihat Semua
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($recentActivities->isEmpty())
                            <div class="text-center py-5" id="no-activity-message">
                                <div class="avatar avatar-xl mb-3">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                        <i class='bx bx-info-circle bx-lg'></i>
                                    </span>
                                </div>
                                <h6 class="mb-1">Tidak Ada Aktivitas</h6>
                                <p class="text-muted mb-0">Belum ada aktivitas di minggu ini untuk ditampilkan</p>
                            </div>
                        @else
                            <!-- Activity Counter -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-label-primary me-2"
                                        id="activity-count">{{ $recentActivities->count() }}</span>
                                    <small class="text-muted">aktivitas ditemukan</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <small class="text-muted me-2">Urutkan:</small>
                                    <select class="form-select form-select-sm" style="width: auto;"
                                        onchange="sortActivities(this.value)">
                                        <option value="newest">Terbaru</option>
                                        <option value="oldest">Terlama</option>
                                        <option value="name">Nama A-Z</option>
                                        <option value="action">Jenis Aksi</option>
                                    </select>
                                </div>
                            </div>

                            <div class="activity-timeline" id="activity-timeline">
                                @foreach ($recentActivities as $index => $activity)
                                    <div class="activity-item" data-date="{{ $activity->created_at->format('Y-m-d') }}"
                                        data-name="{{ $activity->mahasiswa->name ?? 'N/A' }}"
                                        data-action="{{ $activity->aksi }}"
                                        data-timestamp="{{ $activity->created_at->timestamp }}"
                                        style="{{ $index >= 8 ? 'display: none;' : '' }}">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="avatar avatar-xs me-2">
                                                        @php
                                                            $badgeClass = match ($activity->aksi) {
                                                                'diajukan' => 'bg-label-primary',
                                                                'diproses' => 'bg-label-info',
                                                                'disetujui', 'disetujui_kaprodi' => 'bg-label-success',
                                                                'ditolak' => 'bg-label-danger',
                                                                'siap_diambil' => 'bg-label-warning',
                                                                'diambil' => 'bg-label-dark',
                                                                default => 'bg-label-secondary',
                                                            };
                                                        @endphp
                                                        <span class="avatar-initial rounded {{ $badgeClass }}">
                                                            {{ substr($activity->mahasiswa->name ?? 'N/A', 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">
                                                            {{ $activity->mahasiswa->name ?? 'Data tidak tersedia' }}</h6>
                                                        <small
                                                            class="text-muted">{{ $activity->mahasiswa->nim ?? 'NIM tidak tersedia' }}</small>
                                                    </div>
                                                </div>

                                                <div class="d-flex flex-wrap gap-2 mb-2">
                                                    @php
                                                        $suratInfo = match ($activity->surat_type) {
                                                            'App\\Models\\SuratAktifKuliah' => [
                                                                'icon' => 'bx-user-check',
                                                                'text' => 'Aktif Kuliah',
                                                                'class' => 'primary',
                                                            ],
                                                            'App\\Models\\SuratIjinSurvey' => [
                                                                'icon' => 'bx-search-alt',
                                                                'text' => 'Ijin Survey',
                                                                'class' => 'info',
                                                            ],
                                                            'App\\Models\\SuratCutiAkademik' => [
                                                                'icon' => 'bx-pause',
                                                                'text' => 'Cuti Akademik',
                                                                'class' => 'warning',
                                                            ],
                                                            'App\\Models\\SuratPindah' => [
                                                                'icon' => 'bx-transfer',
                                                                'text' => 'Pindah',
                                                                'class' => 'secondary',
                                                            ],
                                                            default => [
                                                                'icon' => 'bx-file',
                                                                'text' => class_basename($activity->surat_type),
                                                                'class' => 'secondary',
                                                            ],
                                                        };
                                                    @endphp
                                                    <span class="badge bg-label-{{ $suratInfo['class'] }}">
                                                        <i
                                                            class='bx {{ $suratInfo['icon'] }} me-1'></i>{{ $suratInfo['text'] }}
                                                    </span>
                                                    <span class="badge bg-label-{{ $badgeClass }}">
                                                        {{ Str::title(str_replace('_', ' ', $activity->aksi)) }}
                                                    </span>
                                                </div>

                                                @if ($activity->keterangan)
                                                    <p class="text-muted mb-0 small">
                                                        {{ Str::limit($activity->keterangan, 100) }}</p>
                                                @endif
                                            </div>
                                            <div class="text-end">
                                                <small
                                                    class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                                <br>
                                                <small
                                                    class="text-muted">{{ $activity->created_at->format('H:i') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Show More Button -->
                            @if ($recentActivities->count() > 8)
                                <div class="text-center mt-3" id="show-more-container">
                                    <button class="btn btn-outline-secondary btn-sm" onclick="toggleShowMore()">
                                        <span id="show-more-text">Tampilkan {{ $recentActivities->count() - 8 }} aktivitas
                                            lainnya</span>
                                        <i class='bx bx-chevron-down ms-1' id="show-more-icon"></i>
                                    </button>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Perlu Perhatian Section -->
        @if (
            $featureStats['need_attention']['surat_pending'] > 0 ||
                $featureStats['need_attention']['sempro_pending'] > 0 ||
                $featureStats['need_attention']['ujian_pending'] > 0 ||
                $featureStats['need_attention']['peminjaman_pending'] > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-white">
                                <i class='bx bx-error-circle me-2'></i>Perlu Perhatian
                            </h5>
                            <span class="badge bg-white text-warning">
                                {{ $featureStats['need_attention']['surat_pending'] + $featureStats['need_attention']['sempro_pending'] + $featureStats['need_attention']['ujian_pending'] + $featureStats['need_attention']['peminjaman_pending'] }}
                                Item
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @if ($featureStats['need_attention']['surat_pending'] > 0)
                                    <div class="col-md-6 col-lg-3">
                                        <a href="{{ route('admin.surat-aktif-kuliah.index') }}"
                                            class="text-decoration-none">
                                            <div class="d-flex align-items-center p-3 bg-warning bg-opacity-10 rounded">
                                                <div class="avatar avatar-sm me-3">
                                                    <span class="avatar-initial rounded bg-warning">
                                                        <i class='bx bx-envelope text-white'></i>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0">Surat Pending</h6>
                                                    <small
                                                        class="text-muted">{{ $featureStats['need_attention']['surat_pending'] }}
                                                        pengajuan menunggu</small>
                                                </div>
                                                <i class='bx bx-chevron-right text-warning'></i>
                                            </div>
                                        </a>
                                    </div>
                                @endif

                                @if ($featureStats['need_attention']['sempro_pending'] > 0)
                                    <div class="col-md-6 col-lg-3">
                                        <a href="{{ route('admin.pendaftaran-seminar-proposal.index') }}"
                                            class="text-decoration-none">
                                            <div class="d-flex align-items-center p-3 bg-warning bg-opacity-10 rounded">
                                                <div class="avatar avatar-sm me-3">
                                                    <span class="avatar-initial rounded bg-warning">
                                                        <i class='bx bx-book-open text-white'></i>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0">Sempro Pending</h6>
                                                    <small
                                                        class="text-muted">{{ $featureStats['need_attention']['sempro_pending'] }}
                                                        pendaftaran menunggu</small>
                                                </div>
                                                <i class='bx bx-chevron-right text-warning'></i>
                                            </div>
                                        </a>
                                    </div>
                                @endif

                                @if ($featureStats['need_attention']['ujian_pending'] > 0)
                                    <div class="col-md-6 col-lg-3">
                                        <a href="{{ route('admin.pendaftaran-ujian-hasil.index') }}"
                                            class="text-decoration-none">
                                            <div class="d-flex align-items-center p-3 bg-warning bg-opacity-10 rounded">
                                                <div class="avatar avatar-sm me-3">
                                                    <span class="avatar-initial rounded bg-warning">
                                                        <i class='bx bxs-graduation text-white'></i>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0">Ujian Pending</h6>
                                                    <small
                                                        class="text-muted">{{ $featureStats['need_attention']['ujian_pending'] }}
                                                        pendaftaran menunggu</small>
                                                </div>
                                                <i class='bx bx-chevron-right text-warning'></i>
                                            </div>
                                        </a>
                                    </div>
                                @endif

                                @if ($featureStats['need_attention']['peminjaman_pending'] > 0)
                                    <div class="col-md-6 col-lg-3">
                                        <a href="{{ route('admin.peminjaman-proyektor.index') }}"
                                            class="text-decoration-none">
                                            <div class="d-flex align-items-center p-3 bg-warning bg-opacity-10 rounded">
                                                <div class="avatar avatar-sm me-3">
                                                    <span class="avatar-initial rounded bg-warning">
                                                        <i class='bx bx-calendar text-white'></i>
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0">Peminjaman Pending</h6>
                                                    <small
                                                        class="text-muted">{{ $featureStats['need_attention']['peminjaman_pending'] }}
                                                        peminjaman menunggu</small>
                                                </div>
                                                <i class='bx bx-chevron-right text-warning'></i>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>


    @push('scripts')
        <script>
            let currentChart = null;
            let currentChartType = 'bar';

            document.addEventListener('DOMContentLoaded', function() {
                initializeChart();

                // Auto refresh setiap 5 menit
                setInterval(updateLastUpdated, 60000); // Update setiap menit
                setInterval(refreshDashboard, 300000); // Refresh data setiap 5 menit
            });

            function initializeChart() {
                const ctx = document.getElementById('suratTrendChart').getContext('2d');

                // Detect dark mode
                const isDarkMode = document.documentElement.getAttribute('data-theme') === 'dark';
                const textColor = isDarkMode ? '#e4e6ea' : '#697a8d';
                const gridColor = isDarkMode ? '#404553' : '#f5f5f5';

                if (currentChart) {
                    currentChart.destroy();
                }

                // Configuration untuk setiap tipe chart
                let chartConfig = {
                    type: currentChartType === 'area' ? 'line' : currentChartType,
                    data: {
                        labels: @json($months),
                        datasets: getDatasetConfig()
                    },
                    options: getChartOptions(textColor, gridColor)
                };

                currentChart = new Chart(ctx, chartConfig);
            }

            function getDatasetConfig() {
                const isArea = currentChartType === 'area';
                const isLine = currentChartType === 'line';

                return [{
                    label: 'Surat Aktif Kuliah',
                    data: @json($aktifKuliahCounts),
                    borderColor: '#696cff',
                    backgroundColor: isArea ? 'rgba(105, 108, 255, 0.3)' : (isLine ? 'rgba(105, 108, 255, 0.1)' :
                        'rgba(105, 108, 255, 0.8)'),
                    borderWidth: isLine || isArea ? 3 : 2,
                    fill: isArea ? '+1' : false, // +1 berarti fill ke dataset berikutnya
                    tension: isLine || isArea ? 0.4 : 0,
                    pointRadius: isLine || isArea ? 4 : 0,
                    pointHoverRadius: isLine || isArea ? 6 : 0,
                    pointBackgroundColor: '#696cff',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }, {
                    label: 'Surat Ijin Survey',
                    data: @json($ijinSurveyCounts),
                    borderColor: '#03c3ec',
                    backgroundColor: isArea ? 'rgba(3, 195, 236, 0.3)' : (isLine ? 'rgba(3, 195, 236, 0.1)' :
                        'rgba(3, 195, 236, 0.8)'),
                    borderWidth: isLine || isArea ? 3 : 2,
                    fill: isArea ? '+1' : false,
                    tension: isLine || isArea ? 0.4 : 0,
                    pointRadius: isLine || isArea ? 4 : 0,
                    pointHoverRadius: isLine || isArea ? 6 : 0,
                    pointBackgroundColor: '#03c3ec',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }, {
                    label: 'Surat Cuti Akademik',
                    data: @json($cutiAkademikCounts),
                    borderColor: '#ffab00',
                    backgroundColor: isArea ? 'rgba(255, 171, 0, 0.3)' : (isLine ? 'rgba(255, 171, 0, 0.1)' :
                        'rgba(255, 171, 0, 0.8)'),
                    borderWidth: isLine || isArea ? 3 : 2,
                    fill: isArea ? '+1' : false,
                    tension: isLine || isArea ? 0.4 : 0,
                    pointRadius: isLine || isArea ? 4 : 0,
                    pointHoverRadius: isLine || isArea ? 6 : 0,
                    pointBackgroundColor: '#ffab00',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }, {
                    label: 'Surat Pindah',
                    data: @json($pindahCounts),
                    borderColor: '#71dd37',
                    backgroundColor: isArea ? 'rgba(113, 221, 55, 0.3)' : (isLine ? 'rgba(113, 221, 55, 0.1)' :
                        'rgba(113, 221, 55, 0.8)'),
                    borderWidth: isLine || isArea ? 3 : 2,
                    fill: isArea ? 'origin' : false, // Dataset terakhir fill ke origin (bottom)
                    tension: isLine || isArea ? 0.4 : 0,
                    pointRadius: isLine || isArea ? 4 : 0,
                    pointHoverRadius: isLine || isArea ? 6 : 0,
                    pointBackgroundColor: '#71dd37',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }];
            }

            function getChartOptions(textColor, gridColor) {
                return {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: textColor,
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#696cff',
                            borderWidth: 1,
                            cornerRadius: 8,
                            padding: 12,
                            displayColors: true,
                            usePointStyle: true,
                            callbacks: {
                                title: function(context) {
                                    return 'Periode: ' + context[0].label;
                                },
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.raw + ' surat';
                                },
                                afterBody: function(context) {
                                    const total = context.reduce((sum, item) => sum + item.raw, 0);
                                    return 'Total: ' + total + ' surat';
                                }
                            }
                        },
                        // Menambahkan plugin untuk area chart styling
                        ...(currentChartType === 'area' && {
                            filler: {
                                propagate: true
                            }
                        })
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: textColor,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            stacked: currentChartType === 'area', // Stack untuk area chart
                            grid: {
                                color: gridColor,
                                drawBorder: false
                            },
                            ticks: {
                                color: textColor,
                                font: {
                                    size: 11
                                },
                                precision: 0,
                                callback: function(value) {
                                    return value + ' surat';
                                }
                            }
                        }
                    },
                    elements: {
                        point: {
                            radius: currentChartType === 'line' || currentChartType === 'area' ? 4 : 0,
                            hoverRadius: 6
                        },
                        line: {
                            tension: currentChartType === 'line' || currentChartType === 'area' ? 0.4 : 0
                        }
                    }
                };
            }

            function changeChartType(type) {
                currentChartType = type;

                // Update button states
                document.querySelectorAll('[onclick^="changeChartType"]').forEach(btn => {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline-secondary');
                });
                event.target.closest('button').classList.remove('btn-outline-secondary');
                event.target.closest('button').classList.add('btn-primary');

                // Animate chart transition
                const chartContainer = document.querySelector('.chart-container');
                chartContainer.style.opacity = '0.5';

                setTimeout(() => {
                    initializeChart();
                    chartContainer.style.opacity = '1';
                }, 150);
            }

            function refreshDashboard() {
                showLoader();

                fetch(window.location.href, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        // Update content areas
                        const parser = new DOMParser();
                        const newDoc = parser.parseFromString(html, 'text/html');

                        // Update specific sections
                        const sections = ['.stats-card', '.activity-timeline'];
                        sections.forEach(selector => {
                            const oldElements = document.querySelectorAll(selector);
                            const newElements = newDoc.querySelectorAll(selector);

                            oldElements.forEach((oldEl, index) => {
                                if (newElements[index]) {
                                    oldEl.innerHTML = newElements[index].innerHTML;
                                }
                            });
                        });

                        updateLastUpdated();
                        hideLoader();

                        // Show success message
                        showToast('Dashboard berhasil diperbarui', 'success');
                    })
                    .catch(error => {
                        hideLoader();
                        showToast('Gagal memperbarui dashboard', 'error');
                        console.error('Error:', error);
                    });
            }

            function updateLastUpdated() {
                document.getElementById('lastUpdated').textContent = new Date().toLocaleString('id-ID', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            function filterActivities(period) {
                const activities = document.querySelectorAll('.activity-item');
                const now = new Date();
                let cutoffDate;
                let periodText = '';

                switch (period) {
                    case 'today':
                        cutoffDate = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                        periodText =
                            `Hari Ini (${now.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })})`;
                        break;
                    case 'week':
                        // Senin minggu ini
                        const monday = new Date(now);
                        monday.setDate(now.getDate() - now.getDay() + 1);
                        monday.setHours(0, 0, 0, 0);
                        cutoffDate = monday;

                        const sunday = new Date(monday);
                        sunday.setDate(monday.getDate() + 6);

                        periodText =
                            `Minggu Ini (${monday.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })} - ${sunday.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })})`;
                        break;
                    default:
                        cutoffDate = null;
                }

                let visibleCount = 0;

                activities.forEach(activity => {
                    if (!cutoffDate) {
                        activity.style.display = 'block';
                        visibleCount++;
                        return;
                    }

                    const activityDate = new Date(activity.dataset.date);
                    if (activityDate >= cutoffDate) {
                        activity.style.display = 'block';
                        visibleCount++;
                    } else {
                        activity.style.display = 'none';
                    }
                });

                // Update period info
                document.getElementById('activity-period-info').textContent = periodText;

                // Update activity count
                document.getElementById('activity-count').textContent = visibleCount;

                // Update button states
                document.querySelectorAll('[data-period]').forEach(btn => {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline-secondary');
                });
                document.querySelector(`[data-period="${period}"]`).classList.remove('btn-outline-secondary');
                document.querySelector(`[data-period="${period}"]`).classList.add('btn-primary');

                // Show/hide no activity message
                const noActivityMessage = document.getElementById('no-activity-message');
                const activityTimeline = document.getElementById('activity-timeline');

                if (visibleCount === 0 && noActivityMessage) {
                    noActivityMessage.style.display = 'block';
                    if (activityTimeline) activityTimeline.style.display = 'none';
                } else {
                    if (noActivityMessage) noActivityMessage.style.display = 'none';
                    if (activityTimeline) activityTimeline.style.display = 'block';
                }

                // Reset show more functionality
                resetShowMore();
            }

            function sortActivities(sortBy) {
                const timeline = document.getElementById('activity-timeline');
                const activities = Array.from(timeline.children);

                activities.sort((a, b) => {
                    switch (sortBy) {
                        case 'newest':
                            return parseInt(b.dataset.timestamp) - parseInt(a.dataset.timestamp);
                        case 'oldest':
                            return parseInt(a.dataset.timestamp) - parseInt(b.dataset.timestamp);
                        case 'name':
                            return a.dataset.name.localeCompare(b.dataset.name);
                        case 'action':
                            return a.dataset.action.localeCompare(b.dataset.action);
                        default:
                            return 0;
                    }
                });

                // Re-append sorted activities
                activities.forEach(activity => timeline.appendChild(activity));

                // Reset show more functionality
                resetShowMore();
            }

            function toggleShowMore() {
                const activities = document.querySelectorAll('.activity-item');
                const showMoreText = document.getElementById('show-more-text');
                const showMoreIcon = document.getElementById('show-more-icon');
                const isShowing = showMoreIcon.classList.contains('bx-chevron-up');

                activities.forEach((activity, index) => {
                    if (index >= 8) {
                        activity.style.display = isShowing ? 'none' : 'block';
                    }
                });

                if (isShowing) {
                    // Currently showing all, switch to show less
                    showMoreText.textContent = `Tampilkan ${activities.length - 8} aktivitas lainnya`;
                    showMoreIcon.classList.remove('bx-chevron-up');
                    showMoreIcon.classList.add('bx-chevron-down');
                } else {
                    // Currently showing limited, switch to show all
                    showMoreText.textContent = 'Tampilkan lebih sedikit';
                    showMoreIcon.classList.remove('bx-chevron-down');
                    showMoreIcon.classList.add('bx-chevron-up');
                }
            }

            function resetShowMore() {
                const activities = document.querySelectorAll('.activity-item');
                const showMoreContainer = document.getElementById('show-more-container');
                const showMoreText = document.getElementById('show-more-text');
                const showMoreIcon = document.getElementById('show-more-icon');

                // Hide activities beyond 8
                let visibleActivities = 0;
                activities.forEach((activity, index) => {
                    if (activity.style.display !== 'none') {
                        if (visibleActivities >= 8) {
                            activity.style.display = 'none';
                        } else {
                            visibleActivities++;
                        }
                    }
                });

                // Show/hide show more button
                const totalVisible = Array.from(activities).filter(a => a.style.display !== 'none').length;
                if (showMoreContainer) {
                    if (totalVisible > 8) {
                        showMoreContainer.style.display = 'block';
                        showMoreText.textContent = `Tampilkan ${totalVisible - 8} aktivitas lainnya`;
                        showMoreIcon.classList.remove('bx-chevron-up');
                        showMoreIcon.classList.add('bx-chevron-down');
                    } else {
                        showMoreContainer.style.display = 'none';
                    }
                }
            }

            // Initialize dengan filter minggu ini saat page load
            document.addEventListener('DOMContentLoaded', function() {
                initializeChart();

                // Set default filter ke minggu ini
                // filterActivities('week'); // Tidak perlu dipanggil karena data sudah difilter di controller

                // Auto refresh setiap 5 menit
                setInterval(updateLastUpdated, 60000);
                setInterval(refreshDashboard, 300000);
            });

            function showToast(message, type = 'info') {
                // Implementation for toast notification
                const toast = document.createElement('div');
                toast.className =
                    `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
                toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                }, 5000);
            }

            // Listen for theme changes to update chart
            document.addEventListener('themeChanged', function() {
                setTimeout(initializeChart, 100); // Small delay to ensure CSS variables are updated
            });

            // Trigger theme change event when dark mode is toggled
            const originalSetTheme = window.setTheme;
            if (originalSetTheme) {
                window.setTheme = function(theme) {
                    originalSetTheme(theme);
                    document.dispatchEvent(new CustomEvent('themeChanged', {
                        detail: {
                            theme
                        }
                    }));
                };
            }
        </script>
    @endpush
@endsection
