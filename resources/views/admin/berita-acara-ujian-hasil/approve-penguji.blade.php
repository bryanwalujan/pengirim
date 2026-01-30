{{-- filepath: resources/views/admin/berita-acara-ujian-hasil/approve-penguji.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Persetujuan Berita Acara - Dosen Penguji')

@push('styles')
    <style>
        .rounded-xl {
            border-radius: 0.75rem !important;
        }

        .bg-label-amber {
            background-color: #fff2e0 !important;
            color: #ffab00 !important;
        }

        .progress-bar-premium {
            background: linear-gradient(45deg, #4CAF50, #8BC34A);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @php
            $jadwal = $beritaAcara->jadwalUjianHasil;
            $pendaftaran = $jadwal->pendaftaranUjianHasil;
            $mahasiswa = $pendaftaran->user;
        @endphp

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <span class="text-muted fw-light">Berita Acara Ujian Hasil /</span> Persetujuan Dosen
                </h4>
                <p class="text-muted mb-0">
                    <i class="bx bx-user-check me-1 text-primary"></i>
                    Validasi Pelaksanaan Ujian Mahasiswa
                </p>
            </div>
            <a href="{{ route('admin.berita-acara-ujian-hasil.show', $beritaAcara) }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>

        {{-- Alert Instruksi --}}
        <div class="alert alert-info alert-dismissible mb-4 border-0 shadow-sm" role="alert">
            <h6 class="alert-heading mb-1 fw-bold">
                <i class="bx bx-info-circle me-2"></i>Instruksi Persetujuan
            </h6>
            <div class="small">
                Sebagai <strong>Dosen Penguji</strong>, Anda diminta untuk memberikan validasi digital atas pelaksanaan
                ujian hasil.
                <ol class="mb-0 ps-3 mt-2">
                    <li>Verifikasi data pelaksanaan ujian (Waktu & Ruangan).</li>
                    <li>Berikan persetujuan sebagai bukti kehadiran dan partisipasi Anda.</li>
                    <li>Pastikan Anda juga telah mengisi <strong>Penilaian</strong> dan <strong>Lembar Koreksi</strong> di
                        halaman detail.</li>
                </ol>
                <div class="mt-3 bg-white bg-opacity-50 p-3 rounded-3 border border-info border-opacity-25">
                    <i class="bx bx-git-merge me-1 text-info"></i>
                    <strong>Alur Berita Acara:</strong>
                    <ul class="mb-0 ps-3 mt-1 text-muted">
                        <li>Semua <strong>Dosen Penguji</strong> wajib memberikan persetujuan.</li>
                        <li>Berita Acara akan terkunci dan PDF akan digenerate secara otomatis.</li>
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        {{-- Progress Persetujuan --}}
        @php $progress = $beritaAcara->getTtdPengujiProgress(); @endphp
        <div class="card mb-4 border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-label-primary border-0 py-3">
                <h5 class="mb-0 fw-bold d-flex align-items-center">
                    <i class="bx bx-task me-2 text-primary"></i>Progress Persetujuan Anggota Penguji
                </h5>
            </div>
            <div class="card-body pt-4">
                <div class="mb-1">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">{{ $progress['signed'] }} dari {{ $progress['total'] }} penguji telah
                            memvalidasi</span>
                        <span class="badge bg-label-success fw-bold">{{ $progress['percentage'] }}% Selesai</span>
                    </div>
                    <div class="progress rounded-pill shadow-none" style="height: 12px; background-color: #f5f5f9;">
                        <div class="progress-bar progress-bar-premium progress-bar-striped progress-bar-animated"
                            role="progressbar" style="width: {{ $progress['percentage'] }}%"
                            aria-valuenow="{{ $progress['percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>

                @if ($progress['signed'] < $progress['total'])
                    <div
                        class="mt-3 p-2 px-3 rounded-3 bg-light border-start border-warning border-3 d-flex align-items-center">
                        <i class="bx bx-time text-warning me-2"></i>
                        <span class="small text-muted">Menunggu <strong>{{ $progress['total'] - $progress['signed'] }}
                                penguji lainnya</strong> untuk memberikan validasi.</span>
                    </div>
                @else
                    <div
                        class="mt-3 p-2 px-3 rounded-3 bg-label-success border-start border-success border-3 d-flex align-items-center">
                        <i class="bx bx-check-double text-success me-2"></i>
                        <span class="small fw-bold">Seluruh anggota telah memvalidasi! Menunggu untuk finalisasi.</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            {{-- Left Column: Info Ujian --}}
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-bold">
                            <i class="bx bx-detail me-2 text-warning"></i>Detail Pelaksanaan
                        </h5>
                        <span class="badge bg-label-warning rounded-pill">Data Ujian</span>
                    </div>
                    <div class="card-body pt-4">
                        <div class="row mb-4">
                            <div class="col-12 mb-4 text-center">
                                <div class="avatar avatar-xl mx-auto mb-3">
                                    <span class="avatar-initial rounded-circle bg-label-primary shadow-sm">
                                        <i class="bx bx-user fs-2"></i>
                                    </span>
                                </div>
                                <h5 class="fw-bold mb-0 text-dark">{{ $mahasiswa->name }}</h5>
                                <p class="text-muted mb-0 small">NIM: {{ $mahasiswa->nim }}</p>
                            </div>

                            <div class="col-12">
                                <div class="p-3 bg-light rounded-3 border mb-3">
                                    <small class="text-muted d-block mb-1 text-uppercase fw-bold">Judul Skripsi</small>
                                    <span class="fw-semibold text-dark leading-relaxed">
                                        {{ $beritaAcara->judul_skripsi ?? ($pendaftaran->judul_skripsi ?? '-') }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-calendar me-2 fs-4 text-primary"></i>
                                    <div>
                                        <small class="text-muted d-block">Tanggal Ujian</small>
                                        <span
                                            class="fw-bold">{{ \Carbon\Carbon::parse($jadwal->tanggal_ujian)->isoFormat('D MMMM Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3 text-md-end">
                                <div class="d-flex align-items-center justify-content-md-end">
                                    <i class="bx bx-time-five me-2 fs-4 text-primary"></i>
                                    <div class="text-start text-md-end">
                                        <small class="text-muted d-block">Waktu & Ruangan</small>
                                        <span class="fw-bold">{{ $jadwal->waktu_mulai }} -
                                            {{ $jadwal->waktu_selesai }}</span><br>
                                        <span
                                            class="badge bg-label-secondary small mt-1">{{ $beritaAcara->ruangan ?? ($jadwal->ruangan ?? '-') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Daftar Penguji --}}
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header border-bottom">
                        <h5 class="mb-0 fw-bold">
                            <i class="bx bx-group me-2 text-warning"></i>Panel Dewan Penguji
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4 py-3" width="60%">Nama Dosen</th>
                                        <th class="py-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Filter penguji hadir (exclude ketua penguji)
                                        $filterPenguji = $pengujiHadir->filter(
                                            fn($p) => $p->pivot->posisi !== 'Ketua Penguji',
                                        );
                                    @endphp
                                    @forelse ($filterPenguji as $dosen)
                                        @php
                                            $hasSigned = $beritaAcara->hasSignedByPenguji($dosen->id);
                                            $isCurrentUser = $dosen->id === Auth::id();
                                        @endphp
                                        <tr
                                            class="{{ $isCurrentUser ? 'table-warning bg-label-amber shadow-none border-transparent' : '' }}">
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-3">
                                                        <span
                                                            class="avatar-initial rounded-circle {{ $isCurrentUser ? 'bg-warning' : 'bg-label-primary' }}">
                                                            {{ strtoupper(substr($dosen->name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $dosen->name }}</div>
                                                        <small class="text-muted">
                                                            @if (str_contains($dosen->pivot->posisi, '(PS1)'))
                                                                PS1
                                                            @elseif(str_contains($dosen->pivot->posisi, '(PS2)'))
                                                                PS2
                                                            @else
                                                                {{ $dosen->pivot->posisi }}
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center pe-4">
                                                @if ($hasSigned)
                                                    @php
                                                        $signature = collect(
                                                            $beritaAcara->ttd_dosen_penguji,
                                                        )->firstWhere('dosen_id', $dosen->id);
                                                    @endphp
                                                    <span
                                                        class="badge bg-label-success p-2 px-3 fw-bold shadow-none border-0">
                                                        <i class="bx bx-check-double me-1"></i> VALID
                                                    </span>
                                                    <div class="x-small text-muted mt-1" style="font-size: 0.65rem;">
                                                        {{ \Carbon\Carbon::parse($signature['signed_at'])->isoFormat('HH:mm') }}
                                                    </div>
                                                @else
                                                    <span
                                                        class="badge bg-label-warning p-2 px-3 fw-bold shadow-none border-0">
                                                        <i class="bx bx-time-five me-1"></i> MENUNGGU
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-5 text-muted small italic">Tidak ada
                                                penguji anggota ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Prerequisites Check --}}
        @php
            $canSign = $beritaAcara->canBeSignedByPenguji(Auth::id());
            $prerequisitesMet = $hasPenilaian ?? false; // Only penilaian is required, lembar koreksi is optional
        @endphp

        {{-- Warning for incomplete prerequisites --}}
        @if (!$hasPenilaian)
            <div class="alert alert-warning border-0 shadow-sm mb-4">
                <div class="d-flex align-items-start gap-3">
                    <i class="bx bx-error-circle fs-3 text-warning"></i>
                    <div>
                        <h6 class="alert-heading mb-2 fw-bold">Prasyarat Belum Terpenuhi</h6>
                        <p class="mb-2">Sebelum memberikan persetujuan, Anda harus melengkapi:</p>
                        <ul class="mb-3 ps-3">
                            <li><strong>Penilaian Ujian</strong> - Anda belum mengisi form penilaian.</li>
                        </ul>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('dosen.berita-acara-ujian-hasil.penilaian', $beritaAcara) }}"
                                class="btn btn-warning">
                                <i class="bx bx-edit me-1"></i> Isi Penilaian
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Optional: Lembar Koreksi for Pembimbing --}}
        @if ($isPembimbing && !$hasKoreksi && $hasPenilaian)
            <div class="alert alert-info border-0 shadow-sm mb-4">
                <div class="d-flex align-items-start gap-3">
                    <i class="bx bx-info-circle fs-3 text-info"></i>
                    <div>
                        <h6 class="alert-heading mb-2 fw-bold">Lembar Koreksi (Opsional)</h6>
                        <p class="mb-2">Sebagai Dosen Pembimbing, Anda dapat mengisi lembar koreksi skripsi jika
                            diperlukan.</p>
                        <a href="{{ route('dosen.berita-acara-ujian-hasil.koreksi', $beritaAcara) }}"
                            class="btn btn-outline-info btn-sm">
                            <i class="bx bx-file me-1"></i> Isi Lembar Koreksi
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form Persetujuan --}}
        @if ($canSign && $prerequisitesMet)
            <div class="card border-0 shadow-lg mt-2 overflow-hidden">
                <div class="bg-primary p-3 px-4 d-flex align-items-center gap-3">
                    <i class="bx bx-edit text-white fs-3"></i>
                    <h5 class="text-white mb-0 fw-bold">Konfirmasi Kehadiran & Persetujuan</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.berita-acara-ujian-hasil.sign-penguji', $beritaAcara) }}"
                        method="POST" id="formApprovePenguji">
                        @csrf
                        <div class="row align-items-center">
                            <div class="col-md-9 mb-4 mb-md-0">
                                <div class="p-3 bg-light rounded-3 border">
                                    <div class="form-check custom-option custom-option-basic">
                                        <label class="form-check-label custom-option-content" for="confirmation">
                                            <input class="form-check-input @error('confirmation') is-invalid @enderror"
                                                type="checkbox" id="confirmation" name="confirmation" value="1"
                                                required>
                                            <span class="custom-option-header">
                                                <span class="h6 mb-0 fw-bold text-dark">Pernyataan Validasi Digital</span>
                                            </span>
                                            <span class="custom-option-body mt-2 d-block small text-muted">
                                                Saya dengan sadar menyatakan bahwa saya hadir dalam pelaksanaan ujian hasil
                                                skripsi mahasiswa tersebut di atas. Seluruh proses telah berjalan sesuai
                                                ketentuan administrasi akademik UNIMA, dan saya memberikan persetujuan
                                                digital saya untuk diproses lebih lanjut.
                                            </span>
                                        </label>
                                        @error('confirmation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mt-3 d-flex align-items-center gap-2 text-warning small fw-medium">
                                    <i class="bx bx-shield-quarter"></i>
                                    <span>Tanda tangan digital ini memiliki kekuatan hukum yang sah di lingkungan aplikasi
                                        E-Service.</span>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <button type="submit" class="btn btn-primary btn-lg w-100 shadow p-3 fw-bold"
                                    id="btnApprove" disabled>
                                    <i class="bx bx-check-circle me-1"></i> Berikan Persetujuan
                                </button>
                                <p class="text-muted small mt-2 mb-0">Klik checkbox untuk aktifkan</p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @elseif ($canSign && !$prerequisitesMet)
            {{-- Form disabled because prerequisites not met - already shown warning above --}}
            <div class="card border-0 shadow-sm mt-2 bg-label-secondary">
                <div class="card-body p-4 text-center">
                    <div class="avatar avatar-lg mx-auto mb-3">
                        <span class="avatar-initial rounded-circle bg-secondary">
                            <i class="bx bx-lock text-white fs-1"></i>
                        </span>
                    </div>
                    <h5 class="fw-bold text-secondary mb-2">Form Persetujuan Terkunci</h5>
                    <p class="text-muted mb-0 mx-auto" style="max-width: 500px;">
                        Silakan lengkapi prasyarat di atas untuk membuka form persetujuan.
                    </p>
                </div>
            </div>
        @elseif ($beritaAcara->hasSignedByPenguji(Auth::id()))
            <div class="card border-0 shadow-sm mt-2 bg-label-success">
                <div class="card-body p-4 text-center">
                    <div class="avatar avatar-lg mx-auto mb-3">
                        <span class="avatar-initial rounded-circle bg-success">
                            <i class="bx bx-check-double text-white fs-1"></i>
                        </span>
                    </div>
                    <h4 class="fw-bold text-success mb-2">Validasi Anda Berhasil Dicatat!</h4>
                    <p class="text-muted mb-0 mx-auto" style="max-width: 500px;">
                        Terima kasih atas partisipasi Anda dalam ujian ini. Anda dapat memantau status penyelesaian berita
                        acara melalui halaman detail.
                    </p>
                    <a href="{{ route('admin.berita-acara-ujian-hasil.show', $beritaAcara) }}"
                        class="btn btn-success mt-4">
                        <i class="bx bx-show me-1"></i> Kembali ke Detail
                    </a>
                </div>
            </div>
        @else
            {{-- Fallback: User cannot sign for unknown reason --}}
            <div class="card border-0 shadow-sm mt-2 bg-label-danger">
                <div class="card-body p-4 text-center">
                    <div class="avatar avatar-lg mx-auto mb-3">
                        <span class="avatar-initial rounded-circle bg-danger">
                            <i class="bx bx-x text-white fs-1"></i>
                        </span>
                    </div>
                    <h5 class="fw-bold text-danger mb-2">Tidak Dapat Memberikan Persetujuan</h5>
                    <p class="text-muted mb-0 mx-auto" style="max-width: 500px;">
                        Anda tidak memiliki akses untuk memberikan persetujuan pada berita acara ini.
                        Pastikan Anda terdaftar sebagai Dosen Penguji (bukan Ketua Penguji) untuk ujian ini.
                    </p>
                    <a href="{{ route('admin.berita-acara-ujian-hasil.show', $beritaAcara) }}"
                        class="btn btn-outline-danger mt-4">
                        <i class="bx bx-arrow-back me-1"></i> Kembali ke Detail
                    </a>
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
        const formApprove = document.getElementById('formApprovePenguji');
        if (formApprove) {
            formApprove.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Validasi Digital',
                    html: `
                        <div class="text-start">
                            <p class="mb-3">Anda akan memberikan persetujuan sebagai <strong>Dosen Penguji</strong> untuk:</p>
                            <div class="p-3 bg-light rounded-3 border mb-3">
                                <div class="fw-bold text-dark">{{ $mahasiswa->name }}</div>
                                <div class="small text-muted mb-2">NIM: {{ $mahasiswa->nim }}</div>
                                <div class="x-small text-muted italic">Ujian Hasil: {{ \Carbon\Carbon::parse($jadwal->tanggal_ujian)->isoFormat('D MMMM Y') }}</div>
                            </div>
                            <div class="alert alert-warning border-0 small mb-0 d-flex gap-2">
                                <i class="bx bx-info-circle fs-4"></i>
                                <div>
                                    Persetujuan ini akan tercatat secara permanen di server log. Pastikan Anda telah memberikan nilai jika diperlukan.
                                </div>
                            </div>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="bx bx-check-circle me-1"></i> Ya, validasi sekarang',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#696cff',
                    cancelButtonColor: '#8592a3',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'btn btn-primary px-4',
                        cancelButton: 'btn btn-secondary px-4'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Sedang Memproses...',
                            html: 'Menyimpan tanda tangan digital Anda',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        this.submit();
                    }
                });
            });
        }
    </script>
@endpush
