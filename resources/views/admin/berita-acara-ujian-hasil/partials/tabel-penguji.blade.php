{{-- Examiner Approval Status Table --}}
@props(['jadwal', 'user', 'isStaff'])

<div class="card mb-4 shadow-sm border-0">
    <div class="card-header border-bottom p-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="bx bx-group me-2 text-warning"></i>Status Persetujuan Dewan Penguji</h5>
        @if ($isStaff)
            <a href="{{ route('admin.berita-acara-ujian-hasil.manage-penguji', $beritaAcara) }}" class="btn btn-sm btn-outline-primary">
                <i class="bx bx-edit me-1"></i>Kelola Penguji
            </a>
        @endif
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="text-nowrap">
                    <th class="ps-4 fw-bold py-3" width="5%">No</th>
                    <th class="fw-bold py-3">Nama Dosen</th>
                    <th class="fw-bold py-3 text-center">Jabatan</th>
                    <th class="fw-bold py-3 text-center" width="25%">Status Persetujuan</th>
                    @if ($isStaff && $beritaAcara->isMenungguTtdPenguji())
                        <th class="pe-4 fw-bold py-3 text-center" width="15%">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @if ($jadwal)
                    @php
                        $allPenguji = $jadwal
                            ->dosenPenguji()
                            ->orderByRaw(
                                "CASE 
                                WHEN posisi = 'Ketua Penguji' THEN 1 
                                WHEN posisi = 'Penguji 1' THEN 2 
                                WHEN posisi = 'Penguji 2' THEN 3 
                                WHEN posisi = 'Penguji 3' THEN 4 
                                WHEN posisi LIKE '%(PS1)%' THEN 5
                                WHEN posisi LIKE '%(PS2)%' THEN 6
                                ELSE 7 END",
                            )
                            ->get();
                    @endphp
                    @foreach ($allPenguji as $index => $dosen)
                        @php
                            $isKetuaPenguji = $dosen->pivot->posisi === 'Ketua Penguji';
                            $hasSigned = false;
                            $signedAt = null;
                            $isStaffApproval = false;

                            if ($isKetuaPenguji) {
                                $hasSigned = $beritaAcara->hasKetuaSigned();
                                $signedAt = $beritaAcara->ttd_ketua_penguji_at;
                            } else {
                                $hasSigned = $beritaAcara->hasSignedByPenguji($dosen->id);
                                if ($hasSigned) {
                                    $signature = collect($beritaAcara->ttd_dosen_penguji)->firstWhere('dosen_id', $dosen->id);
                                    $signedAt = $signature['signed_at'] ?? null;
                                    $isStaffApproval = $signature['approved_by_staff'] ?? false;
                                }
                            }
                            $isCurrentUser = $dosen->id === $user->id;
                        @endphp
                        <tr class="{{ $isCurrentUser ? 'table-warning bg-label-amber shadow-none border-transparent' : '' }}">
                            <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex justify-content-start align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded-circle {{ $isCurrentUser ? 'bg-warning' : 'bg-label-primary' }}">
                                            {{ strtoupper(substr($dosen->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">{{ $dosen->name }}</span>
                                        <small class="text-muted">NIP: {{ $dosen->nip ?? '-' }}</small>
                                        @if ($isCurrentUser)
                                            <span class="x-small text-warning fw-bold"><i class="bx bx-star me-1"></i>Akun Anda</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $isKetuaPenguji ? 'bg-dark' : 'bg-label-secondary' }} rounded-pill p-2 px-3">
                                    @if (str_contains($dosen->pivot->posisi, '(PS1)'))
                                        PS1
                                    @elseif(str_contains($dosen->pivot->posisi, '(PS2)'))
                                        PS2
                                    @else
                                        {{ $dosen->pivot->posisi }}
                                    @endif
                                </span>
                            </td>
                            <td class="text-center">
                                @if ($hasSigned)
                                    <div class="d-inline-flex flex-column align-items-center">
                                        <span class="badge bg-label-success p-2 px-3 fw-bold">
                                            <i class="bx bx-check-double me-1"></i> Sudah Disetujui
                                        </span>
                                        @if ($signedAt)
                                            <small class="text-muted x-small mt-1 mt-md-0" style="font-size: 0.65rem;">
                                                {{ \Carbon\Carbon::parse($signedAt)->isoFormat('D MMM, HH:mm') }}
                                            </small>
                                        @endif
                                        @if ($isStaffApproval)
                                            <span class="badge bg-label-info mt-1 x-small" style="font-size: 0.6rem;" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Diverifikasi oleh Staff: {{ $signature['staff_name'] ?? 'Admin' }}">
                                                <i class="bx bx-user-check me-1"></i>DISETUJUI STAF
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="badge bg-label-warning p-2 px-3 fw-bold">
                                        <i class="bx bx-time-five me-1"></i> Menunggu
                                    </span>
                                @endif
                            </td>
                            @if ($isStaff && $beritaAcara->isMenungguTtdPenguji())
                                <td class="pe-4 text-center">
                                    @if (!$hasSigned && !$isKetuaPenguji)
                                        <button type="button" class="btn btn-sm btn-outline-primary fw-bold transition-all"
                                            onclick="showApproveOnBehalfModal({{ $dosen->id }}, '{{ addslashes($dosen->name) }}', '{{ $dosen->pivot->posisi }}')">
                                            <i class="bx bx-user-check me-1"></i>Approve
                                        </button>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bx bx-folder-open fs-2 mb-2 text-muted"></i>
                                <span class="text-muted fw-medium">DATA PENGUJI TIDAK TERSEDIA</span>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
