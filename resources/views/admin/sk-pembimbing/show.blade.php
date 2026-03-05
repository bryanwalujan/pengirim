{{-- filepath: resources/views/admin/sk-pembimbing/show.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Detail Pendaftaran SK Pembimbing')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.sk-pembimbing.index') }}">Pendaftaran SK Pembimbing</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Detail</li>
            </ol>
        </nav>

        {{-- Header Section --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div class="me-3">
                <h4 class="fw-bold mb-1">Detail Pendaftaran SK Pembimbing</h4>
                <p class="text-muted mb-0">
                    <i class="bx bx-calendar me-1"></i>
                    Diajukan pada {{ $pengajuan->created_at->format('d F Y, H:i') }} WIB
                </p>
            </div>
            <div class="d-flex gap-2">
                {!! $pengajuan->status_badge !!}
                <a href="{{ route('admin.sk-pembimbing.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
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

        <div class="row">
            {{-- Left Column --}}
            <div class="col-xl-4 col-lg-5 mb-4">
                {{-- Card: Profile Mahasiswa --}}
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <div class="avatar avatar-xl mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    <i class="bx bx-user fs-1"></i>
                                </span>
                            </div>
                            <h5 class="mb-1">{{ $pengajuan->mahasiswa->name ?? '-' }}</h5>
                            <div class="mb-2">
                                <span class="badge bg-label-primary">{{ $pengajuan->mahasiswa->nim ?? '-' }}</span>
                            </div>
                            <p class="text-muted mb-0">
                                <i class="bx bx-envelope me-1"></i>{{ $pengajuan->mahasiswa->email ?? '-' }}
                            </p>
                        </div>

                        <hr class="my-3">

                        <div class="text-start">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">
                                    <i class="bx bx-book-open me-1"></i>Judul Skripsi
                                </label>
                                <p class="mb-0 fw-semibold">{{ $pengajuan->judul_skripsi }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Timeline Status --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bx bx-time-five me-1"></i>Timeline Proses
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="timeline mb-0">
                            <li class="timeline-item timeline-item-transparent">
                                <div class="timeline-event">
                                    <div class="timeline-header mb-1">
                                        <h6 class="mb-0">Pengajuan Diajukan</h6>
                                        <small
                                            class="text-muted">{{ $pengajuan->created_at->format('d M Y, H:i') }}</small>
                                    </div>
                                    <p class="mb-0 small">Mahasiswa melakukan pendaftaran Pendaftaran SK Pembimbing</p>
                                </div>
                            </li>



                            @if ($pengajuan->ps_assigned_at)
                                <li class="timeline-item timeline-item-transparent">
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0">Pembimbing Ditentukan</h6>
                                            <small
                                                class="text-muted">{{ $pengajuan->ps_assigned_at->format('d M Y, H:i') }}</small>
                                        </div>
                                        <p class="mb-0 small">Oleh {{ $pengajuan->psAssignedByUser->name ?? 'System' }}</p>
                                    </div>
                                </li>
                            @endif

                            @if ($pengajuan->ttd_korprodi_at)
                                <li class="timeline-item timeline-item-transparent">
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0">TTD Korprodi</h6>
                                            <small
                                                class="text-muted">{{ $pengajuan->ttd_korprodi_at->format('d M Y, H:i') }}</small>
                                        </div>
                                        <p class="mb-0 small">Oleh {{ $pengajuan->ttdKorprodiUser->name ?? '-' }}</p>
                                    </div>
                                </li>
                            @endif

                            @if ($pengajuan->ttd_kajur_at)
                                <li class="timeline-item timeline-item-transparent">
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0">TTD Kajur</h6>
                                            <small
                                                class="text-muted">{{ $pengajuan->ttd_kajur_at->format('d M Y, H:i') }}</small>
                                        </div>
                                        <p class="mb-0 small">Oleh {{ $pengajuan->ttdKajurUser->name ?? '-' }}</p>
                                    </div>
                                </li>
                            @endif

                            @if ($pengajuan->isSelesai())
                                <li class="timeline-item timeline-item-transparent">
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0">Proses Selesai</h6>
                                        </div>
                                        <p class="mb-0 small">Pendaftaran SK Pembimbing telah diterbitkan</p>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                {{-- Card: Quick Actions --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bx bx-cog me-2"></i>Aksi
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">

                            {{-- STAFF ONLY: Assign Pembimbing --}}
                            @if (auth()->user()->hasRole('staff'))
                                @if (!$pengajuan->hasPembimbingAssigned())
                                    <a href="{{ route('admin.sk-pembimbing.assign-pembimbing', $pengajuan) }}"
                                        class="btn btn-primary">
                                        <i class="bx bx-user-plus me-1"></i>Tentukan Pembimbing
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                        data-bs-target="#rejectModal">
                                        <i class="bx bx-x-circle me-1"></i>Tolak Pengajuan
                                    </button>
                                @endif
                            @endif

                            {{-- TTD KORPRODI --}}
                            @if ($pengajuan->isMenungguTtdKorprodi())
                                @if (auth()->user()->isKoordinatorProdi() || auth()->user()->hasRole('staff'))
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#signKorprodiModal">
                                        <i class="bx bx-pen me-1"></i>TTD Koordinator Prodi
                                    </button>
                                @else
                                    <div class="alert alert-warning mb-0">
                                        <small><i class="bx bx-hourglass me-1"></i>Menunggu TTD Koordinator Prodi</small>
                                    </div>
                                @endif
                            @endif

                            {{-- TTD KAJUR --}}
                            @if ($pengajuan->isMenungguTtdKajur())
                                @if (auth()->user()->isKetuaJurusan() || auth()->user()->hasRole('staff'))
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#signKajurModal">
                                        <i class="bx bx-pen me-1"></i>TTD Ketua Jurusan
                                    </button>
                                @else
                                    <div class="alert alert-info mb-0">
                                        <small><i class="bx bx-hourglass me-1"></i>Menunggu TTD Ketua Jurusan</small>
                                    </div>
                                @endif
                            @endif

                            {{-- Download SK --}}
                            @if ($pengajuan->isSelesai() && $pengajuan->file_surat_sk)
                                <a href="{{ route('admin.sk-pembimbing.download-sk', $pengajuan) }}"
                                    class="btn btn-success">
                                    <i class="bx bx-download me-1"></i>Download Surat
                                </a>
                            @endif

                            {{-- Status Info --}}
                            @if ($pengajuan->isDitolak())
                                <div class="alert alert-danger mb-0">
                                    <small><i class="bx bx-x-circle me-1"></i>Pengajuan ditolak</small>
                                    @if ($pengajuan->alasan_ditolak)
                                        <br><small class="text-muted">{{ $pengajuan->alasan_ditolak }}</small>
                                    @endif
                                </div>
                            @elseif ($pengajuan->isSelesai())
                                <div class="alert alert-success mb-0">
                                    <small><i class="bx bx-check-circle me-1"></i>SK telah diterbitkan</small>
                                </div>
                            @endif

                            {{-- Delete (Staff Only) --}}
                            @if (auth()->user()->hasRole('staff'))
                                <hr class="my-2">
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal">
                                    <i class="bx bx-trash me-1"></i>Hapus Pengajuan
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="col-xl-8 col-lg-7">
                {{-- Card: Alasan Penolakan (Tampil pertama jika ditolak) --}}
                @if ($pengajuan->isDitolak() && $pengajuan->alasan_ditolak)
                    <div class="card mb-4 border-danger">
                        <div class="card-header bg-danger">
                            <h5 class="card-title mb-0 text-white">
                                <i class="bx bx-x-circle me-2"></i>Pengajuan Ditolak
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-danger bg-danger bg-opacity-10 border-0 my-3">
                                <div class="d-flex">
                                    <i class="bx bx-error-circle fs-4 me-2 text-danger"></i>
                                    <div>
                                        <strong class="text-danger">Pengajuan ini telah ditolak</strong>
                                        <p class="mb-0 text-muted small">
                                            Mahasiswa perlu mengajukan ulang dengan dokumen yang sesuai.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">
                                    <i class="bx bx-message-alt-error me-1"></i>Alasan Penolakan
                                </label>
                                <div class="p-3 bg-lighter rounded border-start border-danger border-3">
                                    <p class="mb-0">{{ $pengajuan->alasan_ditolak }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Card: Pembimbing Skripsi --}}
                @if ($pengajuan->hasPembimbingAssigned())
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-user-check me-2"></i>Pembimbing Skripsi
                            </h5>
                            @if ($pengajuan->ps_assigned_at)
                                <small class="text-muted">
                                    Ditentukan: {{ $pengajuan->ps_assigned_at->format('d M Y') }}
                                </small>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="border rounded p-3 text-center">
                                        <div class="avatar avatar-md mx-auto mb-2">
                                            <span class="avatar-initial rounded-circle bg-label-primary">1</span>
                                        </div>
                                        <h6 class="mb-1">{{ $pengajuan->dosenPembimbing1->name ?? '-' }}</h6>
                                        <small class="text-muted">Pembimbing 1 (PS1)</small>
                                        @if ($pengajuan->dosenPembimbing1?->nip)
                                            <div class="mt-1">
                                                <small class="text-muted">NIP:
                                                    {{ $pengajuan->dosenPembimbing1->nip }}</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded p-3 text-center">
                                        <div class="avatar avatar-md mx-auto mb-2">
                                            <span class="avatar-initial rounded-circle bg-label-info">2</span>
                                        </div>
                                        <h6 class="mb-1">{{ $pengajuan->dosenPembimbing2->name ?? 'Tidak ada' }}</h6>
                                        <small class="text-muted">Pembimbing 2 (PS2)</small>
                                        @if ($pengajuan->dosenPembimbing2?->nip)
                                            <div class="mt-1">
                                                <small class="text-muted">NIP:
                                                    {{ $pengajuan->dosenPembimbing2->nip }}</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if ($pengajuan->psAssignedByUser)
                                <div class="mt-3 pt-3 border-top">
                                    <small class="text-muted">
                                        <i class="bx bx-user me-1"></i>
                                        Ditentukan oleh: {{ $pengajuan->psAssignedByUser->name }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Card: Progress Tanda Tangan --}}
                @if ($pengajuan->hasPembimbingAssigned())
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-pen me-2"></i>Progress Tanda Tangan
                            </h5>
                        </div>
                        <div class="card-body">
                            {{-- Progress Bar --}}
                            @php
                                $progress = 0;
                                if ($pengajuan->ttd_korprodi_at) {
                                    $progress += 50;
                                }
                                if ($pengajuan->ttd_kajur_at) {
                                    $progress += 50;
                                }
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Progress Tanda Tangan</small>
                                    <small class="fw-bold">{{ $progress }}%</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ $progress }}%"></div>
                                </div>
                            </div>

                            {{-- TTD Status Cards --}}
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div
                                        class="border rounded p-3 {{ $pengajuan->ttd_korprodi_at ? 'border-success bg-success bg-opacity-10' : '' }}">
                                        <div class="d-flex align-items-center">
                                            @if ($pengajuan->ttd_korprodi_at)
                                                <i class="bx bx-check-circle text-success fs-4 me-2"></i>
                                            @else
                                                <i class="bx bx-time-five text-warning fs-4 me-2"></i>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">TTD Korprodi</h6>
                                                @if ($pengajuan->ttd_korprodi_at)
                                                    <small class="text-success">
                                                        {{ $pengajuan->ttd_korprodi_at->format('d M Y, H:i') }}
                                                    </small>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $pengajuan->ttdKorprodiUser->name ?? '-' }}
                                                    </small>
                                                @else
                                                    <small class="text-muted">Menunggu</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div
                                        class="border rounded p-3 {{ $pengajuan->ttd_kajur_at ? 'border-success bg-success bg-opacity-10' : '' }}">
                                        <div class="d-flex align-items-center">
                                            @if ($pengajuan->ttd_kajur_at)
                                                <i class="bx bx-check-circle text-success fs-4 me-2"></i>
                                            @else
                                                <i class="bx bx-time-five text-warning fs-4 me-2"></i>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">TTD Kajur</h6>
                                                @if ($pengajuan->ttd_kajur_at)
                                                    <small class="text-success">
                                                        {{ $pengajuan->ttd_kajur_at->format('d M Y, H:i') }}
                                                    </small>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $pengajuan->ttdKajurUser->name ?? '-' }}
                                                    </small>
                                                @else
                                                    <small class="text-muted">
                                                        {{ $pengajuan->ttd_korprodi_at ? 'Menunggu' : 'Menunggu TTD Korprodi' }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Verification Code --}}
                            @if ($pengajuan->verification_code && $pengajuan->isSelesai())
                                <div class="bg-lighter rounded p-3 mt-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted d-block">Kode Verifikasi</small>
                                            <code class="fs-6">{{ $pengajuan->verification_code }}</code>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            onclick="copyToClipboard('{{ $pengajuan->verification_code }}')">
                                            <i class="bx bx-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Card: Dokumen Pendukung dengan Preview --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-file me-2"></i>Dokumen Pendukung
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- Document Selector Tabs --}}
                        <div class="document-selector mb-3">
                            <div class="row g-2">
                                {{-- Surat Permohonan --}}
                                <div class="col-md-6 col-lg-4">
                                    <div class="document-item {{ $pengajuan->file_surat_permohonan ? 'has-file' : 'no-file' }}"
                                        data-document-type="permohonan"
                                        data-document-url="{{ $pengajuan->file_surat_permohonan ? route('admin.sk-pembimbing.view-document', [$pengajuan, 'permohonan']) : '' }}"
                                        data-document-name="Surat Permohonan">
                                        <div class="document-icon">
                                            <i class="bx bx-file-blank text-primary"></i>
                                        </div>
                                        <div class="document-info">
                                            <span class="document-title">Surat Permohonan</span>
                                            @if ($pengajuan->file_surat_permohonan)
                                                <span class="document-status text-success">
                                                    <i class="bx bx-check-circle"></i> Tersedia
                                                </span>
                                            @else
                                                <span class="document-status text-muted">
                                                    <i class="bx bx-x-circle"></i> Tidak ada
                                                </span>
                                            @endif
                                        </div>
                                        @if ($pengajuan->file_surat_permohonan)
                                            <div class="document-actions">
                                                <button type="button" class="btn btn-sm btn-icon btn-preview"
                                                    title="Preview">
                                                    <i class="bx bx-show"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Slip UKT --}}
                                <div class="col-md-6 col-lg-4">
                                    <div class="document-item {{ $pengajuan->file_slip_ukt ? 'has-file' : 'no-file' }}"
                                        data-document-type="ukt"
                                        data-document-url="{{ $pengajuan->file_slip_ukt ? route('admin.sk-pembimbing.view-document', [$pengajuan, 'ukt']) : '' }}"
                                        data-document-name="Slip UKT">
                                        <div class="document-icon">
                                            <i class="bx bx-receipt text-success"></i>
                                        </div>
                                        <div class="document-info">
                                            <span class="document-title">Slip UKT</span>
                                            @if ($pengajuan->file_slip_ukt)
                                                <span class="document-status text-success">
                                                    <i class="bx bx-check-circle"></i> Tersedia
                                                </span>
                                            @else
                                                <span class="document-status text-muted">
                                                    <i class="bx bx-x-circle"></i> Tidak ada
                                                </span>
                                            @endif
                                        </div>
                                        @if ($pengajuan->file_slip_ukt)
                                            <div class="document-actions">
                                                <button type="button" class="btn btn-sm btn-icon btn-preview"
                                                    title="Preview">
                                                    <i class="bx bx-show"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Proposal Revisi --}}
                                <div class="col-md-6 col-lg-4">
                                    <div class="document-item {{ $pengajuan->file_proposal_revisi ? 'has-file' : 'no-file' }}"
                                        data-document-type="proposal"
                                        data-document-url="{{ $pengajuan->file_proposal_revisi ? route('admin.sk-pembimbing.view-document', [$pengajuan, 'proposal']) : '' }}"
                                        data-document-name="Proposal Revisi">
                                        <div class="document-icon">
                                            <i class="bx bx-book-content text-info"></i>
                                        </div>
                                        <div class="document-info">
                                            <span class="document-title">Proposal Revisi</span>
                                            @if ($pengajuan->file_proposal_revisi)
                                                <span class="document-status text-success">
                                                    <i class="bx bx-check-circle"></i> Tersedia
                                                </span>
                                            @else
                                                <span class="document-status text-muted">
                                                    <i class="bx bx-x-circle"></i> Tidak ada
                                                </span>
                                            @endif
                                        </div>
                                        @if ($pengajuan->file_proposal_revisi)
                                            <div class="document-actions">
                                                <button type="button" class="btn btn-sm btn-icon btn-preview"
                                                    title="Preview">
                                                    <i class="bx bx-show"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Document Preview Container --}}
                        <div id="documentPreviewContainer" class="document-preview-container" style="display: none;">
                            <div class="preview-header">
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-file-blank me-2 fs-4"></i>
                                    <div>
                                        <h6 class="mb-0" id="previewDocumentTitle">Dokumen</h6>
                                        <small class="text-muted" id="previewDocumentInfo">Klik dokumen di atas untuk
                                            preview</small>
                                    </div>
                                </div>
                                <div class="preview-actions">
                                    <a href="#" id="previewNewTabBtn" class="btn btn-sm btn-outline-primary me-2"
                                        target="_blank">
                                        <i class="bx bx-link-external me-1"></i> Buka Tab Baru
                                    </a>
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger"
                                        id="closePreviewBtn">
                                        <i class="bx bx-x"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="preview-body">
                                <div id="previewLoading" class="preview-loading">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 mb-0 text-muted">Memuat dokumen...</p>
                                </div>
                                <div id="previewError" class="preview-error" style="display: none;">
                                    <i class="bx bx-error-circle text-danger fs-1"></i>
                                    <p class="mt-2 mb-0">Gagal memuat dokumen</p>
                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                                        id="retryPreviewBtn">
                                        <i class="bx bx-refresh me-1"></i> Coba Lagi
                                    </button>
                                </div>
                                <iframe id="previewFrame" class="preview-frame" style="display: none;"></iframe>
                            </div>
                        </div>

                        {{-- Empty State when no preview selected --}}
                        <div id="noPreviewState" class="no-preview-state">
                            <div class="text-center py-4">
                                <i class="bx bx-file-find text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2 mb-0">Klik salah satu dokumen di atas untuk melihat preview</p>
                            </div>
                        </div>

                        {{-- Pendaftaran SK Pembimbing (Final) - Separate section --}}
                        @if ($pengajuan->file_surat_sk)
                            <div class="mt-3 pt-3 border-top">
                                <a href="{{ route('admin.sk-pembimbing.download-sk', $pengajuan) }}"
                                    class="btn btn-success w-100">
                                    <i class="bx bx-download me-1"></i> Download Surat Permohonan Penerbitan Pendaftaran SK Pembimbing Skripsi (Final)
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Card: Info Surat --}}
                @if ($pengajuan->nomor_surat)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-detail me-2"></i>Info Surat
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Nomor Surat</label>
                                    <p class="fw-medium mb-0">{{ $pengajuan->nomor_surat }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Tanggal Surat</label>
                                    <p class="fw-medium mb-0">{{ $pengajuan->tanggal_surat?->format('d F Y') ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>



    {{-- Reject Modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.sk-pembimbing.reject', $pengajuan) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tolak Pengajuan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="bx bx-error me-2"></i>Pengajuan yang ditolak tidak dapat diproses kembali.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="alasan_ditolak" class="form-control" rows="3" required
                                placeholder="Jelaskan alasan penolakan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Sign Kajur Modal --}}
    <div class="modal fade" id="signKajurModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.sk-pembimbing.sign-kajur', $pengajuan) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tanda Tangan Ketua Jurusan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-2"></i>
                            Dengan menandatangani, Anda menyetujui pendaftaran Pendaftaran SK Pembimbing ini sebagai Ketua Jurusan.
                            SK akan langsung diterbitkan setelah Anda menandatangani.
                        </div>
                        <p><strong>Mahasiswa:</strong> {{ $pengajuan->mahasiswa->name ?? '-' }}</p>
                        <p><strong>PS1:</strong> {{ $pengajuan->dosenPembimbing1->name ?? '-' }}</p>
                        <p class="mb-0"><strong>PS2:</strong> {{ $pengajuan->dosenPembimbing2->name ?? 'Tidak ada' }}
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success"><i class="bx bx-pen me-1"></i>Tanda Tangan &
                            Terbitkan SK</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Sign Korprodi Modal --}}
    <div class="modal fade" id="signKorprodiModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.sk-pembimbing.sign-korprodi', $pengajuan) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-pen me-2"></i>Tanda Tangan Koordinator Prodi
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-2"></i>
                            Dengan menandatangani, Anda menyetujui penentuan pembimbing skripsi untuk mahasiswa ini.
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">Mahasiswa</label>
                            <p class="fw-semibold mb-0">{{ $pengajuan->mahasiswa->name ?? '-' }}</p>
                            <small class="text-muted">{{ $pengajuan->mahasiswa->nim ?? '-' }}</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">Judul Skripsi</label>
                            <p class="mb-0">{{ Str::limit($pengajuan->judul_skripsi, 100) }}</p>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <label class="form-label text-muted">Pembimbing 1</label>
                                <p class="fw-semibold mb-0">{{ $pengajuan->dosenPembimbing1->name ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted">Pembimbing 2</label>
                                <p class="fw-semibold mb-0">{{ $pengajuan->dosenPembimbing2->name ?? 'Tidak ada' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-pen me-1"></i>Tanda Tangan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Sign Kajur Modal --}}
    <div class="modal fade" id="signKajurModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.sk-pembimbing.sign-kajur', $pengajuan) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-pen me-2"></i>Tanda Tangan Ketua Jurusan
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success">
                            <i class="bx bx-check-circle me-2"></i>
                            Koordinator Prodi sudah menandatangani. Dengan menandatangani, Pendaftaran SK Pembimbing akan diterbitkan.
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">Mahasiswa</label>
                            <p class="fw-semibold mb-0">{{ $pengajuan->mahasiswa->name ?? '-' }}</p>
                            <small class="text-muted">{{ $pengajuan->mahasiswa->nim ?? '-' }}</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">Judul Skripsi</label>
                            <p class="mb-0">{{ Str::limit($pengajuan->judul_skripsi, 100) }}</p>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <label class="form-label text-muted">Pembimbing 1</label>
                                <p class="fw-semibold mb-0">{{ $pengajuan->dosenPembimbing1->name ?? '-' }}</p>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted">Pembimbing 2</label>
                                <p class="fw-semibold mb-0">{{ $pengajuan->dosenPembimbing2->name ?? 'Tidak ada' }}</p>
                            </div>
                        </div>

                        @if ($pengajuan->ttdKorprodiUser)
                            <div class="mt-3 pt-3 border-top">
                                <small class="text-muted">
                                    <i class="bx bx-check me-1 text-success"></i>
                                    Ditandatangani Korprodi: {{ $pengajuan->ttdKorprodiUser->name }}
                                    pada {{ $pengajuan->ttd_korprodi_at->format('d M Y H:i') }}
                                </small>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bx bx-pen me-1"></i>Tanda Tangan & Terbitkan SK
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.sk-pembimbing.destroy', $pengajuan) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Pengajuan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="bx bx-error me-2"></i>
                            Pengajuan yang dihapus tidak dapat dikembalikan.
                        </div>
                        <p>Apakah Anda yakin ingin menghapus pengajuan SK Pembimbing untuk:</p>
                        <p class="fw-bold">{{ $pengajuan->mahasiswa->name ?? '-' }}
                            ({{ $pengajuan->mahasiswa->nim ?? '-' }})</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Timeline Styles */
        .timeline {
            position: relative;
            padding-left: 1.5rem;
            list-style: none;
            margin: 0;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.25rem;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-item-transparent {
            border-left: 2px solid #e9ecef;
            padding-left: 1.5rem;
            margin-left: -1.5rem;
        }

        .timeline-event {
            padding-bottom: 0;
        }

        /* Document Selector Styles */
        .document-selector {
            border-bottom: 1px solid #eee;
            padding-bottom: 1rem;
        }

        .document-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #fff;
            min-height: 70px;
        }

        .document-item.has-file:hover {
            border-color: #696cff;
            background: rgba(105, 108, 255, 0.04);
            transform: translateY(-1px);
        }

        .document-item.has-file.active {
            border-color: #696cff;
            background: rgba(105, 108, 255, 0.08);
            box-shadow: 0 2px 8px rgba(105, 108, 255, 0.15);
        }

        .document-item.no-file {
            cursor: default;
            opacity: 0.6;
            background: #f8f9fa;
        }

        .document-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(105, 108, 255, 0.1);
            border-radius: 0.5rem;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }

        .document-icon i {
            font-size: 1.25rem;
        }

        .document-info {
            flex: 1;
            min-width: 0;
        }

        .document-title {
            display: block;
            font-weight: 500;
            font-size: 0.8125rem;
            color: #566a7f;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .document-status {
            display: flex;
            align-items: center;
            font-size: 0.7rem;
            margin-top: 2px;
        }

        .document-status i {
            font-size: 0.75rem;
            margin-right: 2px;
        }

        .document-actions {
            margin-left: 0.5rem;
        }

        .document-actions .btn-preview {
            background: rgba(105, 108, 255, 0.1);
            border: none;
            color: #696cff;
            width: 28px;
            height: 28px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
        }

        .document-actions .btn-preview:hover {
            background: #696cff;
            color: #fff;
        }

        /* Document Preview Container */
        .document-preview-container {
            border: 1px solid #e9ecef;
            border-radius: 0.5rem;
            overflow: hidden;
            background: #fff;
            margin-top: 1rem;
        }

        .preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .preview-header h6 {
            font-size: 0.875rem;
        }

        .preview-actions {
            display: flex;
            align-items: center;
        }

        .preview-body {
            position: relative;
            height: 500px;
            background: #f0f0f0;
        }

        .preview-frame {
            width: 100%;
            height: 100%;
            border: none;
        }

        .preview-loading,
        .preview-error {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #fff;
        }

        .no-preview-state {
            background: #f8f9fa;
            border: 2px dashed #e9ecef;
            border-radius: 0.5rem;
            margin-top: 1rem;
        }

        .no-preview-state i {
            opacity: 0.5;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .document-item {
                padding: 0.5rem;
                min-height: 60px;
            }

            .document-icon {
                width: 32px;
                height: 32px;
                margin-right: 0.5rem;
            }

            .document-title {
                font-size: 0.75rem;
            }

            .preview-body {
                height: 350px;
            }

            .preview-header {
                flex-direction: column;
                gap: 0.5rem;
            }

            .preview-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const documentItems = document.querySelectorAll('.document-item.has-file');
            const previewContainer = document.getElementById('documentPreviewContainer');
            const noPreviewState = document.getElementById('noPreviewState');
            const previewFrame = document.getElementById('previewFrame');
            const previewLoading = document.getElementById('previewLoading');
            const previewError = document.getElementById('previewError');
            const previewTitle = document.getElementById('previewDocumentTitle');
            const previewInfo = document.getElementById('previewDocumentInfo');
            const previewNewTabBtn = document.getElementById('previewNewTabBtn');
            const closePreviewBtn = document.getElementById('closePreviewBtn');
            const retryPreviewBtn = document.getElementById('retryPreviewBtn');

            let currentDocumentUrl = '';

            function showPreview(url, name) {
                currentDocumentUrl = url;

                // Update UI
                noPreviewState.style.display = 'none';
                previewContainer.style.display = 'block';
                previewTitle.textContent = name;
                previewInfo.textContent = 'Memuat...';
                previewNewTabBtn.href = url;

                // Show loading
                previewLoading.style.display = 'flex';
                previewError.style.display = 'none';
                previewFrame.style.display = 'none';

                // Load document
                previewFrame.onload = function() {
                    previewLoading.style.display = 'none';
                    previewFrame.style.display = 'block';
                    previewInfo.textContent = 'PDF Document';
                };

                previewFrame.onerror = function() {
                    previewLoading.style.display = 'none';
                    previewError.style.display = 'flex';
                };

                previewFrame.src = url;
            }

            function closePreview() {
                previewContainer.style.display = 'none';
                noPreviewState.style.display = 'block';
                previewFrame.src = '';
                documentItems.forEach(item => item.classList.remove('active'));
            }

            // Document item click
            documentItems.forEach(item => {
                item.addEventListener('click', function() {
                    const url = this.dataset.documentUrl;
                    const name = this.dataset.documentName;

                    if (url) {
                        documentItems.forEach(i => i.classList.remove('active'));
                        this.classList.add('active');
                        showPreview(url, name);
                    }
                });
            });

            // Close preview
            if (closePreviewBtn) {
                closePreviewBtn.addEventListener('click', closePreview);
            }

            // Retry preview
            if (retryPreviewBtn) {
                retryPreviewBtn.addEventListener('click', function() {
                    if (currentDocumentUrl) {
                        const activeItem = document.querySelector('.document-item.active');
                        const name = activeItem ? activeItem.dataset.documentName : 'Dokumen';
                        showPreview(currentDocumentUrl, name);
                    }
                });
            }

            // Copy to clipboard function
            window.copyToClipboard = function(text) {
                navigator.clipboard.writeText(text).then(function() {
                    // Show toast or notification
                    alert('Kode verifikasi berhasil disalin!');
                }).catch(function(err) {
                    console.error('Could not copy text: ', err);
                });
            };
        });
    </script>
@endpush
