{{-- filepath: resources/views/admin/berita-acara-ujian-hasil/index.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Berita Acara Ujian Hasil')

@push('styles')
    <style>
        /* Sneat-styled enhancements */
        .card-border-shadow-primary {
            border-left: 3px solid #696cff;
        }

        .card-border-shadow-success {
            border-left: 3px solid #71dd37;
        }

        .card-border-shadow-warning {
            border-left: 3px solid #ffab00;
        }

        .card-border-shadow-danger {
            border-left: 3px solid #ff3e1d;
        }

        /* Clickable row styles */
        .clickable-row {
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .clickable-row:hover {
            background-color: rgba(67, 89, 113, 0.08) !important;
        }

        .clickable-row:active {
            background-color: rgba(67, 89, 113, 0.12) !important;
        }

        /* Don't change cursor on actions column */
        .clickable-row td:last-child {
            cursor: default;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(67, 89, 113, 0.04);
        }

        .nav-pills .nav-link {
            transition: all 0.2s ease;
        }

        .nav-pills .nav-link:hover:not(.active) {
            background-color: rgba(67, 89, 113, 0.04);
        }

        /* Responsive tabs */
        @media (max-width: 576px) {
            .nav-pills {
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .nav-pills .nav-link {
                white-space: nowrap;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">Ujian Hasil</li>
                <li class="breadcrumb-item active">Berita Acara</li>
            </ol>
        </nav>

        {{-- Header --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div class="me-3">
                <h4 class="fw-bold mb-1">
                    <i class="bx bxs-file me-2 text-primary"></i>Berita Acara Ujian Hasil
                </h4>
                <p class="text-muted mb-0">Kelola dan monitor berita acara ujian hasil mahasiswa</p>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bx bx-check-circle fs-4 me-2"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error-circle fs-4 me-2"></i>
                    <div>{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                <div class="card card-border-shadow-primary h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bxs-file bx-sm"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $stats['total'] }}</h4>
                        </div>
                        <p class="mb-1 fw-semibold">Total Berita Acara</p>
                        <p class="mb-0">
                            <small class="text-muted">Semua berita acara yang dibuat</small>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                <div class="card card-border-shadow-warning h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="bx bx-time bx-sm"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0">
                                {{ ($stats['menunggu_ttd_penguji'] ?? 0) + ($stats['menunggu_ttd_panitia'] ?? 0) }}</h4>
                        </div>
                        <p class="mb-1 fw-semibold">Menunggu TTD</p>
                        <p class="mb-0">
                            <small class="text-muted">Dalam proses persetujuan</small>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                <div class="card card-border-shadow-success h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="bx bx-check-circle bx-sm"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $stats['selesai'] ?? 0 }}</h4>
                        </div>
                        <p class="mb-1 fw-semibold">Selesai</p>
                        <p class="mb-0">
                            <small class="text-muted">Sudah ditandatangani lengkap</small>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                <div class="card card-border-shadow-danger h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="bx bx-x-circle bx-sm"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $stats['ditolak'] ?? 0 }}</h4>
                        </div>
                        <p class="mb-1 fw-semibold">Ditolak</p>
                        <p class="mb-0">
                            <small class="text-muted">Perlu penjadwalan ulang</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Table Card --}}
        <div class="card">
            {{-- Card Header with Tabs --}}
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-list-ul me-1"></i> Data Berita Acara
                    </h5>
                </div>

                {{-- Filter Tabs --}}
                <ul class="nav nav-pills card-header-pills" role="tablist">
                    <li class="nav-item">
                        <a href="{{ route('admin.berita-acara-ujian-hasil.index') }}"
                            class="nav-link {{ !request('status') && !request('filter') ? 'active' : '' }}">
                            <i class="bx bx-list-check bx-xs me-1"></i>
                            <span class="d-none d-sm-inline">Semua</span>
                        </a>
                    </li>

                    @if (Auth::user()->hasRole('dosen'))
                        <li class="nav-item">
                            <a href="{{ route('admin.berita-acara-ujian-hasil.index', ['filter' => 'penguji']) }}"
                                class="nav-link {{ request('filter') === 'penguji' ? 'active' : '' }}">
                                <i class="bx bx-pen bx-xs me-1"></i>
                                <span class="d-none d-sm-inline">Menunggu TTD Saya</span>
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a href="{{ route('admin.berita-acara-ujian-hasil.index', ['status' => 'menunggu_ttd']) }}"
                                class="nav-link {{ request('status') === 'menunggu_ttd' ? 'active' : '' }}">
                                <i class="bx bx-time bx-xs me-1"></i>
                                <span class="d-none d-sm-inline">Menunggu TTD</span>
                            </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a href="{{ route('admin.berita-acara-ujian-hasil.index', ['status' => 'selesai']) }}"
                            class="nav-link {{ request('status') === 'selesai' ? 'active' : '' }}">
                            <i class="bx bx-check-circle bx-xs me-1"></i>
                            <span class="d-none d-sm-inline">Selesai</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Card Body with Filters --}}
            <div class="card-body border-bottom">
                <form action="{{ route('admin.berita-acara-ujian-hasil.index') }}" method="GET" class="row g-3">
                    {{-- Preserve status/filter from tab --}}
                    @if (request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    @if (request('filter'))
                        <input type="hidden" name="filter" value="{{ request('filter') }}">
                    @endif

                    <div class="col-md-5">
                        <label class="form-label">Cari Mahasiswa</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Nama atau NIM..."
                                value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-filter-alt me-1"></i> Filter
                        </button>
                        @if (request()->hasAny(['search']))
                            <a href="{{ route('admin.berita-acara-ujian-hasil.index', request()->only(['status', 'filter'])) }}"
                                class="btn btn-outline-secondary">
                                <i class="bx bx-reset me-1"></i> Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Mahasiswa</th>
                            <th width="20%">Tanggal Ujian</th>
                            <th width="15%">Status</th>
                            <th width="20%">Progress TTD</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($beritaAcaras as $index => $ba)
                            @php
                                $jadwal = $ba->jadwalUjianHasil;
                                if ($jadwal) {
                                    $mahasiswa = $jadwal->pendaftaranUjianHasil->user;
                                    $tanggalUjian = $jadwal->tanggal_ujian;
                                    $waktuMulai = $jadwal->waktu_mulai;
                                } else {
                                    $mahasiswa = (object) ['name' => $ba->mahasiswa_name, 'nim' => $ba->mahasiswa_nim];
                                    $tanggalUjian = null;
                                    $waktuMulai = null;
                                }
                            @endphp
                            <tr class="clickable-row {{ !$jadwal ? 'table-danger' : '' }}"
                                data-href="{{ route('admin.berita-acara-ujian-hasil.show', $ba) }}">
                                <td><span class="fw-medium">{{ $beritaAcaras->firstItem() + $index }}</span></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <strong class="mb-1">{{ $mahasiswa->name ?? '-' }}</strong>
                                        <small class="text-muted">{{ $mahasiswa->nim ?? '-' }}</small>
                                        @if (!$jadwal)
                                            <small class="text-danger mt-1">
                                                <i class="bx bx-info-circle me-1"></i>Data Jadwal Hilang
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if ($tanggalUjian)
                                        <div class="d-flex flex-column gap-1">
                                            <span class="badge bg-label-primary">
                                                <i class="bx bx-calendar bx-xs me-1"></i>
                                                {{ \Carbon\Carbon::parse($tanggalUjian)->isoFormat('dddd, D MMMM Y') }}
                                            </span>
                                            <small class="text-muted">
                                                <i class="bx bx-time bx-xs me-1"></i>
                                                {{ $waktuMulai }} WIB
                                            </small>
                                        </div>
                                    @else
                                        <span class="badge bg-label-danger">
                                            <i class="bx bx-x bx-xs me-1"></i>
                                            -
                                        </span>
                                    @endif
                                </td>
                                <td>{!! $ba->status_badge !!}</td>
                                <td>
                                    @if ($ba->isSigned())
                                        <div class="d-flex flex-column gap-1">
                                            <span class="badge bg-success">
                                                <i class="bx bx-check-circle me-1"></i>Selesai
                                            </span>
                                            <small class="text-muted">
                                                {{ $ba->ttd_ketua_penguji_at?->isoFormat('D/M/Y HH:mm') }}
                                            </small>
                                        </div>
                                    @else
                                        @php $progress = $ba->getTtdPengujiProgress(); @endphp
                                        <div class="d-flex flex-column">
                                            <div class="d-flex justify-content-between mb-1">
                                                <small
                                                    class="text-muted">{{ $progress['signed'] }}/{{ $progress['total'] }}
                                                    Penguji</small>
                                                <small class="fw-semibold">{{ $progress['percentage'] }}%</small>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-info" role="progressbar"
                                                    style="width: {{ $progress['percentage'] }}%"
                                                    aria-valuenow="{{ $progress['percentage'] }}" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center" onclick="event.stopPropagation();">
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <a class="dropdown-item"
                                                href="{{ route('admin.berita-acara-ujian-hasil.show', $ba) }}">
                                                <i class="bx bx-show me-2"></i>Lihat Detail
                                            </a>

                                            @if ($ba->file_path)
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.berita-acara-ujian-hasil.view-pdf', $ba) }}"
                                                    target="_blank">
                                                    <i class="bx bxs-file-pdf me-2"></i>Preview PDF
                                                </a>
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.berita-acara-ujian-hasil.download-pdf', $ba) }}">
                                                    <i class="bx bx-download me-2"></i>Download PDF
                                                </a>
                                            @endif

                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bx bxs-file display-4 text-muted mb-3"></i>
                                        <h5 class="text-muted">Tidak ada data berita acara</h5>
                                        <p class="text-muted mb-0">Belum ada berita acara untuk filter ini</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($beritaAcaras->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        {{ $beritaAcaras->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ========== CLICKABLE ROW ==========
            const clickableRows = document.querySelectorAll('.clickable-row');
            clickableRows.forEach(row => {
                row.addEventListener('click', function(e) {
                    // Jangan redirect jika klik di dropdown atau link/button
                    if (e.target.closest('.dropdown') ||
                        e.target.closest('a') ||
                        e.target.closest('button') ||
                        e.target.closest('form')) {
                        return;
                    }

                    const href = this.dataset.href;
                    if (href) {
                        window.location.href = href;
                    }
                });
            });

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

    </script>
@endpush
