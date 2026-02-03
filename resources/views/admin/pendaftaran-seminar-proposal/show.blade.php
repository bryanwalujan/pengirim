{{-- filepath: /c:/laragon/www/eservice-app/resources/views/admin/pendaftaran-seminar-proposal/show.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Detail Pendaftaran Seminar Proposal')

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
                    <a href="{{ route('admin.pendaftaran-seminar-proposal.index') }}">Pendaftaran Seminar Proposal</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Detail</li>
            </ol>
        </nav>

        {{-- Header Section --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div class="me-3">
                <h4 class="fw-bold mb-1">Detail Pendaftaran Seminar Proposal</h4>
                <p class="text-muted mb-0">
                    <i class="bx bx-calendar me-1"></i>
                    Diajukan pada {{ $pendaftaran->created_at->format('d F Y, H:i') }} WIB
                </p>
            </div>
            <div class="d-flex gap-2">
                {!! $pendaftaran->status_badge !!}
                <a href="{{ route('admin.pendaftaran-seminar-proposal.index') }}" class="btn btn-outline-secondary btn-sm">
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
                            <h5 class="mb-1">{{ $pendaftaran->user->name }}</h5>
                            <div class="mb-2">
                                <span class="badge bg-label-primary">{{ $pendaftaran->user->nim }}</span>
                            </div>
                            <p class="text-muted mb-0">
                                <i class="bx bx-envelope me-1"></i>{{ $pendaftaran->user->email }}
                            </p>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-around text-center">
                            <div>
                                <i class="bx bx-calendar-alt text-primary fs-4"></i>
                                <h6 class="mb-0 mt-1">{{ $pendaftaran->angkatan }}</h6>
                                <small class="text-muted">Angkatan</small>
                            </div>
                            <div class="border-start"></div>
                            <div>
                                <i class="bx bx-medal text-success fs-4"></i>
                                <h6 class="mb-0 mt-1">{{ number_format($pendaftaran->ipk, 2) }}</h6>
                                <small class="text-muted">IPK</small>
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
                                <span class="timeline-point timeline-point-success"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-1">
                                        <h6 class="mb-0">Pendaftaran Diajukan</h6>
                                        <small
                                            class="text-muted">{{ $pendaftaran->created_at->format('d M Y, H:i') }}</small>
                                    </div>
                                    <p class="mb-0 small">Mahasiswa mengajukan pendaftaran seminar proposal</p>
                                </div>
                            </li>

                            @if ($pendaftaran->tanggal_penentuan_pembahas)
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-primary"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0">Pembahas Ditentukan</h6>
                                            <small
                                                class="text-muted">{{ $pendaftaran->tanggal_penentuan_pembahas->format('d M Y, H:i') }}</small>
                                        </div>
                                        <p class="mb-0 small">Oleh {{ $pendaftaran->penentuPembahas->name ?? 'System' }}
                                        </p>
                                    </div>
                                </li>
                            @endif

                            @if ($pendaftaran->suratUsulan)
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-info"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0">Surat Digenerate</h6>
                                            <small
                                                class="text-muted">{{ $pendaftaran->suratUsulan->created_at->format('d M Y, H:i') }}</small>
                                        </div>
                                        <p class="mb-0 small">Nomor: {{ $pendaftaran->suratUsulan->nomor_surat }}</p>
                                    </div>
                                </li>

                                @if ($pendaftaran->suratUsulan->ttd_kaprodi_at)
                                    <li class="timeline-item timeline-item-transparent">
                                        <span class="timeline-point timeline-point-success"></span>
                                        <div class="timeline-event">
                                            <div class="timeline-header mb-1">
                                                <h6 class="mb-0">TTD Korprodi</h6>
                                                <small
                                                    class="text-muted">{{ $pendaftaran->suratUsulan->ttd_kaprodi_at->format('d M Y, H:i') }}</small>
                                            </div>
                                            <p class="mb-0 small">Oleh
                                                {{ $pendaftaran->suratUsulan->ttdKaprodiBy->name ?? '-' }}</p>
                                        </div>
                                    </li>
                                @endif

                                @if ($pendaftaran->suratUsulan->ttd_kajur_at)
                                    <li class="timeline-item timeline-item-transparent">
                                        <span class="timeline-point timeline-point-success"></span>
                                        <div class="timeline-event">
                                            <div class="timeline-header mb-1">
                                                <h6 class="mb-0">TTD Kajur</h6>
                                                <small
                                                    class="text-muted">{{ $pendaftaran->suratUsulan->ttd_kajur_at->format('d M Y, H:i') }}</small>
                                            </div>
                                            <p class="mb-0 small">Oleh
                                                {{ $pendaftaran->suratUsulan->ttdKajurBy->name ?? '-' }}</p>
                                        </div>
                                    </li>
                                @endif
                            @endif

                            @if ($pendaftaran->status === 'selesai')
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-success"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0">Proses Selesai</h6>
                                        </div>
                                        <p class="mb-0 small">Pendaftaran telah selesai diproses</p>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                {{-- Di bagian Quick Actions Card, ubah kondisi tombol hapus --}}
                @can('manage pendaftaran sempro')
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bx bx-cog me-1"></i>Quick Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            {{-- TTD KAPRODI - Untuk Korprodi atau Staff --}}
                            @if ($pendaftaran->status === 'menunggu_ttd_kaprodi')
                                @if (auth()->user()->isKoordinatorProdi() || auth()->user()->hasRole('staff'))
                                    <button type="button" class="btn btn-success w-100 mb-2" data-bs-toggle="modal"
                                        data-bs-target="#ttdKaprodiModal">
                                        <i class="bx bx-pen me-1"></i> Tanda Tangan Korprodi
                                    </button>
                                @else
                                    <div class="alert alert-info mb-2">
                                        <i class="bx bx-info-circle me-1"></i>
                                        <small>Menunggu tanda tangan dari Korprodi</small>
                                    </div>
                                @endif
                            @endif

                            {{-- TTD KAJUR - Untuk Kajur atau Staff --}}
                            @if ($pendaftaran->status === 'menunggu_ttd_kajur')
                                @if (auth()->user()->isKetuaJurusan() || auth()->user()->hasRole('staff'))
                                    <button type="button" class="btn btn-primary w-100 mb-2" data-bs-toggle="modal"
                                        data-bs-target="#ttdKajurModal">
                                        <i class="bx bx-pen me-1"></i> Tanda Tangan Kajur
                                    </button>
                                @else
                                    <div class="alert alert-info mb-2">
                                        <i class="bx bx-info-circle me-1"></i>
                                        <small>Menunggu tanda tangan dari Kajur</small>
                                    </div>
                                @endif
                            @endif

                            {{-- STAFF ONLY ACTIONS --}}
                            @if (auth()->user()->hasRole('staff'))
                                @if ($pendaftaran->status === 'pending')
                                    <a href="{{ route('admin.pendaftaran-seminar-proposal.assign-pembahas', $pendaftaran) }}"
                                        class="btn btn-primary w-100 mb-2">
                                        <i class="bx bx-user-check me-1"></i> Tentukan Pembahas
                                    </a>
                                @elseif ($pendaftaran->status === 'pembahas_ditentukan' && !$pendaftaran->suratUsulan)
                                    <button type="button" class="btn btn-success w-100 mb-2" data-bs-toggle="modal"
                                        data-bs-target="#generateSuratModal">
                                        <i class="bx bx-file me-1"></i> Generate Surat
                                    </button>
                                @endif

                                @if ($pendaftaran->isPembahasDitentukan() && !$pendaftaran->suratUsulan)
                                    <button type="button" class="btn btn-warning w-100 mb-2" data-bs-toggle="modal"
                                        data-bs-target="#resetPembahasModal">
                                        <i class="bx bx-reset me-1"></i> Reset Pembahas
                                    </button>
                                @endif

                                {{-- Tombol Reject - Hanya staff, status pending/pembahas_ditentukan --}}
                                @if (in_array($pendaftaran->status, ['pending', 'pembahas_ditentukan']))
                                    <button type="button" class="btn btn-danger w-100 mb-2" data-bs-toggle="modal"
                                        data-bs-target="#rejectModal">
                                        <i class="bx bx-x-circle me-1"></i> Tolak Pendaftaran
                                    </button>
                                @endif

                                {{-- ✅ TOMBOL HAPUS - SELALU MUNCUL UNTUK STAFF --}}
                                <hr class="my-4">
                                <div class="d-grid">
                                    <button type="button"
                                        class="btn {{ $pendaftaran->status === 'selesai' ? 'btn-danger' : 'btn-outline-danger' }}"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class="bx bx-trash me-1"></i>
                                        Hapus Pendaftaran
                                        @if ($pendaftaran->status === 'selesai')
                                            <span class="badge bg-white text-danger ms-1">!</span>
                                        @endif
                                    </button>
                                    @if ($pendaftaran->status === 'selesai')
                                        <small class="text-danger text-center mt-1">
                                            <i class="bx bx-error-circle"></i> Data sudah selesai diproses
                                        </small>
                                    @endif
                                </div>
                            @endif

                            {{-- Download Surat - Untuk semua yang punya akses --}}
                            @if ($pendaftaran->suratUsulan)
                                <a href="{{ route('admin.pendaftaran-seminar-proposal.download-surat', $pendaftaran) }}"
                                    target="_blank" class="btn btn-outline-primary w-100 my-2">
                                    <i class="bx bx-download me-1"></i> Download Surat
                                </a>
                            @endif

                            {{-- Status Dosen Button (hanya untuk staff) --}}
                            @if (auth()->user()->hasRole('staff'))
                                <button type="button" class="btn btn-info w-100 mb-2" data-bs-toggle="modal"
                                    data-bs-target="#modalDosenStatus">
                                    <i class="bx bx-list-ul me-1"></i> Status Beban Dosen
                                </button>
                            @endif
                        </div>
                    </div>
                @endcan
            </div>

            {{-- Right Column --}}
            <div class="col-xl-8 col-lg-7">
                {{-- Card: Alasan Penolakan (Tampil pertama jika ditolak) --}}
                @if ($pendaftaran->status === 'ditolak' && $pendaftaran->alasan_penolakan)
                    <div class="card mb-4 border-danger">
                        <div class="card-header bg-danger">
                            <h5 class="card-title mb-0 text-white">
                                <i class="bx bx-x-circle me-2"></i>Pendaftaran Ditolak
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-danger bg-danger bg-opacity-10 border-0 my-3">
                                <div class="d-flex">
                                    <i class="bx bx-error-circle fs-4 me-2 text-danger"></i>
                                    <div>
                                        <strong class="text-danger">Pendaftaran ini telah ditolak</strong>
                                        <p class="mb-0 text-muted small">Mahasiswa perlu mengajukan ulang pendaftaran
                                            dengan dokumen yang sesuai.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">
                                    <i class="bx bx-message-alt-error me-1"></i>Alasan Penolakan
                                </label>
                                <div class="p-3 bg-lighter rounded border-start border-danger border-3">
                                    <p class="mb-0">{{ $pendaftaran->alasan_penolakan }}</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-center text-muted small">
                                <i class="bx bx-time me-1"></i>
                                <span>Ditolak pada {{ $pendaftaran->updated_at->format('d F Y, H:i') }} WIB</span>
                            </div>
                        </div>
                    </div>
                @endif
                {{-- Card: Informasi Proposal --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-book-open me-2"></i>Informasi Proposal
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-muted mb-1">
                                    <i class="bx bx-bookmark me-1"></i>Judul Skripsi
                                </label>
                                <div class="p-3 bg-lighter rounded">
                                    <p class="mb-0 fw-semibold">{!! $pendaftaran->judul_skripsi !!}</p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted mb-1">
                                    <i class="bx bx-user-pin me-1"></i>Dosen Pembimbing
                                </label>
                                <div class="d-flex align-items-center p-3 bg-lighter rounded">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ strtoupper(substr($pendaftaran->dosenPembimbing->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $pendaftaran->dosenPembimbing->name }}</h6>
                                        <small class="text-muted">{{ $pendaftaran->dosenPembimbing->nip ?? '-' }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted mb-1">
                                    <i class="bx bx-group me-1"></i>Komisi Proposal
                                </label>
                                <div class="p-3 bg-lighter rounded">
                                    @if ($pendaftaran->komisiProposal)
                                        <span class="badge bg-label-success mb-1">
                                            <i class="bx bx-check-circle me-1"></i>Sudah Disetujui
                                        </span>
                                        <a href="{{ route('admin.komisi-proposal.show', $pendaftaran->komisiProposal) }}"
                                            class="btn btn-xs btn-primary rounded">
                                            <i class="bx bx-link-external me-1"></i>Lihat Detail Komisi
                                        </a>
                                    @else
                                        <span class="badge bg-label-warning">
                                            <i class="bx bx-time me-1"></i>Belum Ada
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                                {{-- Transkrip Nilai --}}
                                <div class="col-md-6 col-lg-3">
                                    <div class="document-item {{ $pendaftaran->file_transkrip_nilai ? 'has-file' : 'no-file' }}"
                                        data-document-type="transkrip"
                                        data-document-url="{{ $pendaftaran->file_transkrip_nilai ? route('admin.pendaftaran-seminar-proposal.view.transkrip', $pendaftaran) : '' }}"
                                        data-document-name="Transkrip Nilai">
                                        <div class="document-icon">
                                            <i class="bx bx-file-blank text-primary"></i>
                                        </div>
                                        <div class="document-info">
                                            <span class="document-title">Transkrip Nilai</span>
                                            @if ($pendaftaran->file_transkrip_nilai)
                                                <span class="document-status text-success">
                                                    <i class="bx bx-check-circle"></i> Tersedia
                                                </span>
                                            @else
                                                <span class="document-status text-muted">
                                                    <i class="bx bx-x-circle"></i> Tidak ada
                                                </span>
                                            @endif
                                        </div>
                                        @if ($pendaftaran->file_transkrip_nilai)
                                            <div class="document-actions">
                                                <button type="button" class="btn btn-sm btn-icon btn-preview"
                                                    title="Preview">
                                                    <i class="bx bx-show"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Proposal Penelitian --}}
                                <div class="col-md-6 col-lg-3">
                                    <div class="document-item {{ $pendaftaran->file_proposal_penelitian ? 'has-file' : 'no-file' }}"
                                        data-document-type="proposal"
                                        data-document-url="{{ $pendaftaran->file_proposal_penelitian ? route('admin.pendaftaran-seminar-proposal.view.proposal', $pendaftaran) : '' }}"
                                        data-document-name="Proposal Penelitian">
                                        <div class="document-icon">
                                            <i class="bx bx-book-content text-info"></i>
                                        </div>
                                        <div class="document-info">
                                            <span class="document-title">Proposal Penelitian</span>
                                            @if ($pendaftaran->file_proposal_penelitian)
                                                <span class="document-status text-success">
                                                    <i class="bx bx-check-circle"></i> Tersedia
                                                </span>
                                            @else
                                                <span class="document-status text-muted">
                                                    <i class="bx bx-x-circle"></i> Tidak ada
                                                </span>
                                            @endif
                                        </div>
                                        @if ($pendaftaran->file_proposal_penelitian)
                                            <div class="document-actions">
                                                <button type="button" class="btn btn-sm btn-icon btn-preview"
                                                    title="Preview">
                                                    <i class="bx bx-show"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Surat Permohonan --}}
                                <div class="col-md-6 col-lg-3">
                                    <div class="document-item {{ $pendaftaran->file_surat_permohonan ? 'has-file' : 'no-file' }}"
                                        data-document-type="permohonan"
                                        data-document-url="{{ $pendaftaran->file_surat_permohonan ? route('admin.pendaftaran-seminar-proposal.view.permohonan', $pendaftaran) : '' }}"
                                        data-document-name="Surat Permohonan">
                                        <div class="document-icon">
                                            <i class="bx bx-envelope text-warning"></i>
                                        </div>
                                        <div class="document-info">
                                            <span class="document-title">Surat Permohonan</span>
                                            @if ($pendaftaran->file_surat_permohonan)
                                                <span class="document-status text-success">
                                                    <i class="bx bx-check-circle"></i> Tersedia
                                                </span>
                                            @else
                                                <span class="document-status text-muted">
                                                    <i class="bx bx-x-circle"></i> Tidak ada
                                                </span>
                                            @endif
                                        </div>
                                        @if ($pendaftaran->file_surat_permohonan)
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
                                <div class="col-md-6 col-lg-3">
                                    <div class="document-item {{ $pendaftaran->file_slip_ukt ? 'has-file' : 'no-file' }}"
                                        data-document-type="slip_ukt"
                                        data-document-url="{{ $pendaftaran->file_slip_ukt ? route('admin.pendaftaran-seminar-proposal.view.slip-ukt', $pendaftaran) : '' }}"
                                        data-document-name="Slip UKT">
                                        <div class="document-icon">
                                            <i class="bx bx-receipt text-success"></i>
                                        </div>
                                        <div class="document-info">
                                            <span class="document-title">Slip UKT</span>
                                            @if ($pendaftaran->file_slip_ukt)
                                                <span class="document-status text-success">
                                                    <i class="bx bx-check-circle"></i> Tersedia
                                                </span>
                                            @else
                                                <span class="document-status text-muted">
                                                    <i class="bx bx-x-circle"></i> Tidak ada
                                                </span>
                                            @endif
                                        </div>
                                        @if ($pendaftaran->file_slip_ukt)
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
                                    <a href="#" id="previewDownloadBtn" class="btn btn-sm btn-outline-primary me-2"
                                        target="_blank">
                                        <i class="bx bx-download me-1"></i> Download
                                    </a>
                                    <a href="#" id="previewNewTabBtn" class="btn btn-sm btn-outline-secondary me-2"
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

                        {{-- Download All Documents --}}
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex flex-wrap gap-2">
                                @if ($pendaftaran->file_transkrip_nilai)
                                    <a href="{{ route('admin.pendaftaran-seminar-proposal.download.transkrip', $pendaftaran) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-download me-1"></i> Transkrip
                                    </a>
                                @endif
                                @if ($pendaftaran->file_proposal_penelitian)
                                    <a href="{{ route('admin.pendaftaran-seminar-proposal.download.proposal', $pendaftaran) }}"
                                        class="btn btn-sm btn-outline-info">
                                        <i class="bx bx-download me-1"></i> Proposal
                                    </a>
                                @endif
                                @if ($pendaftaran->file_surat_permohonan)
                                    <a href="{{ route('admin.pendaftaran-seminar-proposal.download.permohonan', $pendaftaran) }}"
                                        class="btn btn-sm btn-outline-warning">
                                        <i class="bx bx-download me-1"></i> Permohonan
                                    </a>
                                @endif
                                @if ($pendaftaran->file_slip_ukt)
                                    <a href="{{ route('admin.pendaftaran-seminar-proposal.download.slip-ukt', $pendaftaran) }}"
                                        class="btn btn-sm btn-outline-success">
                                        <i class="bx bx-download me-1"></i> Slip UKT
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Dosen Pembahas --}}
                @if ($pendaftaran->isPembahasDitentukan())
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-user-check me-2"></i>Dosen Pembahas
                            </h5>
                            @if ($pendaftaran->tanggal_penentuan_pembahas)
                                <small class="text-muted">
                                    Ditentukan: {{ $pendaftaran->tanggal_penentuan_pembahas->format('d M Y') }}
                                </small>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach ($pendaftaran->getPembahasWithDosen() as $pembahas)
                                    <div class="col-md-4">
                                        <div class="border rounded p-3 text-center">
                                            <div class="avatar avatar-md mx-auto mb-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ $pembahas->posisi }}
                                                </span>
                                            </div>
                                            <h6 class="mb-1">{{ $pembahas->dosen->name }}</h6>
                                            <small class="text-muted">Pembahas {{ $pembahas->posisi }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if ($pendaftaran->penentuPembahas)
                                <div class="mt-3 pt-3 border-top">
                                    <small class="text-muted">
                                        <i class="bx bx-user me-1"></i>
                                        Ditentukan oleh: {{ $pendaftaran->penentuPembahas->name }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Card: Surat Usulan --}}
                @if ($pendaftaran->suratUsulan)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-file-blank me-2"></i>Surat Usulan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Nomor Surat</label>
                                    <p class="fw-medium mb-0">{{ $pendaftaran->suratUsulan->nomor_surat }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Tanggal Surat</label>
                                    <p class="fw-medium mb-0">
                                        {{ $pendaftaran->suratUsulan->tanggal_surat->format('d F Y') }}</p>
                                </div>
                            </div>

                            {{-- Progress TTD --}}
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-2">Progress Tanda Tangan</label>
                                @php
                                    $progress = 0;
                                    if ($pendaftaran->suratUsulan->ttd_kaprodi_at) {
                                        $progress += 50;
                                    }
                                    if ($pendaftaran->suratUsulan->ttd_kajur_at) {
                                        $progress += 50;
                                    }
                                @endphp
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ $progress }}%"></div>
                                </div>
                                <small class="text-muted">{{ $progress }}% selesai</small>
                            </div>

                            {{-- TTD Status Cards --}}
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <div
                                        class="border rounded p-3 {{ $pendaftaran->suratUsulan->ttd_kaprodi_at ? 'border-success bg-success bg-opacity-10' : '' }}">
                                        <div class="d-flex align-items-center">
                                            @if ($pendaftaran->suratUsulan->ttd_kaprodi_at)
                                                <i class="bx bx-check-circle text-success fs-4 me-2"></i>
                                            @else
                                                <i class="bx bx-time-five text-warning fs-4 me-2"></i>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">TTD Korprodi</h6>
                                                @if ($pendaftaran->suratUsulan->ttd_kaprodi_at)
                                                    <small class="text-success">
                                                        {{ $pendaftaran->suratUsulan->ttd_kaprodi_at->format('d M Y, H:i') }}
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
                                        class="border rounded p-3 {{ $pendaftaran->suratUsulan->ttd_kajur_at ? 'border-success bg-success bg-opacity-10' : '' }}">
                                        <div class="d-flex align-items-center">
                                            @if ($pendaftaran->suratUsulan->ttd_kajur_at)
                                                <i class="bx bx-check-circle text-success fs-4 me-2"></i>
                                            @else
                                                <i class="bx bx-time-five text-warning fs-4 me-2"></i>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">TTD Kajur</h6>
                                                @if ($pendaftaran->suratUsulan->ttd_kajur_at)
                                                    <small class="text-success">
                                                        {{ $pendaftaran->suratUsulan->ttd_kajur_at->format('d M Y, H:i') }}
                                                    </small>
                                                @else
                                                    <small class="text-muted">Menunggu TTD Korprodi</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Verification Code --}}
                            @if ($pendaftaran->suratUsulan->verification_code)
                                <div class="bg-lighter rounded p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted d-block">Kode Verifikasi</small>
                                            <code class="fs-6">{{ $pendaftaran->suratUsulan->verification_code }}</code>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            onclick="copyToClipboard('{{ $pendaftaran->suratUsulan->verification_code }}')">
                                            <i class="bx bx-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Status Progress Card --}}
                @if ($pendaftaran->status !== 'selesai')
                    <div class="card border-primary">
                        <div class="card-body">
                            @if ($pendaftaran->status === 'pending')
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md me-3">
                                        <span class="avatar-initial rounded-circle bg-label-warning">
                                            <i class="bx bx-time-five"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Langkah Selanjutnya</h6>
                                        <p class="mb-0 text-muted">Tentukan dosen pembahas untuk melanjutkan proses</p>
                                    </div>
                                </div>
                            @elseif ($pendaftaran->status === 'pembahas_ditentukan')
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md me-3">
                                        <span class="avatar-initial rounded-circle bg-label-info">
                                            <i class="bx bx-file"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Langkah Selanjutnya</h6>
                                        <p class="mb-0 text-muted">Generate surat usulan untuk melanjutkan</p>
                                    </div>
                                </div>
                            @elseif ($pendaftaran->status === 'menunggu_ttd_kaprodi')
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md me-3">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            <i class="bx bx-pen"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Menunggu Tanda Tangan</h6>
                                        <p class="mb-0 text-muted">Surat menunggu tanda tangan dari Korprodi</p>
                                    </div>
                                </div>
                            @elseif ($pendaftaran->status === 'menunggu_ttd_kajur')
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md me-3">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            <i class="bx bx-pen"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Menunggu Tanda Tangan</h6>
                                        <p class="mb-0 text-muted">Surat menunggu tanda tangan dari Kajur</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="card bg-success text-white">
                        <div class="card-body text-center py-4">
                            <i class="bx bx-check-circle fs-1 mb-2"></i>
                            <h5 class="mb-1 text-white">Proses Selesai</h5>
                            <p class="mb-0 opacity-75">Pendaftaran seminar proposal telah selesai diproses</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @include('admin.pendaftaran-seminar-proposal.modals.generate-surat')
    @include('admin.pendaftaran-seminar-proposal.modals.ttd-kaprodi')
    @include('admin.pendaftaran-seminar-proposal.modals.ttd-kajur')
    @include('admin.pendaftaran-seminar-proposal.modals.reset-pembahas')
    @include('admin.pendaftaran-seminar-proposal.modals.delete')
    @if (in_array($pendaftaran->status, ['pending', 'pembahas_ditentukan']))
        @include('admin.pendaftaran-seminar-proposal.modals.reject')
    @endif
    {{-- Include Dosen Status Modal (hanya untuk staff) --}}
    @if (auth()->user()->hasRole('staff'))
        @include('admin.pendaftaran-seminar-proposal.modals.dosen-status-modal')
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Document Preview Variables
            const documentItems = document.querySelectorAll('.document-item.has-file');
            const previewContainer = document.getElementById('documentPreviewContainer');
            const noPreviewState = document.getElementById('noPreviewState');
            const previewFrame = document.getElementById('previewFrame');
            const previewLoading = document.getElementById('previewLoading');
            const previewError = document.getElementById('previewError');
            const previewTitle = document.getElementById('previewDocumentTitle');
            const previewInfo = document.getElementById('previewDocumentInfo');
            const previewDownloadBtn = document.getElementById('previewDownloadBtn');
            const previewNewTabBtn = document.getElementById('previewNewTabBtn');
            const closePreviewBtn = document.getElementById('closePreviewBtn');
            const retryPreviewBtn = document.getElementById('retryPreviewBtn');

            let currentDocumentUrl = '';
            let currentDocumentType = '';

            // Document Item Click Handler
            documentItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();

                    const url = this.dataset.documentUrl;
                    const name = this.dataset.documentName;
                    const type = this.dataset.documentType;

                    if (!url) return;

                    // Update active state
                    documentItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');

                    // Load preview
                    loadPreview(url, name, type);
                });
            });

            // Load Preview Function
            function loadPreview(url, name, type) {
                currentDocumentUrl = url;
                currentDocumentType = type;

                // Show container, hide no preview state
                noPreviewState.style.display = 'none';
                previewContainer.style.display = 'block';

                // Show loading
                previewLoading.style.display = 'flex';
                previewError.style.display = 'none';
                previewFrame.style.display = 'none';

                // Update header
                previewTitle.textContent = name;
                previewInfo.textContent = 'Memuat dokumen...';

                // Set download and new tab URLs
                const downloadUrl = url.replace('/view-', '/download-');
                previewDownloadBtn.href = downloadUrl;
                previewNewTabBtn.href = url;

                // Load iframe
                previewFrame.onload = function() {
                    previewLoading.style.display = 'none';
                    previewFrame.style.display = 'block';
                    previewInfo.textContent = 'Dokumen berhasil dimuat';
                };

                previewFrame.onerror = function() {
                    showPreviewError();
                };

                // Set timeout for loading
                setTimeout(() => {
                    if (previewLoading.style.display !== 'none') {
                        // Still loading after 10 seconds, might be an issue
                        // But let's not show error, just update info
                        previewInfo.textContent = 'Dokumen sedang dimuat, mohon tunggu...';
                    }
                }, 10000);

                previewFrame.src = url;
            }

            // Show Preview Error
            function showPreviewError() {
                previewLoading.style.display = 'none';
                previewFrame.style.display = 'none';
                previewError.style.display = 'flex';
                previewInfo.textContent = 'Gagal memuat dokumen';
            }

            // Close Preview
            closePreviewBtn.addEventListener('click', function() {
                previewContainer.style.display = 'none';
                noPreviewState.style.display = 'block';
                previewFrame.src = '';
                documentItems.forEach(i => i.classList.remove('active'));
            });

            // Retry Preview
            retryPreviewBtn.addEventListener('click', function() {
                if (currentDocumentUrl) {
                    const activeItem = document.querySelector('.document-item.active');
                    if (activeItem) {
                        loadPreview(currentDocumentUrl, activeItem.dataset.documentName,
                            currentDocumentType);
                    }
                }
            });

            // Copy to Clipboard Function
            window.copyToClipboard = function(text) {
                navigator.clipboard.writeText(text).then(function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersalin!',
                        text: 'Kode verifikasi berhasil disalin ke clipboard',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }, function(err) {
                    console.error('Could not copy text: ', err);
                });
            };

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

        /* Document Preview Container Styles */
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

        /* Additional Styles */
        .bg-lighter {
            background-color: rgba(67, 89, 113, 0.04);
        }

        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            box-shadow: 0 4px 12px 0 rgba(67, 89, 113, 0.16);
            transform: translateY(-2px);
        }

        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
            border-left: 2px solid #ddd;
        }

        .timeline-item:last-child {
            border-left: 2px solid transparent;
            padding-bottom: 0;
        }

        .timeline-point {
            position: absolute;
            left: -6px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #fff;
            border: 2px solid #ddd;
        }

        .timeline-point-primary {
            border-color: #696cff;
            background-color: #696cff;
        }

        .timeline-point-success {
            border-color: #71dd37;
            background-color: #71dd37;
        }

        .timeline-point-info {
            border-color: #03c3ec;
            background-color: #03c3ec;
        }

        .timeline-event {
            padding-left: 1.5rem;
        }

        .card {
            transition: all 0.3s ease;
        }

        .progress-bar {
            transition: width 0.6s ease;
        }

        .avatar-xl {
            width: 5rem;
            height: 5rem;
        }

        .avatar-xl .avatar-initial {
            font-size: 2rem;
        }

        .bg-gradient-primary {
            background: linear-gradient(45deg, #696cff, #9395ff);
        }

        /* Responsive adjustments */
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
