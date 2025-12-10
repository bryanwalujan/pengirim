{{-- filepath: resources/views/admin/jadwal-seminar-proposal/index.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Jadwal Seminar Proposal')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Jadwal Seminar Proposal</li>
            </ol>
        </nav>

        {{-- Header --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div class="me-3">
                <h4 class="fw-bold mb-1">
                    <i class="bx bx-calendar-event me-2 text-primary"></i>Jadwal Seminar Proposal
                </h4>
                <p class="text-muted mb-0">Kelola dan monitor jadwal ujian seminar proposal mahasiswa</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.jadwal-seminar-proposal.calendar') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bx bx-calendar me-1"></i> Lihat Kalender
                </a>
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

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error fs-4 me-2"></i>
                    <div>{{ session('warning') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Statistics Cards --}}
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
                <div class="card card-border-shadow-primary h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-primary">
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
                        <i class="bx bx-list-ul me-1"></i> Data Jadwal Seminar Proposal
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
                        <a href="{{ route('admin.jadwal-seminar-proposal.index', ['status' => 'menunggu_sk']) }}"
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
                        <a href="{{ route('admin.jadwal-seminar-proposal.index', ['status' => 'menunggu_jadwal']) }}"
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
                        <a href="{{ route('admin.jadwal-seminar-proposal.index', ['status' => 'dijadwalkan']) }}"
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
                        <a href="{{ route('admin.jadwal-seminar-proposal.index', ['status' => 'selesai']) }}"
                            class="nav-link {{ $status === 'selesai' ? 'active' : '' }}">
                            <i class="bx bx-check-circle bx-xs me-1"></i>
                            <span class="d-none d-sm-inline">Selesai</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.jadwal-seminar-proposal.index', ['status' => 'semua']) }}"
                            class="nav-link {{ $status === 'semua' ? 'active' : '' }}">
                            <i class="bx bx-list-check bx-xs me-1"></i>
                            <span class="d-none d-sm-inline">Semua</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Card Body with Table --}}
            <div class="card-body">
                {{-- Bulk Delete Form (Hidden) --}}
                <form id="bulkDeleteForm" action="{{ route('admin.jadwal-seminar-proposal.bulk-destroy') }}"
                    method="POST" style="display: none;">
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
                        {{-- Update bagian tbody --}}
                        <tbody class="table-border-bottom-0">
                            @forelse($jadwals as $index => $jadwal)
                                <tr class="clickable-row"
                                    data-href="{{ route('admin.jadwal-seminar-proposal.show', $jadwal) }}"
                                    style="cursor: pointer;">
                                    {{-- Checkbox --}}
                                    <td onclick="event.stopPropagation();">
                                        @if ($jadwal->canBeDeleted())
                                            <div class="form-check">
                                                <input class="form-check-input row-checkbox" type="checkbox"
                                                    value="{{ $jadwal->id }}" data-status="{{ $jadwal->status }}"
                                                    data-mahasiswa="{{ $jadwal->pendaftaranSeminarProposal->user->name }}"
                                                    id="checkbox{{ $jadwal->id }}">
                                                <label class="form-check-label"
                                                    for="checkbox{{ $jadwal->id }}"></label>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- No --}}
                                    <td><span class="fw-medium">{{ $jadwals->firstItem() + $index }}</span></td>

                                    {{-- Mahasiswa --}}
                                    <td>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-0">{{ $jadwal->pendaftaranSeminarProposal->user->name }}</h6>
                                            <small class="text-muted">NIM:
                                                {{ $jadwal->pendaftaranSeminarProposal->user->nim }}</small>
                                        </div>
                                    </td>

                                    {{-- Judul --}}
                                    <td>
                                        <span class="text-truncate d-block" style="max-width: 250px;"
                                            data-bs-toggle="tooltip"
                                            title="{{ strip_tags($jadwal->pendaftaranSeminarProposal->judul_skripsi) }}">
                                            {{ Str::limit(strip_tags($jadwal->pendaftaranSeminarProposal->judul_skripsi), 50) }}
                                        </span>
                                    </td>

                                    {{-- Pembimbing --}}
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ strtoupper(substr($jadwal->pendaftaranSeminarProposal->dosenPembimbing->name ?? 'N', 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <small class="text-truncate d-block" style="max-width: 120px;">
                                                    {{ $jadwal->pendaftaranSeminarProposal->dosenPembimbing->name ?? '-' }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Jadwal --}}
                                    <td>
                                        @if ($jadwal->hasJadwal())
                                            <div class="d-flex flex-column gap-1">
                                                <small>
                                                    <i class="bx bx-calendar me-1 text-primary"></i>
                                                    <strong>{{ \Carbon\Carbon::parse($jadwal->tanggal)->locale('id')->translatedFormat('d M Y') }}</strong>
                                                </small>
                                                <small class="text-muted">
                                                    <i class="bx bx-time me-1"></i>
                                                    {{ $jadwal->jam_formatted }}
                                                </small>
                                                <small class="text-muted">
                                                    <i class="bx bx-door-open me-1"></i>
                                                    {{ $jadwal->ruangan }}
                                                </small>
                                            </div>
                                        @else
                                            <span class="badge bg-label-secondary">
                                                <i class="bx bx-time-five me-1"></i>Belum Dijadwalkan
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td>{!! $jadwal->status_badge !!}</td>

                                    {{-- Actions --}}
                                    <td class="text-center" onclick="event.stopPropagation();">
                                        <div class="dropdown">
                                            <button type="button"
                                                class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                {{-- View Detail --}}
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.jadwal-seminar-proposal.show', $jadwal) }}">
                                                    <i class="bx bx-show me-2"></i> Lihat Detail
                                                </a>

                                                {{-- SK Actions --}}
                                                @if ($jadwal->hasSkFile())
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.jadwal-seminar-proposal.view-sk', $jadwal) }}"
                                                        target="_blank">
                                                        <i class="bx bx-file-blank me-2"></i> Lihat SK
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.jadwal-seminar-proposal.download-sk', $jadwal) }}">
                                                        <i class="bx bx-download me-2"></i> Download SK
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                @endif

                                                {{-- Buat/Edit Jadwal --}}
                                                @if ($jadwal->status === 'menunggu_jadwal')
                                                    <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                                        data-bs-target="#scheduleModal"
                                                        data-jadwal-id="{{ $jadwal->id }}"
                                                        data-mahasiswa-nama="{{ $jadwal->pendaftaranSeminarProposal->user->name }}"
                                                        data-mahasiswa-nim="{{ $jadwal->pendaftaranSeminarProposal->user->nim }}"
                                                        data-mahasiswa-judul="{{ strip_tags($jadwal->pendaftaranSeminarProposal->judul_skripsi) }}">
                                                        <i class="bx bx-calendar-plus me-2 text-primary"></i> Buat Jadwal
                                                    </button>
                                                @endif

                                                @if ($jadwal->status === 'dijadwalkan')
                                                    <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                                        data-bs-target="#scheduleModal"
                                                        data-jadwal-id="{{ $jadwal->id }}"
                                                        data-mahasiswa-nama="{{ $jadwal->pendaftaranSeminarProposal->user->name }}"
                                                        data-mahasiswa-nim="{{ $jadwal->pendaftaranSeminarProposal->user->nim }}"
                                                        data-mahasiswa-judul="{{ strip_tags($jadwal->pendaftaranSeminarProposal->judul_skripsi) }}"
                                                        data-tanggal="{{ $jadwal->tanggal ? $jadwal->tanggal->format('Y-m-d') : '' }}"
                                                        data-jam-mulai="{{ $jadwal->jam_mulai }}"
                                                        data-jam-selesai="{{ $jadwal->jam_selesai }}"
                                                        data-ruangan="{{ $jadwal->ruangan }}">
                                                        <i class="bx bx-edit me-2 text-warning"></i> Edit Jadwal
                                                    </button>

                                                    <div class="dropdown-divider"></div>

                                                    {{-- Kirim Ulang --}}
                                                    <form
                                                        action="{{ route('admin.jadwal-seminar-proposal.kirim-ulang-undangan', $jadwal) }}"
                                                        method="POST" class="d-inline kirim-ulang-form">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="bx bx-send me-2 text-info"></i> Kirim Ulang Undangan
                                                        </button>
                                                    </form>

                                                    {{-- Mark Selesai --}}
                                                    <form
                                                        action="{{ route('admin.jadwal-seminar-proposal.mark-selesai', $jadwal) }}"
                                                        method="POST" class="d-inline mark-selesai-form">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="bx bx-check-circle me-2 text-success"></i> Tandai
                                                            Selesai
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- Delete --}}
                                                @if ($jadwal->canBeDeleted())
                                                    <div class="dropdown-divider"></div>
                                                    <form
                                                        action="{{ route('admin.jadwal-seminar-proposal.destroy', $jadwal) }}"
                                                        method="POST" class="d-inline delete-form"
                                                        data-mahasiswa="{{ $jadwal->pendaftaranSeminarProposal->user->name }}"
                                                        data-nim="{{ $jadwal->pendaftaranSeminarProposal->user->nim }}"
                                                        data-status="{{ $jadwal->status }}"
                                                        data-confirmation="{{ $jadwal->getDeleteConfirmationMessage() }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bx bx-trash me-2"></i> Hapus Jadwal
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
                                            <i class="bx bx-info-circle bx-lg text-muted mb-3"></i>
                                            <h6 class="text-muted mb-1">Tidak Ada Data</h6>
                                            <p class="text-muted mb-0">
                                                Tidak ada jadwal dengan status
                                                <strong>{{ ucwords(str_replace('_', ' ', $status)) }}</strong>
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
    @include('admin.jadwal-seminar-proposal.modals.schedule-modal')

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ========== CLICKABLE ROW ==========
            const clickableRows = document.querySelectorAll('.clickable-row');
            clickableRows.forEach(row => {
                row.addEventListener('click', function(e) {
                    // Jangan redirect jika klik di checkbox, dropdown, atau link
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

                // Add hover effect
                row.addEventListener('mouseenter', function() {
                    if (!this.querySelector('.row-checkbox:checked')) {
                        this.style.backgroundColor = 'rgba(67, 89, 113, 0.08)';
                    }
                });

                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // ========== SCHEDULE MODAL ==========
            const scheduleModal = document.getElementById('scheduleModal');
            if (scheduleModal) {
                scheduleModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const tanggal = button.getAttribute('data-tanggal');
                    const jamMulai = button.getAttribute('data-jam-mulai');
                    const jamSelesai = button.getAttribute('data-jam-selesai');
                    const ruangan = button.getAttribute('data-ruangan');

                    if (tanggal) document.getElementById('tanggal').value = tanggal;
                    if (jamMulai) document.getElementById('jam_mulai').value = jamMulai;
                    if (jamSelesai) document.getElementById('jam_selesai').value = jamSelesai;
                    if (ruangan) document.getElementById('ruangan').value = ruangan;
                });
            }

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
                        '<span class="badge bg-warning">Sudah Ada SK Proposal!</span>' :
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
                        text: 'Tandai seminar proposal ini sebagai selesai?',
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
                        text: 'Kirim ulang undangan ke semua dosen?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#17a2b8',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="bx bx-send me-1"></i> Ya, Kirim',
                        cancelButtonText: '<i class="bx bx-x me-1"></i> Batal',
                        customClass: {
                            confirmButton: 'btn btn-info me-2',
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

            // ========== BULK DELETE ==========
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
                        row.style.backgroundColor = 'rgba(67, 89, 113, 0.1)';
                    } else {
                        row.style.backgroundColor = '';
                    }
                });
            }

            if (bulkDeleteBtn) {
                bulkDeleteBtn.addEventListener('click', function() {
                    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
                    const jadwalIds = Array.from(checkedBoxes).map(cb => cb.value);
                    const mahasiswaList = Array.from(checkedBoxes).map(cb => cb.dataset.mahasiswa);

                    if (jadwalIds.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak Ada yang Dipilih',
                            text: 'Pilih minimal 1 jadwal untuk dihapus.',
                        });
                        return;
                    }

                    const mahasiswaListHtml = mahasiswaList
                        .slice(0, 5)
                        .map((nama, idx) => `${idx + 1}. ${nama}`)
                        .join('<br>');

                    const moreText = mahasiswaList.length > 5 ?
                        `<br><em>... dan ${mahasiswaList.length - 5} lainnya</em>` :
                        '';

                    Swal.fire({
                        title: 'Konfirmasi Bulk Delete',
                        html: `
                            <div class="text-start">
                                <p class="mb-2"><strong>Total yang akan dihapus: ${jadwalIds.length} jadwal</strong></p>
                                <div class="alert alert-info mb-2">
                                    <strong>Mahasiswa:</strong><br>
                                    ${mahasiswaListHtml}${moreText}
                                </div>
                                <div class="alert alert-danger mb-0">
                                    <i class="bx bx-error-circle me-2"></i>
                                    <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan!
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
                        buttonsStyling: false
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

@push('styles')
    <style>
        /* Sneat-styled enhancements */
        .card-border-shadow-warning {
            border-left: 3px solid #ffab00;
        }

        .card-border-shadow-info {
            border-left: 3px solid #03c3ec;
        }

        .card-border-shadow-primary {
            border-left: 3px solid #696cff;
        }

        .card-border-shadow-success {
            border-left: 3px solid #71dd37;
        }

        /* Clickable row styles */
        .clickable-row {
            transition: background-color 0.2s ease;
        }

        .clickable-row:hover {
            background-color: rgba(67, 89, 113, 0.08) !important;
        }

        .clickable-row:active {
            background-color: rgba(67, 89, 113, 0.12) !important;
        }

        /* Don't change cursor on checkbox and actions */
        .clickable-row td:first-child,
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

        /* Selected row highlight */
        .clickable-row.row-selected {
            background-color: rgba(67, 89, 113, 0.1) !important;
        }
    </style>
@endpush
