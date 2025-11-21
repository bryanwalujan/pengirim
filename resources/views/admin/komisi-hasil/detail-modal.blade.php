{{-- filepath: /c:/laragon/www/eservice-app/resources/views/admin/komisi-hasil/detail-modal.blade.php --}}
<div class="modal-body" x-data="komisiHasilDetail()">
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
                        <th>Pembimbing 1</th>
                        <td>
                            {{ $komisi->pembimbing1->name ?? 'Belum Ditentukan' }}<br>
                            <small class="text-muted">{{ $komisi->pembimbing1->jabatan ?? '-' }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Pembimbing 2</th>
                        <td>
                            {{ $komisi->pembimbing2->name ?? 'Belum Ditentukan' }}<br>
                            <small class="text-muted">{{ $komisi->pembimbing2->jabatan ?? '-' }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Status Saat Ini</th>
                        <td>
                            @if ($komisi->status == 'pending')
                                <span class="badge bg-warning">Menunggu Pembimbing 1</span>
                            @elseif($komisi->status == 'approved_pembimbing1')
                                <span class="badge bg-info">Menunggu Pembimbing 2</span>
                            @elseif($komisi->status == 'approved_pembimbing2')
                                <span class="badge bg-primary">Menunggu Korprodi</span>
                            @elseif($komisi->status == 'approved')
                                <span class="badge bg-success">Disetujui Lengkap</span>
                            @else
                                <span class="badge bg-danger">Ditolak</span>
                            @endif
                        </td>
                    </tr>

                    @if ($komisi->penandatanganPembimbing1)
                        <tr>
                            <th>Disetujui Pembimbing 1</th>
                            <td>
                                <strong>{{ $komisi->penandatanganPembimbing1->name }}</strong><br>
                                <small class="text-muted">
                                    {{ $komisi->tanggal_persetujuan_pembimbing1?->translatedFormat('d M Y H:i') }} WITA
                                </small>
                            </td>
                        </tr>
                    @endif

                    @if ($komisi->penandatanganPembimbing2)
                        <tr>
                            <th>Disetujui Pembimbing 2</th>
                            <td>
                                <strong>{{ $komisi->penandatanganPembimbing2->name }}</strong><br>
                                <small class="text-muted">
                                    {{ $komisi->tanggal_persetujuan_pembimbing2?->translatedFormat('d M Y H:i') }} WITA
                                </small>
                            </td>
                        </tr>
                    @endif

                    @if ($komisi->penandatanganKorprodi)
                        <tr>
                            <th>Disetujui Korprodi</th>
                            <td>
                                <strong>{{ $komisi->penandatanganKorprodi->name }}</strong><br>
                                <small class="text-muted">
                                    {{ $komisi->tanggal_persetujuan_korprodi?->translatedFormat('d M Y H:i') }} WITA
                                </small>
                            </td>
                        </tr>
                    @endif

                    @if ($komisi->keterangan && $komisi->status === 'rejected')
                        <tr>
                            <th style="vertical-align: top">Alasan Penolakan</th>
                            <td style="vertical-align: top">
                                <div class="alert alert-danger mb-0">
                                    {{ $komisi->keterangan }}
                                </div>
                            </td>
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
                $isPembimbing1ForThis = $user->hasRole('dosen') && $komisi->dosen_pembimbing1_id == $user->id;
                $isPembimbing2ForThis = $user->hasRole('dosen') && $komisi->dosen_pembimbing2_id == $user->id;

                // Check staff override permission
                $canOverrideApproval = $user->hasRole(['staff']);

                // Conditions untuk approval
                $canApprovePembimbing1 =
                    ($isPembimbing1ForThis || $canOverrideApproval) && $komisi->canBeApprovedByPembimbing1();
                $canApprovePembimbing2 =
                    ($isPembimbing2ForThis || $canOverrideApproval) && $komisi->canBeApprovedByPembimbing2();
                $canApproveKorprodi = ($isKorprodi || $canOverrideApproval) && $komisi->canBeApprovedByKorprodi();

                // Permission untuk delete
                $canDelete = $user->hasRole('staff') || $isPembimbing1ForThis || $isPembimbing2ForThis || $isKorprodi;
            @endphp

            {{-- ========== SECTION: APPROVE PEMBIMBING 1 ========== --}}
            @if ($canApprovePembimbing1)
                {{-- Override Warning --}}
                @if ($canOverrideApproval && !$isPembimbing1ForThis)
                    <div class="alert alert-warning mb-3">
                        <i class="bx bx-shield-quarter me-1"></i>
                        <strong>Administrative Override:</strong> Anda dapat menyetujui pengajuan ini atas nama
                        Pembimbing 1.<br>
                        <small>Persetujuan akan dicatat atas nama:
                            <strong>{{ $komisi->pembimbing1->name }}</strong></small>
                    </div>

                    <form id="formApprovePembimbing1_{{ $komisi->id }}"
                        action="{{ route('admin.komisi-hasil.approve.pembimbing1', $komisi->id) }}" method="POST"
                        class="mb-3" @submit.prevent="handleApprovePembimbing1"
                        data-mahasiswa="{{ $komisi->user->name }}" data-nim="{{ $komisi->user->nim }}"
                        data-pembimbing1-name="{{ $komisi->pembimbing1->name }}" data-is-override="true">
                        @csrf
                        <input type="hidden" name="action" value="approve">

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bx bx-check-shield me-2"></i>Override: Setujui sebagai Pembimbing 1
                        </button>
                    </form>
                @else
                    {{-- Normal Pembimbing 1 --}}
                    <div class="alert alert-info mb-3">
                        <i class="bx bx-info-circle me-1"></i>
                        <strong>Info:</strong> Anda adalah Pembimbing 1 mahasiswa ini.<br>
                        <small>Klik tombol di bawah untuk menyetujui atau menolak pengajuan.</small>
                    </div>

                    <form id="formApprovePembimbing1_{{ $komisi->id }}"
                        action="{{ route('admin.komisi-hasil.approve.pembimbing1', $komisi->id) }}" method="POST"
                        class="mb-3" @submit.prevent="handleApprovePembimbing1"
                        data-mahasiswa="{{ $komisi->user->name }}" data-nim="{{ $komisi->user->nim }}"
                        data-pembimbing1-name="{{ $komisi->pembimbing1->name }}" data-is-override="false">
                        @csrf
                        <input type="hidden" name="action" value="approve">

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bx bx-check-circle me-2"></i>Setujui sebagai Pembimbing 1
                        </button>
                    </form>
                @endif

                {{-- Form Reject Pembimbing 1 --}}
                <div class="card border-danger">
                    <div class="card-body">
                        <h6 class="card-title text-danger mb-3">
                            <i class="bx bx-x-circle me-1"></i>Tolak Pengajuan
                        </h6>

                        <form id="formRejectPembimbing1_{{ $komisi->id }}"
                            action="{{ route('admin.komisi-hasil.approve.pembimbing1', $komisi->id) }}" method="POST"
                            @submit.prevent="handleRejectPembimbing1" data-mahasiswa="{{ $komisi->user->name }}"
                            data-nim="{{ $komisi->user->nim }}">
                            @csrf
                            <input type="hidden" name="action" value="reject">

                            <div class="mb-3">
                                <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="keterangan" rows="4" x-model="rejectReasonPembimbing1"
                                    placeholder="Jelaskan alasan penolakan (minimal 10 karakter)..." required></textarea>
                                <small class="text-muted">Minimal 10 karakter</small>
                            </div>

                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bx bx-x-circle me-2"></i>Tolak Pengajuan
                            </button>
                        </form>
                    </div>
                </div>

                {{-- ========== SECTION: APPROVE PEMBIMBING 2 ========== --}}
            @elseif($canApprovePembimbing2)
                @if ($canOverrideApproval && !$isPembimbing2ForThis)
                    <div class="alert alert-warning mb-3">
                        <i class="bx bx-shield-quarter me-1"></i>
                        <strong>Administrative Override:</strong> Anda dapat menyetujui sebagai Pembimbing 2.<br>
                        <small>Persetujuan akan dicatat atas nama:
                            <strong>{{ $komisi->pembimbing2->name }}</strong></small>
                    </div>

                    <form id="formApprovePembimbing2_{{ $komisi->id }}"
                        action="{{ route('admin.komisi-hasil.approve.pembimbing2', $komisi->id) }}" method="POST"
                        class="mb-3" @submit.prevent="handleApprovePembimbing2"
                        data-mahasiswa="{{ $komisi->user->name }}" data-nim="{{ $komisi->user->nim }}"
                        data-pembimbing2-name="{{ $komisi->pembimbing2->name }}" data-is-override="true">
                        @csrf
                        <input type="hidden" name="action" value="approve">

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bx bx-check-shield me-2"></i>Override: Setujui sebagai Pembimbing 2
                        </button>
                    </form>
                @else
                    <div class="alert alert-info mb-3">
                        <i class="bx bx-info-circle me-1"></i>
                        <strong>Info:</strong> Anda adalah Pembimbing 2 mahasiswa ini.<br>
                        <small>Pembimbing 1 sudah menyetujui. Silakan review dan berikan keputusan Anda.</small>
                    </div>

                    <form id="formApprovePembimbing2_{{ $komisi->id }}"
                        action="{{ route('admin.komisi-hasil.approve.pembimbing2', $komisi->id) }}" method="POST"
                        class="mb-3" @submit.prevent="handleApprovePembimbing2"
                        data-mahasiswa="{{ $komisi->user->name }}" data-nim="{{ $komisi->user->nim }}"
                        data-pembimbing2-name="{{ $komisi->pembimbing2->name }}" data-is-override="false">
                        @csrf
                        <input type="hidden" name="action" value="approve">

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bx bx-check-circle me-2"></i>Setujui sebagai Pembimbing 2
                        </button>
                    </form>
                @endif

                {{-- Form Reject Pembimbing 2 --}}
                <div class="card border-danger">
                    <div class="card-body">
                        <h6 class="card-title text-danger mb-3">
                            <i class="bx bx-x-circle me-1"></i>Tolak Pengajuan
                        </h6>

                        <form id="formRejectPembimbing2_{{ $komisi->id }}"
                            action="{{ route('admin.komisi-hasil.approve.pembimbing2', $komisi->id) }}"
                            method="POST" @submit.prevent="handleRejectPembimbing2"
                            data-mahasiswa="{{ $komisi->user->name }}" data-nim="{{ $komisi->user->nim }}">
                            @csrf
                            <input type="hidden" name="action" value="reject">

                            <div class="mb-3">
                                <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="keterangan" rows="4" x-model="rejectReasonPembimbing2"
                                    placeholder="Jelaskan alasan penolakan (minimal 10 karakter)..." required></textarea>
                                <small class="text-muted">Minimal 10 karakter</small>
                            </div>

                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bx bx-x-circle me-2"></i>Tolak Pengajuan
                            </button>
                        </form>
                    </div>
                </div>

                {{-- ========== SECTION: APPROVE KORPRODI ========== --}}
            @elseif($canApproveKorprodi)
                @if ($canOverrideApproval && !$isKorprodi)
                    <div class="alert alert-warning mb-3">
                        <i class="bx bx-shield-quarter me-1"></i>
                        <strong>Administrative Override:</strong> Anda dapat menyetujui sebagai Korprodi.<br>
                        <small>Ini akan menjadi persetujuan final.</small>
                    </div>

                    <form id="formApproveKorprodi_{{ $komisi->id }}"
                        action="{{ route('admin.komisi-hasil.approve.korprodi', $komisi->id) }}" method="POST"
                        class="mb-3" @submit.prevent="handleApproveKorprodi"
                        data-mahasiswa="{{ $komisi->user->name }}" data-nim="{{ $komisi->user->nim }}"
                        data-is-override="true">
                        @csrf
                        <input type="hidden" name="action" value="approve">

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bx bx-check-shield me-2"></i>Override: Setujui Final sebagai Korprodi
                        </button>
                    </form>
                @else
                    <div class="alert alert-info mb-3">
                        <i class="bx bx-info-circle me-1"></i>
                        <strong>Info:</strong> Anda dapat menyetujui sebagai Koordinator Program Studi.<br>
                        <small>Kedua pembimbing sudah menyetujui. Ini adalah persetujuan final.</small>
                    </div>

                    <form id="formApproveKorprodi_{{ $komisi->id }}"
                        action="{{ route('admin.komisi-hasil.approve.korprodi', $komisi->id) }}" method="POST"
                        class="mb-3" @submit.prevent="handleApproveKorprodi"
                        data-mahasiswa="{{ $komisi->user->name }}" data-nim="{{ $komisi->user->nim }}"
                        data-is-override="false">
                        @csrf
                        <input type="hidden" name="action" value="approve">

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bx bx-check-circle me-2"></i>Setujui Final sebagai Korprodi
                        </button>
                    </form>
                @endif

                {{-- Form Reject Korprodi --}}
                <div class="card border-danger">
                    <div class="card-body">
                        <h6 class="card-title text-danger mb-3">
                            <i class="bx bx-x-circle me-1"></i>Tolak Pengajuan
                        </h6>

                        <form id="formRejectKorprodi_{{ $komisi->id }}"
                            action="{{ route('admin.komisi-hasil.approve.korprodi', $komisi->id) }}" method="POST"
                            @submit.prevent="handleRejectKorprodi" data-mahasiswa="{{ $komisi->user->name }}"
                            data-nim="{{ $komisi->user->nim }}">
                            @csrf
                            <input type="hidden" name="action" value="reject">

                            <div class="mb-3">
                                <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="keterangan" rows="4" x-model="rejectReasonKorprodi"
                                    placeholder="Jelaskan alasan penolakan (minimal 10 karakter)..." required></textarea>
                                <small class="text-muted">Minimal 10 karakter</small>
                            </div>

                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bx bx-x-circle me-2"></i>Tolak Pengajuan
                            </button>
                        </form>
                    </div>
                </div>

                {{-- ========== SECTION: VIEW ONLY ========== --}}
            @else
                @if ($komisi->status == 'approved' && $komisi->file_komisi_hasil)
                    <a href="{{ route('admin.komisi-hasil.download', $komisi->id) }}" class="btn btn-success w-100">
                        <i class="bx bx-download me-2"></i>Download Dokumen Final
                    </a>
                @elseif($komisi->status == 'approved_pembimbing2' && $isKorprodi && $komisi->file_komisi_pembimbing2)
                    <a href="{{ route('admin.komisi-hasil.download', $komisi->id) }}"
                        class="btn btn-info w-100 mb-2">
                        <i class="bx bx-download me-2"></i>Download Dokumen (Pembimbing 1 & 2)
                    </a>
                    <div class="alert alert-warning mb-0">
                        <i class="bx bx-info-circle me-1"></i>
                        Menunggu persetujuan Anda sebagai Korprodi untuk dokumen final.
                    </div>
                @elseif(
                    $komisi->status == 'approved_pembimbing1' &&
                        ($isPembimbing2ForThis || $canOverrideApproval) &&
                        $komisi->file_komisi_pembimbing1)
                    <a href="{{ route('admin.komisi-hasil.download', $komisi->id) }}"
                        class="btn btn-info w-100 mb-2">
                        <i class="bx bx-download me-2"></i>Download Dokumen (Pembimbing 1)
                    </a>
                    <div class="alert alert-warning mb-0">
                        <i class="bx bx-info-circle me-1"></i>
                        Menunggu persetujuan Pembimbing 2.
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="bx bx-info-circle me-1"></i>
                        <strong>Status:</strong> Pengajuan sedang dalam proses approval.
                    </div>
                @endif
            @endif

            {{-- ========== SECTION: DELETE ========== --}}
            @if ($canDelete)
                <div class="card border-danger mt-3">
                    <div class="card-body">
                        <h6 class="card-title text-danger mb-3">
                            <i class="bx bx-trash me-1"></i>Zona Berbahaya
                        </h6>
                        <p class="text-muted mb-3">Menghapus pengajuan ini akan menghapus semua data dan file terkait.
                        </p>
                        <button type="button" class="btn btn-danger w-100"
                            @click="handleDelete({{ $komisi->id }}, '{{ $komisi->user->name }}', '{{ $komisi->user->nim }}', '{{ $komisi->status }}')">
                            <i class="bx bx-trash me-2"></i>Hapus Pengajuan
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Styles --}}
<style>
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

    #detailModal.modal {
        z-index: 9999;
    }

    .modal-backdrop {
        z-index: 9998;
    }

    .swal2-actions {
        gap: 1rem !important;
    }

    .swal2-styled.btn {
        margin: 0 !important;
        padding: 0.625rem 1.5rem !important;
        font-size: 0.9375rem !important;
        font-weight: 500 !important;
    }

    .swal2-loader {
        border-color: #696cff transparent #696cff transparent !important;
    }

    .card.border-danger {
        border-width: 2px;
    }

    .card.border-danger .card-title {
        margin-bottom: 1rem;
    }
</style>
