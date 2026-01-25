{{-- filepath: resources/views/admin/jadwal-ujian-hasil/index.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Jadwal Ujian Hasil')

@push('styles')
    <style>
        /* Orange theme for Ujian Hasil */
        .card-border-shadow-warning {
            border-left: 3px solid #ffab00;
        }

        .card-border-shadow-info {
            border-left: 3px solid #03c3ec;
        }

        .card-border-shadow-orange {
            border-left: 3px solid #ff9f43;
        }

        .card-border-shadow-success {
            border-left: 3px solid #71dd37;
        }

        /* Clickable row styles */
        .clickable-row {
            transition: background-color 0.2s ease;
        }

        .clickable-row:hover {
            background-color: rgba(255, 159, 67, 0.08) !important;
        }

        .clickable-row:active {
            background-color: rgba(255, 159, 67, 0.12) !important;
        }

        .clickable-row td:first-child,
        .clickable-row td:last-child {
            cursor: default;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(255, 159, 67, 0.04);
        }

        .nav-pills .nav-link {
            transition: all 0.2s ease;
        }

        .nav-pills .nav-link:hover:not(.active) {
            background-color: rgba(255, 159, 67, 0.04);
        }

        .nav-pills .nav-link.active {
            background-color: #ff9f43;
        }

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

        .clickable-row.row-selected {
            background-color: rgba(255, 159, 67, 0.1) !important;
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
                <li class="breadcrumb-item active">Jadwal Ujian Hasil</li>
            </ol>
        </nav>

        {{-- Header --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div class="me-3">
                <h4 class="fw-bold mb-1">
                    <i class="bx bx-calendar-event me-2 text-warning"></i>Jadwal Ujian Hasil
                </h4>
                <p class="text-muted mb-0">Kelola dan monitor jadwal ujian hasil skripsi mahasiswa</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.jadwal-ujian-hasil.calendar') }}" class="btn btn-outline-warning btn-sm">
                    <i class="bx bx-calendar me-1"></i> Lihat Kalender
                </a>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bx bx-check-circle fs-4 me-2"></i>
                    <div>{!! session('success') !!}</div>
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

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error fs-4 me-2"></i>
                    <div>{{ session('warning') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Statistics Cards with Orange Theme --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                <div class="card card-border-shadow-warning h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="bx bx-upload bx-sm"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $stats['menunggu_sk'] }}</h4>
                        </div>
                        <p class="mb-1 fw-semibold">Menunggu Upload SK</p>
                        <p class="mb-0">
                            <small class="text-muted">Mahasiswa belum upload SK</small>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                <div class="card card-border-shadow-info h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="bx bx-calendar-check bx-sm"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $stats['menunggu_jadwal'] }}</h4>
                        </div>
                        <p class="mb-1 fw-semibold">Menunggu Penjadwalan</p>
                        <p class="mb-0">
                            <small class="text-muted">Perlu dijadwalkan admin</small>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
                <div class="card card-border-shadow-orange h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="bx bx-calendar bx-sm"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $stats['dijadwalkan'] }}</h4>
                        </div>
                        <p class="mb-1 fw-semibold">Sudah Dijadwalkan</p>
                        <p class="mb-0">
                            <small class="text-muted">Jadwal aktif & terkirim</small>
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
                            <h4 class="ms-1 mb-0">{{ $stats['selesai'] }}</h4>
                        </div>
                        <p class="mb-1 fw-semibold">Selesai</p>
                        <p class="mb-0">
                            <small class="text-muted">Ujian telah dilaksanakan</small>
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
                        <i class="bx bx-list-ul me-1"></i> Data Jadwal Ujian Hasil
                    </h5>

                    {{-- Bulk Delete Button --}}
                    <button type="button" class="btn btn-sm btn-danger" id="bulkDeleteBtn" style="display: none;">
                        <i class="bx bx-trash me-1"></i>
                        <span class="d-none d-sm-inline">Hapus Terpilih</span>
                        (<span id="selectedCount">0</span>)
                    </button>
                </div>

                {{-- Filter Tabs --}}
                <ul class="nav nav-pills card-header-pills" role="tablist">
                    <li class="nav-item">
                        <a href="{{ route('admin.jadwal-ujian-hasil.index', ['status' => 'menunggu_sk']) }}"
                            class="nav-link {{ $status === 'menunggu_sk' ? 'active' : '' }}">
                            <i class="bx bx-upload bx-xs me-1"></i>
                            <span class="d-none d-sm-inline">Menunggu SK</span>
                            <span class="d-inline d-sm-none">SK</span>
                            @if ($stats['menunggu_sk'] > 0)
                                <span class="badge rounded-pill bg-danger ms-1">{{ $stats['menunggu_sk'] }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.jadwal-ujian-hasil.index', ['status' => 'menunggu_jadwal']) }}"
                            class="nav-link {{ $status === 'menunggu_jadwal' ? 'active' : '' }}">
                            <i class="bx bx-calendar-check bx-xs me-1"></i>
                            <span class="d-none d-sm-inline">Menunggu Jadwal</span>
                            <span class="d-inline d-sm-none">Jadwal</span>
                            @if ($stats['menunggu_jadwal'] > 0)
                                <span class="badge rounded-pill bg-info ms-1">{{ $stats['menunggu_jadwal'] }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.jadwal-ujian-hasil.index', ['status' => 'dijadwalkan']) }}"
                            class="nav-link {{ $status === 'dijadwalkan' ? 'active' : '' }}">
                            <i class="bx bx-calendar bx-xs me-1"></i>
                            <span class="d-none d-sm-inline">Dijadwalkan</span>
                            <span class="d-inline d-sm-none">Aktif</span>
                            @if ($stats['dijadwalkan'] > 0)
                                <span class="badge rounded-pill bg-success ms-1">{{ $stats['dijadwalkan'] }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.jadwal-ujian-hasil.index', ['status' => 'selesai']) }}"
                            class="nav-link {{ $status === 'selesai' ? 'active' : '' }}">
                            <i class="bx bx-check-circle bx-xs me-1"></i>
                            <span class="d-none d-sm-inline">Selesai</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.jadwal-ujian-hasil.index', ['status' => 'semua']) }}"
                            class="nav-link {{ $status === 'semua' ? 'active' : '' }}">
                            <i class="bx bx-list-check bx-xs me-1"></i>
                            <span class="d-none d-sm-inline">Semua</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Card Body with Table --}}
            <div class="card-body">
                {{-- Bulk Delete Form --}}
                <form id="bulkDeleteForm" action="{{ route('admin.jadwal-ujian-hasil.bulk-destroy') }}"
                    method="POST">
                    @csrf
                    <div id="bulkDeleteInputs"></div>
                </form>

                {{-- Table --}}
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="3%">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                        <label class="form-check-label" for="selectAll"></label>
                                    </div>
                                </th>
                                <th width="4%">No</th>
                                <th width="17%">Mahasiswa</th>
                                <th width="20%">Judul Skripsi</th>
                                <th width="13%">Pembimbing</th>
                                <th width="16%">Jadwal</th>
                                <th width="11%">Status</th>
                                <th width="16%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($jadwals as $index => $jadwal)
                                <tr class="clickable-row"
                                    data-href="{{ route('admin.jadwal-ujian-hasil.show', $jadwal) }}">
                                    {{-- Checkbox --}}
                                    <td onclick="event.stopPropagation();">
                                        @if ($jadwal->canBeDeleted())
                                            <div class="form-check">
                                                <input class="form-check-input row-checkbox" type="checkbox"
                                                    value="{{ $jadwal->id }}"
                                                    data-mahasiswa="{{ $jadwal->pendaftaranUjianHasil->user->name }}"
                                                    data-nim="{{ $jadwal->pendaftaranUjianHasil->user->nim }}"
                                                    id="check-{{ $jadwal->id }}">
                                                <label class="form-check-label" for="check-{{ $jadwal->id }}"></label>
                                            </div>
                                        @else
                                            <div class="text-center">
                                                <i class="bx bx-lock-alt text-muted" data-bs-toggle="tooltip"
                                                    title="Tidak dapat dihapus"></i>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- No --}}
                                    <td><span class="fw-medium">{{ $jadwals->firstItem() + $index }}</span></td>

                                    {{-- Mahasiswa --}}
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong
                                                class="mb-1">{{ $jadwal->pendaftaranUjianHasil->user->name }}</strong>
                                            <small
                                                class="text-muted">{{ $jadwal->pendaftaranUjianHasil->user->nim }}</small>

                                            {{-- BATCH INFO --}}
                                            @if ($jadwal->isDijadwalkan() && $jadwal->hasJadwal())
                                                @php
                                                    $batchCount = \App\Models\JadwalUjianHasil::getScheduledCountByDate(
                                                        $jadwal->tanggal_ujian,
                                                    );
                                                @endphp
                                                @if ($batchCount > 1)
                                                    <span class="badge badge-sm bg-label-warning mt-1">
                                                        <i class="bx bx-group bx-xs"></i>
                                                        Batch: {{ $batchCount }} mahasiswa
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Judul --}}
                                    <td>
                                        <span class="text-truncate d-block" style="max-width: 250px;"
                                            data-bs-toggle="tooltip" title="{{ $jadwal->pendaftaranUjianHasil->judul_skripsi }}">
                                            {{ Str::limit(strip_tags($jadwal->pendaftaranUjianHasil->judul_skripsi), 50) }}
                                        </span>
                                    </td>

                                    {{-- Pembimbing --}}
                                    <td>
                                        <div class="d-flex flex-column">
                                            @if($jadwal->pendaftaranUjianHasil->dosenPembimbing1)
                                                <small class="text-muted">
                                                    <i class="bx bx-user bx-xs me-1"></i>
                                                    {{ Str::limit($jadwal->pendaftaranUjianHasil->dosenPembimbing1->name, 20) }}
                                                </small>
                                            @endif
                                            @if($jadwal->pendaftaranUjianHasil->dosenPembimbing2)
                                                <small class="text-muted">
                                                    <i class="bx bx-user bx-xs me-1"></i>
                                                    {{ Str::limit($jadwal->pendaftaranUjianHasil->dosenPembimbing2->name, 20) }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Jadwal --}}
                                    <td>
                                        @if ($jadwal->hasJadwal())
                                            <div class="d-flex flex-column gap-1">
                                                <span class="badge bg-label-warning">
                                                    <i class="bx bx-calendar bx-xs me-1"></i>
                                                    {{ $jadwal->tanggal_ujian->format('d M Y') }}
                                                </span>
                                                <small class="text-muted">
                                                    <i class="bx bx-time bx-xs me-1"></i>
                                                    {{ $jadwal->jam_formatted }}
                                                </small>
                                                <small class="text-muted">
                                                    <i class="bx bx-door-open bx-xs me-1"></i>
                                                    {{ Str::limit($jadwal->ruangan, 25) }}
                                                </small>

                                                {{-- BATCH INFO BADGES --}}
                                                @php
                                                    $batchDay = \App\Models\JadwalUjianHasil::getScheduledCountByDate(
                                                        $jadwal->tanggal_ujian,
                                                    );
                                                    $batchTime = \App\Models\JadwalUjianHasil::getScheduledCountByDateTime(
                                                        $jadwal->tanggal_ujian,
                                                        $jadwal->waktu_mulai,
                                                        $jadwal->waktu_selesai,
                                                    );
                                                @endphp

                                                @if ($batchDay > 1 || $batchTime > 1)
                                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                                        @if ($batchDay > 1)
                                                            <span class="badge badge-sm bg-label-warning"
                                                                data-bs-toggle="tooltip"
                                                                title="{{ $batchDay }} mahasiswa ujian pada hari ini">
                                                                <i class="bx bx-calendar-event bx-xs"></i>
                                                                {{ $batchDay }} mhs/hari
                                                            </span>
                                                        @endif

                                                        @if ($batchTime > 1)
                                                            <span class="badge badge-sm bg-label-success"
                                                                data-bs-toggle="tooltip"
                                                                title="{{ $batchTime }} mahasiswa ujian pada jam yang sama">
                                                                <i class="bx bx-time bx-xs"></i> {{ $batchTime }}
                                                                mhs/jam
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge bg-label-secondary">
                                                <i class="bx bx-calendar-x bx-xs me-1"></i>
                                                Belum Dijadwalkan
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td>{!! $jadwal->status_badge !!}</td>

                                    {{-- Actions --}}
                                    <td class="text-center" onclick="event.stopPropagation();">
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu shadow-sm">
                                                {{-- View Detail --}}
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.jadwal-ujian-hasil.show', $jadwal) }}">
                                                    <i class="bx bx-show me-1"></i> Lihat Detail
                                                </a>

                                                @if ($jadwal->status === 'menunggu_jadwal' || $jadwal->status === 'dijadwalkan')
                                                    {{-- Set Jadwal --}}
                                                    <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                                        data-bs-target="#scheduleModal"
                                                        data-jadwal-id="{{ $jadwal->id }}"
                                                        data-mahasiswa-nama="{{ $jadwal->pendaftaranUjianHasil->user->name }}"
                                                        data-mahasiswa-nim="{{ $jadwal->pendaftaranUjianHasil->user->nim }}"
                                                        data-mahasiswa-judul="{{ $jadwal->pendaftaranUjianHasil->judul_skripsi }}"
                                                        data-tanggal_ujian="{{ $jadwal->tanggal_ujian?->format('Y-m-d') }}"
                                                        data-jam-mulai="{{ $jadwal->waktu_mulai }}"
                                                        data-jam-selesai="{{ $jadwal->waktu_selesai }}"
                                                        data-ruangan="{{ $jadwal->ruangan }}">
                                                        <i class="bx bx-calendar-edit me-1"></i>
                                                        {{ $jadwal->hasJadwal() ? 'Edit Jadwal' : 'Set Jadwal' }}
                                                    </button>
                                                @endif

                                                @if ($jadwal->status === 'dijadwalkan')
                                                    <div class="dropdown-divider"></div>

                                                    {{-- Mark as Selesai --}}
                                                    @if ($jadwal->canMarkAsSelesai())
                                                        <form
                                                            action="{{ route('admin.jadwal-ujian-hasil.mark-selesai', $jadwal) }}"
                                                            method="POST" class="mark-selesai-form">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item text-success">
                                                                <i class="bx bx-check-circle me-1"></i> Tandai Selesai
                                                            </button>
                                                        </form>
                                                    @endif

                                                    {{-- Kirim Ulang Undangan --}}
                                                    <form
                                                        action="{{ route('admin.jadwal-ujian-hasil.kirim-ulang-undangan', $jadwal) }}"
                                                        method="POST" class="kirim-ulang-form">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-info">
                                                            <i class="bx bx-send me-1"></i> Kirim Ulang Undangan
                                                        </button>
                                                    </form>
                                                @endif

                                                @if ($jadwal->canBeDeleted())
                                                    <div class="dropdown-divider"></div>

                                                    {{-- Delete --}}
                                                    <form
                                                        action="{{ route('admin.jadwal-ujian-hasil.destroy', $jadwal) }}"
                                                        method="POST" class="delete-form"
                                                        data-mahasiswa="{{ $jadwal->pendaftaranUjianHasil->user->name }}"
                                                        data-nim="{{ $jadwal->pendaftaranUjianHasil->user->nim }}"
                                                        data-status="{{ $jadwal->status }}"
                                                        data-confirmation="{{ $jadwal->getDeleteConfirmationMessage() }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bx bx-trash me-1"></i> Hapus Jadwal
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bx bx-calendar-x display-4 text-muted mb-3"></i>
                                            <h5 class="text-muted">Tidak ada data jadwal</h5>
                                            <p class="text-muted mb-0">Belum ada jadwal ujian hasil untuk status ini
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($jadwals->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $jadwals->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Include Modal --}}
    @include('admin.jadwal-ujian-hasil.modals.schedule-modal')

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ========== CLICKABLE ROW ==========
            const clickableRows = document.querySelectorAll('.clickable-row');
            clickableRows.forEach(row => {
                row.addEventListener('click', function(e) {
                    if (e.target.closest('.form-check') ||
                        e.target.closest('.dropdown') ||
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

                row.addEventListener('mouseenter', function() {
                    if (!this.querySelector('.row-checkbox:checked')) {
                        this.style.backgroundColor = 'rgba(255, 159, 67, 0.08)';
                    }
                });

                row.addEventListener('mouseleave', function() {
                    if (!this.querySelector('.row-checkbox:checked')) {
                        this.style.backgroundColor = '';
                    }
                });
            });

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // ========== DELETE CONFIRMATION ==========
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const mahasiswa = this.dataset.mahasiswa;
                    const nim = this.dataset.nim;
                    const status = this.dataset.status;
                    const confirmation = this.dataset.confirmation;

                    const statusBadge = status === 'dijadwalkan' ?
                        '<span class="badge bg-danger">Sudah Dijadwalkan & Undangan Terkirim!</span>' :
                        status === 'menunggu_jadwal' ?
                        '<span class="badge bg-warning">Sudah Ada SK Ujian Hasil!</span>' :
                        '<span class="badge bg-secondary">Belum Ada SK</span>';

                    Swal.fire({
                        title: 'Konfirmasi Hapus Jadwal',
                        html: `
                            <div class="text-start">
                                <p class="mb-2"><strong>Mahasiswa:</strong> ${mahasiswa}</p>
                                <p class="mb-2"><strong>NIM:</strong> ${nim}</p>
                                <p class="mb-3"><strong>Status:</strong> ${statusBadge}</p>
                                <div class="alert alert-warning mb-0">
                                    <i class="bx bx-error me-2"></i>${confirmation}
                                </div>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="bx bx-trash me-1"></i> Ya, Hapus!',
                        cancelButtonText: '<i class="bx bx-x me-1"></i> Batal',
                        customClass: {
                            confirmButton: 'btn btn-danger me-2',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });

            // ========== MARK AS SELESAI CONFIRMATION ==========
            const markSelesaiForms = document.querySelectorAll('.mark-selesai-form');
            markSelesaiForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Konfirmasi Tandai Selesai',
                        text: 'Tandai ujian hasil ini sebagai selesai?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="bx bx-check-circle me-1"></i> Ya, Selesai',
                        cancelButtonText: '<i class="bx bx-x me-1"></i> Batal',
                        customClass: {
                            confirmButton: 'btn btn-success me-2',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });

            // ========== KIRIM ULANG CONFIRMATION ==========
            const kirimUlangForms = document.querySelectorAll('.kirim-ulang-form');
            kirimUlangForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Konfirmasi Kirim Ulang',
                        text: 'Kirim ulang undangan ke semua dosen penguji?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#ff9f43',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="bx bx-send me-1"></i> Ya, Kirim',
                        cancelButtonText: '<i class="bx bx-x me-1"></i> Batal',
                        customClass: {
                            confirmButton: 'btn btn-warning me-2',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });

            // ========== BULK DELETE FUNCTIONALITY ==========
            const selectAllCheckbox = document.getElementById('selectAll');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const selectedCount = document.getElementById('selectedCount');
            const bulkDeleteForm = document.getElementById('bulkDeleteForm');
            const bulkDeleteInputs = document.getElementById('bulkDeleteInputs');

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    rowCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateBulkDeleteButton();
                    updateRowHighlight();
                });
            }

            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSelectAllState();
                    updateBulkDeleteButton();
                    updateRowHighlight();
                });
            });

            function updateSelectAllState() {
                const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                const totalCount = rowCheckboxes.length;

                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = checkedCount === totalCount && totalCount > 0;
                    selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
                }
            }

            function updateBulkDeleteButton() {
                const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
                const count = checkedBoxes.length;

                if (count > 0) {
                    bulkDeleteBtn.style.display = 'inline-block';
                    selectedCount.textContent = count;
                } else {
                    bulkDeleteBtn.style.display = 'none';
                }
            }

            function updateRowHighlight() {
                rowCheckboxes.forEach(checkbox => {
                    const row = checkbox.closest('tr');
                    if (checkbox.checked) {
                        row.classList.add('row-selected');
                        row.style.backgroundColor = 'rgba(255, 159, 67, 0.1)';
                    } else {
                        row.classList.remove('row-selected');
                        row.style.backgroundColor = '';
                    }
                });
            }

            if (bulkDeleteBtn) {
                bulkDeleteBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
                    const jadwalIds = Array.from(checkedBoxes).map(cb => cb.value);
                    const mahasiswaList = Array.from(checkedBoxes).map(cb => ({
                        nama: cb.dataset.mahasiswa,
                        nim: cb.dataset.nim
                    }));

                    if (jadwalIds.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak Ada yang Dipilih',
                            text: 'Pilih minimal 1 jadwal untuk dihapus.',
                            confirmButtonColor: '#ff9f43',
                        });
                        return;
                    }

                    const mahasiswaListHtml = mahasiswaList
                        .slice(0, 5)
                        .map((mhs, idx) => `${idx + 1}. ${mhs.nama} (${mhs.nim})`)
                        .join('<br>');

                    const moreText = mahasiswaList.length > 5 ?
                        `<br><em class="text-muted">... dan ${mahasiswaList.length - 5} lainnya</em>` :
                        '';

                    Swal.fire({
                        title: 'Konfirmasi Bulk Delete',
                        html: `
                            <div class="text-start">
                                <div class="alert alert-warning mb-3">
                                    <i class="bx bx-error-circle me-2"></i>
                                    <strong>Total yang akan dihapus: ${jadwalIds.length} jadwal</strong>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title mb-2">Mahasiswa yang akan di-reset:</h6>
                                        <div class="small">
                                            ${mahasiswaListHtml}${moreText}
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-danger mt-3 mb-0">
                                    <i class="bx bx-info-circle me-2"></i>
                                    <strong>Perhatian:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Data jadwal akan dihapus</li>
                                        <li>File SK Ujian Hasil akan dihapus</li>
                                        <li>Status kembali ke "Menunggu SK"</li>
                                        <li>Mahasiswa harus upload SK baru</li>
                                    </ul>
                                </div>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="bx bx-trash me-1"></i> Ya, Hapus Semua!',
                        cancelButtonText: '<i class="bx bx-x me-1"></i> Batal',
                        customClass: {
                            confirmButton: 'btn btn-danger me-2',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false,
                        width: '600px'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            bulkDeleteInputs.innerHTML = '';

                            jadwalIds.forEach(id => {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'jadwal_ids[]';
                                input.value = id;
                                bulkDeleteInputs.appendChild(input);
                            });

                            Swal.fire({
                                title: 'Menghapus...',
                                html: 'Mohon tunggu, sedang menghapus jadwal...',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            bulkDeleteForm.submit();
                        }
                    });
                });
            }

            // Auto dismiss alerts
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert-dismissible');
                alerts.forEach(alert => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        });
    </script>
@endpush
