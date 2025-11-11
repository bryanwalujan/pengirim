{{-- filepath: resources/views/admin/komisi-proposal/detail-modal.blade.php --}}
<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <th width="200px">Nama Mahasiswa</th>
                        <td>{{ $komisi->user->name }}</td>
                    </tr>
                    <tr>
                        <th>NIM</th>
                        <td>{{ $komisi->user->nim }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Pengajuan</th>
                        <td>{{ \Carbon\Carbon::parse($komisi->created_at)->translatedFormat('l, d M Y H:i') }} WITA</td>
                    </tr>
                    <tr>
                        <th style="vertical-align: top">Judul Skripsi</th>
                        <td style="vertical-align: top">{!! $komisi->judul_skripsi !!}</td>
                    </tr>
                    <tr>
                        <th>Pembimbing Akademik</th>
                        <td>
                            {{ $komisi->pembimbing->name }}<br>
                            <small class="text-muted">{{ $komisi->pembimbing->jabatan ?? '-' }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Status Saat Ini</th>
                        <td>
                            @if ($komisi->status == 'pending')
                                <span class="badge bg-label-warning">Menunggu Persetujuan PA</span>
                            @elseif($komisi->status == 'approved_pa')
                                <span class="badge bg-label-info">Disetujui PA - Menunggu Korprodi</span>
                            @elseif($komisi->status == 'approved')
                                <span class="badge bg-label-success">Disetujui Lengkap</span>
                            @else
                                <span class="badge bg-label-danger">Ditolak</span>
                            @endif
                        </td>
                    </tr>

                    @if ($komisi->penandatanganPA)
                        <tr>
                            <th>Disetujui PA oleh</th>
                            <td>
                                {{ $komisi->penandatanganPA->name }}<br>
                                <small class="text-muted">
                                    {{ $komisi->penandatanganPA->jabatan ?? '-' }}<br>
                                    {{ $komisi->tanggal_persetujuan_pa->format('d M Y, H:i') }} WITA
                                </small>
                            </td>
                        </tr>
                    @endif

                    @if ($komisi->penandatanganKorprodi)
                        <tr>
                            <th>Disetujui Korprodi oleh</th>
                            <td>
                                {{ $komisi->penandatanganKorprodi->name }}<br>
                                <small class="text-muted">
                                    {{ $komisi->penandatanganKorprodi->jabatan ?? '-' }}<br>
                                    {{ $komisi->tanggal_persetujuan_korprodi->format('d M Y, H:i') }} WITA
                                </small>
                            </td>
                        </tr>
                    @endif

                    @if ($komisi->keterangan)
                        <tr>
                            <th style="vertical-align: top">Keterangan</th>
                            <td style="vertical-align: top">{{ $komisi->keterangan }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            @php
                $user = Auth::user();

                // Helper function untuk cek Korprodi
                $isKoordinatorProdi = function ($user) {
                    if (!$user->hasRole('dosen')) {
                        return false;
                    }
                    $jabatan = strtolower($user->jabatan ?? '');
                    return str_contains($jabatan, 'koordinator program studi') ||
                        str_contains($jabatan, 'korprodi') ||
                        str_contains($jabatan, 'kaprodi') ||
                        str_contains($jabatan, 'ketua program studi');
                };

                $isKorprodi = $isKoordinatorProdi($user);

                // Cek apakah user adalah PA untuk proposal INI
                $isPAForThisProposal = $user->hasRole('dosen') && $komisi->dosen_pembimbing_id == $user->id;

                // Conditions untuk approval
                $canApprovePA = $isPAForThisProposal && $komisi->canBeApprovedByPA();
                $canApproveKorprodi = $isKorprodi && $komisi->canBeApprovedByKorprodi();

                // Permission untuk delete
                $canDelete = $user->hasRole('staff') || $isPAForThisProposal || $isKorprodi;
            @endphp

            @if ($canApprovePA)
                {{-- Form untuk PA --}}
                <div class="alert alert-info mb-3">
                    <i class="bx bx-info-circle me-1"></i>
                    <strong>Info:</strong> Anda adalah Pembimbing Akademik mahasiswa ini dan dapat menyetujui
                    pengajuan.<br>
                    <small>
                        <strong>Nama Anda:</strong> {{ $user->name }}<br>
                        <strong>Jabatan:</strong> {{ $user->jabatan ?? '-' }}<br>
                        <strong>Status:</strong> PA untuk {{ $komisi->user->name }} ({{ $komisi->user->nim }})
                    </small>
                </div>

                {{-- Form APPROVE --}}
                <form action="{{ route('admin.komisi-proposal.approve-pa', $komisi->id) }}" method="POST"
                    id="formApprovePA_{{ $komisi->id }}" class="komisi-approval-form mb-3">
                    @csrf
                    <input type="hidden" name="action" value="approve">

                    <button type="button" class="btn btn-success w-100 btn-approve-pa"
                        data-form-id="formApprovePA_{{ $komisi->id }}" data-mahasiswa="{{ $komisi->user->name }}"
                        data-nim="{{ $komisi->user->nim }}">
                        <i class="bx bx-check-circle me-1"></i> Setujui sebagai PA
                    </button>
                </form>

                {{-- Form REJECT dengan Textarea --}}
                <div class="card border-danger">
                    <div class="card-body">
                        <h6 class="card-title text-danger mb-3">
                            <i class="bx bx-x-circle me-1"></i> Tolak Pengajuan
                        </h6>

                        <form action="{{ route('admin.komisi-proposal.approve-pa', $komisi->id) }}" method="POST"
                            id="formRejectPA_{{ $komisi->id }}" class="komisi-approval-form">
                            @csrf
                            <input type="hidden" name="action" value="reject">

                            <div class="mb-3">
                                <label for="keteranganPA_{{ $komisi->id }}" class="form-label">
                                    Alasan Penolakan <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="keteranganPA_{{ $komisi->id }}" name="keterangan" rows="4"
                                    placeholder="Berikan alasan penolakan yang jelas dan detail..." required></textarea>
                                <small class="text-muted">Minimal 10 karakter</small>
                            </div>

                            <button type="button" class="btn btn-danger w-100 btn-reject-pa"
                                data-form-id="formRejectPA_{{ $komisi->id }}"
                                data-keterangan-id="keteranganPA_{{ $komisi->id }}"
                                data-mahasiswa="{{ $komisi->user->name }}" data-nim="{{ $komisi->user->nim }}">
                                <i class="bx bx-x-circle me-1"></i> Tolak Pengajuan
                            </button>
                        </form>
                    </div>
                </div>
            @elseif($canApproveKorprodi)
                {{-- Form untuk Korprodi --}}
                <div class="alert alert-info mb-3">
                    <i class="bx bx-info-circle me-1"></i>
                    <strong>Info:</strong> Anda dapat menyetujui pengajuan ini sebagai Koordinator Program Studi.<br>
                    <small>
                        <strong>Nama Anda:</strong> {{ $user->name }}<br>
                        <strong>Jabatan:</strong> {{ $user->jabatan ?? '-' }}
                    </small>
                </div>

                {{-- Form APPROVE --}}
                <form action="{{ route('admin.komisi-proposal.approve-korprodi', $komisi->id) }}" method="POST"
                    id="formApproveKorprodi_{{ $komisi->id }}" class="komisi-approval-form mb-3">
                    @csrf
                    <input type="hidden" name="action" value="approve">

                    <button type="button" class="btn btn-success w-100 btn-approve-korprodi"
                        data-form-id="formApproveKorprodi_{{ $komisi->id }}"
                        data-mahasiswa="{{ $komisi->user->name }}" data-nim="{{ $komisi->user->nim }}">
                        <i class="bx bx-check-circle me-1"></i> Setujui sebagai Korprodi
                    </button>
                </form>

                {{-- Form REJECT dengan Textarea --}}
                <div class="card border-danger">
                    <div class="card-body">
                        <h6 class="card-title text-danger mb-3">
                            <i class="bx bx-x-circle me-1"></i> Tolak Pengajuan
                        </h6>

                        <form action="{{ route('admin.komisi-proposal.approve-korprodi', $komisi->id) }}"
                            method="POST" id="formRejectKorprodi_{{ $komisi->id }}" class="komisi-approval-form">
                            @csrf
                            <input type="hidden" name="action" value="reject">

                            <div class="mb-3">
                                <label for="keteranganKorprodi_{{ $komisi->id }}" class="form-label">
                                    Alasan Penolakan <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="keteranganKorprodi_{{ $komisi->id }}" name="keterangan" rows="4"
                                    placeholder="Berikan alasan penolakan yang jelas dan detail..." required></textarea>
                                <small class="text-muted">Minimal 10 karakter</small>
                            </div>

                            <button type="button" class="btn btn-danger w-100 btn-reject-korprodi"
                                data-form-id="formRejectKorprodi_{{ $komisi->id }}"
                                data-keterangan-id="keteranganKorprodi_{{ $komisi->id }}"
                                data-mahasiswa="{{ $komisi->user->name }}" data-nim="{{ $komisi->user->nim }}">
                                <i class="bx bx-x-circle me-1"></i> Tolak Pengajuan
                            </button>
                        </form>
                    </div>
                </div>
            @else
                {{-- View only --}}
                @if ($komisi->status == 'approved' && $komisi->file_komisi)
                    <a href="{{ route('admin.komisi-proposal.download', $komisi->id) }}"
                        class="btn btn-success w-100" target="_blank">
                        <i class="bx bxs-file-pdf me-1"></i> Download PDF Final
                    </a>
                @elseif($komisi->status == 'approved_pa' && $isKorprodi && $komisi->file_komisi_pa)
                    <a href="{{ route('admin.komisi-proposal.download', $komisi->id) }}"
                        class="btn btn-info w-100 mb-2" target="_blank">
                        <i class="bx bxs-file-pdf me-1"></i> Download PDF (Persetujuan PA)
                    </a>
                    <div class="alert alert-warning mb-0">
                        <i class="bx bx-info-circle me-1"></i>
                        Menunggu persetujuan Anda sebagai Koordinator Program Studi
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="bx bx-info-circle me-1"></i>
                        @if ($komisi->status == 'pending')
                            Menunggu persetujuan dari Pembimbing Akademik ({{ $komisi->pembimbing->name }})
                        @elseif($komisi->status == 'approved_pa')
                            Menunggu persetujuan dari Koordinator Program Studi
                        @elseif($komisi->status == 'rejected')
                            Pengajuan ditolak
                            @if ($komisi->keterangan)
                                <br><small><strong>Alasan:</strong> {{ $komisi->keterangan }}</small>
                            @endif
                        @else
                            Status: {{ $komisi->status }}
                        @endif

                        @if ($user->hasRole('dosen'))
                            <hr class="my-2">
                            <small>
                                <strong>Informasi Anda:</strong><br>
                                Nama: {{ $user->name }}<br>
                                Jabatan: {{ $user->jabatan ?? 'Tidak ada jabatan' }}<br>
                                @if ($isPAForThisProposal)
                                    <span class="badge bg-success">Anda adalah PA mahasiswa ini</span>
                                @else
                                    <span class="badge bg-secondary">Anda bukan PA mahasiswa ini</span>
                                @endif
                            </small>
                        @endif
                    </div>
                @endif
            @endif

            {{-- Delete Button Section - SHOW untuk Admin, PA, atau Korprodi --}}
            @if ($canDelete)
                <div class="card border-danger mt-3">
                    <div class="card-body">
                        <h6 class="card-title text-danger mb-3">
                            <i class="bx bx-trash me-1"></i> Zona Berbahaya
                        </h6>

                        <div class="alert alert-warning mb-3">
                            <i class="bx bx-info-circle me-1"></i>
                            <strong>Perhatian:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Menghapus proposal akan menghapus <strong>semua data dan file PDF</strong> terkait
                                </li>
                                <li>Mahasiswa <strong>dapat mengajukan proposal baru</strong> setelah dihapus</li>
                                <li>Tindakan ini <strong>tidak dapat dibatalkan</strong></li>
                            </ul>
                        </div>

                        <div class="d-grid">
                            <button type="button" class="btn btn-danger btn-delete-proposal"
                                data-proposal-id="{{ $komisi->id }}" data-mahasiswa="{{ $komisi->user->name }}"
                                data-nim="{{ $komisi->user->nim }}" data-status="{{ $komisi->status }}"
                                data-status-text="{{ $komisi->getDeleteConfirmationMessage() }}">
                                <i class="bx bx-trash me-1"></i> Hapus Pengajuan Proposal
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- SweetAlert2 Script - SIMPLIFIED --}}
<script>
    (function() {
        'use strict';

        // Konfigurasi SweetAlert2 dengan spacing button
        const swalConfig = {
            customClass: {
                container: 'swal-high-zindex',
                popup: 'swal-popup-custom',
                confirmButton: 'btn btn-success btn-lg px-4 me-3', // TAMBAH me-3 untuk spacing
                cancelButton: 'btn btn-secondary btn-lg px-4',
                denyButton: 'btn btn-danger btn-lg px-4 me-3'
            },
            buttonsStyling: false,
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            backdrop: 'rgba(0, 0, 0, 0.6)',
            heightAuto: false
        };

        // Handler untuk Approve PA - SIMPLE CONFIRMATION
        $(document).on('click', '.btn-approve-pa', function(e) {
            e.preventDefault();

            const formId = $(this).data('form-id');
            const mahasiswa = $(this).data('mahasiswa');
            const nim = $(this).data('nim');
            const form = document.getElementById(formId);

            Swal.fire({
                ...swalConfig,
                title: '<strong>Setujui Pengajuan?</strong>',
                html: `
                <div class="text-start">
                    <p class="mb-3">Apakah Anda yakin ingin <strong class="text-success">MENYETUJUI</strong> pengajuan komisi proposal ini?</p>
                    <div class="alert alert-info mb-0">
                        <i class="bx bx-info-circle me-2"></i>
                        <strong>Detail Mahasiswa:</strong><br>
                        <small>
                            Nama: <strong>${mahasiswa}</strong><br>
                            NIM: <strong>${nim}</strong>
                        </small>
                    </div>
                </div>
            `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="bx bx-check-circle me-2"></i>Ya, Setujui',
                cancelButtonText: '<i class="bx bx-x me-2"></i>Batal',
                didOpen: () => {
                    const swalContainer = document.querySelector('.swal2-container');
                    if (swalContainer) {
                        swalContainer.style.zIndex = '99999';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        ...swalConfig,
                        title: 'Memproses...',
                        html: '<p class="mb-0">Mohon tunggu, sedang memproses persetujuan...</p>',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            const swalContainer = document.querySelector(
                                '.swal2-container');
                            if (swalContainer) {
                                swalContainer.style.zIndex = '99999';
                            }
                        }
                    });
                    form.submit();
                }
            });
        });

        // Handler untuk Delete Proposal
        $(document).on('click', '.btn-delete-proposal', function(e) {
            e.preventDefault();

            const proposalId = $(this).data('proposal-id');
            const mahasiswa = $(this).data('mahasiswa');
            const nim = $(this).data('nim');
            const status = $(this).data('status');
            const statusText = $(this).data('status-text');

            const statusBadge = {
                'pending': '<span class="badge bg-warning">Menunggu PA</span>',
                'approved_pa': '<span class="badge bg-info">Menunggu Korprodi</span>',
                'approved': '<span class="badge bg-success">Disetujui Lengkap</span>',
                'rejected': '<span class="badge bg-danger">Ditolak</span>'
            };

            Swal.fire({
                ...swalConfig,
                title: '<strong>Hapus Pengajuan Proposal?</strong>',
                html: `
                <div class="text-start">
                    <div class="alert alert-danger mb-3">
                        <i class="bx bx-error-circle me-2"></i>
                        <strong>PERINGATAN!</strong> Tindakan ini akan menghapus:
                        <ul class="mb-0 mt-2">
                            <li>Data pengajuan proposal</li>
                            <li>File PDF yang sudah di-generate</li>
                            <li>Semua riwayat persetujuan</li>
                        </ul>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <strong>Detail Mahasiswa:</strong><br>
                            <small>
                                Nama: <strong>${mahasiswa}</strong><br>
                                NIM: <strong>${nim}</strong><br>
                                Status: ${statusBadge[status] || status}<br>
                                Keterangan: ${statusText}
                            </small>
                        </div>
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="bx bx-info-circle me-2"></i>
                        Setelah dihapus, mahasiswa dapat mengajukan proposal baru.
                    </div>
                </div>
            `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="bx bx-trash me-2"></i>Ya, Hapus Sekarang',
                cancelButtonText: '<i class="bx bx-x me-2"></i>Batal',
                didOpen: () => {
                    const swalContainer = document.querySelector('.swal2-container');
                    if (swalContainer) {
                        swalContainer.style.zIndex = '99999';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Loading state
                    Swal.fire({
                        ...swalConfig,
                        title: 'Menghapus...',
                        html: '<p class="mb-0">Mohon tunggu, sedang menghapus data dan file...</p>',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            const swalContainer = document.querySelector(
                                '.swal2-container');
                            if (swalContainer) {
                                swalContainer.style.zIndex = '99999';
                            }
                        }
                    });

                    // AJAX Delete Request
                    $.ajax({
                        url: `/admin/komisi-proposal/${proposalId}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                ...swalConfig,
                                icon: 'success',
                                title: 'Berhasil Dihapus!',
                                html: `
                                <p>${response.message}</p>
                                <small class="text-muted">Halaman akan dimuat ulang...</small>
                            `,
                                showConfirmButton: false,
                                timer: 2000,
                                didOpen: () => {
                                    const swalContainer = document
                                        .querySelector('.swal2-container');
                                    if (swalContainer) {
                                        swalContainer.style.zIndex =
                                        '99999';
                                    }
                                }
                            }).then(() => {
                                // Close modal dan reload page
                                $('#detailModal').modal('hide');
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            let errorMessage = 'Terjadi kesalahan saat menghapus data.';

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                ...swalConfig,
                                icon: 'error',
                                title: 'Gagal Menghapus!',
                                html: `<p>${errorMessage}</p>`,
                                confirmButtonText: '<i class="bx bx-check me-2"></i>OK',
                                customClass: {
                                    ...swalConfig.customClass,
                                    confirmButton: 'btn btn-primary btn-lg px-4'
                                },
                                didOpen: () => {
                                    const swalContainer = document
                                        .querySelector('.swal2-container');
                                    if (swalContainer) {
                                        swalContainer.style.zIndex =
                                        '99999';
                                    }
                                }
                            });
                        }
                    });
                }
            });
        });

        console.log('Komisi Proposal Delete Script Loaded');

        // Handler untuk Reject PA - VALIDATE TEXTAREA IN MODAL
        $(document).on('click', '.btn-reject-pa', function(e) {
            e.preventDefault();

            const formId = $(this).data('form-id');
            const keteranganId = $(this).data('keterangan-id');
            const mahasiswa = $(this).data('mahasiswa');
            const nim = $(this).data('nim');
            const form = document.getElementById(formId);
            const keteranganTextarea = document.getElementById(keteranganId);
            const keterangan = keteranganTextarea.value.trim();

            // VALIDASI di sini, bukan di SweetAlert
            if (!keterangan) {
                Swal.fire({
                    ...swalConfig,
                    icon: 'warning',
                    title: 'Alasan Penolakan Kosong',
                    html: '<p class="mb-0">Mohon isi alasan penolakan terlebih dahulu!</p>',
                    confirmButtonText: '<i class="bx bx-check me-2"></i>OK',
                    customClass: {
                        ...swalConfig.customClass,
                        confirmButton: 'btn btn-primary btn-lg px-4'
                    },
                    didOpen: () => {
                        const swalContainer = document.querySelector('.swal2-container');
                        if (swalContainer) {
                            swalContainer.style.zIndex = '99999';
                        }
                    }
                }).then(() => {
                    // Focus ke textarea
                    keteranganTextarea.focus();
                });
                return false;
            }

            if (keterangan.length < 10) {
                Swal.fire({
                    ...swalConfig,
                    icon: 'warning',
                    title: 'Alasan Terlalu Pendek',
                    html: `<p class="mb-0">Alasan penolakan!<br><small class="text-muted">Saat ini: ${keterangan.length} karakter</small></p>`,
                    confirmButtonText: '<i class="bx bx-check me-2"></i>OK',
                    customClass: {
                        ...swalConfig.customClass,
                        confirmButton: 'btn btn-primary btn-lg px-4'
                    },
                    didOpen: () => {
                        const swalContainer = document.querySelector('.swal2-container');
                        if (swalContainer) {
                            swalContainer.style.zIndex = '99999';
                        }
                    }
                }).then(() => {
                    keteranganTextarea.focus();
                });
                return false;
            }

            // CONFIRMATION setelah validasi OK
            Swal.fire({
                ...swalConfig,
                title: '<strong>Tolak Pengajuan?</strong>',
                html: `
                <div class="text-start">
                    <p class="mb-3">Apakah Anda yakin ingin <strong class="text-danger">MENOLAK</strong> pengajuan komisi proposal ini?</p>
                    <div class="alert alert-warning mb-3">
                        <i class="bx bx-info-circle me-2"></i>
                        <strong>Detail Mahasiswa:</strong><br>
                        <small>
                            Nama: <strong>${mahasiswa}</strong><br>
                            NIM: <strong>${nim}</strong>
                        </small>
                    </div>
                    <div class="alert alert-light mb-0">
                        <strong>Alasan Penolakan:</strong><br>
                        <small>${keterangan}</small>
                    </div>
                </div>
            `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="bx bx-x-circle me-2"></i>Ya, Tolak',
                cancelButtonText: '<i class="bx bx-arrow-back me-2"></i>Batal',
                didOpen: () => {
                    const swalContainer = document.querySelector('.swal2-container');
                    if (swalContainer) {
                        swalContainer.style.zIndex = '99999';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        ...swalConfig,
                        title: 'Memproses...',
                        html: '<p class="mb-0">Mohon tunggu, sedang memproses penolakan...</p>',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            const swalContainer = document.querySelector(
                                '.swal2-container');
                            if (swalContainer) {
                                swalContainer.style.zIndex = '99999';
                            }
                        }
                    });
                    form.submit();
                }
            });
        });

        // Handler untuk Approve Korprodi
        $(document).on('click', '.btn-approve-korprodi', function(e) {
            e.preventDefault();

            const formId = $(this).data('form-id');
            const mahasiswa = $(this).data('mahasiswa');
            const nim = $(this).data('nim');
            const form = document.getElementById(formId);

            Swal.fire({
                ...swalConfig,
                title: '<strong>Setujui sebagai Korprodi?</strong>',
                html: `
                <div class="text-start">
                    <p class="mb-3">Apakah Anda yakin ingin <strong class="text-success">MENYETUJUI</strong> pengajuan komisi proposal ini sebagai <strong>Koordinator Program Studi</strong>?</p>
                    <div class="alert alert-info mb-3">
                        <i class="bx bx-info-circle me-2"></i>
                        <strong>Detail Mahasiswa:</strong><br>
                        <small>
                            Nama: <strong>${mahasiswa}</strong><br>
                            NIM: <strong>${nim}</strong>
                        </small>
                    </div>
                    <div class="alert alert-success mb-0">
                        <i class="bx bx-check-shield me-2"></i>
                        <small>Setelah disetujui, dokumen final akan dihasilkan dengan tanda tangan lengkap.</small>
                    </div>
                </div>
            `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="bx bx-check-circle me-2"></i>Ya, Setujui',
                cancelButtonText: '<i class="bx bx-x me-2"></i>Batal',
                didOpen: () => {
                    const swalContainer = document.querySelector('.swal2-container');
                    if (swalContainer) {
                        swalContainer.style.zIndex = '99999';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        ...swalConfig,
                        title: 'Memproses...',
                        html: '<p class="mb-0">Mohon tunggu, sedang memproses persetujuan final...</p>',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            const swalContainer = document.querySelector(
                                '.swal2-container');
                            if (swalContainer) {
                                swalContainer.style.zIndex = '99999';
                            }
                        }
                    });
                    form.submit();
                }
            });
        });

        // Handler untuk Reject Korprodi
        $(document).on('click', '.btn-reject-korprodi', function(e) {
            e.preventDefault();

            const formId = $(this).data('form-id');
            const keteranganId = $(this).data('keterangan-id');
            const mahasiswa = $(this).data('mahasiswa');
            const nim = $(this).data('nim');
            const form = document.getElementById(formId);
            const keteranganTextarea = document.getElementById(keteranganId);
            const keterangan = keteranganTextarea.value.trim();

            // VALIDASI
            if (!keterangan) {
                Swal.fire({
                    ...swalConfig,
                    icon: 'warning',
                    title: 'Alasan Penolakan Kosong',
                    html: '<p class="mb-0">Mohon isi alasan penolakan terlebih dahulu!</p>',
                    confirmButtonText: '<i class="bx bx-check me-2"></i>OK',
                    customClass: {
                        ...swalConfig.customClass,
                        confirmButton: 'btn btn-primary btn-lg px-4'
                    },
                    didOpen: () => {
                        const swalContainer = document.querySelector('.swal2-container');
                        if (swalContainer) {
                            swalContainer.style.zIndex = '99999';
                        }
                    }
                }).then(() => {
                    keteranganTextarea.focus();
                });
                return false;
            }

            if (keterangan.length < 10) {
                Swal.fire({
                    ...swalConfig,
                    icon: 'warning',
                    title: 'Alasan Terlalu Pendek',
                    html: `<p class="mb-0">Alasan penolakan minimal 10 karakter!<br><small class="text-muted">Saat ini: ${keterangan.length} karakter</small></p>`,
                    confirmButtonText: '<i class="bx bx-check me-2"></i>OK',
                    customClass: {
                        ...swalConfig.customClass,
                        confirmButton: 'btn btn-primary btn-lg px-4'
                    },
                    didOpen: () => {
                        const swalContainer = document.querySelector('.swal2-container');
                        if (swalContainer) {
                            swalContainer.style.zIndex = '99999';
                        }
                    }
                }).then(() => {
                    keteranganTextarea.focus();
                });
                return false;
            }

            // CONFIRMATION
            Swal.fire({
                ...swalConfig,
                title: '<strong>Tolak sebagai Korprodi?</strong>',
                html: `
                <div class="text-start">
                    <p class="mb-3">Apakah Anda yakin ingin <strong class="text-danger">MENOLAK</strong> pengajuan komisi proposal ini sebagai <strong>Koordinator Program Studi</strong>?</p>
                    <div class="alert alert-warning mb-3">
                        <i class="bx bx-info-circle me-2"></i>
                        <strong>Detail Mahasiswa:</strong><br>
                        <small>
                            Nama: <strong>${mahasiswa}</strong><br>
                            NIM: <strong>${nim}</strong>
                        </small>
                    </div>
                    <div class="alert alert-light mb-0">
                        <strong>Alasan Penolakan:</strong><br>
                        <small>${keterangan}</small>
                    </div>
                </div>
            `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="bx bx-x-circle me-2"></i>Ya, Tolak',
                cancelButtonText: '<i class="bx bx-arrow-back me-2"></i>Batal',
                didOpen: () => {
                    const swalContainer = document.querySelector('.swal2-container');
                    if (swalContainer) {
                        swalContainer.style.zIndex = '99999';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        ...swalConfig,
                        title: 'Memproses...',
                        html: '<p class="mb-0">Mohon tunggu, sedang memproses penolakan...</p>',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            const swalContainer = document.querySelector(
                                '.swal2-container');
                            if (swalContainer) {
                                swalContainer.style.zIndex = '99999';
                            }
                        }
                    });
                    form.submit();
                }
            });
        });

        console.log('Komisi Proposal Approval Script Loaded');

    })();
</script>

<style>
    /* Fix z-index untuk SweetAlert2 di atas modal Bootstrap */
    .swal-high-zindex {
        z-index: 99999 !important;
    }

    .swal2-container {
        z-index: 99999 !important;
    }

    .swal-popup-custom {
        font-size: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .swal2-html-container {
        margin: 1rem 0 !important;
    }

    /* Prevent modal backdrop dimming twice */
    #detailModal.modal {
        z-index: 9999;
    }

    .modal-backdrop {
        z-index: 9998;
    }

    /* Styling untuk button dalam SweetAlert dengan SPACING */
    .swal2-actions {
        gap: 1rem !important;
        /* Tambah gap antar button */
    }

    .swal2-styled.btn {
        margin: 0 !important;
        padding: 0.625rem 1.5rem !important;
        font-size: 0.9375rem !important;
        font-weight: 500 !important;
    }

    /* Loading spinner custom */
    .swal2-loader {
        border-color: #696cff transparent #696cff transparent !important;
    }

    /* Card styling untuk reject form */
    .card.border-danger {
        border-width: 2px;
    }

    .card.border-danger .card-title {
        margin-bottom: 1rem;
    }
</style>
