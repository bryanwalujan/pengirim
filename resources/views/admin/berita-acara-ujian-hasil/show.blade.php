{{-- filepath: resources/views/admin/berita-acara-ujian-hasil/show.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Detail Berita Acara Ujian Hasil')

@push('styles')
    {{-- Ensure Boxicons is available --}}
    <style>
        .rounded-xl {
            border-radius: 0.75rem !important;
        }

        .rounded-2xl {
            border-radius: 1rem !important;
        }

        .x-small {
            font-size: 0.7rem !important;
        }

        .fs-small {
            font-size: 0.8rem !important;
        }

        .leading-relaxed {
            line-height: 1.6 !important;
        }

        /* Workflow Timeline */
        .workflow-timeline {
            position: relative;
        }

        .timeline-step {
            position: relative;
        }

        .timeline-step::before {
            content: '';
            position: absolute;
            left: 0;
            top: 24px;
            bottom: 0;
            width: 2px;
            background: #eceef1;
            /* Sneat lighter border */
            margin-left: -1px;
        }

        .timeline-step:last-child::before {
            display: none;
        }

        .timeline-step.active::before {
            background: #ffab00;
            /* Sneat warning/amber */
        }

        /* Card Hover Effects */
        .card {
            transition: all 0.3s ease;
        }

        /* Custom Scrollbar for Table */
        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f5f5f9;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #d9dee3;
            border-radius: 3px;
        }

        /* Premium Swal Classes */
        .premium-swal-container .swal2-popup {
            padding: 2rem;
        }

        /* Sneat specific overrides for premium feel */
        .bg-label-amber {
            background-color: #fff2e0 !important;
            color: #ffab00 !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @php
            // Handle null jadwalUjianHasil (for rejected BA)
            $jadwal = $beritaAcara->jadwalUjianHasil;

            if ($jadwal) {
                $pendaftaran = $jadwal->pendaftaranUjianHasil;
                $mahasiswa = $pendaftaran->user;
            } else {
                // BA ditolak, jadwal sudah dihapus
                $pendaftaran = null;
                $mahasiswa = (object) [
                    'id' => $beritaAcara->mahasiswa_id,
                    'name' => $beritaAcara->mahasiswa_name,
                    'nim' => $beritaAcara->mahasiswa_nim,
                ];
            }

            // User context
            $user = Auth::user();

            // Role checks
            $isDosen = $user->hasRole('dosen');
            $isStaff = $user->hasRole('staff') || $user->hasRole('admin');

            // Check apakah user adalah penguji (bukan ketua)
            $isPenguji = false;
            if ($isDosen && $jadwal) {
                $isPenguji = $jadwal
                    ->dosenPenguji()
                    ->where('users.id', $user->id)
                    ->where('posisi', '!=', 'Ketua Penguji')
                    ->exists();
            }

            // Check apakah user adalah pembimbing (PS1/PS2)
            $isPembimbing = $isDosen ? $beritaAcara->isPembimbing($user->id) : false;
            $myKoreksi = $isPembimbing ? $beritaAcara->getLembarKoreksiFrom($user->id) : null;

            // Check apakah user adalah ketua penguji
            $isKetua = false;
            $ketuaPenguji = null;
            if ($isDosen && $jadwal) {
                $ketuaPenguji = $jadwal->dosenPenguji()->wherePivot('posisi', 'Ketua Penguji')->first();

                if ($ketuaPenguji) {
                    $isKetua = $ketuaPenguji->id === $user->id;
                }
            }

            // Get penguji yang hadir (exclude ketua)
            $pengujiHadir = $jadwal
                ? $jadwal->dosenPenguji()->wherePivot('posisi', '!=', 'Ketua Penguji')->get()
                : collect();
        @endphp

        {{-- Breadcrumb --}}
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style2 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.berita-acara-ujian-hasil.index') }}">Berita Acara</a>
                    </li>
                    <li class="breadcrumb-item active fw-bold" aria-current="page">Detail</li>
                </ol>
            </nav>
        </div>

        {{-- Header Section --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-lg">
                    <span class="avatar-initial rounded-2 bg-label-warning">
                        <i class="bx bx-file bx-sm"></i>
                    </span>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">Detail Berita Acara</h4>
                    <p class="text-muted mb-0">Kelola dan tinjau berkas berita acara pelaksanaan ujian hasil</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.berita-acara-ujian-hasil.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
        </div>

        {{-- Alert Area --}}
        @if (!$jadwal && $beritaAcara->isDitolak())
            <div class="alert alert-danger d-flex align-items-start gap-3 p-4 rounded-3 text-white" role="alert"
                style="background-color: #ff3e1d !important; border: 0;">
                <i class="bx bx-x-circle fs-3 mt-1"></i>
                <div>
                    <h6 class="fw-bold mb-1 text-white">Berita Acara Ditolak</h6>
                    <p class="mb-0">Berita acara ini telah ditolak. Jadwal ujian hasil telah dihapus dari sistem.
                        Mahasiswa harus membuat pendaftaran ujian hasil baru.</p>
                </div>
            </div>
        @endif

        {{-- Progress Banner --}}
        @if ($beritaAcara->isMenungguTtdPenguji())
            @php $progress = $beritaAcara->getTtdPengujiProgress(); @endphp
            <div class="card bg-label-warning border-0 shadow-none mb-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2 fw-bold">
                            <i class="bx bx-time fs-4"></i>
                            <span>Menunggu Persetujuan Penguji</span>
                        </div>
                        <span class="fw-bold">{{ $progress['percentage'] }}% Selesai</span>
                    </div>
                    <div class="progress bg-white rounded-pill" style="height: 10px;">
                        <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" role="progressbar"
                            style="width: {{ $progress['percentage'] }}%" aria-valuenow="{{ $progress['percentage'] }}"
                            aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <p class="mt-3 mb-0 small fw-medium">
                        <i class="bx bx-info-circle me-1"></i>
                        {{ $progress['signed'] }} dari {{ $progress['total'] }} penguji telah menandatangani berkas ini.
                        @if ($progress['signed'] == $progress['total'])
                            <span class="text-success fw-bold ms-1">Siap diproses oleh Ketua Penguji!</span>
                        @endif
                    </p>
                </div>
            </div>
        @endif

        {{-- Status & Quick Action Bar --}}
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <small class="text-uppercase text-muted fw-bold d-block mb-1">Status Saat Ini</small>
                        <div class="d-flex align-items-center gap-3">
                            <div>
                                {!! $beritaAcara->status_badge !!}
                            </div>
                            <div class="text-muted small">
                                <i class="bx bx-info-circle me-1 text-warning"></i>
                                {{ $beritaAcara->workflow_message }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex justify-content-md-end flex-wrap gap-2">
                        {{-- TOMBOL UNTUK PENGUJI --}}
                        @if ($isPenguji)
                            @if ($beritaAcara->canBeSignedByPenguji($user->id))
                                <a href="{{ route('admin.berita-acara-ujian-hasil.approve-penguji', $beritaAcara) }}"
                                    class="btn btn-primary fw-bold shadow-sm">
                                    <i class="bx bx-check-circle me-1"></i>Berikan Persetujuan
                                </a>
                            @elseif ($beritaAcara->hasSignedByPenguji($user->id))
                                <span class="badge bg-label-success p-2 px-3 fs-6">
                                    <i class="bx bx-check-circle me-1"></i> Sudah Disetujui
                                </span>
                            @endif
                        @endif

                        {{-- TOMBOL LEMBAR KOREKSI (PS1/PS2 - OPSIONAL) --}}
                        @if ($isPembimbing)
                            <a href="{{ route('dosen.berita-acara-ujian-hasil.koreksi', $beritaAcara) }}"
                                class="btn btn-outline-info fw-bold">
                                <i
                                    class="bx bx-edit me-1"></i>{{ $myKoreksi ? 'Ubah Lembar Koreksi' : 'Isi Lembar Koreksi' }}
                                <span class="badge bg-label-info ms-2">Opsional</span>
                            </a>
                        @endif

                        {{-- TOMBOL UNTUK KETUA PENGUJI --}}
                        @if ($isKetua)
                            @if ($beritaAcara->canBeFilledAndSignedByKetua($user->id))
                                <a href="{{ route('admin.berita-acara-ujian-hasil.fill-by-ketua', $beritaAcara) }}"
                                    class="btn btn-warning fw-bold shadow-sm text-white">
                                    <i class="bx bx-edit me-1"></i>Isi & Tanda Tangan BA
                                </a>
                            @elseif ($beritaAcara->hasKetuaSigned())
                                <span class="badge bg-label-success p-2 px-3 fs-6">
                                    <i class="bx bx-check-circle me-1"></i> Berkas Telah Selesai
                                </span>
                            @endif
                        @endif

                        {{-- TOMBOL STAFF/ADMIN --}}
                        @if ($isStaff)
                            @if ($beritaAcara->isMenungguTtdKetua())
                                <a href="{{ route('admin.berita-acara-ujian-hasil.fill-on-behalf', $beritaAcara) }}"
                                    class="btn btn-dark fw-bold shadow-sm">
                                    <i class="bx bx-user-check me-1"></i>Override Ketua
                                </a>
                            @endif

                            @if ($beritaAcara->isSelesai() && !$beritaAcara->file_path)
                                <form action="{{ route('admin.berita-acara-ujian-hasil.generate-pdf', $beritaAcara) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-info fw-bold shadow-sm">
                                        <i class="bx bxs-file-pdf me-1"></i>Generate PDF
                                    </button>
                                </form>
                            @endif

                            @can('delete', $beritaAcara)
                                <button type="button" class="btn btn-outline-danger fw-bold"
                                    onclick="deleteBeritaAcara({{ $beritaAcara->id }}, '{{ addslashes($mahasiswa->name) }}', '{{ $beritaAcara->status }}')">
                                    <i class="bx bx-trash me-1"></i>Hapus
                                </button>
                            @endcan
                        @endif

                        {{-- TOMBOL DOWNLOAD PDF --}}
                        @if ($beritaAcara->file_path)
                            <a href="{{ route('admin.berita-acara-ujian-hasil.view-pdf', $beritaAcara) }}"
                                class="btn btn-label-secondary fw-bold" target="_blank">
                                <i class="bx bx-show me-1"></i>Lihat PDF
                            </a>
                            <a href="{{ route('admin.berita-acara-ujian-hasil.download-pdf', $beritaAcara) }}"
                                class="btn btn-primary fw-bold shadow-sm">
                                <i class="bx bx-download me-1"></i>Download
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                {{-- Detail Information Card --}}
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header border-bottom p-4">
                        <h5 class="mb-0 fw-bold"><i class="bx bx-info-circle me-2 text-warning"></i>Informasi Mahasiswa &
                            Ujian</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="p-3 rounded-3 border bg-light">
                                    <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Data
                                        Mahasiswa</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar">
                                            <span class="avatar-initial rounded bg-warning">
                                                <i class="bx bx-user"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-bold fs-6">{{ $mahasiswa->name }}</div>
                                            <div class="text-muted small">{{ $mahasiswa->nim }} •
                                                {{ $beritaAcara->mahasiswa_prodi ?? 'Teknik Informatika' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded-3 border bg-light h-100">
                                    <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Waktu &
                                        Tempat</label>
                                    @if ($jadwal && $jadwal->tanggal_ujian)
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="avatar">
                                                <span class="avatar-initial rounded bg-warning">
                                                    <i class="bx bx-calendar"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <div class="fw-bold small">
                                                    {{ \Carbon\Carbon::parse($jadwal->tanggal_ujian)->isoFormat('dddd, D MMMM Y') }}
                                                </div>
                                                <div class="text-muted small mb-1">
                                                    {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }} WITA
                                                </div>
                                                <span class="badge bg-label-warning rounded-pill">
                                                    <i class="bx bx-buildings me-1"></i>
                                                    {{ $beritaAcara->ruangan ?? ($jadwal?->ruangan ?? '-') }}
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-muted italic py-2 small">Informasi jadwal tidak tersedia</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-4 rounded-3 border bg-light">
                                    <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Judul
                                        Skripsi</label>
                                    <h6 class="fw-bold mb-0 leading-relaxed">
                                        {{ $beritaAcara->judul_skripsi ?? ($pendaftaran?->judul_skripsi ?? '-') }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Examiners Status Card --}}
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header border-bottom p-4">
                        <h5 class="mb-0 fw-bold"><i class="bx bx-group me-2 text-warning"></i>Status Persetujuan Dewan
                            Penguji</h5>
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
                                                    $signature = collect($beritaAcara->ttd_dosen_penguji)->firstWhere(
                                                        'dosen_id',
                                                        $dosen->id,
                                                    );
                                                    $signedAt = $signature['signed_at'] ?? null;
                                                    $isStaffApproval = $signature['approved_by_staff'] ?? false;
                                                }
                                            }
                                            $isCurrentUser = $dosen->id === $user->id;
                                        @endphp
                                        <tr
                                            class="{{ $isCurrentUser ? 'table-warning bg-label-amber shadow-none border-transparent' : '' }}">
                                            <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex justify-content-start align-items-center">
                                                    <div class="avatar avatar-sm me-3">
                                                        <span
                                                            class="avatar-initial rounded-circle {{ $isCurrentUser ? 'bg-warning' : 'bg-label-primary' }}">
                                                            {{ strtoupper(substr($dosen->name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-bold text-dark">{{ $dosen->name }}</span>
                                                        <small class="text-muted">NIP: {{ $dosen->nip ?? '-' }}</small>
                                                        @if ($isCurrentUser)
                                                            <span class="x-small text-warning fw-bold"><i
                                                                    class="bx bx-star me-1"></i>Akun Anda</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge {{ $isKetuaPenguji ? 'bg-dark' : 'bg-label-secondary' }} rounded-pill p-2 px-3">
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
                                                            <small class="text-muted x-small mt-1 mt-md-0"
                                                                style="font-size: 0.65rem;">
                                                                {{ \Carbon\Carbon::parse($signedAt)->isoFormat('D MMM, HH:mm') }}
                                                            </small>
                                                        @endif
                                                        @if ($isStaffApproval)
                                                            <span class="badge bg-label-info mt-1 x-small"
                                                                style="font-size: 0.6rem;" data-bs-toggle="tooltip"
                                                                data-bs-placement="top"
                                                                title="Diverifikasi oleh Staff: {{ $signature['staff_name'] ?? 'Admin' }}">
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
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary fw-bold transition-all"
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

                {{-- Final Content Card (Filled by Ketua) --}}
                @if ($beritaAcara->isFilledByKetua())
                    <div class="card mb-4 shadow-sm border-0 overflow-hidden">
                        <div class="card-header bg-dark p-4 border-0">
                            <h5 class="mb-0 fw-bold text-white"><i class="bx bx-clipboard me-2"></i>Keputusan Berita Acara
                            </h5>
                        </div>
                        <div class="card-body p-4 bg-white">
                            <div class="row g-4">
                                <div class="col-md-7 border-end">
                                    <label class="text-muted small fw-bold text-uppercase mb-3 d-block">1. Keputusan
                                        Akhir</label>
                                    <div class="p-4 rounded-3 bg-light border mb-4">
                                        <div class="mb-3">{!! $beritaAcara->keputusan_badge !!}</div>
                                        <p class="text-dark leading-relaxed mb-0 font-italic">
                                            "{{ $beritaAcara->keputusan_description }}"</p>
                                    </div>

                                    @if ($beritaAcara->catatan_tambahan)
                                        <label class="text-muted small fw-bold text-uppercase mb-3 d-block">3. Catatan
                                            Tambahan</label>
                                        <div class="p-3 rounded-2 border border-dashed text-muted fs-small">
                                            {{ $beritaAcara->catatan_tambahan }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-5 ps-md-4">
                                    @if ($beritaAcara->average_nilai)
                                        <label class="text-muted small fw-bold text-uppercase mb-3 d-block">2. Nilai
                                            Rata-rata</label>
                                        <div class="text-center p-4 rounded-3 bg-label-warning border">
                                            <small class="text-muted mb-1 d-block">Skor Akhir</small>
                                            <div class="display-5 fw-bold text-warning mb-0">
                                                {{ $beritaAcara->average_nilai }}</div>
                                            <span
                                                class="badge bg-warning text-white rounded-pill px-3 py-1 fw-bold fs-6 mt-2 shadow-sm">GRADE
                                                A</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-top p-3 text-center">
                            <div class="small text-muted">
                                <i class="bx bx-check-circle me-1 text-success"></i> Disahkan oleh
                                <span
                                    class="fw-bold text-dark">{{ $beritaAcara->ketuaPenguji->name ?? 'Ketua Penguji' }}</span>
                                <span class="mx-2">|</span>
                                <span
                                    class="font-mono">{{ $beritaAcara->ttd_ketua_penguji_at?->isoFormat('D MMMM Y • HH:mm') ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card mb-4 border border-dashed bg-light text-center py-5">
                        <div class="card-body">
                            <div class="avatar avatar-lg mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-label-secondary">
                                    <i class="bx bx-edit-alt"></i>
                                </span>
                            </div>
                            <h5 class="fw-bold">Isi Berita Acara Belum Tersedia</h5>
                            <p class="text-muted mb-0 small mx-auto" style="max-width: 350px;">
                                @if ($beritaAcara->isMenungguTtdPenguji())
                                    Sistem menunggu semua dosen penguji menyetujui dokumen sebelum Ketua Penguji dapat
                                    mengisikan hasil.
                                @else
                                    Ketua Penguji dapat melakukan pengisian data hasil ujian melalui panel kontrol di atas.
                                @endif
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Penilaian & Lembar Koreksi Card --}}
                @if (
                    $beritaAcara->penilaians->count() > 0 ||
                        $beritaAcara->lembarKoreksis->count() > 0 ||
                        ($isDosen && $beritaAcara->isMenungguTtdPenguji()))
                    <div class="card mb-4 shadow-sm border-0 overflow-hidden">
                        <div class="card-header border-bottom p-4">
                            <h5 class="mb-0 fw-bold"><i class="bx bx-bar-chart-alt-2 me-2 text-warning"></i>Rekapitulasi
                                Nilai & Koreksi</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4 fw-bold py-3" width="5%">No</th>
                                        <th class="fw-bold py-3">Dosen Penguji</th>
                                        <th class="fw-bold py-3 text-center" width="15%">Nilai Akhir</th>
                                        <th class="fw-bold py-3 text-center" width="15%">Status Koreksi</th>
                                        <th class="pe-4 fw-bold py-3 text-center" width="25%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($jadwal)
                                        @foreach ($jadwal->dosenPenguji as $index => $penguji)
                                            @php
                                                $penilaian = $beritaAcara->penilaians
                                                    ->where('dosen_id', $penguji->id)
                                                    ->first();
                                                $koreksi = $beritaAcara->lembarKoreksis
                                                    ->where('dosen_id', $penguji->id)
                                                    ->first();
                                                $isPembimbing =
                                                    str_contains($penguji->pivot->posisi, 'PS1') ||
                                                    str_contains($penguji->pivot->posisi, 'PS2') ||
                                                    str_contains($penguji->pivot->posisi, 'Pembimbing');
                                                $isCurrentDosen = $penguji->id === $user->id;
                                                $isKetuaPenguji = $penguji->pivot->posisi === 'Ketua Penguji';
                                                $canEditPenilaian =
                                                    $isCurrentDosen &&
                                                    $beritaAcara->isMenungguTtdPenguji() &&
                                                    !$isKetuaPenguji;
                                            @endphp
                                            <tr class="{{ $isCurrentDosen ? 'table-warning' : '' }}">
                                                <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <span
                                                                class="avatar-initial rounded-circle {{ $isCurrentDosen ? 'bg-warning' : 'bg-label-primary' }}">
                                                                {{ strtoupper(substr($penguji->name, 0, 1)) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold">{{ $penguji->name }}</div>
                                                            <small
                                                                class="text-muted">{{ $penguji->pivot->posisi }}</small>
                                                            @if ($isCurrentDosen)
                                                                <span class="badge bg-warning ms-1"
                                                                    style="font-size: 9px;">Anda</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @if ($penilaian)
                                                        <div class="d-flex flex-column align-items-center">
                                                            <span
                                                                class="fw-bold text-warning fs-5">{{ number_format($penilaian->nilai_mutu ?? 0, 2) }}</span>
                                                            <span class="badge bg-label-warning px-2 rounded"
                                                                style="font-size: 10px;">{{ $penilaian->grade_letter }}</span>
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">Belum diinput</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($isPembimbing)
                                                        @if ($koreksi)
                                                            <span class="badge bg-label-success p-2 rounded-2">
                                                                <i class="bx bx-check-double"></i> Selesai
                                                            </span>
                                                        @else
                                                            <span class="badge bg-label-secondary p-2 rounded-2">
                                                                <i class="bx bx-minus"></i> Belum
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted small">-</span>
                                                    @endif
                                                </td>
                                                <td class="pe-4 text-center">
                                                    <div class="d-flex justify-content-center gap-1 flex-wrap">
                                                        {{-- Tombol Lihat Detail Penilaian --}}
                                                        @if ($penilaian)
                                                            <button class="btn btn-sm btn-outline-info"
                                                                onclick="showDetailModal({{ $penguji->id }}, '{{ addslashes($penguji->name) }}', '{{ $penguji->pivot->posisi }}')"
                                                                data-bs-toggle="tooltip" title="Lihat Detail Penilaian">
                                                                <i class="bx bx-show"></i>
                                                            </button>
                                                        @endif

                                                        {{-- Tombol Edit/Isi Penilaian untuk Dosen yang login --}}
                                                        @if ($canEditPenilaian)
                                                            <a href="{{ route('dosen.berita-acara-ujian-hasil.penilaian', $beritaAcara) }}"
                                                                class="btn btn-sm {{ $penilaian ? 'btn-outline-warning' : 'btn-primary' }}"
                                                                data-bs-toggle="tooltip"
                                                                title="{{ $penilaian ? 'Edit Penilaian' : 'Isi Penilaian' }}">
                                                                <i
                                                                    class="bx {{ $penilaian ? 'bx-edit' : 'bx-plus' }}"></i>
                                                                {{ $penilaian ? 'Edit' : 'Isi' }}
                                                            </a>
                                                        @endif

                                                        {{-- Tombol Edit/Isi Lembar Koreksi untuk PS1/PS2 --}}
                                                        @if ($isPembimbing && $isCurrentDosen && $beritaAcara->isMenungguTtdPenguji())
                                                            <a href="{{ route('dosen.berita-acara-ujian-hasil.koreksi', $beritaAcara) }}"
                                                                class="btn btn-sm {{ $koreksi ? 'btn-outline-secondary' : 'btn-secondary' }}"
                                                                data-bs-toggle="tooltip"
                                                                title="{{ $koreksi ? 'Edit Koreksi' : 'Isi Koreksi' }}">
                                                                <i class="bx {{ $koreksi ? 'bx-edit' : 'bx-plus' }}"></i>
                                                                Koreksi
                                                            </a>
                                                        @endif

                                                        {{-- Jika tidak ada aksi --}}
                                                        @if (!$penilaian && !$canEditPenilaian && !($isPembimbing && $isCurrentDosen && $beritaAcara->isMenungguTtdPenguji()))
                                                            <span class="text-muted small">-</span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right Sidebar --}}
            <div class="col-lg-4">
                {{-- Timeline Workflow --}}
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header border-bottom p-4">
                        <h5 class="mb-0 fw-bold"><i class="bx bx-history me-2 text-warning"></i>Alur Proses</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="workflow-timeline">
                            {{-- Step 1 --}}
                            <div class="timeline-step active pb-4 ps-4 border-start border-2">
                                <div class="step-icon position-absolute rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                                    style="width: 24px; height: 24px; left: -13px; top: 0;">
                                    <i class="bx bx-check fs-small"></i>
                                </div>
                                <div class="fw-bold small mb-1">Berita Acara Dibuat</div>
                                <div class="text-muted x-small mb-1 font-mono">
                                    {{ $beritaAcara->created_at->isoFormat('D MMM Y, HH:mm') }}</div>
                                <div class="text-muted x-small">Diterbitkan oleh
                                    {{ $beritaAcara->pembuatBeritaAcara->name ?? 'Sistem' }}</div>
                            </div>

                            {{-- Step 2 --}}
                            @php $isStep2Done = $beritaAcara->allPengujiHaveSigned(); @endphp
                            <div class="timeline-step {{ $isStep2Done ? 'active' : '' }} pb-4 ps-4 border-start border-2">
                                <div class="step-icon position-absolute rounded-circle {{ $isStep2Done ? 'bg-success text-white' : 'bg-secondary text-white' }} d-flex align-items-center justify-content-center"
                                    style="width: 24px; height: 24px; left: -13px; top: 0;">
                                    <i class="bx {{ $isStep2Done ? 'bx-check' : 'bx-time' }} fs-small"></i>
                                </div>
                                <div class="fw-bold {{ $isStep2Done ? 'text-dark' : 'text-muted' }} small mb-1">Validasi
                                    Penguji</div>
                                <div class="text-muted x-small">
                                    @php $p = $beritaAcara->getTtdPengujiProgress(); @endphp
                                    {{ $p['signed'] }}/{{ $p['total'] }} Dosen telah validasi.
                                </div>
                            </div>

                            {{-- Step 3 --}}
                            @php $isStep3Done = $beritaAcara->hasKetuaSigned(); @endphp
                            <div class="timeline-step {{ $isStep3Done ? 'active' : '' }} pb-4 ps-4 border-start border-2">
                                <div class="step-icon position-absolute rounded-circle {{ $isStep3Done ? 'bg-success text-white' : 'bg-secondary text-white' }} d-flex align-items-center justify-content-center"
                                    style="width: 24px; height: 24px; left: -13px; top: 0;">
                                    <i class="bx {{ $isStep3Done ? 'bx-check' : 'bx-edit' }} fs-small"></i>
                                </div>
                                <div class="fw-bold {{ $isStep3Done ? 'text-dark' : 'text-muted' }} small mb-1">Hasil &
                                    TTD Ketua</div>
                                @if ($isStep3Done)
                                    <div class="text-muted x-small font-mono">
                                        {{ $beritaAcara->ttd_ketua_penguji_at->isoFormat('D MMM Y, HH:mm') }}</div>
                                @else
                                    <div class="text-muted x-small italic text-warning">Menunggu giliran...</div>
                                @endif
                            </div>

                            {{-- Step 4 --}}
                            @php $isStep4Done = !is_null($beritaAcara->file_path); @endphp
                            <div class="timeline-step ps-4 border-start border-2">
                                <div class="step-icon position-absolute rounded-circle {{ $isStep4Done ? 'bg-warning text-white' : 'bg-secondary text-white' }} d-flex align-items-center justify-content-center"
                                    style="width: 24px; height: 24px; left: -13px; top: 0;">
                                    <i class="bx bx-file fs-small"></i>
                                </div>
                                <div class="fw-bold {{ $isStep4Done ? 'text-dark' : 'text-muted' }} small mb-1">Arsip PDF
                                </div>
                                <div class="text-muted x-small">
                                    {{ $isStep4Done ? 'Berkas digital telah tersedia.' : 'Menunggu penyelesaian proses.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Digital Integrity Card --}}
                @if ($beritaAcara->verification_code)
                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                        <div class="card-body p-0">
                            <div class="bg-warning p-3 px-4 d-flex align-items-center gap-3 shadow-sm">
                                <i class="bx bx-shield-quarter text-white fs-3"></i>
                                <div class="text-white fw-bold">Autentikasi Digital</div>
                            </div>
                            <div class="p-4 text-center">
                                <small class="text-muted text-uppercase fw-bold mb-2 d-block">Kode Verifikasi</small>
                                <div class="bg-light rounded p-3 mb-3 border">
                                    <code
                                        class="fs-4 text-dark fw-bold font-mono tracking-widest">{{ $beritaAcara->verification_code }}</code>
                                </div>
                                @if ($beritaAcara->verification_url)
                                    <a href="{{ $beritaAcara->verification_url }}" target="_blank"
                                        class="btn btn-dark w-100 shadow-sm">
                                        <i class="bx bx-qr-scan me-2"></i>Validasi Berkas
                                    </a>
                                @endif
                                <div class="mt-3 text-muted x-small">
                                    Gunakan kode ini untuk mengecek keaslian dokumen melalui portal publik E-Services UNIMA.
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal: Detail Penilaian & Koreksi --}}
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header border-bottom p-4 bg-label-primary">
                    <h5 class="modal-title fw-bold">
                        <i class="bx bx-detail me-2 text-primary"></i>Detail Penilaian & Koreksi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase">Dosen Penguji</label>
                        <input type="text" class="form-control bg-light border-0 fw-bold" id="detail_dosen_name"
                            readonly>
                        <small class="text-muted" id="detail_posisi"></small>
                    </div>

                    {{-- Penilaian Section --}}
                    <div id="penilaianSection" class="mb-4">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">
                            <i class="bx bx-bar-chart-alt-2 me-2 text-warning"></i>Penilaian
                        </h6>
                        <div id="penilaianContent" class="p-3 bg-light rounded">
                            <div class="text-center text-muted py-3">
                                <i class="bx bx-info-circle fs-4"></i>
                                <p class="mb-0 mt-2">Belum ada data penilaian</p>
                            </div>
                        </div>
                    </div>

                    {{-- Lembar Koreksi Section --}}
                    <div id="koreksiSection" class="mb-4">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">
                            <i class="bx bx-edit me-2 text-info"></i>Lembar Koreksi Skripsi
                        </h6>
                        <div id="koreksiContent" class="p-3 bg-light rounded">
                            <div class="text-center text-muted py-3">
                                <i class="bx bx-info-circle fs-4"></i>
                                <p class="mb-0 mt-2">Belum ada lembar koreksi</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-4">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Approve On Behalf of Penguji --}}
    <div class="modal fade" id="approveOnBehalfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <form action="" method="POST" id="approveOnBehalfForm">
                    @csrf
                    <div class="modal-header border-bottom p-4">
                        <h5 class="modal-title fw-bold">
                            <i class="bx bx-user-check me-2 text-warning"></i>Approve Atas Nama Dosen
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <input type="hidden" name="dosen_id" id="modal_dosen_id">

                        <div class="alert bg-label-warning border-0 mb-4 d-flex gap-3">
                            <i class="bx bx-info-circle fs-4"></i>
                            <div class="small">
                                <strong>Pemberitahuan:</strong> Fitur ini digunakan hanya jika dosen bersangkutan memberikan
                                mandat atau berhalangan mengakses sistem secara teknis.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase">Dosen Penguji</label>
                            <input type="text" class="form-control bg-light border-0 fw-bold" id="modal_dosen_name"
                                readonly>
                        </div>

                        <div id="lembarKoreksiSection" class="mb-4" style="display:none;">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2">Lembar Koreksi Skripsi
                                (Input Data)</label>
                            <div class="p-3 bg-light border rounded mb-3">
                                <small class="text-muted d-block mb-3"><i class="bx bx-pencil me-1"></i>Masukkan hasil
                                    koreksi dari pembimbing.</small>
                                <table class="table table-bordered bg-white" id="koreksiTable">
                                    <thead>
                                        <tr class="table-light">
                                            <th width="15%" class="x-small text-uppercase">Halaman</th>
                                            <th class="x-small text-uppercase">Catatan Koreksi</th>
                                            <th width="5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="koreksiTableBody">
                                        {{-- Rows added via JS --}}
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2 fw-bold"
                                    onclick="addKoreksiRow()">
                                    <i class="bx bx-plus me-1"></i>Tambah Baris Koreksi
                                </button>
                            </div>
                        </div>

                        {{-- Penilaian Section (Staff Override) --}}
                        <div id="penilaianSection" class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase mb-2">
                                <i class="bx bx-star me-1"></i>Penilaian Atas Nama Dosen (Opsional)
                            </label>
                            <div class="p-3 bg-light border rounded">
                                <div class="alert alert-info border-0 py-2 px-3 mb-3">
                                    <small><i class="bx bx-info-circle me-1"></i>Masukkan nilai mutu langsung (skala 0.00 -
                                        4.00). Nilai komponen akan dihitung otomatis.</small>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="nilai_mutu" class="form-label small fw-bold">Nilai Mutu (0.00 -
                                            4.00)</label>
                                        <input type="text" class="form-control" id="nilai_mutu" name="nilai_mutu"
                                            placeholder="Contoh: 3.50" maxlength="4"
                                            oninput="validateAndClampNilaiMutu(this)"
                                            onblur="formatNilaiMutu(this)"
                                            onkeypress="return isValidNilaiMutuKey(event)">
                                        <small class="text-muted">Format: X.XX (contoh: 3.50, 2.75, 4.00)</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Preview Grade</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <span id="gradePreview" class="badge bg-secondary fs-6 px-3 py-2">-</span>
                                            <small id="gradeDescription" class="text-muted">Masukkan nilai</small>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="catatan_penilaian" class="form-label small fw-bold">Catatan Penilaian
                                            (Opsional)</label>
                                        <textarea class="form-control" id="catatan_penilaian" name="catatan_penilaian" rows="2"
                                            placeholder="Catatan tambahan untuk penilaian..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="alasan" class="form-label fw-bold text-muted small text-uppercase">Alasan
                                Persetujuan</label>
                            <textarea class="form-control border rounded" id="alasan" name="alasan" rows="3"
                                placeholder="Berikan alasan singkat..." maxlength="500"></textarea>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="confirmation" id="confirmation"
                                required>
                            <label class="form-check-label text-muted small" for="confirmation">
                                Saya menyatakan bahwa persetujuan ini dilakukan secara sah dan akan tercatat secara permanen
                                dalam sistem log audit.
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer border-top p-4">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-warning text-white px-4 fw-bold">
                            Konfirmasi & Approve
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Prepare data for modal
        const penilaianData = @json($beritaAcara->penilaians->keyBy('dosen_id'));
        const koreksiData = {};
        @foreach ($beritaAcara->lembarKoreksis as $koreksi)
            koreksiData[{{ $koreksi->dosen_id }}] = {
                id: {{ $koreksi->id }},
                dosen_id: {{ $koreksi->dosen_id }},
                koreksi_data: @json($koreksi->koreksi_data),
                created_at: '{{ $koreksi->created_at }}'
            };
        @endforeach
        const pengujiData = @json($jadwal ? $jadwal->dosenPenguji->keyBy('id') : collect());

        // Function to calculate grade letter based on nilai_mutu
        function getGradeLetter(nilaiMutu) {
            if (nilaiMutu === null || nilaiMutu === undefined) return '-';
            if (nilaiMutu >= 3.60) return 'A';
            if (nilaiMutu >= 3.00) return 'B';
            if (nilaiMutu >= 2.00) return 'C';
            if (nilaiMutu >= 1.00) return 'D';
            return 'E';
        }

        // Function to get badge class based on grade
        function getGradeBadgeClass(grade) {
            switch (grade) {
                case 'A':
                    return 'bg-success';
                case 'B':
                    return 'bg-info';
                case 'C':
                    return 'bg-warning';
                case 'D':
                    return 'bg-danger';
                case 'E':
                    return 'bg-dark';
                default:
                    return 'bg-label-warning';
            }
        }

        // Function to show detail modal
        function showDetailModal(dosenId, dosenName, posisi) {
            document.getElementById('detail_dosen_name').value = dosenName;
            document.getElementById('detail_posisi').textContent = posisi;

            const penilaian = penilaianData[dosenId];

            // Populate Penilaian
            const penilaianContent = document.getElementById('penilaianContent');
            if (penilaian) {
                // Komponen penilaian dengan bobot
                const komponenLabels = {
                    nilai_kebaruan: {
                        label: 'Kebaruan dan Signifikansi Penelitian',
                        bobot: 1.5
                    },
                    nilai_kesesuaian: {
                        label: 'Kesesuaian Judul, Masalah, Tujuan, Pembahasan, Kesimpulan, dan Saran',
                        bobot: 1.5
                    },
                    nilai_metode: {
                        label: 'Metode Penelitian dan Pemecahan Masalah',
                        bobot: 1
                    },
                    nilai_kajian_teori: {
                        label: 'Kajian Teori',
                        bobot: 1
                    },
                    nilai_hasil_penelitian: {
                        label: 'Hasil Penelitian',
                        bobot: 3
                    },
                    nilai_referensi: {
                        label: 'Referensi',
                        bobot: 1
                    },
                    nilai_tata_bahasa: {
                        label: 'Tata Bahasa',
                        bobot: 1
                    }
                };

                let komponenRows = '';
                Object.entries(komponenLabels).forEach(([key, config]) => {
                    const nilai = penilaian[key];
                    if (nilai !== null && nilai !== undefined) {
                        const kontribusi = ((nilai / 100) * config.bobot).toFixed(2);
                        komponenRows += `
                            <tr>
                                <td>${config.label}</td>
                                <td class="text-center fw-bold">${config.bobot}</td>
                                <td class="text-center">${nilai}</td>
                                <td class="text-center fw-bold text-primary">${kontribusi}</td>
                            </tr>
                        `;
                    }
                });

                penilaianContent.innerHTML = `
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded border">
                                <small class="text-muted d-block mb-1">Nilai Mutu (Skala 4.00)</small>
                                <div class="fs-3 fw-bold text-warning">${penilaian.nilai_mutu ? parseFloat(penilaian.nilai_mutu).toFixed(2) : '-'}</div>
                                <span class="badge ${getGradeBadgeClass(getGradeLetter(penilaian.nilai_mutu))}">${getGradeLetter(penilaian.nilai_mutu)}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded border">
                                <small class="text-muted d-block mb-1">Total Bobot</small>
                                <div class="fs-3 fw-bold text-info">${penilaian.total_nilai ? parseFloat(penilaian.total_nilai).toFixed(2) : '-'}</div>
                                <small class="text-muted">dari maksimal 10.00</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Komponen</th>
                                            <th class="text-center" width="12%">Bobot</th>
                                            <th class="text-center" width="15%">Nilai</th>
                                            <th class="text-center" width="15%">Kontribusi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${komponenRows}
                                    </tbody>
                                    <tfoot class="table-primary">
                                        <tr>
                                            <th colspan="3" class="text-end">Total Bobot</th>
                                            <th class="text-center">${penilaian.total_nilai ? parseFloat(penilaian.total_nilai).toFixed(2) : '-'}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="mt-2 p-2 bg-light rounded border">
                                <small class="text-muted">
                                    <strong>Rumus:</strong> Nilai Mutu = (Total Bobot / 10) × 4 = ${penilaian.nilai_mutu ? parseFloat(penilaian.nilai_mutu).toFixed(2) : '-'}
                                </small>
                            </div>
                        </div>
                        ${penilaian.catatan ? `
                                                <div class="col-12">
                                                    <label class="form-label fw-bold small">Catatan Dosen:</label>
                                                    <div class="p-3 bg-white rounded border">
                                                        <p class="mb-0 small text-muted">${penilaian.catatan}</p>
                                                    </div>
                                                </div>
                                            ` : ''}
                    </div>
                `;
            } else {
                penilaianContent.innerHTML = `
                    <div class="text-center text-muted py-3">
                        <i class="bx bx-info-circle fs-4"></i>
                        <p class="mb-0 mt-2">Belum ada data penilaian</p>
                    </div>
                `;
            }

            // Populate Koreksi
            const koreksiContent = document.getElementById('koreksiContent');
            const koreksi = koreksiData[dosenId];

            if (koreksi && koreksi.koreksi_data && koreksi.koreksi_data.length > 0) {
                koreksiContent.innerHTML = `
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" width="15%">Halaman</th>
                                    <th>Catatan Koreksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${koreksi.koreksi_data.map(item => `
                                                                                    <tr>
                                                                                        <td class="text-center fw-bold">${item.halaman || '-'}</td>
                                                                                        <td>${item.catatan || '-'}</td>
                                                                                    </tr>
                                                                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 p-2 bg-white rounded border">
                        <small class="text-muted">
                            <i class="bx bx-time me-1"></i>
                            Diisi pada: ${koreksi.created_at ? new Date(koreksi.created_at).toLocaleDateString('id-ID', { 
                                day: 'numeric', 
                                month: 'long', 
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            }) : '-'}
                        </small>
                    </div>
                `;
            } else {
                koreksiContent.innerHTML = `
                    <div class="text-center text-muted py-3">
                        <i class="bx bx-info-circle fs-4"></i>
                        <p class="mb-0 mt-2">Belum ada lembar koreksi</p>
                    </div>
                `;
            }

            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
        }

        // Function to show approve on behalf modal
        function showApproveOnBehalfModal(dosenId, dosenName, posisi) {
            const form = document.getElementById('approveOnBehalfForm');
            form.action = `/admin/berita-acara-ujian-hasil/{{ $beritaAcara->id }}/approve-on-behalf`;

            document.getElementById('modal_dosen_id').value = dosenId;
            document.getElementById('modal_dosen_name').value = dosenName;

            document.getElementById('alasan').value = '';
            document.getElementById('confirmation').checked = false;

            // Reset penilaian fields
            document.getElementById('nilai_mutu').value = '';
            document.getElementById('catatan_penilaian').value = '';
            updateGradePreview('');
            hideNilaiMutuWarning();

            // Handle Lembar Koreksi
            const koreksiSection = document.getElementById('lembarKoreksiSection');
            const tbody = document.getElementById('koreksiTableBody');

            // Check if position contains PS1 or PS2 or Pembimbing
            const isPembimbing = posisi && (posisi.includes('PS1') || posisi.includes('PS2') || posisi.includes(
                'Pembimbing'));

            if (isPembimbing) {
                koreksiSection.style.display = 'block';
                tbody.innerHTML = ''; // Clear previous
                // Add one default row if empty
                if (tbody.children.length === 0) {
                    addKoreksiRow();
                }
            } else {
                koreksiSection.style.display = 'none';
                tbody.innerHTML = '';
            }

            const modal = new bootstrap.Modal(document.getElementById('approveOnBehalfModal'));
            modal.show();
        }

        // Function to update grade preview based on nilai_mutu input
        // ========== NILAI MUTU VALIDATION FUNCTIONS ==========
        
        // Only allow digits and one decimal point
        function isValidNilaiMutuKey(event) {
            const char = String.fromCharCode(event.which);
            const input = event.target;
            const currentValue = input.value;
            
            // Allow: backspace, delete, tab, escape, enter
            if ([8, 9, 13, 27, 46].includes(event.keyCode)) return true;
            
            // Allow digits 0-9
            if (/[0-9]/.test(char)) {
                // Check if adding this digit would exceed 4.00
                const newValue = currentValue + char;
                const numValue = parseFloat(newValue);
                if (!isNaN(numValue) && numValue > 4) {
                    return false;
                }
                return true;
            }
            
            // Allow one decimal point
            if (char === '.') {
                return !currentValue.includes('.');
            }
            
            return false;
        }
        
        // Validate and clamp nilai mutu in real-time
        function validateAndClampNilaiMutu(input) {
            let value = input.value;
            
            // Remove invalid characters (only allow digits and one decimal)
            value = value.replace(/[^0-9.]/g, '');
            
            // Ensure only one decimal point
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }
            
            // Limit to 2 decimal places
            if (parts.length === 2 && parts[1].length > 2) {
                value = parts[0] + '.' + parts[1].substring(0, 2);
            }
            
            // Parse and validate range
            const numValue = parseFloat(value);
            
            if (!isNaN(numValue)) {
                // Clamp to max 4.00
                if (numValue > 4) {
                    value = '4.00';
                    input.value = value;
                    showNilaiMutuWarning('Nilai maksimal adalah 4.00');
                } else if (numValue < 0) {
                    value = '0.00';
                    input.value = value;
                    showNilaiMutuWarning('Nilai minimal adalah 0.00');
                } else {
                    input.value = value;
                    hideNilaiMutuWarning();
                }
            } else {
                input.value = value;
            }
            
            updateGradePreview(input.value);
        }
        
        // Format nilai mutu on blur (ensure 2 decimal places)
        function formatNilaiMutu(input) {
            let value = input.value.trim();
            
            if (value === '' || value === '.') {
                input.value = '';
                updateGradePreview('');
                return;
            }
            
            let numValue = parseFloat(value);
            
            if (isNaN(numValue)) {
                input.value = '';
                updateGradePreview('');
                return;
            }
            
            // Clamp between 0 and 4
            numValue = Math.min(4, Math.max(0, numValue));
            
            // Format to 2 decimal places
            input.value = numValue.toFixed(2);
            updateGradePreview(input.value);
        }
        
        // Show warning message
        function showNilaiMutuWarning(message) {
            let warning = document.getElementById('nilaiMutuWarning');
            if (!warning) {
                warning = document.createElement('div');
                warning.id = 'nilaiMutuWarning';
                warning.className = 'text-danger small mt-1';
                const input = document.getElementById('nilai_mutu');
                input.parentNode.appendChild(warning);
            }
            warning.innerHTML = '<i class="bx bx-error-circle me-1"></i>' + message;
            warning.style.display = 'block';
            
            setTimeout(() => {
                hideNilaiMutuWarning();
            }, 2000);
        }
        
        // Hide warning message
        function hideNilaiMutuWarning() {
            const warning = document.getElementById('nilaiMutuWarning');
            if (warning) {
                warning.style.display = 'none';
            }
        }

        function updateGradePreview(value) {
            const gradePreview = document.getElementById('gradePreview');
            const gradeDescription = document.getElementById('gradeDescription');

            if (!value || value === '' || isNaN(parseFloat(value))) {
                gradePreview.textContent = '-';
                gradePreview.className = 'badge bg-secondary fs-6 px-3 py-2';
                gradeDescription.textContent = 'Masukkan nilai';
                return;
            }

            const nilaiMutu = parseFloat(value);
            let grade, badgeClass, description;

            if (nilaiMutu >= 3.60) {
                grade = 'A';
                badgeClass = 'bg-success';
                description = 'Sangat Baik';
            } else if (nilaiMutu >= 3.00) {
                grade = 'B';
                badgeClass = 'bg-info';
                description = 'Baik';
            } else if (nilaiMutu >= 2.00) {
                grade = 'C';
                badgeClass = 'bg-warning';
                description = 'Cukup';
            } else if (nilaiMutu >= 1.00) {
                grade = 'D';
                badgeClass = 'bg-danger';
                description = 'Kurang';
            } else {
                grade = 'E';
                badgeClass = 'bg-dark';
                description = 'Sangat Kurang';
            }

            gradePreview.textContent = grade;
            gradePreview.className = `badge ${badgeClass} fs-6 px-3 py-2`;
            gradeDescription.textContent = `${description} (${parseFloat(nilaiMutu).toFixed(2)})`;
        }

        let koreksiRowIndex = 0;

        function addKoreksiRow() {
            const tbody = document.getElementById('koreksiTableBody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <input type="text" name="lembar_koreksi[${koreksiRowIndex}][halaman]" class="form-control form-control-sm" placeholder="Hal.">
                </td>
                <td>
                    <textarea name="lembar_koreksi[${koreksiRowIndex}][catatan]" class="form-control form-control-sm" rows="1" placeholder="Catatan..."></textarea>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-icon btn-label-danger" onclick="this.closest('tr').remove()">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
            koreksiRowIndex++;
        }

        function deleteBeritaAcara(id, mahasiswaName, status) {
            const statusText = {
                'draft': 'Draft',
                'menunggu_ttd_penguji': 'Menunggu TTD Penguji',
                'menunggu_ttd_ketua': 'Menunggu TTD Ketua',
                'selesai': 'Selesai',
                'ditolak': 'Ditolak'
            };

            const isSelesai = status === 'selesai';
            const warningMessage = isSelesai ?
                `<div class="alert alert-danger mt-3 mb-0 text-white" style="background-color: #ff3e1d !important;">
                        <i class="bx bx-error-circle me-2"></i>
                        <strong>PERINGATAN!</strong> Dokumen ini sudah <strong>SELESAI</strong>. 
                        Penghapusan akan menghilangkan semua data permanen!
                   </div>` :
                `<div class="alert alert-warning mt-3 mb-0" style="background-color: #fff2e0 !important; color: #ffab00 !important;">
                        <i class="bx bx-error-circle me-2"></i>
                        Data berita acara akan dihapus permanen!
                   </div>`;

            Swal.fire({
                title: isSelesai ? '⚠️ Hapus Dokumen Selesai?' : 'Hapus Berita Acara?',
                html: `
                    <div class="text-center p-2">
                        <p class="text-muted mb-4">Konfirmasi penghapusan rekaman untuk mahasiswa:</p>
                        <div class="p-3 bg-light rounded-3 mb-3 border">
                            <div class="fw-bold fs-5 text-dark">${mahasiswaName}</div>
                            <div class="text-warning fw-bold small mb-0">${statusText[status] || status}</div>
                        </div>
                        ${warningMessage}
                    </div>
                `,
                icon: isSelesai ? 'error' : 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus Permanen',
                cancelButtonText: 'Batalkan',
                confirmButtonColor: '#ff3e1d',
                cancelButtonColor: '#8592a3',
                reverseButtons: true,
                customClass: {
                    container: 'premium-swal-container',
                    popup: 'rounded-3 border-0 shadow-lg',
                    confirmButton: 'btn btn-danger px-4 py-2 fw-bold',
                    cancelButton: 'btn btn-secondary px-4 py-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/berita-acara-ujian-hasil/${id}`;

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
            });
        }
    </script>
@endpush
