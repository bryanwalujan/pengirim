{{-- filepath: resources/views/admin/jadwal-seminar-proposal/index.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Jadwal Seminar Proposal')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="bx bx-calendar-event me-2"></i>Jadwal Seminar Proposal
                </h4>
                <p class="text-muted mb-0">Kelola jadwal ujian seminar proposal mahasiswa</p>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bx bx-error-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bx bx-error me-2"></i>
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <span class="fw-semibold d-block mb-1">Menunggu Upload SK</span>
                                <h3 class="card-title mb-2">{{ $stats['menunggu_sk'] }}</h3>
                                <small class="text-warning fw-semibold">
                                    <i class="bx bx-upload"></i>
                                    Mahasiswa Belum Upload
                                </small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="bx bx-upload bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <span class="fw-semibold d-block mb-1">Menunggu Penjadwalan</span>
                                <h3 class="card-title mb-2">{{ $stats['menunggu_jadwal'] }}</h3>
                                <small class="text-info fw-semibold">
                                    <i class="bx bx-calendar-check"></i>
                                    Perlu Dijadwalkan
                                </small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="bx bx-calendar-check bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <span class="fw-semibold d-block mb-1">Sudah Dijadwalkan</span>
                                <h3 class="card-title mb-2">{{ $stats['dijadwalkan'] }}</h3>
                                <small class="text-primary fw-semibold">
                                    <i class="bx bx-calendar"></i>
                                    Jadwal Aktif
                                </small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-calendar bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <span class="fw-semibold d-block mb-1">Selesai</span>
                                <h3 class="card-title mb-2">{{ $stats['selesai'] }}</h3>
                                <small class="text-success fw-semibold">
                                    <i class="bx bx-check-circle"></i>
                                    Ujian Selesai
                                </small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="bx bx-check-circle bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Table Card --}}
        <div class="card">
            <div class="card-header border-bottom">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-filter-alt me-1"></i> Filter & Data Jadwal
                    </h5>

                    {{-- ✅ TAMBAHAN: Bulk Delete Button --}}
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn" style="display: none;">
                            <i class="bx bx-trash me-1"></i> Hapus Terpilih (<span id="selectedCount">0</span>)
                        </button>
                    </div>
                </div>


                {{-- Filter Tabs --}}
                <ul class="nav nav-pills mb-0" role="tablist">
                    <li class="nav-item">
                        <a href="{{ route('admin.jadwal-seminar-proposal.index', ['status' => 'menunggu_sk']) }}"
                            class="nav-link {{ $status === 'menunggu_sk' ? 'active' : '' }}">
                            Menunggu SK
                            @if ($stats['menunggu_sk'] > 0)
                                <span
                                    class="badge rounded-pill badge-center bg-danger ms-1">{{ $stats['menunggu_sk'] }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.jadwal-seminar-proposal.index', ['status' => 'menunggu_jadwal']) }}"
                            class="nav-link {{ $status === 'menunggu_jadwal' ? 'active' : '' }}">
                            Menunggu Jadwal
                            @if ($stats['menunggu_jadwal'] > 0)
                                <span
                                    class="badge rounded-pill badge-center bg-info ms-1">{{ $stats['menunggu_jadwal'] }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.jadwal-seminar-proposal.index', ['status' => 'dijadwalkan']) }}"
                            class="nav-link {{ $status === 'dijadwalkan' ? 'active' : '' }}">
                            Dijadwalkan
                            @if ($stats['dijadwalkan'] > 0)
                                <span
                                    class="badge rounded-pill badge-center bg-success ms-1">{{ $stats['dijadwalkan'] }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.jadwal-seminar-proposal.index', ['status' => 'selesai']) }}"
                            class="nav-link {{ $status === 'selesai' ? 'active' : '' }}">
                            Selesai
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.jadwal-seminar-proposal.index', ['status' => 'semua']) }}"
                            class="nav-link {{ $status === 'semua' ? 'active' : '' }}">
                            Semua
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                {{-- ✅ TAMBAHAN: Bulk Delete Form --}}
                <form id="bulkDeleteForm" action="{{ route('admin.jadwal-seminar-proposal.bulk-destroy') }}"
                    method="POST" style="display: none;">
                    @csrf
                    <input type="hidden" name="_method" value="POST">
                    <div id="bulkDeleteInputs"></div>
                </form>

                {{-- Table --}}
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                {{-- ✅ TAMBAHAN: Checkbox column --}}
                                <th width="3%">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th width="5%">No</th>
                                <th width="18%">Mahasiswa</th>
                                <th width="22%">Judul Skripsi</th>
                                <th width="13%">Pembimbing</th>
                                <th width="15%">Jadwal</th>
                                <th width="10%">Status</th>
                                <th width="14%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwals as $index => $jadwal)
                                <tr>
                                    {{-- ✅ TAMBAHAN: Checkbox per row --}}
                                    <td>
                                        @if ($jadwal->canBeDeleted())
                                            <div class="form-check">
                                                <input class="form-check-input row-checkbox" type="checkbox"
                                                    value="{{ $jadwal->id }}" data-status="{{ $jadwal->status }}"
                                                    data-mahasiswa="{{ $jadwal->pendaftaranSeminarProposal->user->name }}">
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $jadwals->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span
                                                class="fw-semibold">{{ $jadwal->pendaftaranSeminarProposal->user->name }}</span>
                                            <small
                                                class="text-muted">{{ $jadwal->pendaftaranSeminarProposal->user->nim }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <small>{{ Str::limit($jadwal->pendaftaranSeminarProposal->judul_skripsi, 60) }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $jadwal->pendaftaranSeminarProposal->dosenPembimbing->name ?? '-' }}
                                        </small>
                                    </td>
                                    <td>
                                        @if ($jadwal->hasJadwal())
                                            <div class="d-flex flex-column">
                                                <small class="fw-semibold">
                                                    <i class="bx bx-calendar me-1"></i>
                                                    {{ \Carbon\Carbon::parse($jadwal->tanggal)->format('d M Y') }}
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
                                            <span class="badge bg-label-secondary">Belum Dijadwalkan</span>
                                        @endif
                                    </td>
                                    <td>{!! $jadwal->status_badge !!}</td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                {{-- View Detail --}}
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.jadwal-seminar-proposal.show', $jadwal) }}">
                                                    <i class="bx bx-show me-1"></i> Lihat Detail
                                                </a>

                                                {{-- Lihat SK --}}
                                                @if ($jadwal->hasSkFile())
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.jadwal-seminar-proposal.view-sk', $jadwal) }}"
                                                        target="_blank">
                                                        <i class="bx bx-file-blank me-1"></i> Lihat SK Proposal
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.jadwal-seminar-proposal.download-sk', $jadwal) }}">
                                                        <i class="bx bx-download me-1"></i> Download SK
                                                    </a>
                                                @endif

                                                <div class="dropdown-divider"></div>

                                                {{-- Buat Jadwal --}}
                                                @if ($jadwal->status === 'menunggu_jadwal')
                                                    <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                                        data-bs-target="#scheduleModal"
                                                        data-jadwal-id="{{ $jadwal->id }}"
                                                        data-mahasiswa-nama="{{ $jadwal->pendaftaranSeminarProposal->user->name }}"
                                                        data-mahasiswa-nim="{{ $jadwal->pendaftaranSeminarProposal->user->nim }}"
                                                        data-mahasiswa-judul="{{ $jadwal->pendaftaranSeminarProposal->judul_skripsi }}">
                                                        <i class="bx bx-calendar-plus me-1 text-primary"></i> Buat Jadwal
                                                    </button>
                                                @endif

                                                {{-- Edit Jadwal --}}
                                                @if ($jadwal->status === 'dijadwalkan')
                                                    <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                                        data-bs-target="#scheduleModal"
                                                        data-jadwal-id="{{ $jadwal->id }}"
                                                        data-mahasiswa-nama="{{ $jadwal->pendaftaranSeminarProposal->user->name }}"
                                                        data-mahasiswa-nim="{{ $jadwal->pendaftaranSeminarProposal->user->nim }}"
                                                        data-mahasiswa-judul="{{ $jadwal->pendaftaranSeminarProposal->judul_skripsi }}"
                                                        data-tanggal="{{ $jadwal->tanggal ? $jadwal->tanggal->format('Y-m-d') : '' }}"
                                                        data-jam-mulai="{{ $jadwal->jam_mulai }}"
                                                        data-jam-selesai="{{ $jadwal->jam_selesai }}"
                                                        data-ruangan="{{ $jadwal->ruangan }}">
                                                        <i class="bx bx-edit me-1 text-warning"></i> Edit Jadwal
                                                    </button>

                                                    <div class="dropdown-divider"></div>

                                                    {{-- Kirim Ulang Undangan --}}
                                                    <form
                                                        action="{{ route('admin.jadwal-seminar-proposal.kirim-ulang-undangan', $jadwal) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Kirim ulang undangan ke semua dosen?')">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="bx bx-send me-1 text-info"></i> Kirim Ulang Undangan
                                                        </button>
                                                    </form>

                                                    {{-- Mark as Selesai --}}
                                                    <form
                                                        action="{{ route('admin.jadwal-seminar-proposal.mark-selesai', $jadwal) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Tandai seminar proposal ini sebagai selesai?')">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="bx bx-check-circle me-1 text-success"></i> Tandai
                                                            Selesai
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- ✅ TAMBAHAN: Delete Button --}}
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
                                        <i class="bx bx-info-circle bx-lg text-muted mb-3 d-block"></i>
                                        <p class="text-muted mb-0">
                                            Tidak ada data jadwal dengan status
                                            <strong>{{ ucwords(str_replace('_', ' ', $status)) }}</strong>
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($jadwals->hasPages())
                    <div class="mt-4">
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

            // ========== BULK DELETE ==========
            const selectAllCheckbox = document.getElementById('selectAll');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const selectedCount = document.getElementById('selectedCount');
            const bulkDeleteForm = document.getElementById('bulkDeleteForm');
            const bulkDeleteInputs = document.getElementById('bulkDeleteInputs');

            // Select All functionality
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    rowCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateBulkDeleteButton();
                });
            }

            // Individual checkbox change
            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSelectAllState();
                    updateBulkDeleteButton();
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

            // Bulk Delete Button Click
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
                        <div class="alert alert-info">
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
                            // Populate hidden inputs
                            bulkDeleteInputs.innerHTML = '';
                            jadwalIds.forEach(id => {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'jadwal_ids[]';
                                input.value = id;
                                bulkDeleteInputs.appendChild(input);
                            });

                            // Submit form
                            bulkDeleteForm.submit();
                        }
                    });
                });
            }
        });
    </script>
@endpush
