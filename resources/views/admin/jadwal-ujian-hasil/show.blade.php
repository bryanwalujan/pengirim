{{-- filepath: resources/views/admin/jadwal-ujian-hasil/show.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Detail Jadwal Ujian Hasil')

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
                    <a href="{{ route('admin.jadwal-ujian-hasil.index') }}">Jadwal Ujian Hasil</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Detail</li>
            </ol>
        </nav>

        {{-- Header Section --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div class="me-3">
                <h4 class="fw-bold mb-1">
                    <i class="bx bx-calendar-event me-2 text-warning"></i>Detail Jadwal Ujian Hasil
                </h4>
                <p class="text-muted mb-0">
                    <i class="bx bx-user me-1"></i>
                    {{ $jadwal->pendaftaranUjianHasil->user->name }}
                    ({{ $jadwal->pendaftaranUjianHasil->user->nim }})
                </p>
            </div>
            <div class="d-flex gap-2">
                {!! $jadwal->status_badge !!}
                <a href="{{ route('admin.jadwal-ujian-hasil.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bx bx-check-circle fs-4 me-2"></i>
                    <div>{!! session('success') !!}</div>
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
                                <span class="avatar-initial rounded-circle bg-label-warning">
                                    {{ strtoupper(substr($jadwal->pendaftaranUjianHasil->user->name, 0, 2)) }}
                                </span>
                            </div>
                            <h5 class="mb-1">{{ $jadwal->pendaftaranUjianHasil->user->name }}</h5>
                            <div class="mb-2">
                                <span class="badge bg-label-warning">{{ $jadwal->pendaftaranUjianHasil->user->nim }}</span>
                            </div>
                            <p class="text-muted mb-0">
                                <i class="bx bx-envelope me-1"></i>{{ $jadwal->pendaftaranUjianHasil->user->email }}
                            </p>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-around text-center">
                            <div>
                                <i class="bx bx-calendar-alt text-warning fs-4"></i>
                                <h6 class="mb-0 mt-1">{{ $jadwal->pendaftaranUjianHasil->angkatan }}</h6>
                                <small class="text-muted">Angkatan</small>
                            </div>
                            <div class="border-start"></div>
                            <div>
                                <i class="bx bx-medal text-success fs-4"></i>
                                <h6 class="mb-0 mt-1">{{ number_format($jadwal->pendaftaranUjianHasil->ipk, 2) }}</h6>
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
                                            <h6 class="mb-0">SK Ujian Hasil Diupload</h6>
                                            <small class="text-muted">{{ $jadwal->updated_at->locale('id')->translatedFormat('d F Y, H:i') }}</small>
                                        </div>
                                        <p class="mb-0 small">Mahasiswa telah mengupload SK Ujian Hasil</p>
                                    </div>
                                </li>
                            @endif

                            {{-- Jadwal Dibuat --}}
                            @if ($jadwal->hasJadwal())
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-warning"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0">Jadwal Ditentukan</h6>
                                            <small class="text-muted">{{ $jadwal->updated_at->locale('id')->translatedFormat('d F Y, H:i') }}</small>
                                        </div>
                                        <p class="mb-0 small">Jadwal ujian hasil telah dibuat oleh admin</p>
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
                                            <small class="text-muted">{{ $jadwal->updated_at->locale('id')->translatedFormat('d F Y, H:i') }}</small>
                                        </div>
                                        <p class="mb-0 small">Undangan telah dikirim ke dosen penguji</p>
                                    </div>
                                </li>
                            @endif

                            {{-- Selesai --}}
                            @if ($jadwal->isSelesai())
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-success"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0">Ujian Hasil Selesai</h6>
                                            <small class="text-muted">{{ $jadwal->updated_at->locale('id')->translatedFormat('d F Y, H:i') }}</small>
                                        </div>
                                        <p class="mb-0 small">Ujian hasil skripsi telah dilaksanakan</p>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                {{-- Card: Quick Actions --}}
                @can('manage jadwal ujian hasil')
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bx bx-cog me-1"></i>Quick Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            {{-- Buat Jadwal --}}
                            @if ($jadwal->status === 'menunggu_jadwal')
                                <button type="button" class="btn btn-warning w-100 mb-2" data-bs-toggle="modal"
                                    data-bs-target="#scheduleModal" data-jadwal-id="{{ $jadwal->id }}"
                                    data-mahasiswa-nama="{{ $jadwal->pendaftaranUjianHasil->user->name }}"
                                    data-mahasiswa-nim="{{ $jadwal->pendaftaranUjianHasil->user->nim }}"
                                    data-mahasiswa-judul="{{ $jadwal->pendaftaranUjianHasil->judul_skripsi }}">
                                    <i class="bx bx-calendar-plus me-1"></i> Buat Jadwal
                                </button>
                            @endif

                            {{-- Edit Jadwal --}}
                            @if ($jadwal->status === 'dijadwalkan')
                                <button type="button" class="btn btn-warning w-100 mb-2" data-bs-toggle="modal"
                                    data-bs-target="#scheduleModal" data-jadwal-id="{{ $jadwal->id }}"
                                    data-mahasiswa-nama="{{ $jadwal->pendaftaranUjianHasil->user->name }}"
                                    data-mahasiswa-nim="{{ $jadwal->pendaftaranUjianHasil->user->nim }}"
                                    data-mahasiswa-judul="{{ $jadwal->pendaftaranUjianHasil->judul_skripsi }}"
                                    data-tanggal_ujian="{{ $jadwal->tanggal_ujian ? $jadwal->tanggal_ujian->format('Y-m-d') : '' }}"
                                    data-jam-mulai="{{ $jadwal->waktu_mulai }}"
                                    data-jam-selesai="{{ $jadwal->waktu_selesai }}"
                                    data-ruangan="{{ $jadwal->ruangan }}">
                                    <i class="bx bx-edit me-1"></i> Edit Jadwal
                                </button>

                                {{-- Mark as Selesai --}}
                                @if ($jadwal->canMarkAsSelesai())
                                    <form action="{{ route('admin.jadwal-ujian-hasil.mark-selesai', $jadwal) }}"
                                        method="POST" class="mark-selesai-form">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100 mb-2">
                                            <i class="bx bx-check-circle me-1"></i> Tandai Selesai
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.jadwal-ujian-hasil.kirim-ulang-undangan', $jadwal) }}"
                                    method="POST" class="kirim-ulang-form">
                                    @csrf
                                    <button type="submit" class="btn btn-info w-100 mb-2">
                                        <i class="bx bx-send me-1"></i> Kirim Ulang Undangan
                                    </button>
                                </form>
                            @endif

                            {{-- SK Actions --}}
                            @if ($jadwal->hasSkFile())
                                <a href="{{ route('admin.jadwal-ujian-hasil.download-sk', $jadwal) }}"
                                    class="btn btn-outline-warning w-100 mb-2">
                                    <i class="bx bx-download me-1"></i> Download SK
                                </a>
                            @endif

                            {{-- Delete Button --}}
                            @if ($jadwal->canBeDeleted())
                                <form action="{{ route('admin.jadwal-ujian-hasil.destroy', $jadwal) }}" method="POST"
                                    class="delete-form"
                                    data-mahasiswa="{{ $jadwal->pendaftaranUjianHasil->user->name }}"
                                    data-nim="{{ $jadwal->pendaftaranUjianHasil->user->nim }}"
                                    data-status="{{ $jadwal->status }}"
                                    data-confirmation="{{ $jadwal->getDeleteConfirmationMessage() }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="bx bx-trash me-1"></i> Hapus Jadwal
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endcan
            </div>

            {{-- Right Column --}}
            <div class="col-xl-8 col-lg-7">
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
                                    <p class="mb-0 fw-semibold">
                                        {{ strip_tags($jadwal->pendaftaranUjianHasil->judul_skripsi) }}</p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted mb-1">
                                    <i class="bx bx-user-pin me-1"></i>Dosen Pembimbing 1
                                </label>
                                <div class="d-flex align-items-center p-3 bg-lighter rounded">
                                    @if($jadwal->pendaftaranUjianHasil->dosenPembimbing1)
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-warning">
                                                {{ strtoupper(substr($jadwal->pendaftaranUjianHasil->dosenPembimbing1->name, 0, 2)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $jadwal->pendaftaranUjianHasil->dosenPembimbing1->name }}</h6>
                                            <small class="text-muted">{{ $jadwal->pendaftaranUjianHasil->dosenPembimbing1->nip ?? '-' }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">Belum ditentukan</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted mb-1">
                                    <i class="bx bx-user-pin me-1"></i>Dosen Pembimbing 2
                                </label>
                                <div class="d-flex align-items-center p-3 bg-lighter rounded">
                                    @if($jadwal->pendaftaranUjianHasil->dosenPembimbing2)
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-info">
                                                {{ strtoupper(substr($jadwal->pendaftaranUjianHasil->dosenPembimbing2->name, 0, 2)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $jadwal->pendaftaranUjianHasil->dosenPembimbing2->name }}</h6>
                                            <small class="text-muted">{{ $jadwal->pendaftaranUjianHasil->dosenPembimbing2->nip ?? '-' }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">Belum ditentukan</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Informasi Jadwal --}}
                @if ($jadwal->hasJadwal())
                    <div class="card mb-4 border-warning">
                        <div class="card-header bg-warning bg-opacity-10 mb-3">
                            <h5 class="card-title mb-0 text-warning">
                                <i class="bx bx-calendar me-2"></i>Informasi Jadwal Ujian Hasil
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="p-3 bg-warning bg-opacity-10 rounded text-center hover-shadow">
                                        <i class="bx bx-calendar text-warning fs-3 mb-2"></i>
                                        <label class="form-label text-muted mb-1 d-block small">Tanggal</label>
                                        <h6 class="mb-0 text-warning">
                                            {{ \Carbon\Carbon::parse($jadwal->tanggal_ujian)->locale('id')->translatedFormat('d F Y') }}
                                        </h6>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($jadwal->tanggal_ujian)->locale('id')->translatedFormat('l') }}</small>
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
                                    <div class="p-3 bg-info bg-opacity-10 rounded text-center hover-shadow">
                                        <i class="bx bx-door-open text-info fs-3 mb-2"></i>
                                        <label class="form-label text-muted mb-1 d-block small">Ruangan</label>
                                        <h6 class="mb-0 text-info text-truncate" title="{{ $jadwal->ruangan }}">
                                            {{ $jadwal->ruangan }}
                                        </h6>
                                    </div>
                                </div>
                            </div>

                            {{-- Jadwal Lengkap --}}
                            <div class="mt-3 p-3 bg-light rounded">
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-info-circle text-warning me-2 fs-4"></i>
                                    <div>
                                        <small class="text-muted d-block">Jadwal Lengkap</small>
                                        <strong class="text-dark">{{ $jadwal->jadwal_lengkap }}</strong>
                                    </div>
                                </div>
                            </div>

                            {{-- Status Ujian Info --}}
                            @if ($jadwal->tanggal_ujian->isPast())
                                <div class="mt-3 alert alert-success mb-0">
                                    <i class="bx bx-check-circle me-2"></i>
                                    <strong>Ujian Sudah Dilaksanakan</strong>
                                    <small class="d-block mt-1">
                                        Dilaksanakan {{ $jadwal->tanggal_ujian->diffForHumans() }}
                                    </small>
                                </div>
                            @elseif ($jadwal->tanggal_ujian->isToday())
                                <div class="mt-3 alert alert-warning mb-0">
                                    <i class="bx bx-calendar-event me-2"></i>
                                    <strong>Ujian Hari Ini</strong>
                                    <small class="d-block mt-1">{{ $jadwal->jam_formatted }}</small>
                                </div>
                            @else
                                <div class="mt-3 alert alert-info mb-0">
                                    <i class="bx bx-calendar me-2"></i>
                                    <strong>Ujian Akan Datang</strong>
                                    <small class="d-block mt-1">{{ $jadwal->tanggal_ujian->diffForHumans() }}</small>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="card mb-4 border-warning">
                        <div class="card-body text-center py-4">
                            <i class="bx bx-calendar-x text-warning" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 mb-2 text-warning">Jadwal Belum Ditentukan</h5>
                            <p class="text-muted mb-3">Jadwal ujian hasil belum dibuat oleh admin</p>
                            @if ($jadwal->status === 'menunggu_jadwal')
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#scheduleModal" data-jadwal-id="{{ $jadwal->id }}"
                                    data-mahasiswa-nama="{{ $jadwal->pendaftaranUjianHasil->user->name }}"
                                    data-mahasiswa-nim="{{ $jadwal->pendaftaranUjianHasil->user->nim }}"
                                    data-mahasiswa-judul="{{ strip_tags($jadwal->pendaftaranUjianHasil->judul_skripsi) }}">
                                    <i class="bx bx-calendar-plus me-1"></i> Buat Jadwal Sekarang
                                </button>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Card: SK Ujian Hasil --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-file-blank me-2"></i>SK Ujian Hasil
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($jadwal->hasSkFile())
                            <div class="alert alert-success border-0 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-check-circle fs-4 me-2"></i>
                                    <div>
                                        <strong>SK Ujian Hasil Tersedia</strong>
                                        <p class="mb-0 small">File SK Ujian Hasil sudah diupload oleh mahasiswa</p>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.jadwal-ujian-hasil.view-sk', $jadwal) }}" target="_blank"
                                    class="btn btn-outline-warning">
                                    <i class="bx bx-link-external me-1"></i> Buka Tab Baru
                                </a>
                                <a href="{{ route('admin.jadwal-ujian-hasil.download-sk', $jadwal) }}"
                                    class="btn btn-success">
                                    <i class="bx bx-download me-1"></i> Download
                                </a>
                            </div>
                        @else
                            <div class="alert alert-warning border-0 mb-0">
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-error fs-4 me-2"></i>
                                    <div>
                                        <strong>SK Ujian Hasil Belum Tersedia</strong>
                                        <p class="mb-0 small">Mahasiswa belum mengupload file SK Ujian Hasil</p>
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
                        <span class="badge bg-label-warning">
                            {{ $jadwal->dosenPenguji->count() }} Dosen
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Dosen</th>
                                        <th>Posisi</th>
                                        <th width="25%">Kontak</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($jadwal->dosenPenguji as $index => $penguji)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-2">
                                                        <span class="avatar-initial rounded-circle bg-label-warning">
                                                            {{ strtoupper(substr($penguji->name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $penguji->name }}</h6>
                                                        <small class="text-muted">NIP: {{ $penguji->nip ?? '-' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-{{ $penguji->pivot->posisi == 'Ketua Penguji' ? 'warning' : 'info' }}">
                                                    <i class="bx bx-user-check me-1"></i>{{ $penguji->pivot->posisi }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="bx bx-envelope me-1"></i>{{ $penguji->email }}
                                                </small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-3">
                                                <span class="text-muted">Belum ada dosen penguji yang ditugaskan</span>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Include Modal --}}
    @include('admin.jadwal-ujian-hasil.modals.schedule-modal')

@endsection

@push('styles')
    <style>
        .hover-shadow:hover {
            box-shadow: 0 4px 12px rgba(255, 159, 67, 0.2);
            transform: translateY(-2px);
            transition: all 0.3s ease;
        }

        .timeline {
            padding-left: 1rem;
        }

        .timeline-item {
            position: relative;
            padding-left: 1.5rem;
            padding-bottom: 1rem;
            border-left: 2px solid #e9ecef;
        }

        .timeline-item:last-child {
            border-left: 2px solid transparent;
        }

        .timeline-point {
            position: absolute;
            left: -7px;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .timeline-point-success {
            background-color: #71dd37;
        }

        .timeline-point-warning {
            background-color: #ff9f43;
        }

        .timeline-point-info {
            background-color: #03c3ec;
        }

        .timeline-point-secondary {
            background-color: #8592a3;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete confirmation
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const mahasiswa = this.dataset.mahasiswa;
                    const nim = this.dataset.nim;
                    const confirmation = this.dataset.confirmation;

                    Swal.fire({
                        title: 'Konfirmasi Hapus Jadwal',
                        html: `
                            <div class="text-start">
                                <p class="mb-2"><strong>Mahasiswa:</strong> ${mahasiswa}</p>
                                <p class="mb-2"><strong>NIM:</strong> ${nim}</p>
                                <div class="alert alert-warning mb-0">
                                    <i class="bx bx-error me-2"></i>${confirmation}
                                </div>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="bx bx-trash me-1"></i> Ya, Hapus!',
                        cancelButtonText: '<i class="bx bx-x me-1"></i> Batal',
                        customClass: {
                            confirmButton: 'btn btn-danger me-2',
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

            // Mark as selesai confirmation
            const markSelesaiForms = document.querySelectorAll('.mark-selesai-form');
            markSelesaiForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Konfirmasi Tandai Selesai',
                        text: 'Tandai ujian hasil ini sebagai selesai?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="bx bx-check-circle me-1"></i> Ya, Selesai',
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
            });

            // Kirim ulang confirmation
            const kirimUlangForms = document.querySelectorAll('.kirim-ulang-form');
            kirimUlangForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Konfirmasi Kirim Ulang',
                        text: 'Kirim ulang undangan ke semua dosen penguji?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#ff9f43',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="bx bx-send me-1"></i> Ya, Kirim',
                        cancelButtonText: '<i class="bx bx-x me-1"></i> Batal',
                        customClass: {
                            confirmButton: 'btn btn-warning me-2',
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
        });
    </script>
@endpush
