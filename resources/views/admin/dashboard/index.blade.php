@extends('layouts.admin.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header with Breadcrumb -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <small class="text-muted">Last Updated: {{ now()->format('d M Y H:i') }}</small>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <!-- Total Requests Card -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-label-primary rounded-pill p-2 mb-2">
                                    <i class='bx bx-file bx-sm'></i>
                                </span>
                                <h5 class="mb-0">{{ array_sum($statusCounts) }}</h5>
                                <small class="text-muted">Total Pengajuan</small>
                            </div>
                            <div class="avatar">
                                <div class="avatar-initial bg-label-primary rounded">
                                    <i class='bx bx-trending-up bx-sm'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Requests Card -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-label-warning rounded-pill p-2 mb-2">
                                    <i class='bx bx-time bx-sm'></i>
                                </span>
                                <h5 class="mb-0">{{ $statusCounts['diajukan'] + $statusCounts['diproses'] }}</h5>
                                <small class="text-muted">Pending</small>
                            </div>
                            <div class="avatar">
                                <div class="avatar-initial bg-label-warning rounded">
                                    <i class='bx bx-loader-circle bx-sm'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approved Requests Card -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-label-success rounded-pill p-2 mb-2">
                                    <i class='bx bx-check-circle bx-sm'></i>
                                </span>
                                <h5 class="mb-0">{{ $statusCounts['disetujui_kaprodi'] + $statusCounts['disetujui'] }}
                                </h5>
                                <small class="text-muted">Disetujui</small>
                            </div>
                            <div class="avatar">
                                <div class="avatar-initial bg-label-success rounded">
                                    <i class='bx bx-check-shield bx-sm'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rejected Requests Card -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-label-danger rounded-pill p-2 mb-2">
                                    <i class='bx bx-x-circle bx-sm'></i>
                                </span>
                                <h5 class="mb-0">{{ $statusCounts['ditolak'] }}</h5>
                                <small class="text-muted">Ditolak</small>
                            </div>
                            <div class="avatar">
                                <div class="avatar-initial bg-label-danger rounded">
                                    <i class='bx bx-error bx-sm'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Breakdown and Chart Row -->
        <div class="row mb-4">
            <!-- Notifications Card -->
            <div class="col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center border-bottom">
                        <h5 class="mb-0"><i class='bx bx-bell me-2'></i>Notifikasi</h5>
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-outline-primary">Lihat
                            Semua</a>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center align-items-center flex-column py-4">
                            <div class="avatar avatar-xl mb-3">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class='bx bx-bell bx-lg'></i>
                                </span>
                            </div>
                            <h4 class="mb-2">{{ $unreadNotifications }}</h4>
                            <p class="text-muted mb-0">Notifikasi Belum Dibaca</p>
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-primary mt-3">Kelola
                                Notifikasi</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center border-bottom">
                        <h5 class="mb-0"><i class='bx bx-bar-chart-alt-2 me-2'></i>Tren Pengajuan Surat</h5>
                        <small class="text-muted">6 Bulan Terakhir</small>
                    </div>
                    <div class="card-body">
                        <canvas id="suratTrendChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center border-bottom">
                        <h5 class="mb-0"><i class='bx bx-history me-2'></i>Aktivitas Terbaru</h5>
                        <a href="{{ route('admin.activities') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="15%">Waktu</th>
                                        <th width="20%">Mahasiswa</th>
                                        <th width="20%">Jenis Surat</th>
                                        <th width="30%">Keterangan</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse ($recentActivities as $activity)
                                        <tr>
                                            <td>
                                                <span
                                                    class="badge bg-label-secondary">{{ $activity->created_at->diffForHumans() }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-xs me-2">
                                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                                            {{ substr($activity->mahasiswa->name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span class="fw-medium">{{ $activity->mahasiswa->name }}</span><br>
                                                        <small class="text-muted">{{ $activity->mahasiswa->nim }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($activity->surat_type === 'App\\Models\\SuratAktifKuliah')
                                                    <span class="badge bg-label-primary">
                                                        <i class='bx bx-user-check me-1'></i> Aktif Kuliah
                                                    </span>
                                                @elseif ($activity->surat_type === 'App\\Models\\SuratIjinSurvey')
                                                    <span class="badge bg-label-info">
                                                        <i class='bx bx-search-alt me-1'></i> Ijin Survey
                                                    </span>
                                                @else
                                                    <span class="badge bg-label-secondary">
                                                        <i class='bx bx-file me-1'></i>
                                                        {{ class_basename($activity->surat_type) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ Str::limit($activity->keterangan, 50) }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-label-{{ $activity->aksi === 'diajukan'
                                                        ? 'primary'
                                                        : ($activity->aksi === 'diproses'
                                                            ? 'info'
                                                            : ($activity->aksi === 'disetujui'
                                                                ? 'success'
                                                                : ($activity->aksi === 'ditolak'
                                                                    ? 'danger'
                                                                    : 'warning'))) }}">
                                                    {{ Str::title($activity->aksi) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <div class="d-flex flex-column justify-content-center align-items-center">
                                                    <i class='bx bx-info-circle bx-lg text-muted mb-2'></i>
                                                    <p class="text-muted mb-0">Tidak ada aktivitas terbaru</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- JavaScript untuk Grafik -->
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('suratTrendChart').getContext('2d');
                    const chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: @json($months), // Pastikan jumlah labels sesuai
                            datasets: [{
                                    label: 'Surat Aktif Kuliah',
                                    data: @json($aktifKuliahCounts), // Pastikan jumlah data sesuai
                                    borderColor: 'rgba(105, 108, 255, 1)',
                                    backgroundColor: 'rgba(105, 108, 255, 0.1)',
                                    tension: 0.3,
                                    fill: true,
                                    borderWidth: 2,
                                    pointRadius: 4,
                                    pointBackgroundColor: 'rgba(105, 108, 255, 1)'
                                },
                                {
                                    label: 'Surat Izin Survey',
                                    data: @json($ijinSurveyCounts),
                                    borderColor: 'rgba(3, 195, 236, 1)',
                                    backgroundColor: 'rgba(3, 195, 236, 0.1)',
                                    tension: 0.3,
                                    fill: true,
                                    borderWidth: 2,
                                    pointRadius: 4,
                                    pointBackgroundColor: 'rgba(3, 195, 236, 1)'
                                },
                                {
                                    label: 'Surat Cuti Akademik',
                                    data: @json($cutiAkademikCounts),
                                    borderColor: 'rgba(255, 171, 0, 1)',
                                    backgroundColor: 'rgba(255, 171, 0, 0.1)',
                                    tension: 0.3,
                                    fill: true,
                                    borderWidth: 2,
                                    pointRadius: 4,
                                    pointBackgroundColor: 'rgba(255, 171, 0, 1)'
                                },
                                {
                                    label: 'Surat Pindah',
                                    data: @json($pindahCounts),
                                    borderColor: 'rgba(113, 221, 55, 1)',
                                    backgroundColor: 'rgba(113, 221, 55, 0.1)',
                                    tension: 0.3,
                                    fill: true,
                                    borderWidth: 2,
                                    pointRadius: 4,
                                    pointBackgroundColor: 'rgba(113, 221, 55, 1)'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        padding: 20,
                                        usePointStyle: true,
                                        pointStyle: 'circle'
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    titleFont: {
                                        size: 14,
                                        weight: 'bold'
                                    },
                                    bodyFont: {
                                        size: 12
                                    },
                                    padding: 12,
                                    cornerRadius: 6,
                                    displayColors: true,
                                    usePointStyle: true,
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': ' + context.raw;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        color: '#697a8d'
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        drawBorder: false,
                                        color: '#f5f5f5'
                                    },
                                    ticks: {
                                        color: '#697a8d',
                                        precision: 0
                                    }
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            }
                        }
                    });
                });
            </script>
        @endpush
    </div>
@endsection
