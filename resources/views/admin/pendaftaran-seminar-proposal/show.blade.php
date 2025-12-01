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
                                        <p class="mb-0 small">
                                            Oleh {{ $pendaftaran->penentuPembahas->name ?? 'System' }}
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
                                                <h6 class="mb-0">TTD Kaprodi</h6>
                                                <small
                                                    class="text-muted">{{ $pendaftaran->suratUsulan->ttd_kaprodi_at->format('d M Y, H:i') }}</small>
                                            </div>
                                            <p class="mb-0 small">
                                                Oleh {{ $pendaftaran->suratUsulan->kaprodi->name ?? 'N/A' }}
                                            </p>
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
                                            <p class="mb-0 small">
                                                Oleh {{ $pendaftaran->suratUsulan->kajur->name ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </li>
                                @endif
                            @endif

                            @if ($pendaftaran->status === 'selesai')
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-success"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0 text-success">
                                                <i class="bx bx-check-circle me-1"></i>Proses Selesai
                                            </h6>
                                        </div>
                                        <p class="mb-0 small">Surat siap digunakan</p>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                {{-- Card: Quick Actions --}}
                @can('manage pendaftaran sempro')
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bx bx-cog me-1"></i>Quick Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            @if ($pendaftaran->status === 'pending')
                                <a href="{{ route('admin.pendaftaran-seminar-proposal.assign-pembahas', $pendaftaran) }}"
                                    class="btn btn-primary w-100 mb-2">
                                    <i class="bx bx-user-check me-1"></i> Tentukan Pembahas
                                </a>
                            @elseif ($pendaftaran->status === 'pembahas_ditentukan' && !$pendaftaran->suratUsulan)
                                <button type="button" class="btn btn-success w-100 mb-2" data-bs-toggle="modal"
                                    data-bs-target="#generateSuratModal">
                                    <i class="bx bx-file-blank me-1"></i> Generate Surat
                                </button>
                            @endif

                            @if (
                                $pendaftaran->isPembahasDitentukan() &&
                                    auth()->user()->hasRole('staff') &&
                                    !$pendaftaran->isKaprodiSigned() &&
                                    !$pendaftaran->isKajurSigned())
                                <button type="button" class="btn btn-warning w-100 mb-2" data-bs-toggle="modal"
                                    data-bs-target="#resetPembahasModal">
                                    <i class="bx bx-reset me-1"></i> Reset Pembahas
                                </button>
                            @endif

                            @if ($pendaftaran->suratUsulan)
                                @php
                                    $user = auth()->user();
                                    $canSign =
                                        $user->hasRole('staff') ||
                                        ($user->hasRole('dosen') &&
                                            (str_contains(strtolower($user->jabatan ?? ''), 'koordinator') ||
                                                str_contains(strtolower($user->jabatan ?? ''), 'ketua') ||
                                                str_contains(strtolower($user->jabatan ?? ''), 'pimpinan')));
                                @endphp

                                @if ($canSign && $pendaftaran->suratUsulan->canBeSignedByKaprodi())
                                    <button type="button" class="btn btn-success w-100 mb-2" data-bs-toggle="modal"
                                        data-bs-target="#ttdKaprodiModal">
                                        <i class="bx bx-pen me-1"></i> TTD Kaprodi
                                    </button>
                                @elseif ($canSign && $pendaftaran->suratUsulan->canBeSignedByKajur())
                                    <button type="button" class="btn btn-primary w-100 mb-2" data-bs-toggle="modal"
                                        data-bs-target="#ttdKajurModal">
                                        <i class="bx bx-pen me-1"></i> TTD Kajur
                                    </button>
                                @endif

                                <a href="{{ route('admin.pendaftaran-seminar-proposal.download-surat', $pendaftaran) }}"
                                    class="btn btn-label-primary w-100 mb-2" target="_blank">
                                    <i class="bx bx-download me-1"></i> Download Surat
                                </a>
                            @endif

                            @if (auth()->user()->hasRole('staff'))
                                <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal">
                                    <i class="bx bx-trash me-1"></i> Hapus Pendaftaran
                                </button>
                            @endif
                        </div>
                    </div>
                @endcan
            </div>

            {{-- Right Column --}}
            <div class="col-xl-8 col-lg-7">
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

                {{-- Card: Dokumen Pendukung --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-file me-2"></i>Dokumen Pendukung
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            {{-- Transkrip Nilai --}}
                            <div class="col-md-6">
                                <div class="card border hover-shadow h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar avatar-sm me-3">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    <i class="bx bx-file"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Transkrip Nilai</h6>
                                                <small class="text-muted">PDF Document</small>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.pendaftaran-seminar-proposal.view.transkrip', $pendaftaran) }}"
                                                class="btn btn-sm btn-primary flex-fill" target="_blank">
                                                <i class="bx bx-show me-1"></i> Lihat
                                            </a>
                                            <a href="{{ route('admin.pendaftaran-seminar-proposal.download.transkrip', $pendaftaran) }}"
                                                class="btn btn-sm btn-outline-primary flex-fill" download>
                                                <i class="bx bx-download me-1"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Proposal Penelitian --}}
                            <div class="col-md-6">
                                <div class="card border hover-shadow h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar avatar-sm me-3">
                                                <span class="avatar-initial rounded-circle bg-label-info">
                                                    <i class="bx bx-file"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Proposal Penelitian</h6>
                                                <small class="text-muted">PDF Document</small>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.pendaftaran-seminar-proposal.view.proposal', $pendaftaran) }}"
                                                class="btn btn-sm btn-info flex-fill" target="_blank">
                                                <i class="bx bx-show me-1"></i> Lihat
                                            </a>
                                            <a href="{{ route('admin.pendaftaran-seminar-proposal.download.proposal', $pendaftaran) }}"
                                                class="btn btn-sm btn-outline-info flex-fill" download>
                                                <i class="bx bx-download me-1"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Surat Permohonan --}}
                            <div class="col-md-6">
                                <div class="card border hover-shadow h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar avatar-sm me-3">
                                                <span class="avatar-initial rounded-circle bg-label-success">
                                                    <i class="bx bx-file"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Surat Permohonan</h6>
                                                <small class="text-muted">PDF Document</small>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.pendaftaran-seminar-proposal.view.permohonan', $pendaftaran) }}"
                                                class="btn btn-sm btn-success flex-fill" target="_blank">
                                                <i class="bx bx-show me-1"></i> Lihat
                                            </a>
                                            <a href="{{ route('admin.pendaftaran-seminar-proposal.download.permohonan', $pendaftaran) }}"
                                                class="btn btn-sm btn-outline-success flex-fill" download>
                                                <i class="bx bx-download me-1"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Slip UKT --}}
                            <div class="col-md-6">
                                <div class="card border hover-shadow h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar avatar-sm me-3">
                                                <span class="avatar-initial rounded-circle bg-label-warning">
                                                    <i class="bx bx-file"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Slip UKT</h6>
                                                <small class="text-muted">
                                                    {{ strtoupper(pathinfo($pendaftaran->file_slip_ukt, PATHINFO_EXTENSION)) }}
                                                    Document
                                                </small>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.pendaftaran-seminar-proposal.view.slip-ukt', $pendaftaran) }}"
                                                class="btn btn-sm btn-warning flex-fill" target="_blank">
                                                <i class="bx bx-show me-1"></i> Lihat
                                            </a>
                                            <a href="{{ route('admin.pendaftaran-seminar-proposal.download.slip-ukt', $pendaftaran) }}"
                                                class="btn btn-sm btn-outline-warning flex-fill" download>
                                                <i class="bx bx-download me-1"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Card: Dosen Pembahas --}}
                @if ($pendaftaran->isPembahasDitentukan())
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-group me-2"></i>Dosen Pembahas
                            </h5>
                            @if ($pendaftaran->tanggal_penentuan_pembahas)
                                <small class="text-muted">
                                    <i class="bx bx-time me-1"></i>
                                    {{ $pendaftaran->tanggal_penentuan_pembahas->format('d M Y, H:i') }}
                                </small>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach ([1 => 'primary', 2 => 'info', 3 => 'success'] as $posisi => $color)
                                    @php
                                        $pembahas = $pendaftaran->{'getPembahas' . $posisi}();
                                    @endphp
                                    <div class="col-md-4">
                                        <div class="card border-{{ $color }} h-100">
                                            <div class="card-body text-center">
                                                <div class="avatar avatar-lg mx-auto mb-2">
                                                    <span
                                                        class="avatar-initial rounded-circle bg-label-{{ $color }}">
                                                        {{ $posisi }}
                                                    </span>
                                                </div>
                                                <h6 class="mb-1">Pembahas {{ $posisi }}</h6>
                                                @if ($pembahas)
                                                    <p class="mb-1 fw-semibold">{{ $pembahas->dosen->name }}</p>
                                                    <small class="text-muted">{{ $pembahas->dosen->nip ?? '-' }}</small>
                                                @else
                                                    <span class="badge bg-label-secondary">Belum Ditentukan</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if ($pendaftaran->penentuPembahas)
                                <div class="alert alert-info mt-3 mb-0">
                                    <div class="d-flex align-items-start">
                                        <i class="bx bx-info-circle fs-5 me-2"></i>
                                        <div class="flex-grow-1">
                                            <h6 class="alert-heading mb-1">Informasi Penentuan</h6>
                                            <p class="mb-0 small">
                                                Ditentukan oleh <strong>{{ $pendaftaran->penentuPembahas->name }}</strong>
                                                pada {{ $pendaftaran->tanggal_penentuan_pembahas->format('d F Y, H:i') }}
                                                WIB
                                            </p>
                                        </div>
                                    </div>
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
                                <i class="bx bx-envelope me-2"></i>Surat Usulan Pembahas
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted mb-1">Nomor Surat</label>
                                    <div class="p-2 bg-lighter rounded">
                                        <code class="text-dark">{{ $pendaftaran->suratUsulan->nomor_surat }}</code>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted mb-1">Tanggal Surat</label>
                                    <div class="p-2 bg-lighter rounded">
                                        <i class="bx bx-calendar me-1"></i>
                                        {{ $pendaftaran->suratUsulan->tanggal_surat->format('d F Y') }}
                                    </div>
                                </div>
                            </div>

                            {{-- Progress TTD --}}
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label text-muted mb-0">Progress Tanda Tangan</label>
                                    @php
                                        $progress = 0;
                                        if ($pendaftaran->suratUsulan->isKaprodiSigned()) {
                                            $progress += 50;
                                        }
                                        if ($pendaftaran->suratUsulan->isKajurSigned()) {
                                            $progress += 50;
                                        }
                                    @endphp
                                    <span class="badge bg-label-primary">{{ $progress }}%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-gradient-primary" role="progressbar"
                                        style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            {{-- TTD Status Cards --}}
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <div
                                        class="p-3 rounded {{ $pendaftaran->suratUsulan->isKaprodiSigned() ? 'bg-label-success' : 'bg-lighter' }}">
                                        <div class="d-flex align-items-center">
                                            <i
                                                class="bx {{ $pendaftaran->suratUsulan->isKaprodiSigned() ? 'bx-check-circle text-success' : 'bx-time text-secondary' }} fs-4 me-2"></i>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Kaprodi</h6>
                                                @if ($pendaftaran->suratUsulan->isKaprodiSigned())
                                                    <small class="text-success">
                                                        {{ $pendaftaran->suratUsulan->ttd_kaprodi_at->format('d/m/Y H:i') }}
                                                    </small>
                                                @else
                                                    <small class="text-muted">Menunggu tanda tangan</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div
                                        class="p-3 rounded {{ $pendaftaran->suratUsulan->isKajurSigned() ? 'bg-label-success' : 'bg-lighter' }}">
                                        <div class="d-flex align-items-center">
                                            <i
                                                class="bx {{ $pendaftaran->suratUsulan->isKajurSigned() ? 'bx-check-circle text-success' : 'bx-time text-secondary' }} fs-4 me-2"></i>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">Kajur</h6>
                                                @if ($pendaftaran->suratUsulan->isKajurSigned())
                                                    <small class="text-success">
                                                        {{ $pendaftaran->suratUsulan->ttd_kajur_at->format('d/m/Y H:i') }}
                                                    </small>
                                                @else
                                                    <small class="text-muted">
                                                        {{ $pendaftaran->suratUsulan->isKaprodiSigned() ? 'Menunggu tanda tangan' : 'Pending' }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Verification Code --}}
                            @if ($pendaftaran->suratUsulan->verification_code)
                                <div class="alert alert-primary mb-0">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="flex-grow-1">
                                            <h6 class="alert-heading mb-1">
                                                <i class="bx bx-qr me-1"></i>Kode Verifikasi
                                            </h6>
                                            <code
                                                class="text-primary fs-5">{{ $pendaftaran->suratUsulan->verification_code }}</code>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-primary"
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
                                <div class="text-center py-3">
                                    <div class="avatar avatar-xl mx-auto mb-3">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            <i class="bx bx-user-check fs-1"></i>
                                        </span>
                                    </div>
                                    <h5 class="mb-2">Langkah Selanjutnya</h5>
                                    <p class="text-muted mb-3">Tentukan 3 dosen pembahas untuk melanjutkan proses</p>
                                    @can('manage pendaftaran sempro')
                                        <a href="{{ route('admin.pendaftaran-seminar-proposal.assign-pembahas', $pendaftaran) }}"
                                            class="btn btn-primary">
                                            <i class="bx bx-user-check me-1"></i> Tentukan Pembahas
                                        </a>
                                    @endcan
                                </div>
                            @elseif ($pendaftaran->status === 'pembahas_ditentukan' && !$pendaftaran->suratUsulan)
                                <div class="text-center py-3">
                                    <div class="avatar avatar-xl mx-auto mb-3">
                                        <span class="avatar-initial rounded-circle bg-label-success">
                                            <i class="bx bx-file-blank fs-1"></i>
                                        </span>
                                    </div>
                                    <h5 class="mb-2">Siap Generate Surat</h5>
                                    <p class="text-muted mb-3">Pembahas sudah ditentukan, lanjutkan untuk generate surat
                                        usulan</p>
                                    @can('manage pendaftaran sempro')
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                            data-bs-target="#generateSuratModal">
                                            <i class="bx bx-file-blank me-1"></i> Generate Surat Usulan
                                        </button>
                                    @endcan
                                </div>
                            @elseif($pendaftaran->suratUsulan && !$pendaftaran->suratUsulan->isFullySigned())
                                <div class="text-center py-3">
                                    <div class="avatar avatar-xl mx-auto mb-3">
                                        <span class="avatar-initial rounded-circle bg-label-info">
                                            <i class="bx bx-pen fs-1"></i>
                                        </span>
                                    </div>
                                    <h5 class="mb-2">Menunggu Tanda Tangan</h5>
                                    <p class="text-muted mb-0">
                                        @if (!$pendaftaran->suratUsulan->isKaprodiSigned())
                                            Menunggu tanda tangan dari Koordinator Program Studi
                                        @else
                                            Menunggu tanda tangan dari Ketua Jurusan
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="card bg-success text-white">
                        <div class="card-body text-center py-4">
                            <div class="avatar avatar-xl mx-auto mb-3">
                                <span class="avatar-initial rounded-circle bg-white text-success">
                                    <i class="bx bx-check-circle fs-1"></i>
                                </span>
                            </div>
                            <h4 class="text-white mb-2">Proses Selesai!</h4>
                            <p class="text-white mb-0">
                                Surat usulan telah ditandatangani lengkap dan siap digunakan
                            </p>
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
@endsection

@push('scripts')
    <script>
        function copyToClipboard(text) {
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
        }

        // Auto dismiss alerts
        setTimeout(() => {
            $('.alert-dismissible').fadeOut('slow');
        }, 5000);
    </script>
@endpush

@push('styles')
    <style>
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
    </style>
@endpush
