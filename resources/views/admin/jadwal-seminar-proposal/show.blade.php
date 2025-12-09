{{-- filepath: resources/views/admin/jadwal-seminar-proposal/show.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Detail Jadwal Seminar Proposal')

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
                    <a href="{{ route('admin.jadwal-seminar-proposal.index') }}">Jadwal Sempro</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Detail</li>
            </ol>
        </nav>

        {{-- Header Section --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div class="me-3">
                <h4 class="fw-bold mb-1">
                    <i class="bx bx-calendar-event me-2"></i>Detail Jadwal Seminar Proposal
                </h4>
                <p class="text-muted mb-0">
                    <i class="bx bx-user me-1"></i>
                    {{ $jadwal->pendaftaranSeminarProposal->user->name }}
                    ({{ $jadwal->pendaftaranSeminarProposal->user->nim }})
                </p>
            </div>
            <div class="d-flex gap-2">
                {!! $jadwal->status_badge !!}
                <a href="{{ route('admin.jadwal-seminar-proposal.index') }}" class="btn btn-outline-secondary btn-sm">
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
                                    {{ strtoupper(substr($jadwal->pendaftaranSeminarProposal->user->name, 0, 2)) }}
                                </span>
                            </div>
                            <h5 class="mb-1">{{ $jadwal->pendaftaranSeminarProposal->user->name }}</h5>
                            <div class="mb-2">
                                <span
                                    class="badge bg-label-primary">{{ $jadwal->pendaftaranSeminarProposal->user->nim }}</span>
                            </div>
                            <p class="text-muted mb-0">
                                <i class="bx bx-envelope me-1"></i>{{ $jadwal->pendaftaranSeminarProposal->user->email }}
                            </p>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-around text-center">
                            <div>
                                <i class="bx bx-calendar-alt text-primary fs-4"></i>
                                <h6 class="mb-0 mt-1">{{ $jadwal->pendaftaranSeminarProposal->angkatan }}</h6>
                                <small class="text-muted">Angkatan</small>
                            </div>
                            <div class="border-start"></div>
                            <div>
                                <i class="bx bx-medal text-success fs-4"></i>
                                <h6 class="mb-0 mt-1">{{ number_format($jadwal->pendaftaranSeminarProposal->ipk, 2) }}</h6>
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
                            {{-- SK Uploaded --}}
                            @if ($jadwal->hasSkFile())
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-success"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0">SK Proposal Diupload</h6>
                                            <small
                                                class="text-muted">{{ $jadwal->updated_at->format('d M Y, H:i') }}</small>
                                        </div>
                                        <p class="mb-0 small">Mahasiswa telah mengupload SK Proposal</p>
                                    </div>
                                </li>
                            @endif

                            {{-- Jadwal Dibuat --}}
                            @if ($jadwal->hasJadwal())
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-primary"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0">Jadwal Ditentukan</h6>
                                            <small
                                                class="text-muted">{{ $jadwal->updated_at->format('d M Y, H:i') }}</small>
                                        </div>
                                        <p class="mb-0 small">Jadwal seminar telah dibuat oleh admin</p>
                                    </div>
                                </li>
                            @endif

                            {{-- Undangan Terkirim --}}
                            @if ($jadwal->isDijadwalkan())
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-info"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0">Undangan Terkirim</h6>
                                            <small
                                                class="text-muted">{{ $jadwal->updated_at->format('d M Y, H:i') }}</small>
                                        </div>
                                        <p class="mb-0 small">Undangan telah dikirim ke dosen pembimbing dan pembahas</p>
                                    </div>
                                </li>
                            @endif

                            {{-- Selesai --}}
                            @if ($jadwal->isSelesai())
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-success"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0">Seminar Selesai</h6>
                                            <small
                                                class="text-muted">{{ $jadwal->updated_at->format('d M Y, H:i') }}</small>
                                        </div>
                                        <p class="mb-0 small">Seminar proposal telah dilaksanakan</p>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                {{-- Card: Quick Actions --}}
                @can('manage jadwal sempro')
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bx bx-cog me-1"></i>Quick Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            {{-- Buat Jadwal --}}
                            @if ($jadwal->status === 'menunggu_jadwal')
                                <button type="button" class="btn btn-primary w-100 mb-2" data-bs-toggle="modal"
                                    data-bs-target="#scheduleModal" data-jadwal-id="{{ $jadwal->id }}"
                                    data-mahasiswa-nama="{{ $jadwal->pendaftaranSeminarProposal->user->name }}"
                                    data-mahasiswa-nim="{{ $jadwal->pendaftaranSeminarProposal->user->nim }}"
                                    data-mahasiswa-judul="{{ strip_tags($jadwal->pendaftaranSeminarProposal->judul_skripsi) }}">
                                    <i class="bx bx-calendar-plus me-1"></i> Buat Jadwal
                                </button>
                            @endif

                            {{-- Edit Jadwal --}}
                            @if ($jadwal->status === 'dijadwalkan')
                                <button type="button" class="btn btn-warning w-100 mb-2" data-bs-toggle="modal"
                                    data-bs-target="#scheduleModal" data-jadwal-id="{{ $jadwal->id }}"
                                    data-mahasiswa-nama="{{ $jadwal->pendaftaranSeminarProposal->user->name }}"
                                    data-mahasiswa-nim="{{ $jadwal->pendaftaranSeminarProposal->user->nim }}"
                                    data-mahasiswa-judul="{{ strip_tags($jadwal->pendaftaranSeminarProposal->judul_skripsi) }}"
                                    data-tanggal="{{ $jadwal->tanggal->format('Y-m-d') }}"
                                    data-jam-mulai="{{ $jadwal->jam_mulai }}" data-jam-selesai="{{ $jadwal->jam_selesai }}"
                                    data-ruangan="{{ $jadwal->ruangan }}">
                                    <i class="bx bx-edit me-1"></i> Edit Jadwal
                                </button>

                                <form action="{{ route('admin.jadwal-seminar-proposal.mark-selesai', $jadwal) }}"
                                    method="POST" class="mark-selesai-form">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100 mb-2">
                                        <i class="bx bx-check-circle me-1"></i> Tandai Selesai
                                    </button>
                                </form>

                                <form action="{{ route('admin.jadwal-seminar-proposal.kirim-ulang-undangan', $jadwal) }}"
                                    method="POST" class="kirim-ulang-form">
                                    @csrf
                                    <button type="submit" class="btn btn-info w-100 mb-2">
                                        <i class="bx bx-send me-1"></i> Kirim Ulang Undangan
                                    </button>
                                </form>
                            @endif

                            {{-- SK Actions --}}
                            @if ($jadwal->hasSkFile())
                                <a href="{{ route('admin.jadwal-seminar-proposal.view-sk', $jadwal) }}" target="_blank"
                                    class="btn btn-outline-primary w-100 mb-2">
                                    <i class="bx bx-show me-1"></i> Lihat SK
                                </a>
                                <a href="{{ route('admin.jadwal-seminar-proposal.download-sk', $jadwal) }}"
                                    class="btn btn-outline-success w-100 mb-2">
                                    <i class="bx bx-download me-1"></i> Download SK
                                </a>
                            @endif

                            {{-- Delete Button --}}
                            @if ($jadwal->canBeDeleted())
                                <hr class="my-3">
                                <div class="d-grid">
                                    <button type="button"
                                        class="btn {{ $jadwal->status === 'dijadwalkan' ? 'btn-danger' : 'btn-outline-danger' }}"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class="bx bx-trash me-1"></i>
                                        Hapus Jadwal
                                        @if ($jadwal->status === 'dijadwalkan')
                                            <span class="badge bg-white text-danger ms-1">!</span>
                                        @endif
                                    </button>
                                    @if ($jadwal->status === 'dijadwalkan')
                                        <small class="text-danger text-center mt-1">
                                            <i class="bx bx-error-circle me-1"></i>
                                            Undangan sudah terkirim!
                                        </small>
                                    @endif
                                </div>
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
                                    <p class="mb-0 fw-semibold">
                                        {{ strip_tags($jadwal->pendaftaranSeminarProposal->judul_skripsi) }}</p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted mb-1">
                                    <i class="bx bx-user-pin me-1"></i>Dosen Pembimbing
                                </label>
                                <div class="d-flex align-items-center p-3 bg-lighter rounded">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ strtoupper(substr($jadwal->pendaftaranSeminarProposal->dosenPembimbing->name ?? 'N/A', 0, 2)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">
                                            {{ $jadwal->pendaftaranSeminarProposal->dosenPembimbing->name ?? '-' }}</h6>
                                        <small
                                            class="text-muted">{{ $jadwal->pendaftaranSeminarProposal->dosenPembimbing->nip ?? '-' }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted mb-1">
                                    <i class="bx bx-group me-1"></i>Komisi Proposal
                                </label>
                                <div class="p-3 bg-lighter rounded">
                                    @if ($jadwal->pendaftaranSeminarProposal->komisiProposal)
                                        <span class="badge bg-label-success mb-1">
                                            <i class="bx bx-check-circle me-1"></i>Sudah Ditentukan
                                        </span>
                                        <a href="{{ route('admin.komisi-proposal.show', $jadwal->pendaftaranSeminarProposal->komisiProposal) }}"
                                            class="btn btn-xs btn-outline-primary mt-1">
                                            <i class="bx bx-show me-1"></i>Lihat Detail
                                        </a>
                                    @else
                                        <span class="badge bg-label-warning">
                                            <i class="bx bx-time me-1"></i>Belum Ditentukan
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Informasi Jadwal --}}
                @if ($jadwal->hasJadwal())
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary bg-opacity-10">
                            <h5 class="card-title mb-0 text-primary">
                                <i class="bx bx-calendar me-2"></i>Informasi Jadwal Seminar
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="p-3 bg-primary bg-opacity-10 rounded text-center hover-shadow">
                                        <i class="bx bx-calendar text-primary fs-3 mb-2"></i>
                                        <label class="form-label text-muted mb-1 d-block small">Tanggal</label>
                                        <h6 class="mb-0 text-primary">{{ $jadwal->tanggal->format('d M Y') }}</h6>
                                        <small class="text-muted">{{ $jadwal->tanggal->format('l') }}</small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="p-3 bg-success bg-opacity-10 rounded text-center hover-shadow">
                                        <i class="bx bx-time text-success fs-3 mb-2"></i>
                                        <label class="form-label text-muted mb-1 d-block small">Waktu</label>
                                        <h6 class="mb-0 text-success">{{ $jadwal->jam_formatted }}</h6>
                                        <small class="text-muted">Zona: WITA</small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="p-3 bg-warning bg-opacity-10 rounded text-center hover-shadow">
                                        <i class="bx bx-door-open text-warning fs-3 mb-2"></i>
                                        <label class="form-label text-muted mb-1 d-block small">Ruangan</label>
                                        <h6 class="mb-0 text-warning text-truncate" title="{{ $jadwal->ruangan }}">
                                            {{ $jadwal->ruangan }}
                                        </h6>
                                    </div>
                                </div>
                            </div>

                            {{-- Jadwal Lengkap --}}
                            <div class="mt-3 p-3 bg-light rounded">
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-info-circle text-primary me-2 fs-4"></i>
                                    <div>
                                        <small class="text-muted d-block">Jadwal Lengkap</small>
                                        <strong class="text-dark">{{ $jadwal->jadwal_lengkap }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card mb-4 border-warning">
                        <div class="card-body text-center py-4">
                            <i class="bx bx-calendar-x text-warning" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 mb-2 text-warning">Jadwal Belum Ditentukan</h5>
                            <p class="text-muted mb-3">Jadwal seminar proposal belum dibuat oleh admin</p>
                            @if ($jadwal->status === 'menunggu_jadwal')
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#scheduleModal" data-jadwal-id="{{ $jadwal->id }}"
                                    data-mahasiswa-nama="{{ $jadwal->pendaftaranSeminarProposal->user->name }}"
                                    data-mahasiswa-nim="{{ $jadwal->pendaftaranSeminarProposal->user->nim }}"
                                    data-mahasiswa-judul="{{ strip_tags($jadwal->pendaftaranSeminarProposal->judul_skripsi) }}">
                                    <i class="bx bx-calendar-plus me-1"></i> Buat Jadwal Sekarang
                                </button>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Card: SK Proposal --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-file-blank me-2"></i>SK Proposal
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($jadwal->hasSkFile())
                            <div class="alert alert-success border-0 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-check-circle fs-4 me-2"></i>
                                    <div>
                                        <strong>SK Proposal Tersedia</strong>
                                        <p class="mb-0 small">File SK Proposal sudah diupload oleh mahasiswa</p>
                                    </div>
                                </div>
                            </div>

                            {{-- SK Preview Container --}}
                            <div id="skPreviewContainer" class="sk-preview-container mb-3">
                                <div class="preview-header">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-file-blank me-2 fs-4"></i>
                                        <div>
                                            <h6 class="mb-0">Preview SK Proposal</h6>
                                            <small
                                                class="text-muted">SK_{{ $jadwal->pendaftaranSeminarProposal->user->nim }}.pdf</small>
                                        </div>
                                    </div>
                                    <div class="preview-actions">
                                        <a href="{{ route('admin.jadwal-seminar-proposal.view-sk', $jadwal) }}"
                                            target="_blank" class="btn btn-sm btn-outline-secondary me-2">
                                            <i class="bx bx-link-external me-1"></i> Buka Tab Baru
                                        </a>
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-danger"
                                            id="closeSkPreviewBtn">
                                            <i class="bx bx-x"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="preview-body">
                                    <div id="skPreviewLoading" class="preview-loading">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2 mb-0 text-muted">Memuat SK Proposal...</p>
                                    </div>
                                    <iframe id="skPreviewFrame" class="preview-frame"
                                        src="{{ route('admin.jadwal-seminar-proposal.view-sk', $jadwal) }}"
                                        style="display: none;"></iframe>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary" id="showSkPreviewBtn">
                                    <i class="bx bx-show me-1"></i> Tampilkan Preview
                                </button>
                                <a href="{{ route('admin.jadwal-seminar-proposal.view-sk', $jadwal) }}" target="_blank"
                                    class="btn btn-outline-primary">
                                    <i class="bx bx-link-external me-1"></i> Buka Tab Baru
                                </a>
                                <a href="{{ route('admin.jadwal-seminar-proposal.download-sk', $jadwal) }}"
                                    class="btn btn-success">
                                    <i class="bx bx-download me-1"></i> Download
                                </a>
                            </div>
                        @else
                            <div class="alert alert-warning border-0 mb-0">
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-error fs-4 me-2"></i>
                                    <div>
                                        <strong>SK Proposal Belum Tersedia</strong>
                                        <p class="mb-0 small">Mahasiswa belum mengupload file SK Proposal</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Card: Tim Penguji --}}
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-group me-2"></i>Tim Penguji
                        </h5>
                        <span class="badge bg-label-info">
                            {{ 1 + count($jadwal->pendaftaranSeminarProposal->proposalPembahas) }} Dosen
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th width="20%">Posisi</th>
                                        <th width="35%">Nama Dosen</th>
                                        <th width="30%">Email</th>
                                        <th width="15%">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Pembimbing --}}
                                    <tr>
                                        <td>
                                            <span class="badge bg-label-primary">
                                                <i class="bx bx-user-pin me-1"></i>Pembimbing
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-xs me-2">
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        {{ strtoupper(substr($jadwal->pendaftaranSeminarProposal->dosenPembimbing->name ?? 'N', 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 small">
                                                        {{ $jadwal->pendaftaranSeminarProposal->dosenPembimbing->name ?? '-' }}
                                                    </h6>
                                                    <small
                                                        class="text-muted">{{ $jadwal->pendaftaranSeminarProposal->dosenPembimbing->nip ?? '-' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <small>{{ $jadwal->pendaftaranSeminarProposal->dosenPembimbing->email ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-success">
                                                <i class="bx bx-check"></i>
                                            </span>
                                        </td>
                                    </tr>

                                    {{-- Pembahas 1, 2, 3 --}}
                                    @forelse ($jadwal->pendaftaranSeminarProposal->proposalPembahas as $pembahas)
                                        <tr>
                                            <td>
                                                <span class="badge bg-label-info">
                                                    <i class="bx bx-user-check me-1"></i>Pembahas {{ $pembahas->posisi }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-xs me-2">
                                                        <span class="avatar-initial rounded-circle bg-label-info">
                                                            {{ strtoupper(substr($pembahas->dosen->name ?? 'N', 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 small">{{ $pembahas->dosen->name ?? '-' }}</h6>
                                                        <small
                                                            class="text-muted">{{ $pembahas->dosen->nip ?? '-' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <small>{{ $pembahas->dosen->email ?? '-' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-success">
                                                    <i class="bx bx-check"></i>
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">
                                                <i class="bx bx-info-circle me-1"></i>
                                                Belum ada dosen pembahas
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($jadwal->status === 'dijadwalkan')
                            <hr class="my-3">
                            <div class="alert alert-info border-0 mb-0">
                                <div class="d-flex align-items-start">
                                    <i class="bx bx-info-circle fs-4 me-2"></i>
                                    <div class="flex-grow-1">
                                        <strong class="d-block mb-1">Informasi Undangan</strong>
                                        <p class="mb-2 small">Undangan sudah dikirim ke semua dosen di atas</p>
                                        <form
                                            action="{{ route('admin.jadwal-seminar-proposal.kirim-ulang-undangan', $jadwal) }}"
                                            method="POST" class="kirim-ulang-form-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-info">
                                                <i class="bx bx-send me-1"></i> Kirim Ulang Undangan
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Include Modals --}}
    @include('admin.jadwal-seminar-proposal.modals.schedule-modal')
    @include('admin.jadwal-seminar-proposal.modals.delete-modal')

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // SK Preview Elements
            const showSkPreviewBtn = document.getElementById('showSkPreviewBtn');
            const closeSkPreviewBtn = document.getElementById('closeSkPreviewBtn');
            const skPreviewContainer = document.getElementById('skPreviewContainer');
            const skPreviewFrame = document.getElementById('skPreviewFrame');
            const skPreviewLoading = document.getElementById('skPreviewLoading');

            if (showSkPreviewBtn && skPreviewContainer) {
                // Initially hide preview container
                skPreviewContainer.style.display = 'none';

                // Show preview
                showSkPreviewBtn.addEventListener('click', function() {
                    skPreviewContainer.style.display = 'block';
                    skPreviewLoading.style.display = 'flex';
                    skPreviewFrame.style.display = 'none';
                    this.style.display = 'none';

                    // Show frame after load
                    skPreviewFrame.onload = function() {
                        skPreviewLoading.style.display = 'none';
                        skPreviewFrame.style.display = 'block';
                    };
                });

                // Close preview
                closeSkPreviewBtn.addEventListener('click', function() {
                    skPreviewContainer.style.display = 'none';
                    showSkPreviewBtn.style.display = 'inline-flex';
                    skPreviewFrame.src = skPreviewFrame.src; // Reload
                });
            }

            // Mark as Selesai Confirmation
            const markSelesaiForm = document.querySelector('.mark-selesai-form');
            if (markSelesaiForm) {
                markSelesaiForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Konfirmasi Tandai Selesai',
                        html: `
                            <div class="text-start">
                                <p class="mb-3">Apakah Anda yakin ingin menandai seminar proposal ini sebagai <strong>selesai</strong>?</p>
                                <div class="alert alert-info mb-0">
                                    <i class="bx bx-info-circle me-2"></i>
                                    <small>Setelah ditandai selesai, jadwal tidak dapat diubah kembali.</small>
                                </div>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="bx bx-check-circle me-1"></i> Ya, Tandai Selesai',
                        cancelButtonText: '<i class="bx bx-x me-1"></i> Batal',
                        customClass: {
                            confirmButton: 'btn btn-success me-2',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            }

            // Kirim Ulang Undangan Confirmation
            const kirimUlangForms = document.querySelectorAll('.kirim-ulang-form, .kirim-ulang-form-inline');
            kirimUlangForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Konfirmasi Kirim Ulang',
                        html: `
                            <div class="text-start">
                                <p class="mb-3">Apakah Anda yakin ingin mengirim ulang undangan ke semua dosen?</p>
                                <div class="alert alert-info mb-0">
                                    <i class="bx bx-info-circle me-2"></i>
                                    <small>Email undangan akan dikirim ke:<br>
                                    • 1 Dosen Pembimbing<br>
                                    • 3 Dosen Pembahas</small>
                                </div>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#17a2b8',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="bx bx-send me-1"></i> Ya, Kirim Ulang',
                        cancelButtonText: '<i class="bx bx-x me-1"></i> Batal',
                        customClass: {
                            confirmButton: 'btn btn-info me-2',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });

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
        /* Enhanced Styles */
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

        /* Timeline Styles */
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

        /* SK Preview Styles */
        .sk-preview-container {
            border: 1px solid #e9ecef;
            border-radius: 0.5rem;
            overflow: hidden;
            background: #fff;
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

        .preview-loading {
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

        /* Avatar Styles */
        .avatar-xl {
            width: 5rem;
            height: 5rem;
        }

        .avatar-xl .avatar-initial {
            font-size: 2rem;
        }

        /* Table Styles */
        .table-hover tbody tr:hover {
            background-color: rgba(67, 89, 113, 0.04);
        }

        /* Responsive */
        @media (max-width: 768px) {
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
