<div class="modal-body" x-data="komisiProposalDetail()">
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
                $isPAForThisProposal = $user->hasRole('dosen') && $komisi->dosen_pembimbing_id == $user->id;

                // TAMBAHAN: Check staff override permission
                $canOverrideApproval = $user->hasRole(['admin', 'staff']);

                // Conditions untuk approval
                $canApprovePA = ($isPAForThisProposal || $canOverrideApproval) && $komisi->canBeApprovedByPA();
                $canApproveKorprodi = ($isKorprodi || $canOverrideApproval) && $komisi->canBeApprovedByKorprodi();

                // Permission untuk delete
                $canDelete = $user->hasRole('staff') || $isPAForThisProposal || $isKorprodi;
            @endphp


            @if ($canApprovePA)
                {{-- Form Approve PA --}}
                @if ($canOverrideApproval && !$isPAForThisProposal)
                    <div class="alert alert-warning mb-3">
                        <i class="bx bx-shield-quarter me-1"></i>
                        <strong>Administrative Override:</strong> Anda dapat menyetujui pengajuan ini atas nama PA.<br>
                        <small>
                            <strong>Nama Anda:</strong> {{ $user->name }}<br>
                            <strong>Role:</strong> {{ $user->getRoleNames()->first() }}<br>
                            <strong>PA Mahasiswa:</strong> {{ $komisi->pembimbing->name }}
                        </small>
                    </div>

                    <form id="formApprovePA_{{ $komisi->id }}"
                        action="{{ route('admin.komisi-proposal.approve-pa', $komisi->id) }}" method="POST"
                        class="mb-3" @submit.prevent="handleApprovePA"
                        data-mahasiswa="{{ $komisi->user->name }}"
                        data-nim="{{ $komisi->user->nim }}"
                        data-pa-name="{{ $komisi->pembimbing->name }}"
                        data-is-override="true">
                        @csrf
                        <input type="hidden" name="action" value="approve">

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bx bx-check-circle me-1"></i> Setujui sebagai Staff (Override PA)
                        </button>
                    </form>
                @else
                    {{-- PA Normal --}}
                    <div class="alert alert-info mb-3">
                        <i class="bx bx-info-circle me-1"></i>
                        <strong>Info:</strong> Anda adalah Pembimbing Akademik mahasiswa ini.<br>
                        <small>
                            <strong>Nama Anda:</strong> {{ $user->name }}<br>
                            <strong>Jabatan:</strong> {{ $user->jabatan ?? '-' }}
                        </small>
                    </div>

                    <form id="formApprovePA_{{ $komisi->id }}"
                        action="{{ route('admin.komisi-proposal.approve-pa', $komisi->id) }}" method="POST"
                        class="mb-3" @submit.prevent="handleApprovePA"
                        data-mahasiswa="{{ $komisi->user->name }}"
                        data-nim="{{ $komisi->user->nim }}"
                        data-is-override="false">
                        @csrf
                        <input type="hidden" name="action" value="approve">

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bx bx-check-circle me-1"></i> Setujui sebagai PA
                        </button>
                    </form>
                @endif

                {{-- Form Reject PA --}}
                <div class="card border-danger">
                    <div class="card-body">
                        <h6 class="card-title text-danger mb-3">
                            <i class="bx bx-x-circle me-1"></i> Tolak Pengajuan
                        </h6>

                        <form id="formRejectPA_{{ $komisi->id }}"
                            action="{{ route('admin.komisi-proposal.approve-pa', $komisi->id) }}" method="POST"
                            @submit.prevent="handleRejectPA"
                            data-mahasiswa="{{ $komisi->user->name }}"
                            data-nim="{{ $komisi->user->nim }}">
                            @csrf
                            <input type="hidden" name="action" value="reject">

                            <div class="mb-3">
                                <label for="keteranganPA_{{ $komisi->id }}" class="form-label">
                                    Alasan Penolakan <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="keteranganPA_{{ $komisi->id }}" name="keterangan" rows="4"
                                    x-model="rejectReason" placeholder="Berikan alasan penolakan yang jelas dan detail..." required></textarea>
                                <small class="text-muted">Minimal 10 karakter</small>
                            </div>

                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bx bx-x-circle me-1"></i> Tolak Pengajuan
                            </button>
                        </form>
                    </div>
                </div>
            @elseif($canApproveKorprodi)
                {{-- Similar structure untuk Korprodi --}}
                @if ($canOverrideApproval && !$isKorprodi)
                    <div class="alert alert-warning mb-3">
                        <i class="bx bx-shield-quarter me-1"></i>
                        <strong>Administrative Override:</strong> Anda dapat menyetujui sebagai Korprodi.
                    </div>

                    <form id="formApproveKorprodi_{{ $komisi->id }}"
                        action="{{ route('admin.komisi-proposal.approve-korprodi', $komisi->id) }}" method="POST"
                        class="mb-3" @submit.prevent="handleApproveKorprodi"
                        data-mahasiswa="{{ $komisi->user->name }}"
                        data-nim="{{ $komisi->user->nim }}"
                        data-is-override="true">
                        @csrf
                        <input type="hidden" name="action" value="approve">

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bx bx-check-circle me-1"></i> Setujui sebagai Staff (Override Korprodi)
                        </button>
                    </form>
                @else
                    <div class="alert alert-info mb-3">
                        <i class="bx bx-info-circle me-1"></i>
                        <strong>Info:</strong> Anda dapat menyetujui sebagai Koordinator Program Studi.
                    </div>

                    <form id="formApproveKorprodi_{{ $komisi->id }}"
                        action="{{ route('admin.komisi-proposal.approve-korprodi', $komisi->id) }}" method="POST"
                        class="mb-3" @submit.prevent="handleApproveKorprodi"
                        data-mahasiswa="{{ $komisi->user->name }}"
                        data-nim="{{ $komisi->user->nim }}"
                        data-is-override="false">
                        @csrf
                        <input type="hidden" name="action" value="approve">

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bx bx-check-circle me-1"></i> Setujui sebagai Korprodi
                        </button>
                    </form>
                @endif

                {{-- Form Reject Korprodi --}}
                <div class="card border-danger">
                    <div class="card-body">
                        <h6 class="card-title text-danger mb-3">
                            <i class="bx bx-x-circle me-1"></i> Tolak Pengajuan
                        </h6>

                        <form id="formRejectKorprodi_{{ $komisi->id }}"
                            action="{{ route('admin.komisi-proposal.approve-korprodi', $komisi->id) }}" method="POST"
                            @submit.prevent="handleRejectKorprodi"
                            data-mahasiswa="{{ $komisi->user->name }}"
                            data-nim="{{ $komisi->user->nim }}">
                            @csrf
                            <input type="hidden" name="action" value="reject">

                            <div class="mb-3">
                                <label for="keteranganKorprodi_{{ $komisi->id }}" class="form-label">
                                    Alasan Penolakan <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="keteranganKorprodi_{{ $komisi->id }}" name="keterangan" rows="4"
                                    x-model="rejectReasonKorprodi" placeholder="Berikan alasan penolakan yang jelas dan detail..." required></textarea>
                                <small class="text-muted">Minimal 10 karakter</small>
                            </div>

                            <button type="submit" class="btn btn-danger w-100">
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

            {{-- Delete Section --}}
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
                                <li>Menghapus proposal akan menghapus <strong>semua data dan file PDF</strong></li>
                                <li>Mahasiswa <strong>dapat mengajukan proposal baru</strong> setelah dihapus</li>
                                <li>Tindakan ini <strong>tidak dapat dibatalkan</strong></li>
                            </ul>
                        </div>

                        <div class="d-grid">
                            <button type="button" class="btn btn-danger"
                                @click="handleDelete({{ $komisi->id }}, '{{ $komisi->user->name }}', '{{ $komisi->user->nim }}', '{{ $komisi->status }}')">
                                <i class="bx bx-trash me-1"></i> Hapus Pengajuan Proposal
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Styles tetap sama --}}
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
