{{-- filepath: resources/views/admin/berita-acara-sempro/show.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Detail Berita Acara')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @php
            // ✅ FIX: Handle null jadwalSeminarProposal (for rejected BA)
            $jadwal = $beritaAcara->jadwalSeminarProposal;
            
            if ($jadwal) {
                $pendaftaran = $jadwal->pendaftaranSeminarProposal;
                $mahasiswa = $pendaftaran->user;
                $pembimbing = $pendaftaran->dosenPembimbing;
            } else {
                // BA ditolak, jadwal sudah dihapus
                // Gunakan data yang tersimpan di BA untuk audit trail
                $pendaftaran = null;
                $mahasiswa = (object)[
                    'id' => $beritaAcara->mahasiswa_id,
                    'name' => $beritaAcara->mahasiswa_name,
                    'nim' => $beritaAcara->mahasiswa_nim,
                ];
                $pembimbing = null;
            }

            // User context
            $user = Auth::user();

            // ✅ TAMBAHKAN: Role checks
            $isDosen = $user->hasRole('dosen');
            $isStaff = $user->hasRole('staff') || $user->hasRole('admin');

            // ✅ PERBAIKAN: Check apakah user adalah pembahas
            $isPembahas = false;
            if ($isDosen && $jadwal) {
                $isPembahas = $jadwal
                    ->dosenPenguji()
                    ->where('users.id', $user->id) // ← PERBAIKAN: gunakan users.id
                    ->where('posisi', '!=', 'Ketua Penguji') // ← PERBAIKAN: exclude ketua
                    ->exists();
            }

            // ✅ PERBAIKAN: Check apakah user adalah ketua penguji
            $isKetua = false;
            $ketuaPenguji = null;
            if ($isDosen && $jadwal) {
                $ketuaPenguji = $jadwal
                    ->dosenPenguji()
                    ->wherePivot('posisi', 'Ketua Penguji') // ← Pastikan konsisten dengan DB
                    ->first();

                if ($ketuaPenguji) {
                    $isKetua = $ketuaPenguji->id === $user->id;
                }
            }

            // ✅ PERBAIKAN: Get pembahas yang hadir (exclude ketua)
            $pembahasHadir = $jadwal
                ? $jadwal->dosenPenguji()->wherePivot('posisi', '!=', 'Ketua Penguji')->get()
                : collect(); // Empty collection if no jadwal
        @endphp

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <span class="text-muted fw-light">Berita Acara /</span> Detail
                </h4>
                <p class="text-muted mb-0">
                    <i class="bx bx-file-blank me-1"></i>
                    Berita Acara Seminar Proposal
                </p>
            </div>
            <div>
                <a href="{{ route('admin.berita-acara-sempro.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
        </div>

        {{-- ✅ NEW: Alert untuk BA yang ditolak (tidak punya jadwal) --}}
        @if (!$jadwal && $beritaAcara->isDitolak())
            <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                <h6 class="alert-heading mb-2">
                    <i class="bx bx-x-circle me-2"></i>Berita Acara Ditolak
                </h6>
                <p class="mb-2">
                    Berita acara ini telah ditolak. Jadwal seminar proposal telah dihapus dari sistem.
                </p>
                <p class="mb-0">
                    <i class="bx bx-info-circle me-1"></i>
                    <small>Mahasiswa harus membuat <strong>Komisi Proposal baru</strong> dengan judul yang direvisi untuk dapat mendaftar seminar proposal kembali.</small>
                </p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- ✅ NEW: Progress Persetujuan Pembahas (jika status = menunggu_ttd_pembahas) --}}
        @if ($beritaAcara->isMenungguTtdPembahas())
            @php
                $progress = $beritaAcara->getTtdPembahasProgress();
            @endphp
            <div class="alert alert-info alert-dismissible mb-4" role="alert">
                <h6 class="alert-heading mb-2">
                    <i class="bx bx-info-circle me-2"></i>Menunggu Persetujuan Pembahas
                </h6>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small">{{ $progress['signed'] }} dari {{ $progress['total'] }} pembahas sudah
                            memberikan persetujuan</span>
                        <span class="small fw-semibold">{{ $progress['percentage'] }}%</span>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar"
                            style="width: {{ $progress['percentage'] }}%" aria-valuenow="{{ $progress['percentage'] }}"
                            aria-valuemin="0" aria-valuemax="100">
                            {{ $progress['signed'] }}/{{ $progress['total'] }}
                        </div>
                    </div>
                </div>
                @if ($progress['signed'] < $progress['total'])
                    <p class="mb-0 small">
                        <i class="bx bx-time me-1"></i>
                        <strong>{{ $progress['total'] - $progress['signed'] }} pembahas</strong> belum memberikan
                        persetujuan.
                    </p>
                @else
                    <p class="mb-0 small text-success">
                        <i class="bx bx-check-circle me-1"></i>
                        Semua pembahas sudah memberikan persetujuan! Menunggu dosen pembimbing.
                    </p>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Status & Actions Bar --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    {{-- Left: Status --}}
                    <div>
                        <label class="form-label fw-bold text-muted small mb-1">Status Berita Acara</label>
                        <div>{!! $beritaAcara->status_badge !!}</div>
                        <small class="text-muted">{{ $beritaAcara->workflow_message }}</small>
                    </div>

                    {{-- Right: Action Buttons --}}
                    <div class="d-flex flex-wrap gap-2">

                        {{-- ✅ TOMBOL UNTUK PEMBAHAS --}}
                        @if ($isPembahas)
                            @if ($beritaAcara->canBeSignedByPembahas($user->id))
                                <a href="{{ route('admin.berita-acara-sempro.approve-pembahas', $beritaAcara) }}"
                                    class="btn btn-success">
                                    <i class="bx bx-check-circle me-1"></i>
                                    Berikan Persetujuan
                                </a>
                            @elseif ($beritaAcara->hasSignedByPembahas($user->id))
                                <div class="alert alert-success mb-0 py-2 px-3">
                                    <i class="bx bx-check-circle me-1"></i>
                                    <small>Anda sudah memberikan persetujuan</small>
                                </div>
                            @endif

                            {{-- Link ke Lembar Catatan --}}
                            @php
                                $lembarCatatan = $beritaAcara->lembarCatatan()->where('dosen_id', $user->id)->first();
                            @endphp
                            @if ($beritaAcara->hasSignedByPembahas($user->id))
                                @if (!$lembarCatatan)
                                    <a href="{{ route('admin.lembar-catatan-sempro.create', $beritaAcara) }}"
                                        class="btn btn-outline-primary">
                                        <i class="bx bx-edit me-1"></i>
                                        Isi Lembar Catatan
                                    </a>
                                @else
                                    <a href="{{ route('admin.lembar-catatan-sempro.show', $lembarCatatan) }}"
                                        class="btn btn-outline-info">
                                        <i class="bx bx-show me-1"></i>
                                        Lihat Lembar Catatan
                                    </a>
                                @endif
                            @endif
                        @endif

                        {{-- ✅ TOMBOL UNTUK PEMBIMBING --}}
                        @if ($isPembimbing || $isKetua)
                            @if ($beritaAcara->canBeFilledAndSignedByPembimbing($user->id))
                                <a href="{{ route('admin.berita-acara-sempro.fill-by-pembimbing', $beritaAcara) }}"
                                    class="btn btn-primary">
                                    <i class="bx bx-edit me-1"></i>
                                    Isi & Tanda Tangan BA
                                </a>
                            @elseif ($beritaAcara->hasPembimbingSigned())
                                <div class="alert alert-success mb-0 py-2 px-3">
                                    <i class="bx bx-check-circle me-1"></i>
                                    <small>Berita Acara sudah selesai</small>
                                </div>
                            @elseif ($beritaAcara->isMenungguTtdPembahas())
                                <div class="alert alert-warning mb-0 py-2 px-3">
                                    <i class="bx bx-time me-1"></i>
                                    <small>Menunggu persetujuan pembahas</small>
                                </div>
                            @endif
                        @endif

                        {{-- ✅ TOMBOL STAFF/ADMIN --}}
                        @if ($isStaff)




                            {{-- Tombol Hapus - Bisa hapus semua status --}}
                            <button type="button" class="btn btn-outline-danger btn-sm"
                                onclick="deleteBeritaAcara({{ $beritaAcara->id }}, '{{ addslashes($mahasiswa->name) }}', '{{ $beritaAcara->status }}')">
                                <i class="bx bx-trash me-1"></i> Hapus
                            </button>
                        @endif

                        {{-- ✅ TOMBOL DOWNLOAD PDF (jika sudah selesai) --}}
                        @if ($beritaAcara->file_path)
                            <a href="{{ route('admin.berita-acara-sempro.view-pdf', $beritaAcara) }}"
                                class="btn btn-outline-info btn-sm" target="_blank">
                                <i class="bx bx-show me-1"></i> Lihat PDF
                            </a>
                            <a href="{{ route('admin.berita-acara-sempro.download-pdf', $beritaAcara) }}"
                                class="btn btn-info btn-sm">
                                <i class="bx bx-download me-1"></i> Download
                            </a>
                        @endif

                        {{-- ✅ TOMBOL KELOLA PEMBAHAS (untuk Staff) --}}
                        @if ($isStaff && !$beritaAcara->isSelesai())
                            <a href="{{ route('admin.berita-acara-sempro.manage-pembahas', $beritaAcara) }}"
                                class="btn btn-warning btn-sm">
                                <i class="bx bx-group me-1"></i> Kelola Pembahas
                            </a>
                        @endif

                        {{-- ✅ NEW: TOMBOL OVERRIDE PEMBIMBING (untuk Staff) --}}
                        @if ($isStaff && $beritaAcara->isMenungguTtdPembimbing())
                            <a href="{{ route('admin.berita-acara-sempro.fill-on-behalf', $beritaAcara) }}"
                                class="btn btn-warning btn-sm">
                                <i class="bx bx-user-check me-1"></i> Override Pembimbing
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Left Column --}}
            <div class="col-lg-8">
                {{-- Info Mahasiswa & Ujian --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-user me-2"></i>Informasi Mahasiswa & Ujian
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small">Mahasiswa</label>
                                <div class="fw-semibold">{{ $mahasiswa->name }}</div>
                                <small class="text-muted">NIM: {{ $mahasiswa->nim }}</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small">Dosen Pembimbing</label>
                                @if ($pembimbing)
                                    <div class="fw-semibold">{{ $pembimbing->name }}</div>
                                    <small class="text-muted">NIP: {{ $pembimbing->nip }}</small>
                                @else
                                    <div class="text-muted fst-italic">Tidak tersedia</div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small">Tanggal & Waktu</label>
                                <div class="fw-semibold">
    {{ $jadwal && $jadwal->tanggal_ujian ? \Carbon\Carbon::parse($jadwal->tanggal_ujian)->isoFormat('dddd, D MMMM Y') : '-' }}
</div>
                                @if ($jadwal && $jadwal->waktu_mulai)
                                    <div class="mt-1">
                                        <span class="badge bg-label-primary">
                                            <i class="bx bx-time-five me-1"></i>
                                            {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }} WITA
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small">Ruangan</label>
                                <div class="fw-semibold">{{ $jadwal ? $jadwal->ruangan : '-' }}</div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold text-muted small">Judul Proposal</label>
                                <div class="text-wrap">
                                    {!! $pendaftaran ? $pendaftaran->judul_skripsi : ($beritaAcara->judul_skripsi ?? '<span class="text-muted fst-italic">Tidak tersedia</span>') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ✅ NEW: Daftar Pembahas & Status TTD --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-group me-2"></i>Dewan Pembahas & Status Persetujuan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Dosen</th>
                                        <th>Posisi</th>
                                        <th width="25%">Status Persetujuan</th>
                                        @if ($isStaff && $beritaAcara->isMenungguTtdPembahas())
                                            <th width="15%">Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // ✅ Sort dosen: Ketua Pembahas pertama, kemudian Anggota Pembahas berurutan
                                        $sortedPembahas = $pembahasHadir->sortBy(function($dosen) {
                                            if ($dosen->pivot->posisi === 'Ketua Pembahas') {
                                                return 0; // Ketua Pembahas di urutan pertama
                                            }
                                            // Extract angka dari "Anggota Pembahas 1", "Anggota Pembahas 2", dst
                                            preg_match('/\d+/', $dosen->pivot->posisi, $matches);
                                            return isset($matches[0]) ? (int)$matches[0] : 999;
                                        })->values(); // ✅ Reset keys agar index dimulai dari 0
                                    @endphp
                                    @foreach ($sortedPembahas as $index => $dosen)
                                        @php
                                            // ✅ PERBAIKAN: Untuk Ketua Pembahas, cek juga ttd_ketua_penguji_at
                                            $isKetuaPembahas = $dosen->pivot->posisi === 'Ketua Pembahas';
                                            $hasSigned = $beritaAcara->hasSignedByPembahas($dosen->id);
                                            
                                            // Jika Ketua Pembahas, cek juga field ttd_ketua_penguji_by
                                            if ($isKetuaPembahas && !$hasSigned) {
                                                $hasSigned = $beritaAcara->ttd_ketua_penguji_by == $dosen->id;
                                            }
                                            
                                            $isCurrentUser = $dosen->id === $user->id;
                                        @endphp
                                        <tr class="{{ $isCurrentUser ? 'table-active' : '' }}">
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <div>
                                                    {{ $dosen->name }}
                                                    @if ($isCurrentUser)
                                                        <span class="badge bg-label-primary ms-1">Anda</span>
                                                    @endif
                                                </div>
                                                <small class="text-muted">NIP: {{ $dosen->nip }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-secondary">
                                                    {{ ucfirst($dosen->pivot->posisi) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($hasSigned)
                                                    @php
                                                        // Cek apakah TTD dari array atau dari field ketua
                                                        if ($isKetuaPembahas && $beritaAcara->ttd_ketua_penguji_by == $dosen->id) {
                                                            // TTD Ketua dari field ttd_ketua_penguji_at
                                                            $signedAt = $beritaAcara->ttd_ketua_penguji_at;
                                                            $isStaffApproval = false;
                                                        } else {
                                                            // TTD dari array ttd_dosen_pembahas
                                                            $signature = collect($beritaAcara->ttd_dosen_pembahas)->firstWhere('dosen_id', $dosen->id);
                                                            $signedAt = $signature['signed_at'] ?? null;
                                                            $isStaffApproval = $signature['approved_by_staff'] ?? false;
                                                        }
                                                    @endphp
                                                    <div>
                                                        <span class="badge bg-success">
                                                            <i class="bx bx-check-circle me-1"></i>Sudah TTD
                                                        </span>
                                                        @if ($isStaffApproval)
                                                            <span class="badge bg-label-info ms-1" 
                                                                  data-bs-toggle="tooltip" 
                                                                  title="Disetujui oleh {{ $signature['staff_name'] ?? 'Staff' }}">
                                                                <i class="bx bx-user-check me-1"></i>via Staff
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if ($signedAt)
                                                        <div class="small text-muted mt-1">
                                                            {{ \Carbon\Carbon::parse($signedAt)->isoFormat('D/M/Y HH:mm') }}
                                                        </div>
                                                    @endif
                                                    @if ($isStaffApproval && isset($signature['approval_reason']))
                                                        <div class="small text-muted fst-italic mt-1">
                                                            <i class="bx bx-info-circle me-1"></i>{{ $signature['approval_reason'] }}
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="bx bx-time me-1"></i>Belum TTD
                                                    </span>
                                                @endif
                                            </td>
                                            @if ($isStaff && $beritaAcara->isMenungguTtdPembahas())
                                                <td>
                                                    @if (!$hasSigned)
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-primary"
                                                                onclick="showApproveOnBehalfModal({{ $dosen->id }}, '{{ addslashes($dosen->name) }}')">
                                                            <i class="bx bx-user-check me-1"></i>Approve
                                                        </button>
                                                    @else
                                                        <span class="text-muted small">-</span>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Isi Berita Acara (dari Pembimbing) --}}
                @if ($beritaAcara->isFilledByPembimbing())
                    <div class="card mb-4">
                        <div class="card-header bg-label-primary">
                            <h5 class="mb-0">
                                <i class="bx bx-clipboard me-2"></i>Isi Berita Acara (Dari Pembimbing)
                            </h5>
                        </div>
                        <div class="card-body">
                            {{-- Kesimpulan --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted small mb-2">
                                    1. Kesimpulan Kelayakan Seminar Proposal Skripsi
                                </label>
                                <div class="p-3 bg-light rounded">
                                    {!! $beritaAcara->keputusan_badge !!}
                                    <div class="text-muted small mt-2">
                                        {{ $beritaAcara->keputusan_description }}
                                    </div>
                                </div>
                            </div>

                            @if ($beritaAcara->catatan_tambahan)
                                <div>
                                    <label class="form-label fw-bold text-muted small mb-2">
                                        2. Catatan Tambahan
                                    </label>
                                    <div class="p-3 bg-light rounded">
                                        {{ $beritaAcara->catatan_tambahan }}
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <div class="d-flex align-items-center text-muted small">
                                <i class="bx bx-user me-2"></i>
                                Diisi oleh: <strong
                                    class="ms-1">{{ $beritaAcara->dosenPembimbingPengisi->name }}</strong>
                                <span class="mx-2">•</span>
                                {{ $beritaAcara->diisi_pembimbing_at->isoFormat('D MMMM Y, HH:mm') }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card mb-4">
                        <div class="card-body text-center py-5">
                            <i class="bx bx-edit-alt display-4 text-muted mb-3"></i>
                            <h5 class="text-muted">Berita Acara Belum Diisi</h5>
                            <p class="text-muted mb-0">
                                @if ($beritaAcara->isMenungguTtdPembahas())
                                    Menunggu persetujuan dari semua dosen pembahas terlebih dahulu.
                                @elseif ($beritaAcara->isMenungguTtdPembimbing())
                                    Menunggu dosen pembimbing untuk mengisi catatan kejadian dan kesimpulan.
                                @else
                                    Berita acara dalam proses.
                                @endif
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Lembar Catatan Penguji --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-notepad me-2"></i>Lembar Catatan Penguji
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($beritaAcara->lembarCatatan->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Dosen Penguji</th>
                                            <th width="20%">Status</th>
                                            <th width="15%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($beritaAcara->lembarCatatan as $index => $catatan)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>
                                                    <div>{{ $catatan->dosen->name }}</div>
                                                    <small class="text-muted">{{ $catatan->dosen->nip }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <i class="bx bx-check me-1"></i>Sudah Diisi
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.lembar-catatan-sempro.show', $catatan) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="bx bx-show me-1"></i>Lihat
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bx bx-notepad display-4 text-muted mb-3"></i>
                                <p class="text-muted mb-0">Belum ada lembar catatan yang diisi oleh penguji.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="col-lg-4">
                {{-- ✅ UPDATE: Timeline/Workflow --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-time me-2"></i>Timeline Workflow
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="timeline mb-0">
                            {{-- 1. Draft Created --}}
                            <li class="timeline-item timeline-item-transparent">
                                <span class="timeline-point timeline-point-success"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-1">
                                        <h6 class="mb-0">Draft Dibuat</h6>
                                        <small
                                            class="text-muted">{{ $beritaAcara->created_at->isoFormat('D MMM Y, HH:mm') }}</small>
                                    </div>
                                    <p class="mb-0 small">
                                        Oleh: {{ $beritaAcara->pembuatBeritaAcara->name ?? 'Staff' }}
                                    </p>
                                </div>
                            </li>

                            {{-- 2. Pembahas TTD --}}
                            <li class="timeline-item timeline-item-transparent">
                                <span
                                    class="timeline-point {{ $beritaAcara->allPembahasHaveSigned() ? 'timeline-point-success' : 'timeline-point-secondary' }}"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-1">
                                        <h6 class="mb-0">Persetujuan Pembahas</h6>
                                        @if ($beritaAcara->allPembahasHaveSigned())
                                            <small class="text-success">
                                                <i class="bx bx-check-circle me-1"></i>Semua sudah TTD
                                            </small>
                                        @else
                                            @php $progress = $beritaAcara->getTtdPembahasProgress(); @endphp
                                            <small class="text-muted">{{ $progress['signed'] }}/{{ $progress['total'] }}
                                                TTD</small>
                                        @endif
                                    </div>
                                </div>
                            </li>

                            {{-- 3. Filled & Signed by Pembimbing --}}
                            <li class="timeline-item timeline-item-transparent">
                                <span
                                    class="timeline-point {{ $beritaAcara->hasPembimbingSigned() ? 'timeline-point-success' : 'timeline-point-secondary' }}"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-1">
                                        <h6 class="mb-0">Diisi & TTD Pembimbing/Ketua</h6>
                                        @if ($beritaAcara->ttd_pembimbing_at)
                                            <small
                                                class="text-muted">{{ $beritaAcara->ttd_pembimbing_at->isoFormat('D MMM Y, HH:mm') }}</small>
                                        @else
                                            <small class="text-muted">Menunggu...</small>
                                        @endif
                                    </div>
                                    @if ($beritaAcara->dosenPembimbingPenandatangan)
                                        <p class="mb-0 small">
                                            <i class="bx bx-check-circle text-success me-1"></i>
                                            Oleh: {{ $beritaAcara->dosenPembimbingPenandatangan->name }}
                                        </p>
                                    @else
                                        @php
                                            // ✅ FIX: Handle null jadwalSeminarProposal
                                            if ($beritaAcara->jadwalSeminarProposal) {
                                                $pembimbing = $beritaAcara->jadwalSeminarProposal->pendaftaranSeminarProposal->dosenPembimbing;
                                                $ketuaPenguji = $beritaAcara->jadwalSeminarProposal->getKetuaPenguji();
                                            } else {
                                                $pembimbing = null;
                                                $ketuaPenguji = null;
                                            }
                                        @endphp
                                        @if ($pembimbing)
                                            <p class="mb-0 small text-muted">
                                                <i class="bx bx-time me-1"></i>
                                                Menunggu: {{ $pembimbing->name }}
                                                @if ($ketuaPenguji && $ketuaPenguji->id !== $pembimbing->id)
                                                    / {{ $ketuaPenguji->name }}
                                                @endif
                                            </p>
                                        @else
                                            <p class="mb-0 small text-muted">
                                                <i class="bx bx-info-circle me-1"></i>
                                                Jadwal tidak tersedia (BA ditolak)
                                            </p>
                                        @endif
                                    @endif
                                </div>
                            </li>

                            {{-- 4. PDF Generated (jika sudah selesai) --}}
                            @if ($beritaAcara->file_path)
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-success"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0">PDF Ter-generate</h6>
                                        </div>
                                        <p class="mb-0 small">
                                            <i class="bx bx-check-circle text-success me-1"></i>
                                            File tersedia untuk didownload
                                        </p>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Modal: Approve On Behalf of Pembahas --}}
    <div class="modal fade" id="approveOnBehalfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="POST" id="approveOnBehalfForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-user-check me-2"></i>Approve Atas Nama Dosen
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="dosen_id" id="modal_dosen_id">
                        
                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-2"></i>
                            <strong>Informasi:</strong> Anda akan memberikan persetujuan atas nama dosen yang tidak dapat mengakses sistem.
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Dosen Pembahas</label>
                            <input type="text" class="form-control" id="modal_dosen_name" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="alasan" class="form-label">
                                Alasan <small class="text-muted">(Opsional)</small>
                            </label>
                            <textarea class="form-control" 
                                      id="alasan" 
                                      name="alasan" 
                                      rows="3" 
                                      placeholder="Contoh: Dosen sedang dinas luar kota, tidak bisa akses sistem, dll."
                                      maxlength="500"></textarea>
                            <div class="form-text">Maksimal 500 karakter</div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="confirmation" id="confirmation" required>
                            <label class="form-check-label" for="confirmation">
                                Saya menyatakan bahwa persetujuan ini dilakukan atas nama dosen yang bersangkutan dan akan tercatat dalam sistem untuk keperluan audit.
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-check me-1"></i>Approve Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Function to show approve on behalf modal
        function showApproveOnBehalfModal(dosenId, dosenName) {
            // Set form action
            const form = document.getElementById('approveOnBehalfForm');
            form.action = `/admin/berita-acara-sempro/{{ $beritaAcara->id }}/approve-on-behalf`;
            
            // Set dosen info
            document.getElementById('modal_dosen_id').value = dosenId;
            document.getElementById('modal_dosen_name').value = dosenName;
            
            // Reset alasan
            document.getElementById('alasan').value = '';
            document.getElementById('confirmation').checked = false;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('approveOnBehalfModal'));
            modal.show();
        }



        function deleteBeritaAcara(id, mahasiswaName, status) {
            // Mapping status ke bahasa Indonesia
            const statusText = {
                'draft': 'Draft',
                'menunggu_ttd_pembahas': 'Menunggu TTD Pembahas',
                'menunggu_ttd_pembimbing': 'Menunggu TTD Pembimbing',
                'selesai': 'Selesai'
            };

            // Pesan warning berbeda untuk status selesai
            const isSelesai = status === 'selesai';
            const warningMessage = isSelesai 
                ? `<div class="alert alert-danger mt-3 mb-0">
                        <i class="bx bx-error-circle me-2"></i>
                        <strong>PERINGATAN KERAS!</strong> Berita acara ini sudah <strong>SELESAI</strong> dan memiliki PDF resmi. 
                        Penghapusan akan menghilangkan semua data dan dokumen terkait secara permanen!
                   </div>`
                : `<div class="alert alert-warning mt-3 mb-0">
                        <i class="bx bx-error-circle me-2"></i>
                        <strong>Perhatian:</strong> Data berita acara dan semua lembar catatan terkait akan dihapus permanen!
                   </div>`;

            Swal.fire({
                title: isSelesai ? '⚠️ Hapus Berita Acara Selesai?' : 'Hapus Berita Acara?',
                html: `
                    <div class="text-start">
                        <p class="mb-2">Anda akan menghapus berita acara dengan detail:</p>
                        <ul class="list-unstyled ps-3">
                            <li><i class="bx bx-user text-primary me-2"></i><strong>Mahasiswa:</strong> ${mahasiswaName}</li>
                            <li><i class="bx bx-info-circle ${isSelesai ? 'text-danger' : 'text-info'} me-2"></i><strong>Status:</strong> <span class="${isSelesai ? 'text-danger fw-bold' : ''}">${statusText[status] || status}</span></li>
                        </ul>
                        ${warningMessage}
                    </div>
                `,
                icon: isSelesai ? 'error' : 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="bx bx-trash me-1"></i> Ya, Hapus Permanen',
                cancelButtonText: '<i class="bx bx-x me-1"></i> Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika status selesai, minta konfirmasi kedua
                    if (isSelesai) {
                        Swal.fire({
                            title: 'Konfirmasi Akhir',
                            html: `
                                <div class="text-start">
                                    <p class="text-danger fw-bold mb-3">
                                        <i class="bx bx-error-circle me-2"></i>
                                        Apakah Anda benar-benar yakin ingin menghapus berita acara yang sudah selesai?
                                    </p>
                                    <p class="mb-2">Ketik <strong class="text-danger">HAPUS</strong> untuk melanjutkan:</p>
                                </div>
                            `,
                            input: 'text',
                            inputPlaceholder: 'Ketik HAPUS',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: '<i class="bx bx-trash me-1"></i> Hapus Sekarang',
                            cancelButtonText: '<i class="bx bx-x me-1"></i> Batalkan',
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            reverseButtons: true,
                            preConfirm: (value) => {
                                if (value !== 'HAPUS') {
                                    Swal.showValidationMessage('Anda harus mengetik "HAPUS" untuk melanjutkan');
                                    return false;
                                }
                                return true;
                            }
                        }).then((finalResult) => {
                            if (finalResult.isConfirmed) {
                                submitDeleteForm(id);
                            }
                        });
                    } else {
                        submitDeleteForm(id);
                    }
                }
            });
        }

        function submitDeleteForm(id) {
            // Show loading
            Swal.fire({
                title: 'Menghapus...',
                html: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/berita-acara-sempro/${id}`;

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';

            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';

            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endpush
