{{-- filepath: resources/views/admin/berita-acara-sempro/approve-pembahas.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Persetujuan Berita Acara - Dosen Pembahas')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @php
            $jadwal = $beritaAcara->jadwalSeminarProposal;
            $pendaftaran = $jadwal->pendaftaranSeminarProposal;
            $mahasiswa = $pendaftaran->user;
            $pembimbing = $pendaftaran->dosenPembimbing;
        @endphp

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <span class="text-muted fw-light">Berita Acara /</span> Persetujuan Pembahas
                </h4>
                <p class="text-muted mb-0">
                    <i class="bx bx-user-check me-1"></i>
                    Sebagai Dosen Pembahas
                </p>
            </div>
            <a href="{{ route('admin.berita-acara-sempro.show', $beritaAcara) }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>

        {{-- Alert Instruksi --}}
        <div class="alert alert-info alert-dismissible mb-4" role="alert">
            <h6 class="alert-heading mb-1">
                <i class="bx bx-info-circle me-2"></i>Instruksi Persetujuan
            </h6>
            <div class="small">
                Sebagai <strong>Dosen Pembahas</strong>, Anda diminta untuk:
                <ol class="mb-0 ps-3 mt-2">
                    <li>Memverifikasi bahwa ujian seminar proposal telah dilaksanakan</li>
                    <li>Memberikan persetujuan sebagai bukti kehadiran Anda</li>
                    <li>Mengisi lembar catatan seminar (opsional, dapat dilakukan nanti)</li>
                </ol>
                <div class="mt-2 bg-info bg-opacity-10 p-2 rounded">
                    <i class="bx bx-lock-alt me-1"></i>
                    <strong>Workflow:</strong>
                    <ul class="mb-0 ps-3 mt-1">
                        <li>Setelah <strong>semua pembahas</strong> memberikan persetujuan</li>
                        <li>Berita acara akan diteruskan ke <strong>Dosen Pembimbing</strong></li>
                        <li>Pembimbing akan mengisi catatan kejadian & kesimpulan</li>
                        <li>Terakhir, <strong>Ketua Penguji</strong> akan menandatangani</li>
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        {{-- Progress Persetujuan Pembahas --}}
        @php
            $progress = $beritaAcara->getTtdPembahasProgress();
        @endphp
        <div class="card mb-4">
            <div class="card-header bg-label-primary">
                <h5 class="mb-0">
                    <i class="bx bx-check-circle me-2"></i>Progress Persetujuan Pembahas
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-semibold">{{ $progress['signed'] }} dari {{ $progress['total'] }} pembahas sudah
                            memberikan persetujuan</span>
                        <span class="fw-semibold">{{ $progress['percentage'] }}%</span>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar"
                            style="width: {{ $progress['percentage'] }}%" aria-valuenow="{{ $progress['percentage'] }}"
                            aria-valuemin="0" aria-valuemax="100">
                            {{ $progress['signed'] }}/{{ $progress['total'] }}
                        </div>
                    </div>
                </div>

                @if ($progress['signed'] < $progress['total'])
                    <div class="alert alert-warning mb-0">
                        <i class="bx bx-time me-1"></i>
                        Menunggu <strong>{{ $progress['total'] - $progress['signed'] }} pembahas lainnya</strong> untuk
                        memberikan persetujuan
                    </div>
                @else
                    <div class="alert alert-success mb-0">
                        <i class="bx bx-check-circle me-1"></i>
                        Semua pembahas sudah memberikan persetujuan! Menunggu dosen pembimbing.
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            {{-- Left Column: Info Ujian --}}
            <div class="col-lg-6 mb-4">
                {{-- Info Mahasiswa --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-user me-2"></i>Informasi Mahasiswa
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td width="40%" class="text-muted">Nama</td>
                                <td class="fw-semibold">{{ $mahasiswa->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">NIM</td>
                                <td class="fw-semibold">{{ $mahasiswa->nim }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Ujian</td>
                                <td class="fw-semibold">
                                    {{ $jadwal->tanggal_ujian->isoFormat('dddd, D MMMM Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Waktu</td>
                                <td class="fw-semibold">
                                    {{ $jadwal->waktu_mulai }} - {{ $jadwal->waktu_selesai }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Ruangan</td>
                                <td class="fw-semibold">{{ $jadwal->ruangan }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Pembimbing</td>
                                <td class="fw-semibold">{{ $pembimbing->name }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Judul Proposal --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-book me-2"></i>Judul Proposal
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $pendaftaran->judul_skripsi }}</p>
                    </div>
                </div>

                {{-- Catatan Tambahan (jika ada) --}}
                @if ($beritaAcara->catatan_tambahan)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bx bx-note me-2"></i>Catatan dari Staff
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $beritaAcara->catatan_tambahan }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right Column: Daftar Pembahas --}}
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-group me-2"></i>Daftar Dewan Pembahas
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
                                        <th width="25%">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pembahasHadir as $index => $dosen)
                                        @php
                                            $hasSigned = $beritaAcara->hasSignedByPembahas($dosen->id);
                                            $isCurrentUser = $dosen->id === Auth::id();
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
                                                        $signature = collect(
                                                            $beritaAcara->ttd_dosen_pembahas,
                                                        )->firstWhere('dosen_id', $dosen->id);
                                                    @endphp
                                                    <span class="badge bg-success">
                                                        <i class="bx bx-check-circle me-1"></i>Sudah TTD
                                                    </span>
                                                    <div class="small text-muted mt-1">
                                                        {{ \Carbon\Carbon::parse($signature['signed_at'])->isoFormat('D/M/Y HH:mm') }}
                                                    </div>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="bx bx-time me-1"></i>Belum TTD
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Persetujuan --}}
        @if ($beritaAcara->canBeSignedByPembahas(Auth::id()))
            <div class="card mt-4">
                <div class="card-body">
                    {{-- ✅ PASTIKAN route name BENAR --}}
                    <form action="{{ route('admin.berita-acara-sempro.sign-pembahas', $beritaAcara) }}" method="POST"
                        id="formApprovePembahas">
                        @csrf

                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="form-check">
                                    <input class="form-check-input @error('confirmation') is-invalid @enderror"
                                        type="checkbox" id="confirmation" name="confirmation" value="1" required>
                                    <label class="form-check-label" for="confirmation">
                                        <strong>Saya menyatakan bahwa:</strong>
                                        <ul class="mb-0 mt-2 small">
                                            <li>Saya hadir dalam ujian seminar proposal ini</li>
                                            <li>Ujian telah dilaksanakan sesuai prosedur</li>
                                            <li>Saya memberikan persetujuan untuk melanjutkan proses berita acara</li>
                                        </ul>
                                    </label>
                                    @error('confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Info Lembar Catatan --}}
                                <div class="alert alert-info mt-3 mb-0">
                                    <i class="bx bx-info-circle me-1"></i>
                                    <small>
                                        <strong>Lembar Catatan:</strong>
                                        Anda dapat mengisi lembar catatan seminar setelah memberikan persetujuan.
                                        Lembar catatan berisi penilaian dan saran untuk mahasiswa.
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <button type="submit" class="btn btn-success btn-lg w-100" id="btnApprove" disabled>
                                    <i class="bx bx-check-circle me-2"></i>Berikan Persetujuan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @else
            {{-- Already signed or cannot sign --}}
            @if ($beritaAcara->hasSignedByPembahas(Auth::id()))
                <div class="alert alert-success mt-4">
                    <h5 class="alert-heading mb-2">
                        <i class="bx bx-check-circle me-2"></i>Persetujuan Berhasil Dicatat
                    </h5>
                    <p class="mb-0">
                        Anda telah memberikan persetujuan untuk berita acara ini.
                        Terima kasih atas partisipasi Anda!
                    </p>
                </div>
            @endif
        @endif

        {{-- Link ke Lembar Catatan --}}
        @if ($beritaAcara->hasSignedByPembahas(Auth::id()))
            @php
                $lembarCatatan = $beritaAcara->lembarCatatan()->where('dosen_id', Auth::id())->first();
            @endphp
            <div class="card mt-4">
                <div class="card-header bg-label-warning">
                    <h5 class="mb-0">
                        <i class="bx bx-note me-2"></i>Lembar Catatan Seminar
                    </h5>
                </div>
                <div class="card-body">
                    @if ($lembarCatatan)
                        <div class="alert alert-success mb-3">
                            <i class="bx bx-check-circle me-1"></i>
                            Anda sudah mengisi lembar catatan seminar
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.lembar-catatan-sempro.show', $lembarCatatan) }}"
                                class="btn btn-outline-primary">
                                <i class="bx bx-show me-1"></i>Lihat Lembar Catatan
                            </a>
                            @if (!$beritaAcara->isSigned())
                                <a href="{{ route('admin.lembar-catatan-sempro.edit', $lembarCatatan) }}"
                                    class="btn btn-outline-warning">
                                    <i class="bx bx-edit me-1"></i>Edit Lembar Catatan
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-warning mb-3">
                            <i class="bx bx-info-circle me-1"></i>
                            Anda belum mengisi lembar catatan seminar
                        </div>
                        <p class="text-muted mb-3">
                            Lembar catatan berisi penilaian Anda terhadap proposal mahasiswa,
                            termasuk aspek kelengkapan, kejelasan tujuan, metode penelitian,
                            dan saran perbaikan.
                        </p>
                        <a href="{{ route('admin.lembar-catatan-sempro.create', $beritaAcara) }}"
                            class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>Isi Lembar Catatan Sekarang
                        </a>
                    @endif>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        // Enable/disable approve button based on checkbox
        const confirmationCheckbox = document.getElementById('confirmation');
        const btnApprove = document.getElementById('btnApprove');

        if (confirmationCheckbox && btnApprove) {
            confirmationCheckbox.addEventListener('change', function() {
                btnApprove.disabled = !this.checked;
            });
        }

        // Form submission with confirmation
        const formApprove = document.getElementById('formApprovePembahas');
        if (formApprove) {
            formApprove.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Konfirmasi Persetujuan',
                    html: `
                        <div class="text-start">
                            <p class="mb-3">Anda akan memberikan persetujuan untuk berita acara:</p>
                            <div class="alert alert-info mb-3">
                                <strong>Mahasiswa:</strong> {{ $mahasiswa->name }} ({{ $mahasiswa->nim }})<br>
                                <strong>Tanggal Ujian:</strong> {{ $jadwal->tanggal_ujian->isoFormat('D MMMM Y') }}
                            </div>
                            <div class="alert alert-warning mb-0">
                                <i class="bx bx-info-circle me-1"></i>
                                <strong>Perhatian:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Persetujuan Anda akan dicatat secara permanen</li>
                                    <li>Jangan lupa mengisi <strong>Lembar Catatan</strong> setelah ini</li>
                                </ul>
                            </div>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="bx bx-check me-1"></i> Ya, Setuju',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Memproses...',
                            html: 'Menyimpan persetujuan Anda',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit form
                        this.submit();
                    }
                });
            });
        }
    </script>
@endpush
