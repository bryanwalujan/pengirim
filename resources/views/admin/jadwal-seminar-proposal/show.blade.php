{{-- filepath: resources/views/admin/jadwal-seminar-proposal/show.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Detail Jadwal Seminar Proposal')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="bx bx-calendar-event me-2"></i>Detail Jadwal Seminar Proposal
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.jadwal-seminar-proposal.index') }}">Jadwal Sempro</a>
                        </li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </nav>
            </div>
            <div class="btn-group">
                <a href="{{ route('admin.jadwal-seminar-proposal.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>

                {{-- ✅ TAMBAHAN: Delete Button di Show Page --}}
                @if ($jadwal->canBeDeleted())
                    <form action="{{ route('admin.jadwal-seminar-proposal.destroy', $jadwal) }}" method="POST"
                        class="d-inline delete-form-show"
                        data-mahasiswa="{{ $jadwal->pendaftaranSeminarProposal->user->name }}"
                        data-nim="{{ $jadwal->pendaftaranSeminarProposal->user->nim }}" data-status="{{ $jadwal->status }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bx bx-trash me-1"></i> Hapus Jadwal
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            {{-- Left Column - Mahasiswa Info --}}
            <div class="col-lg-4 mb-4">
                {{-- Mahasiswa Card --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="bx bx-user me-1"></i> Data Mahasiswa
                        </h5>
                        <div class="mb-3 text-center">
                            <div class="avatar avatar-xl mb-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    {{ strtoupper(substr($jadwal->pendaftaranSeminarProposal->user->name, 0, 2)) }}
                                </span>
                            </div>
                            <h5 class="mb-1">{{ $jadwal->pendaftaranSeminarProposal->user->name }}</h5>
                            <p class="text-muted mb-0">{{ $jadwal->pendaftaranSeminarProposal->user->nim }}</p>
                        </div>
                        <hr>
                        <div class="info-container">
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <span class="fw-semibold me-2">Email:</span>
                                    <span>{{ $jadwal->pendaftaranSeminarProposal->user->email }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-semibold me-2">Angkatan:</span>
                                    <span>{{ $jadwal->pendaftaranSeminarProposal->angkatan }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-semibold me-2">IPK:</span>
                                    <span
                                        class="badge bg-label-success">{{ $jadwal->pendaftaranSeminarProposal->ipk }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Status Card --}}
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="bx bx-info-circle me-1"></i> Status Jadwal
                        </h6>
                        <div class="text-center mb-3">
                            {!! $jadwal->status_badge !!}
                        </div>

                        @if ($jadwal->status === 'menunggu_jadwal')
                            <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal"
                                data-bs-target="#scheduleModal" data-jadwal-id="{{ $jadwal->id }}"
                                data-mahasiswa-nama="{{ $jadwal->pendaftaranSeminarProposal->user->name }}"
                                data-mahasiswa-nim="{{ $jadwal->pendaftaranSeminarProposal->user->nim }}"
                                data-mahasiswa-judul="{!! $jadwal->pendaftaranSeminarProposal->judul_skripsi !!}">
                                <i class="bx bx-calendar-plus me-1"></i> Buat Jadwal
                            </button>
                        @elseif($jadwal->status === 'dijadwalkan')
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#scheduleModal" data-jadwal-id="{{ $jadwal->id }}"
                                    data-mahasiswa-nama="{{ $jadwal->pendaftaranSeminarProposal->user->name }}"
                                    data-mahasiswa-nim="{{ $jadwal->pendaftaranSeminarProposal->user->nim }}"
                                    data-mahasiswa-judul="{!! $jadwal->pendaftaranSeminarProposal->judul_skripsi !!}"
                                    data-tanggal="{{ $jadwal->tanggal->format('Y-m-d') }}"
                                    data-jam-mulai="{{ $jadwal->jam_mulai }}"
                                    data-jam-selesai="{{ $jadwal->jam_selesai }}" data-ruangan="{{ $jadwal->ruangan }}">
                                    <i class="bx bx-edit me-1"></i> Edit Jadwal
                                </button>

                                <form action="{{ route('admin.jadwal-seminar-proposal.mark-selesai', $jadwal) }}"
                                    method="POST"
                                    onsubmit="return confirm('Tandai seminar proposal ini sebagai selesai?')">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bx bx-check-circle me-1"></i> Tandai Selesai
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Column - Details --}}
            <div class="col-lg-8">
                {{-- Judul Skripsi Card --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="bx bx-book me-1"></i> Judul Skripsi
                        </h5>
                        <p class="mb-0">{!! $jadwal->pendaftaranSeminarProposal->judul_skripsi !!}</p>
                    </div>
                </div>

                {{-- Jadwal Info Card --}}
                @if ($jadwal->hasJadwal())
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bx bx-calendar me-1"></i> Informasi Jadwal
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Tanggal</label>
                                    <p class="form-control-plaintext">
                                        <i class="bx bx-calendar me-1 text-primary"></i>
                                        {{ $jadwal->tanggal_formatted }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Waktu</label>
                                    <p class="form-control-plaintext">
                                        <i class="bx bx-time me-1 text-primary"></i>
                                        {{ $jadwal->jam_formatted }}
                                    </p>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Ruangan</label>
                                    <p class="form-control-plaintext">
                                        <i class="bx bx-door-open me-1 text-primary"></i>
                                        {{ $jadwal->ruangan }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- SK Proposal Card --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-file-blank me-1"></i> SK Proposal
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($jadwal->hasSkFile())
                            <div class="alert alert-success mb-3">
                                <i class="bx bx-check-circle me-2"></i>
                                SK Proposal sudah diupload oleh mahasiswa
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.jadwal-seminar-proposal.view-sk', $jadwal) }}" target="_blank"
                                    class="btn btn-primary">
                                    <i class="bx bx-show me-1"></i> Lihat SK
                                </a>
                                <a href="{{ route('admin.jadwal-seminar-proposal.download-sk', $jadwal) }}"
                                    class="btn btn-success">
                                    <i class="bx bx-download me-1"></i> Download SK
                                </a>
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="bx bx-error me-2"></i>
                                SK Proposal belum diupload oleh mahasiswa
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Tim Penguji Card --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-group me-1"></i> Tim Penguji
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Posisi</th>
                                        <th>Nama Dosen</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Pembimbing --}}
                                    <tr>
                                        <td><span class="badge bg-label-primary">Pembimbing</span></td>
                                        <td>{{ $jadwal->pendaftaranSeminarProposal->dosenPembimbing->name ?? '-' }}</td>
                                        <td>{{ $jadwal->pendaftaranSeminarProposal->dosenPembimbing->email ?? '-' }}</td>
                                    </tr>

                                    {{-- Pembahas --}}
                                    @foreach ($jadwal->pendaftaranSeminarProposal->proposalPembahas as $pembahas)
                                        <tr>
                                            <td><span class="badge bg-label-info">Pembahas {{ $pembahas->posisi }}</span>
                                            </td>
                                            <td>{{ $pembahas->dosen->name ?? '-' }}</td>
                                            <td>{{ $pembahas->dosen->email ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($jadwal->status === 'dijadwalkan')
                            <hr>
                            <form action="{{ route('admin.jadwal-seminar-proposal.kirim-ulang-undangan', $jadwal) }}"
                                method="POST" onsubmit="return confirm('Kirim ulang undangan ke semua dosen?')">
                                @csrf
                                <button type="submit" class="btn btn-info">
                                    <i class="bx bx-send me-1"></i> Kirim Ulang Undangan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Include Modal --}}
    @include('admin.jadwal-seminar-proposal.modals.schedule-modal')

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete confirmation for show page
            const deleteFormShow = document.querySelector('.delete-form-show');

            if (deleteFormShow) {
                deleteFormShow.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const mahasiswa = this.dataset.mahasiswa;
                    const nim = this.dataset.nim;
                    const status = this.dataset.status;

                    Swal.fire({
                        title: 'Konfirmasi Hapus Jadwal',
                        html: `
                    <div class="text-start">
                        <p class="mb-2"><strong>Mahasiswa:</strong> ${mahasiswa}</p>
                        <p class="mb-3"><strong>NIM:</strong> ${nim}</p>
                        <div class="alert alert-danger mb-0">
                            <i class="bx bx-error-circle me-2"></i>
                            <strong>Peringatan:</strong> Tindakan ini akan menghapus:
                            <ul class="mb-0 mt-2">
                                <li>Data jadwal seminar proposal</li>
                                <li>File SK Proposal (jika ada)</li>
                            </ul>
                            <p class="mb-0 mt-2">Tindakan ini <strong>tidak dapat dibatalkan</strong>!</p>
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
            }
        });
    </script>
@endpush
