{{-- filepath: /c:/laragon/www/eservice-app/resources/views/admin/komisi-hasil/detail-modal.blade.php --}}
<div>
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
                            {{ $komisi->pembimbing1->name }}<br>
                            <small class="text-muted">{{ $komisi->pembimbing1->jabatan ?? '-' }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Pembimbing 2</th>
                        <td>
                            {{ $komisi->pembimbing2->name }}<br>
                            <small class="text-muted">{{ $komisi->pembimbing2->jabatan ?? '-' }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Status Saat Ini</th>
                        <td>{!! $komisi->status_badge !!}</td>
                    </tr>

                    {{-- Approval Info --}}
                    @if ($komisi->penandatanganPembimbing1)
                        <tr>
                            <th>Disetujui P1 oleh</th>
                            <td>
                                {{ $komisi->penandatanganPembimbing1->name }}<br>
                                <small class="text-muted">
                                    {{ $komisi->tanggal_persetujuan_pembimbing1->translatedFormat('d M Y H:i') }} WITA
                                </small>
                            </td>
                        </tr>
                    @endif

                    @if ($komisi->penandatanganPembimbing2)
                        <tr>
                            <th>Disetujui P2 oleh</th>
                            <td>
                                {{ $komisi->penandatanganPembimbing2->name }}<br>
                                <small class="text-muted">
                                    {{ $komisi->tanggal_persetujuan_pembimbing2->translatedFormat('d M Y H:i') }} WITA
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
                                    {{ $komisi->tanggal_persetujuan_korprodi->translatedFormat('d M Y H:i') }} WITA
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

    {{-- Actions Section --}}
    <div class="row mt-3" x-data="detailActions()">
        <div class="col-12">
            @php
                $user = Auth::user();
                $isKorprodi =
                    $user->hasRole('dosen') &&
                    (str_contains(strtolower($user->jabatan ?? ''), 'koordinator program studi') ||
                        str_contains(strtolower($user->jabatan ?? ''), 'korprodi') ||
                        str_contains(strtolower($user->jabatan ?? ''), 'kaprodi'));
                $isPembimbing1 = $user->hasRole('dosen') && $komisi->dosen_pembimbing1_id == $user->id;
                $isPembimbing2 = $user->hasRole('dosen') && $komisi->dosen_pembimbing2_id == $user->id;

                $canApproveP1 = $isPembimbing1 && $komisi->canBeApprovedByPembimbing1();
                $canApproveP2 = $isPembimbing2 && $komisi->canBeApprovedByPembimbing2();
                $canApproveKorprodi = $isKorprodi && $komisi->canBeApprovedByKorprodi();
                $canDelete = $user->hasRole('staff') || $isPembimbing1 || $isPembimbing2 || $isKorprodi;
            @endphp

            {{-- APPROVAL PEMBIMBING 1 --}}
            @if ($canApproveP1)
                <div class="alert alert-info mb-3">
                    <i class="bx bx-info-circle me-1"></i>
                    <strong>Info:</strong> Anda adalah Pembimbing 1 mahasiswa ini.<br>
                    <small>
                        <strong>Nama:</strong> {{ $user->name }}<br>
                        <strong>Jabatan:</strong> {{ $user->jabatan ?? '-' }}
                    </small>
                </div>

                <button type="button" class="btn btn-success w-100 mb-3"
                    @click="confirmApprove('pembimbing1', '{{ $komisi->id }}', '{{ $komisi->user->name }}', '{{ $komisi->user->nim }}')">
                    <i class="bx bx-check-circle me-1"></i> Setujui sebagai Pembimbing 1
                </button>

                <div class="card border-danger">
                    <div class="card-body">
                        <h6 class="card-title text-danger mb-3">
                            <i class="bx bx-x-circle me-1"></i> Tolak Pengajuan
                        </h6>
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea class="form-control" x-model="rejectReason" rows="4"
                                placeholder="Berikan alasan penolakan yang jelas dan detail..." required></textarea>
                            <small class="text-muted">Minimal 10 karakter</small>
                        </div>
                        <button type="button" class="btn btn-danger w-100"
                            @click="confirmReject('pembimbing1', '{{ $komisi->id }}', '{{ $komisi->user->name }}', '{{ $komisi->user->nim }}')">
                            <i class="bx bx-x-circle me-1"></i> Tolak Pengajuan
                        </button>
                    </div>
                </div>
            @elseif($canApproveP2)
                <div class="alert alert-info mb-3">
                    <i class="bx bx-info-circle me-1"></i>
                    <strong>Info:</strong> Anda adalah Pembimbing 2 mahasiswa ini.<br>
                    <small>
                        <strong>Nama:</strong> {{ $user->name }}<br>
                        <strong>Jabatan:</strong> {{ $user->jabatan ?? '-' }}
                    </small>
                </div>

                <button type="button" class="btn btn-success w-100 mb-3"
                    @click="confirmApprove('pembimbing2', '{{ $komisi->id }}', '{{ $komisi->user->name }}', '{{ $komisi->user->nim }}')">
                    <i class="bx bx-check-circle me-1"></i> Setujui sebagai Pembimbing 2
                </button>

                <div class="card border-danger">
                    <div class="card-body">
                        <h6 class="card-title text-danger mb-3">
                            <i class="bx bx-x-circle me-1"></i> Tolak Pengajuan
                        </h6>
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea class="form-control" x-model="rejectReason" rows="4"
                                placeholder="Berikan alasan penolakan yang jelas dan detail..." required></textarea>
                            <small class="text-muted">Minimal 10 karakter</small>
                        </div>
                        <button type="button" class="btn btn-danger w-100"
                            @click="confirmReject('pembimbing2', '{{ $komisi->id }}', '{{ $komisi->user->name }}', '{{ $komisi->user->nim }}')">
                            <i class="bx bx-x-circle me-1"></i> Tolak Pengajuan
                        </button>
                    </div>
                </div>
            @elseif($canApproveKorprodi)
                <div class="alert alert-info mb-3">
                    <i class="bx bx-info-circle me-1"></i>
                    <strong>Info:</strong> Anda dapat menyetujui sebagai Koordinator Program Studi.<br>
                    <small>
                        <strong>Nama:</strong> {{ $user->name }}<br>
                        <strong>Jabatan:</strong> {{ $user->jabatan ?? '-' }}
                    </small>
                </div>

                <button type="button" class="btn btn-success w-100 mb-3"
                    @click="confirmApprove('korprodi', '{{ $komisi->id }}', '{{ $komisi->user->name }}', '{{ $komisi->user->nim }}')">
                    <i class="bx bx-check-circle me-1"></i> Setujui sebagai Korprodi
                </button>

                <div class="card border-danger">
                    <div class="card-body">
                        <h6 class="card-title text-danger mb-3">
                            <i class="bx bx-x-circle me-1"></i> Tolak Pengajuan
                        </h6>
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea class="form-control" x-model="rejectReason" rows="4"
                                placeholder="Berikan alasan penolakan yang jelas dan detail..." required></textarea>
                            <small class="text-muted">Minimal 10 karakter</small>
                        </div>
                        <button type="button" class="btn btn-danger w-100"
                            @click="confirmReject('korprodi', '{{ $komisi->id }}', '{{ $komisi->user->name }}', '{{ $komisi->user->nim }}')">
                            <i class="bx bx-x-circle me-1"></i> Tolak Pengajuan
                        </button>
                    </div>
                </div>
            @else
                {{-- VIEW ONLY --}}
                @if ($komisi->status == 'approved' && $komisi->file_komisi_hasil)
                    <a href="{{ route('admin.komisi-hasil.download', $komisi->id) }}" class="btn btn-success w-100"
                        target="_blank">
                        <i class="bx bxs-file-pdf me-1"></i> Download PDF Final
                    </a>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="bx bx-info-circle me-1"></i>
                        {{ $komisi->status_text }}
                    </div>
                @endif
            @endif

            {{-- DELETE SECTION --}}
            @if ($canDelete)
                <div class="card border-danger mt-3">
                    <div class="card-body">
                        <h6 class="card-title text-danger mb-3">
                            <i class="bx bx-trash me-1"></i> Zona Berbahaya
                        </h6>
                        <div class="alert alert-warning mb-3">
                            <i class="bx bx-info-circle me-1"></i>
                            <strong>Perhatian:</strong> Menghapus akan menghilangkan semua data dan file PDF.
                        </div>
                        <button type="button" class="btn btn-danger w-100"
                            @click="confirmDelete('{{ $komisi->id }}', '{{ $komisi->user->name }}', '{{ $komisi->user->nim }}', '{{ $komisi->status }}')">
                            <i class="bx bx-trash me-1"></i> Hapus Pengajuan
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
