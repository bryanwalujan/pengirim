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
                        @if ($pendaftaranUjianHasil->suratUsulanSkripsi && 
                             $pendaftaranUjianHasil->suratUsulanSkripsi->status === 'menunggu_ttd_kaprodi' &&
                             (auth()->user()->isKoordinatorProdi() || auth()->user()->hasRole('staff')))
                            <button type="button" class="btn {{ auth()->user()->hasRole('staff') && !auth()->user()->isKoordinatorProdi() ? 'btn-warning' : 'btn-success' }} w-100 mb-2" 
                                    data-bs-toggle="modal" data-bs-target="#ttdKaprodiModal">
                                <i class="bx {{ auth()->user()->hasRole('staff') && !auth()->user()->isKoordinatorProdi() ? 'bx-shield-alt' : 'bx-pen' }} me-1"></i> 
                                @if(auth()->user()->hasRole('staff') && !auth()->user()->isKoordinatorProdi())
                                    Override TTD Korprodi
                                @else
                                    Tanda Tangan Korprodi
                                @endif
                            </button>
                        @endif

                        {{-- TTD KAJUR --}}
                        @if ($pendaftaranUjianHasil->suratUsulanSkripsi && 
                             $pendaftaranUjianHasil->suratUsulanSkripsi->status === 'menunggu_ttd_kajur' &&
                             (auth()->user()->isKetuaJurusan() || auth()->user()->hasRole('staff')))
                            <button type="button" class="btn {{ auth()->user()->hasRole('staff') && !auth()->user()->isKetuaJurusan() ? 'btn-warning' : 'btn-primary' }} w-100 mb-2" 
                                    data-bs-toggle="modal" data-bs-target="#ttdKajurModal">
                                <i class="bx {{ auth()->user()->hasRole('staff') && !auth()->user()->isKetuaJurusan() ? 'bx-shield-alt' : 'bx-pen' }} me-1"></i> 
                                @if(auth()->user()->hasRole('staff') && !auth()->user()->isKetuaJurusan())
                                    Override TTD Kajur
                                @else
                                    Tanda Tangan Kajur
                                @endif
                            </button>
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
                                <form action="{{ route('admin.pendaftaran-ujian-hasil.reset-penguji', $pendaftaranUjianHasil) }}" method="POST" class="d-inline form-confirm-reset-penguji">
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
                                  method="POST" class="d-inline form-confirm-delete">
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

                            @if($pendaftaranUjianHasil->nomor_sk_pembimbing)
                            <div class="col-12">
                                <label class="form-label text-muted mb-1">
                                    <i class="bx bx-hash me-1"></i>Nomor SK Pembimbing
                                </label>
                                <div class="p-3 bg-lighter rounded d-flex align-items-center">
                                    <span class="fw-semibold">{{ $pendaftaranUjianHasil->nomor_sk_pembimbing }}</span>
                                </div>
                            </div>
                            @endif

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

                                {{-- SK Pembimbing --}}
                                <div class="col-md-6 col-lg-3">
                                    <div class="document-item {{ $pendaftaranUjianHasil->file_sk_pembimbing ? 'has-file' : 'no-file' }}"
                                        data-document-type="sk_pembimbing"
                                        data-document-url="{{ $pendaftaranUjianHasil->file_sk_pembimbing ? route('admin.pendaftaran-ujian-hasil.view.sk-pembimbing', $pendaftaranUjianHasil) : '' }}"
                                        data-document-name="SK Pembimbing">
                                        <div class="document-icon">
                                            <i class="bx bx-certification text-secondary"></i>
                                        </div>
                                        <div class="document-info">
                                            <span class="document-title">SK Pembimbing</span>
                                            @if ($pendaftaranUjianHasil->file_sk_pembimbing)
                                                <span class="document-status text-success"><i class="bx bx-check-circle"></i> Tersedia</span>
                                            @else
                                                <span class="document-status text-muted"><i class="bx bx-x-circle"></i> Tidak ada</span>
                                            @endif
                                        </div>
                                        @if ($pendaftaranUjianHasil->file_sk_pembimbing)
                                            <div class="document-actions">
                                                <button type="button" class="btn btn-sm btn-icon btn-preview" title="Preview"><i class="bx bx-show"></i></button>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Slip UKT --}}
                                <div class="col-md-6 col-lg-3">
                                    <div class="document-item {{ $pendaftaranUjianHasil->file_slip_ukt ? 'has-file' : 'no-file' }}"
                                        data-document-type="slip_ukt"
                                        data-document-url="{{ $pendaftaranUjianHasil->file_slip_ukt ? route('admin.pendaftaran-ujian-hasil.view.slip-ukt', $pendaftaranUjianHasil) : '' }}"
                                        data-document-name="Slip UKT">
                                        <div class="document-icon">
                                            <i class="bx bx-credit-card text-success"></i>
                                        </div>
                                        <div class="document-info">
                                            <span class="document-title">Slip UKT</span>
                                            @if ($pendaftaranUjianHasil->file_slip_ukt)
                                                <span class="document-status text-success"><i class="bx bx-check-circle"></i> Tersedia</span>
                                            @else
                                                <span class="document-status text-muted"><i class="bx bx-x-circle"></i> Tidak ada</span>
                                            @endif
                                        </div>
                                        @if ($pendaftaranUjianHasil->file_slip_ukt)
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
                            <div class="d-flex align-items-center gap-2">
                                @if ($pendaftaranUjianHasil->tanggal_penentuan_penguji)
                                    <small class="text-muted">
                                        Ditentukan: {{ \Carbon\Carbon::parse($pendaftaranUjianHasil->tanggal_penentuan_penguji)->format('d M Y') }}
                                    </small>
                                @endif
                                {{-- Status Dosen Button --}}
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#modalDosenStatus" title="Lihat Status Beban Dosen">
                                    <i class="bx bx-list-ul me-1"></i> Status Dosen
                                </button>
                            </div>
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
        <div class="modal fade" id="generateSuratModal" tabindex="-1" aria-labelledby="generateSuratModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="generateSuratModalLabel">
                            <i class="bx bx-file me-2"></i>Generate Surat Usulan Skripsi
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.pendaftaran-ujian-hasil.generate-surat', $pendaftaranUjianHasil) }}" method="POST"
                        id="generateSuratForm">
                        @csrf
                        <div class="modal-body">
                            {{-- Info Section --}}
                            <div class="alert alert-info mb-4">
                                <div class="d-flex">
                                    <i class="bx bx-info-circle fs-4 me-2"></i>
                                    <div>
                                        <strong>Informasi</strong>
                                        <p class="mb-0 small">Surat usulan skripsi akan digenerate dengan nomor surat yang ditentukan di bawah ini.</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Nomor Surat Section --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Nomor Surat</label>

                                {{-- Radio Options --}}
                                <div class="mb-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="nomor_surat_type"
                                            id="nomorSuratAuto" value="auto" checked>
                                        <label class="form-check-label" for="nomorSuratAuto">
                                            <strong>Otomatis</strong>
                                            <span class="text-muted d-block small">Sistem akan generate nomor surat secara otomatis</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="nomor_surat_type"
                                            id="nomorSuratCustom" value="custom">
                                        <label class="form-check-label" for="nomorSuratCustom">
                                            <strong>Custom</strong>
                                            <span class="text-muted d-block small">Tentukan nomor surat sendiri</span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Auto Nomor Preview --}}
                                <div id="autoNomorPreview" class="p-3 bg-lighter rounded mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted d-block">Nomor Surat Berikutnya:</small>
                                            <code class="fs-6" id="nextNomorSurat">{{ $nomorSuratInfo['next_nomor'] ?? '-' }}</code>
                                        </div>
                                    </div>
                                    @if (isset($nomorSuratInfo['last_nomor']) && $nomorSuratInfo['last_nomor'])
                                        <div class="mt-2 pt-2 border-top">
                                            <small class="text-muted">
                                                <i class="bx bx-history me-1"></i>
                                                Terakhir: <code>{{ $nomorSuratInfo['last_nomor'] }}</code>
                                            </small>
                                        </div>
                                    @endif
                                </div>

                                {{-- Custom Nomor Input --}}
                                <div id="customNomorSection" style="display: none;">
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" id="customNomorSurat"
                                            name="custom_nomor_surat" placeholder="Masukkan nomor (1-4 digit)" maxlength="4"
                                            pattern="\d{1,4}">
                                        <span class="input-group-text">/{{ $nomorSuratInfo['prefix'] ?? 'UN41.2/TI' }}/{{ date('Y') }}</span>
                                        <button type="button" class="btn btn-outline-secondary" id="validateNomorBtn">
                                            <i class="bx bx-check"></i> Validasi
                                        </button>
                                    </div>
                                    <div id="customNomorFeedback" class="small"></div>
                                    <div id="customNomorPreview" class="mt-2 p-2 bg-lighter rounded" style="display: none;">
                                        <small class="text-muted">Preview: </small>
                                        <code id="customNomorPreviewText"></code>
                                    </div>
                                </div>
                            </div>

                            {{-- Summary --}}
                            <div class="border rounded p-3 bg-light">
                                <h6 class="mb-3"><i class="bx bx-list-check me-1"></i> Ringkasan</h6>
                                <div class="row g-2 small">
                                    <div class="col-5 text-muted">Mahasiswa:</div>
                                    <div class="col-7 fw-medium">{{ $pendaftaranUjianHasil->user->name }}</div>

                                    <div class="col-5 text-muted">NIM:</div>
                                    <div class="col-7 fw-medium">{{ $pendaftaranUjianHasil->user->nim }}</div>

                                    <div class="col-5 text-muted">Judul:</div>
                                    <div class="col-7 fw-medium text-truncate" title="{{ $pendaftaranUjianHasil->judul_skripsi }}">
                                        {{ Str::limit(strip_tags($pendaftaranUjianHasil->judul_skripsi), 50) }}
                                    </div>

                                    <div class="col-5 text-muted">Penguji:</div>
                                    <div class="col-7 fw-medium">
                                        {{ $pendaftaranUjianHasil->pengujiUjianHasil->count() }} Penguji
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="bx bx-x me-1"></i> Batal
                            </button>
                            <button type="submit" class="btn btn-success" id="generateSuratBtn">
                                <i class="bx bx-file me-1"></i> Generate Surat
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

    {{-- Include Dosen Status Modal --}}
    @include('admin.pendaftaran-ujian-hasil.dosen-status-modal')

    {{-- Include TTD Modals --}}
    @include('admin.pendaftaran-ujian-hasil.modals.ttd-kaprodi')
    @include('admin.pendaftaran-ujian-hasil.modals.ttd-kajur')

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
                previewDownloadBtn.href = url.replace('/view/', '/download/');
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


            // Generate Surat Modal Logic
            const generateSuratModal = document.getElementById('generateSuratModal');

            if (generateSuratModal) {
                // Initialize when modal is shown
                generateSuratModal.addEventListener('shown.bs.modal', function() {
                    initializeModal();
                });

                function initializeModal() {
                    const nomorSuratAuto = document.getElementById('nomorSuratAuto');
                    const nomorSuratCustom = document.getElementById('nomorSuratCustom');
                    const autoNomorPreview = document.getElementById('autoNomorPreview');
                    const customNomorSection = document.getElementById('customNomorSection');
                    const customNomorInput = document.getElementById('customNomorSurat');
                    const validateNomorBtn = document.getElementById('validateNomorBtn');
                    const customNomorFeedback = document.getElementById('customNomorFeedback');
                    const customNomorPreview = document.getElementById('customNomorPreview');
                    const customNomorPreviewText = document.getElementById('customNomorPreviewText');
                    const generateSuratBtn = document.getElementById('generateSuratBtn');
                    const generateSuratForm = document.getElementById('generateSuratForm');

                    let isCustomValid = false;

                    // Toggle between auto and custom
                    function toggleNomorType() {
                        if (nomorSuratCustom.checked) {
                            autoNomorPreview.style.display = 'none';
                            customNomorSection.style.display = 'block';
                            customNomorInput.required = true;
                        } else {
                            autoNomorPreview.style.display = 'block';
                            customNomorSection.style.display = 'none';
                            customNomorInput.required = false;
                            isCustomValid = false;
                            resetCustomValidation();
                        }
                    }

                    nomorSuratAuto.addEventListener('change', toggleNomorType);
                    nomorSuratCustom.addEventListener('change', toggleNomorType);

                    // Validate custom nomor
                    function validateCustomNomor() {
                        const customNumber = customNomorInput.value.trim();

                        if (!customNumber) {
                            showFeedback('warning', 'Masukkan nomor surat terlebih dahulu.');
                            isCustomValid = false;
                            return;
                        }

                        if (!/^\d{1,4}$/.test(customNumber)) {
                            showFeedback('danger', 'Format tidak valid. Masukkan 1-4 digit angka.');
                            isCustomValid = false;
                            return;
                        }

                        validateNomorBtn.disabled = true;
                        validateNomorBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                        fetch('{{ route('admin.pendaftaran-ujian-hasil.validate-nomor-surat') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({
                                    nomor: customNumber
                                }),
                                credentials: 'same-origin'
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.valid) {
                                    showFeedback('success', data.message);
                                    customNomorPreview.style.display = 'block';
                                    customNomorPreviewText.textContent = data.nomor_surat;
                                    isCustomValid = true;
                                } else {
                                    showFeedback('danger', data.message);
                                    customNomorPreview.style.display = 'none';
                                    isCustomValid = false;
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                showFeedback('danger', 'Terjadi kesalahan saat validasi.');
                                isCustomValid = false;
                            })
                            .finally(() => {
                                validateNomorBtn.disabled = false;
                                validateNomorBtn.innerHTML = '<i class="bx bx-check"></i> Validasi';
                            });
                    }

                    validateNomorBtn.addEventListener('click', validateCustomNomor);

                    customNomorInput.addEventListener('input', function() {
                        isCustomValid = false;
                        customNomorPreview.style.display = 'none';
                        resetCustomValidation();
                    });

                    customNomorInput.addEventListener('keypress', function(e) {
                        if (!/\d/.test(e.key)) {
                            e.preventDefault();
                        }
                    });

                    function showFeedback(type, message) {
                        const colors = {
                            success: 'text-success',
                            danger: 'text-danger',
                            warning: 'text-warning'
                        };
                        const icons = {
                            success: 'bx-check-circle',
                            danger: 'bx-error-circle',
                            warning: 'bx-info-circle'
                        };
                        customNomorFeedback.className = `small ${colors[type]}`;
                        customNomorFeedback.innerHTML = `<i class="bx ${icons[type]} me-1"></i>${message}`;
                    }

                    function resetCustomValidation() {
                        customNomorFeedback.innerHTML = '';
                        customNomorPreview.style.display = 'none';
                    }

                    // Form validation
                    generateSuratForm.addEventListener('submit', function(e) {
                        if (nomorSuratCustom.checked && !isCustomValid) {
                            e.preventDefault();
                            Swal.fire({
                                icon: 'warning',
                                title: 'Validasi Diperlukan',
                                text: 'Silakan validasi nomor surat custom terlebih dahulu.',
                                confirmButtonText: 'OK'
                            });
                            return false;
                        }
                        generateSuratBtn.disabled = true;
                        generateSuratBtn.innerHTML =
                            '<span class="spinner-border spinner-border-sm me-1"></span> Generating...';
                    });
                }

                // Reset modal on close
                generateSuratModal.addEventListener('hidden.bs.modal', function() {
                    const nomorSuratAuto = document.getElementById('nomorSuratAuto');
                    const generateSuratBtn = document.getElementById('generateSuratBtn');

                    if (nomorSuratAuto) nomorSuratAuto.checked = true;
                    if (generateSuratBtn) {
                        generateSuratBtn.disabled = false;
                        generateSuratBtn.innerHTML = '<i class="bx bx-file me-1"></i> Generate Surat';
                    }
                });
            }

            // ========== SWEETALERT2 CONFIRMATIONS ==========

            // TTD Korprodi Confirmation
            const formTtdKaprodi = document.querySelector('.form-confirm-ttd-kaprodi');
            if (formTtdKaprodi) {
                formTtdKaprodi.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    Swal.fire({
                        title: 'Konfirmasi Tanda Tangan',
                        text: 'Apakah Anda yakin ingin menandatangani surat ini sebagai Korprodi?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#71dd37',
                        cancelButtonColor: '#8592a3',
                        confirmButtonText: '<i class="bx bx-pen me-1"></i> Ya, Tanda Tangan',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'btn btn-success',
                            cancelButton: 'btn btn-outline-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading
                            Swal.fire({
                                title: 'Memproses...',
                                text: 'Sedang menandatangani surat',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            formTtdKaprodi.submit();
                        }
                    });
                });
            }

            // TTD Kajur Confirmation
            const formTtdKajur = document.querySelector('.form-confirm-ttd-kajur');
            if (formTtdKajur) {
                formTtdKajur.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    Swal.fire({
                        title: 'Konfirmasi Tanda Tangan',
                        text: 'Apakah Anda yakin ingin menandatangani surat ini sebagai Kajur?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#696cff',
                        cancelButtonColor: '#8592a3',
                        confirmButtonText: '<i class="bx bx-pen me-1"></i> Ya, Tanda Tangan',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-outline-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading
                            Swal.fire({
                                title: 'Memproses...',
                                text: 'Sedang menandatangani surat',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            formTtdKajur.submit();
                        }
                    });
                });
            }

            // Reset Penguji Confirmation
            const formResetPenguji = document.querySelector('.form-confirm-reset-penguji');
            if (formResetPenguji) {
                formResetPenguji.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    Swal.fire({
                        title: 'Reset Penguji?',
                        html: '<p class="mb-2">Apakah Anda yakin ingin mereset penguji yang telah ditentukan?</p><p class="text-muted small mb-0">Semua penguji yang telah ditentukan akan dihapus dan Anda perlu menentukan ulang.</p>',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ffab00',
                        cancelButtonColor: '#8592a3',
                        confirmButtonText: '<i class="bx bx-reset me-1"></i> Ya, Reset',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'btn btn-warning',
                            cancelButton: 'btn btn-outline-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading
                            Swal.fire({
                                title: 'Memproses...',
                                text: 'Sedang mereset penguji',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            formResetPenguji.submit();
                        }
                    });
                });
            }

            // Delete Confirmation
            const formDelete = document.querySelector('.form-confirm-delete');
            if (formDelete) {
                formDelete.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    Swal.fire({
                        title: 'Hapus Pendaftaran?',
                        html: '<p class="mb-2">Apakah Anda yakin ingin menghapus pendaftaran ujian hasil ini?</p><p class="text-danger small mb-0"><i class="bx bx-error-circle me-1"></i>Semua data termasuk file akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan!</p>',
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonColor: '#ff3e1d',
                        cancelButtonColor: '#8592a3',
                        confirmButtonText: '<i class="bx bx-trash me-1"></i> Ya, Hapus',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'btn btn-danger',
                            cancelButton: 'btn btn-outline-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading
                            Swal.fire({
                                title: 'Menghapus...',
                                text: 'Sedang menghapus pendaftaran',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            formDelete.submit();
                        }
                    });
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
