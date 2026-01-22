@extends('layouts.admin.app')

@section('title', 'Detail Pendaftaran Ujian Hasil')

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
                    <a href="{{ route('admin.pendaftaran-ujian-hasil.index') }}">Pendaftaran Ujian Hasil</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Detail</li>
            </ol>
        </nav>

        {{-- Header Section --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div class="me-3">
                <h4 class="fw-bold mb-1">Detail Pendaftaran Ujian Hasil</h4>
                <p class="text-muted mb-0">
                    <i class="bx bx-calendar me-1"></i>
                    Diajukan pada {{ $pendaftaranUjianHasil->created_at->format('d F Y, H:i') }} WIB
                </p>
            </div>
            <div class="d-flex gap-2">
                {!! $pendaftaranUjianHasil->status_badge !!}
                <a href="{{ route('admin.pendaftaran-ujian-hasil.index') }}" class="btn btn-outline-secondary btn-sm">
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
                            <h5 class="mb-1">{{ $pendaftaranUjianHasil->user->name }}</h5>
                            <div class="mb-2">
                                <span class="badge bg-label-primary">{{ $pendaftaranUjianHasil->user->nim }}</span>
                            </div>
                            <p class="text-muted mb-0">
                                <i class="bx bx-envelope me-1"></i>{{ $pendaftaranUjianHasil->user->email }}
                            </p>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-around text-center">
                            <div>
                                <i class="bx bx-calendar-alt text-primary fs-4"></i>
                                <h6 class="mb-0 mt-1">{{ $pendaftaranUjianHasil->angkatan }}</h6>
                                <small class="text-muted">Angkatan</small>
                            </div>
                            <div class="border-start"></div>
                            <div>
                                <i class="bx bx-medal text-success fs-4"></i>
                                <h6 class="mb-0 mt-1">{{ number_format($pendaftaranUjianHasil->ipk, 2) }}</h6>
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
                                        <small class="text-muted">{{ $pendaftaranUjianHasil->created_at->format('d M Y, H:i') }}</small>
                                    </div>
                                    <p class="mb-0 small">Mahasiswa mengajukan pendaftaran ujian hasil</p>
                                </div>
                            </li>

                            @if ($pendaftaranUjianHasil->tanggal_penentuan_penguji)
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-primary"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0">Penguji Ditentukan</h6>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($pendaftaranUjianHasil->tanggal_penentuan_penguji)->format('d M Y, H:i') }}</small>
                                        </div>
                                        <p class="mb-0 small">Oleh {{ $pendaftaranUjianHasil->penentuPenguji->name ?? 'System' }}</p>
                                    </div>
                                </li>
                            @endif

                            @if ($pendaftaranUjianHasil->suratUsulanSkripsi)
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-info"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0">Surat Digenerate</h6>
                                            <small class="text-muted">{{ $pendaftaranUjianHasil->suratUsulanSkripsi->created_at->format('d M Y, H:i') }}</small>
                                        </div>
                                        <p class="mb-0 small">Nomor: {{ $pendaftaranUjianHasil->suratUsulanSkripsi->nomor_surat }}</p>
                                    </div>
                                </li>

                                @if ($pendaftaranUjianHasil->suratUsulanSkripsi->ttd_kaprodi_at)
                                    <li class="timeline-item timeline-item-transparent">
                                        <span class="timeline-point timeline-point-success"></span>
                                        <div class="timeline-event">
                                            <div class="timeline-header mb-1">
                                                <h6 class="mb-0">TTD Korprodi</h6>
                                                <small class="text-muted">{{ $pendaftaranUjianHasil->suratUsulanSkripsi->ttd_kaprodi_at->format('d M Y, H:i') }}</small>
                                            </div>
                                            <p class="mb-0 small">Oleh {{ $pendaftaranUjianHasil->suratUsulanSkripsi->ttdKaprodiBy->name ?? '-' }}</p>
                                        </div>
                                    </li>
                                @endif

                                @if ($pendaftaranUjianHasil->suratUsulanSkripsi->ttd_kajur_at)
                                    <li class="timeline-item timeline-item-transparent">
                                        <span class="timeline-point timeline-point-success"></span>
                                        <div class="timeline-event">
                                            <div class="timeline-header mb-1">
                                                <h6 class="mb-0">TTD Kajur</h6>
                                                <small class="text-muted">{{ $pendaftaranUjianHasil->suratUsulanSkripsi->ttd_kajur_at->format('d M Y, H:i') }}</small>
                                            </div>
                                            <p class="mb-0 small">Oleh {{ $pendaftaranUjianHasil->suratUsulanSkripsi->ttdKajurBy->name ?? '-' }}</p>
                                        </div>
                                    </li>
                                @endif
                            @endif

                            @if ($pendaftaranUjianHasil->status === 'selesai')
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

                {{-- Quick Actions --}}
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bx bx-cog me-1"></i>Quick Actions
                        </h6>
                    </div>
                    <div class="card-body">
                         {{-- TTD KAPRODI --}}
                        @if ($pendaftaranUjianHasil->suratUsulanSkripsi && $pendaftaranUjianHasil->suratUsulanSkripsi->canBeSignedByKaprodi(auth()->user()))
                            <form action="{{ route('admin.pendaftaran-ujian-hasil.ttd-kaprodi', $pendaftaranUjianHasil) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Yakin ingin menandatangani sebagai Korprodi?')">
                                    <i class="bx bx-pen me-1"></i> Tanda Tangan Korprodi
                                </button>
                            </form>
                        @endif

                        {{-- TTD KAJUR --}}
                        @if ($pendaftaranUjianHasil->suratUsulanSkripsi && $pendaftaranUjianHasil->suratUsulanSkripsi->canBeSignedByKajur(auth()->user()))
                            <form action="{{ route('admin.pendaftaran-ujian-hasil.ttd-kajur', $pendaftaranUjianHasil) }}" method="POST" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Yakin ingin menandatangani sebagai Kajur?')">
                                    <i class="bx bx-pen me-1"></i> Tanda Tangan Kajur
                                </button>
                            </form>
                        @endif

                        {{-- STAFF ONLY ACTIONS --}}
                        @if (auth()->user()->hasRole('staff'))
                            @if (!$pendaftaranUjianHasil->hasPengujiAssigned())
                                <a href="{{ route('admin.pendaftaran-ujian-hasil.assign-penguji', $pendaftaranUjianHasil) }}" class="btn btn-primary w-100 mb-2">
                                    <i class="bx bx-user-check me-1"></i> Tentukan Penguji
                                </a>
                            @elseif ($pendaftaranUjianHasil->hasPengujiAssigned() && !$pendaftaranUjianHasil->suratUsulanSkripsi)
                                <a href="{{ route('admin.pendaftaran-ujian-hasil.assign-penguji', $pendaftaranUjianHasil) }}" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="bx bx-edit me-1"></i> Ubah Penguji
                                </a>
                                <button type="button" class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#generateSuratModal">
                                    <i class="bx bx-file me-1"></i> Generate Surat
                                </button>
                            @endif
                            
                            @if ($pendaftaranUjianHasil->hasPengujiAssigned() && !$pendaftaranUjianHasil->suratUsulanSkripsi)
                                <form action="{{ route('admin.pendaftaran-ujian-hasil.reset-penguji', $pendaftaranUjianHasil) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin mereset penguji?')" >
                                    @csrf
                                    <button type="submit" class="btn btn-warning w-100 mb-2">
                                        <i class="bx bx-reset me-1"></i> Reset Penguji
                                    </button>
                                </form>
                            @endif

                            {{-- Tombol Reject --}}
                            @if ($pendaftaranUjianHasil->status === 'pending')
                                <button type="button" class="btn btn-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="bx bx-x-circle me-1"></i> Tolak Pendaftaran
                                </button>
                            @endif

                            {{-- Tombol Delete --}}
                            <form action="{{ route('admin.pendaftaran-ujian-hasil.destroy', $pendaftaranUjianHasil) }}" 
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus pendaftaran ujian hasil ini? Semua data termasuk file akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan!');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100 mb-2">
                                    <i class="bx bx-trash me-1"></i> Hapus Pendaftaran
                                </button>
                            </form>
                        @endif

                        {{-- Download Surat --}}
                        @if ($pendaftaranUjianHasil->suratUsulanSkripsi && $pendaftaranUjianHasil->suratUsulanSkripsi->isFullySigned())
                            <a href="{{ route('admin.pendaftaran-ujian-hasil.download-surat', $pendaftaranUjianHasil) }}" target="_blank" class="btn btn-outline-primary w-100 my-2">
                                <i class="bx bx-download me-1"></i> Download Surat
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="col-xl-8 col-lg-7">
                {{-- Card: Alasan Penolakan --}}
                @if ($pendaftaranUjianHasil->isDitolak() && $pendaftaranUjianHasil->alasan_penolakan)
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
                                        <p class="mb-0 text-muted small">Mahasiswa perlu mengajukan ulang pendaftaran.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">
                                    <i class="bx bx-message-alt-error me-1"></i>Alasan Penolakan
                                </label>
                                <div class="p-3 bg-lighter rounded border-start border-danger border-3">
                                    <p class="mb-0">{{ $pendaftaranUjianHasil->alasan_penolakan }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Card: Informasi Skripsi --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-book-open me-2"></i>Informasi Skripsi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-muted mb-1">
                                    <i class="bx bx-bookmark me-1"></i>Judul Skripsi
                                </label>
                                <div class="p-3 bg-lighter rounded">
                                    <p class="mb-0 fw-semibold">{!! $pendaftaranUjianHasil->judul_skripsi !!}</p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted mb-1">
                                    <i class="bx bx-user-pin me-1"></i>Pembimbing 1
                                </label>
                                <div class="d-flex align-items-center p-3 bg-lighter rounded">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ strtoupper(substr($pendaftaranUjianHasil->dosenPembimbing1->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $pendaftaranUjianHasil->dosenPembimbing1->name }}</h6>
                                        <small class="text-muted">{{ $pendaftaranUjianHasil->dosenPembimbing1->nip ?? '-' }}</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label text-muted mb-1">
                                    <i class="bx bx-user-pin me-1"></i>Pembimbing 2
                                </label>
                                <div class="d-flex align-items-center p-3 bg-lighter rounded">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-info">
                                            {{ strtoupper(substr($pendaftaranUjianHasil->dosenPembimbing2->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $pendaftaranUjianHasil->dosenPembimbing2->name }}</h6>
                                        <small class="text-muted">{{ $pendaftaranUjianHasil->dosenPembimbing2->nip ?? '-' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Dokumen Pendukung --}}
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
                                    <div class="document-item {{ $pendaftaranUjianHasil->file_transkrip_nilai ? 'has-file' : 'no-file' }}"
                                        data-document-type="transkrip"
                                        data-document-url="{{ $pendaftaranUjianHasil->file_transkrip_nilai ? route('admin.pendaftaran-ujian-hasil.view.transkrip', $pendaftaranUjianHasil) : '' }}"
                                        data-document-name="Transkrip Nilai">
                                        <div class="document-icon">
                                            <i class="bx bx-file-blank text-primary"></i>
                                        </div>
                                        <div class="document-info">
                                            <span class="document-title">Transkrip Nilai</span>
                                            @if ($pendaftaranUjianHasil->file_transkrip_nilai)
                                                <span class="document-status text-success"><i class="bx bx-check-circle"></i> Tersedia</span>
                                            @else
                                                <span class="document-status text-muted"><i class="bx bx-x-circle"></i> Tidak ada</span>
                                            @endif
                                        </div>
                                        @if ($pendaftaranUjianHasil->file_transkrip_nilai)
                                            <div class="document-actions">
                                                <button type="button" class="btn btn-sm btn-icon btn-preview" title="Preview"><i class="bx bx-show"></i></button>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- File Skripsi --}}
                                <div class="col-md-6 col-lg-3">
                                    <div class="document-item {{ $pendaftaranUjianHasil->file_skripsi ? 'has-file' : 'no-file' }}"
                                        data-document-type="skripsi"
                                        data-document-url="{{ $pendaftaranUjianHasil->file_skripsi ? route('admin.pendaftaran-ujian-hasil.view.skripsi', $pendaftaranUjianHasil) : '' }}"
                                        data-document-name="File Skripsi">
                                        <div class="document-icon">
                                            <i class="bx bx-book-content text-info"></i>
                                        </div>
                                        <div class="document-info">
                                            <span class="document-title">File Skripsi</span>
                                            @if ($pendaftaranUjianHasil->file_skripsi)
                                                <span class="document-status text-success"><i class="bx bx-check-circle"></i> Tersedia</span>
                                            @else
                                                <span class="document-status text-muted"><i class="bx bx-x-circle"></i> Tidak ada</span>
                                            @endif
                                        </div>
                                        @if ($pendaftaranUjianHasil->file_skripsi)
                                            <div class="document-actions">
                                                <button type="button" class="btn btn-sm btn-icon btn-preview" title="Preview"><i class="bx bx-show"></i></button>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Surat Komisi Hasil --}}
                                <div class="col-md-6 col-lg-3">
                                    <div class="document-item {{ $pendaftaranUjianHasil->file_komisi_hasil ? 'has-file' : 'no-file' }}"
                                        data-document-type="komisi_hasil"
                                        data-document-url="{{ $pendaftaranUjianHasil->file_komisi_hasil ? Storage::url($pendaftaranUjianHasil->file_komisi_hasil) : '' }}"
                                        data-document-name="Surat Komisi Hasil">
                                        <div class="document-icon">
                                            <i class="bx bx-envelope text-warning"></i>
                                        </div>
                                        <div class="document-info">
                                            <span class="document-title">Surat Komisi Hasil</span>
                                            @if ($pendaftaranUjianHasil->file_komisi_hasil)
                                                <span class="document-status text-success"><i class="bx bx-check-circle"></i> Tersedia</span>
                                            @else
                                                <span class="document-status text-muted"><i class="bx bx-x-circle"></i> Tidak ada</span>
                                            @endif
                                        </div>
                                        @if ($pendaftaranUjianHasil->file_komisi_hasil)
                                            <div class="document-actions">
                                                <button type="button" class="btn btn-sm btn-icon btn-preview" title="Preview"><i class="bx bx-show"></i></button>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Surat Permohonan --}}
                                <div class="col-md-6 col-lg-3">
                                    <div class="document-item {{ $pendaftaranUjianHasil->file_surat_permohonan ? 'has-file' : 'no-file' }}"
                                        data-document-type="permohonan"
                                        data-document-url="{{ $pendaftaranUjianHasil->file_surat_permohonan ? route('admin.pendaftaran-ujian-hasil.view.permohonan', $pendaftaranUjianHasil) : '' }}"
                                        data-document-name="Surat Permohonan">
                                        <div class="document-icon">
                                            <i class="bx bx-mail-send text-danger"></i>
                                        </div>
                                        <div class="document-info">
                                            <span class="document-title">Surat Permohonan</span>
                                            @if ($pendaftaranUjianHasil->file_surat_permohonan)
                                                <span class="document-status text-success"><i class="bx bx-check-circle"></i> Tersedia</span>
                                            @else
                                                <span class="document-status text-muted"><i class="bx bx-x-circle"></i> Tidak ada</span>
                                            @endif
                                        </div>
                                        @if ($pendaftaranUjianHasil->file_surat_permohonan)
                                            <div class="document-actions">
                                                <button type="button" class="btn btn-sm btn-icon btn-preview" title="Preview"><i class="bx bx-show"></i></button>
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
                                        <small class="text-muted" id="previewDocumentInfo">Klik dokumen di atas untuk preview</small>
                                    </div>
                                </div>
                                <div class="preview-actions">
                                    <a href="#" id="previewDownloadBtn" class="btn btn-sm btn-outline-primary me-2" target="_blank">
                                        <i class="bx bx-download me-1"></i> Download
                                    </a>
                                    <a href="#" id="previewNewTabBtn" class="btn btn-sm btn-outline-secondary me-2" target="_blank">
                                        <i class="bx bx-link-external me-1"></i> Buka Tab Baru
                                    </a>
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger" id="closePreviewBtn">
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
                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="retryPreviewBtn">
                                        <i class="bx bx-refresh me-1"></i> Coba Lagi
                                    </button>
                                </div>
                                <iframe id="previewFrame" class="preview-frame" style="display: none;"></iframe>
                            </div>
                        </div>
                        
                        {{-- Empty State --}}
                        <div id="noPreviewState" class="no-preview-state">
                            <div class="text-center py-4">
                                <i class="bx bx-file-find text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2 mb-0">Klik salah satu dokumen di atas untuk melihat preview</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Tim Penguji --}}
                @if ($pendaftaranUjianHasil->hasPengujiAssigned())
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-user-check me-2"></i>Tim Penguji
                            </h5>
                            @if ($pendaftaranUjianHasil->tanggal_penentuan_penguji)
                                <small class="text-muted">
                                    Ditentukan: {{ \Carbon\Carbon::parse($pendaftaranUjianHasil->tanggal_penentuan_penguji)->format('d M Y') }}
                                </small>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach ($pendaftaranUjianHasil->pengujiUjianHasil as $penguji)
                                    <div class="col-md-4">
                                        <div class="border rounded p-3 text-center h-100">
                                            <div class="avatar avatar-md mx-auto mb-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ $penguji->posisi }}
                                                </span>
                                            </div>
                                            <h6 class="mb-1">{{ $penguji->dosen->name ?? 'Belum ditentukan' }}</h6>
                                            <small class="text-muted">{{ $penguji->dosen->nip ?? '' }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                
                {{-- Card: Surat Usulan --}}
                @if ($pendaftaranUjianHasil->suratUsulanSkripsi)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-file-blank me-2"></i>Surat Usulan Skripsi
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Nomor Surat</label>
                                    <p class="fw-medium mb-0">{{ $pendaftaranUjianHasil->suratUsulanSkripsi->nomor_surat }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small mb-1">Tanggal Surat</label>
                                    <p class="fw-medium mb-0">{{ $pendaftaranUjianHasil->suratUsulanSkripsi->tanggal_surat->translatedFormat('d F Y') }}</p>
                                </div>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <div class="border rounded p-3 {{ $pendaftaranUjianHasil->suratUsulanSkripsi->ttd_kaprodi_at ? 'border-success bg-success bg-opacity-10' : '' }}">
                                         <div class="d-flex align-items-center">
                                            <i class="bx {{ $pendaftaranUjianHasil->suratUsulanSkripsi->ttd_kaprodi_at ? 'bx-check-circle text-success' : 'bx-time-five text-warning' }} fs-4 me-2"></i>
                                            <div>
                                                <h6 class="mb-0">TTD Korprodi</h6>
                                                @if ($pendaftaranUjianHasil->suratUsulanSkripsi->ttd_kaprodi_at)
                                                    <small class="text-success">{{ $pendaftaranUjianHasil->suratUsulanSkripsi->ttd_kaprodi_at->format('d M Y, H:i') }}</small>
                                                @else
                                                    <small class="text-muted">Menunggu</small>
                                                @endif
                                            </div>
                                         </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded p-3 {{ $pendaftaranUjianHasil->suratUsulanSkripsi->ttd_kajur_at ? 'border-success bg-success bg-opacity-10' : '' }}">
                                         <div class="d-flex align-items-center">
                                            <i class="bx {{ $pendaftaranUjianHasil->suratUsulanSkripsi->ttd_kajur_at ? 'bx-check-circle text-success' : 'bx-time-five text-warning' }} fs-4 me-2"></i>
                                            <div>
                                                <h6 class="mb-0">TTD Kajur</h6>
                                                 @if ($pendaftaranUjianHasil->suratUsulanSkripsi->ttd_kajur_at)
                                                    <small class="text-success">{{ $pendaftaranUjianHasil->suratUsulanSkripsi->ttd_kajur_at->format('d M Y, H:i') }}</small>
                                                @else
                                                    <small class="text-muted">Menunggu</small>
                                                @endif
                                            </div>
                                         </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Generate Surat Modal --}}
    @if (auth()->user()->hasRole('staff') && $pendaftaranUjianHasil->canGenerateSurat() && !$pendaftaranUjianHasil->suratUsulanSkripsi)
        <div class="modal fade" id="generateSuratModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bx bx-file me-2"></i>Generate Surat Usulan Skripsi
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.pendaftaran-ujian-hasil.generate-surat', $pendaftaranUjianHasil) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nomor Surat</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomor_surat_type" 
                                           id="nomor_auto" value="auto" checked>
                                    <label class="form-check-label" for="nomor_auto">
                                        Otomatis (Gunakan nomor urut berikutnya)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="nomor_surat_type" 
                                           id="nomor_custom" value="custom">
                                    <label class="form-check-label" for="nomor_custom">
                                        Custom (Tentukan nomor sendiri)
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3" id="customNomorWrapper" style="display: none;">
                                <label class="form-label" for="custom_nomor_surat">Nomor Surat (1-4 digit)</label>
                                <input type="text" class="form-control" id="custom_nomor_surat" 
                                       name="custom_nomor_surat" maxlength="4" pattern="[0-9]{1,4}"
                                       placeholder="Contoh: 123">
                                <small class="form-text text-muted">Masukkan hanya angka, 1-4 digit.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-check me-1"></i> Generate Surat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Reject Modal --}}
    @if (auth()->user()->hasRole('staff') && $pendaftaranUjianHasil->status === 'pending')
        <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="bx bx-x-circle me-2"></i>Tolak Pendaftaran
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.pendaftaran-ujian-hasil.reject', $pendaftaranUjianHasil) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <i class="bx bx-info-circle me-2"></i>
                                Mahasiswa akan menerima notifikasi penolakan.
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="alasan_penolakan">Alasan Penolakan <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="alasan_penolakan" name="alasan_penolakan" 
                                          rows="4" required placeholder="Masukkan alasan penolakan..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="bx bx-x me-1"></i> Tolak Pendaftaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
                previewDownloadBtn.href = url.replace('/view-', '/download-');
                // For direct storage URLs, the replace might not work as expected but they often support download attributes or just open
                // If the URL is a storage URL, we might need a different strategy for "download" button if we want to force download, 
                // but standard link is usually fine.
                if(url.includes('storage')) {
                     previewDownloadBtn.href = url;
                     previewDownloadBtn.setAttribute('download', '');
                } else {
                     previewDownloadBtn.removeAttribute('download');
                }

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

             // Toggle custom nomor surat input for Modal
            const nomorAuto = document.getElementById('nomor_auto');
            const nomorCustom = document.getElementById('nomor_custom');
            const customWrapper = document.getElementById('customNomorWrapper');
            const customInput = document.getElementById('custom_nomor_surat');

            if (nomorAuto && nomorCustom) {
                nomorAuto.addEventListener('change', function() {
                    if (this.checked) {
                        customWrapper.style.display = 'none';
                        customInput.removeAttribute('required');
                    }
                });

                nomorCustom.addEventListener('change', function() {
                    if (this.checked) {
                        customWrapper.style.display = 'block';
                        customInput.setAttribute('required', 'required');
                    }
                });
            }

            // Only allow numeric input for custom nomor surat
            if (customInput) {
                customInput.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }
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
        
        .avatar-xl {
            width: 5rem;
            height: 5rem;
        }

        .avatar-xl .avatar-initial {
            font-size: 2rem;
        }

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
        }
    </style>
@endpush
