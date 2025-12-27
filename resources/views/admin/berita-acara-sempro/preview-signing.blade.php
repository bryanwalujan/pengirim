{{-- filepath: resources/views/admin/berita-acara-sempro/preview-signing.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Preview & Tanda Tangan Berita Acara')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @php
            $jadwal = $beritaAcara->jadwalSeminarProposal;
            $pendaftaran = $jadwal->pendaftaranSeminarProposal;
            $mahasiswa = $pendaftaran->user;
            $pembimbing = $beritaAcara->dosenPembimbingPengisi;
        @endphp

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <span class="text-muted fw-light">Berita Acara /</span> Preview & Tanda Tangan
                </h4>
                <p class="text-muted mb-0">
                    <i class="bx bx-pen me-1"></i>
                    Sebagai Ketua Penguji
                </p>
            </div>
            <a href="{{ route('admin.berita-acara-sempro.show', $beritaAcara) }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>

        {{-- Alert Instruksi --}}
        <div class="alert alert-warning alert-dismissible mb-4" role="alert">
            <h6 class="alert-heading mb-1">
                <i class="bx bx-info-circle me-2"></i>Perhatian - Tanda Tangan Digital
            </h6>
            <div class="small">
                <p class="mb-2">Sebagai <strong>Ketua Penguji</strong>, Anda diminta untuk:</p>
                <ol class="mb-2 ps-3">
                    <li>Memeriksa kembali isi berita acara di bawah ini</li>
                    <li>Memastikan semua data sudah benar dan sesuai</li>
                    <li>Menandatangani secara digital dengan klik tombol "Tandatangani"</li>
                </ol>
                <div class="bg-warning bg-opacity-10 p-2 rounded">
                    <i class="bx bx-lock-alt me-1"></i>
                    <strong>Setelah ditandatangani:</strong>
                    <ul class="mb-0 ps-3 mt-1">
                        <li>Berita acara tidak dapat diubah lagi</li>
                        <li>PDF final akan digenerate secara otomatis</li>
                        <li>Status ujian akan berubah menjadi "Selesai"</li>
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <div class="row">
            {{-- Left Column: Preview Data --}}
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
                        </table>
                    </div>
                </div>

                {{-- Isi Berita Acara --}}
                <div class="card mb-4">
                    <div class="card-header bg-label-primary">
                        <h5 class="mb-0">
                            <i class="bx bx-clipboard me-2"></i>Isi Berita Acara
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- Catatan Kejadian --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small mb-2">
                                1. Catatan Kejadian Selama Seminar
                            </label>
                            <div class="p-3 bg-light rounded">
                                {!! $beritaAcara->catatan_kejadian_badge !!}
                            </div>
                        </div>

                        {{-- Kesimpulan --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small mb-2">
                                2. Kesimpulan Kelayakan Seminar Proposal Skripsi
                            </label>
                            <div class="p-3 bg-light rounded">
                                {!! $beritaAcara->keputusan_badge !!}
                                <div class="text-muted small mt-2">
                                    {{ $beritaAcara->keputusan_description }}
                                </div>
                            </div>
                        </div>

                        {{-- Catatan Tambahan --}}
                        @if ($beritaAcara->catatan_tambahan)
                            <div>
                                <label class="form-label fw-bold text-muted small mb-2">
                                    3. Catatan Tambahan
                                </label>
                                <div class="p-3 bg-light rounded">
                                    {{ $beritaAcara->catatan_tambahan }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Info Pengisi --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-info-circle me-2"></i>Informasi Pengisian
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded-circle bg-label-success">
                                    <i class="bx bx-check"></i>
                                </span>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $pembimbing->name }}</div>
                                <small class="text-muted">
                                    Dosen Pembimbing • Diisi pada
                                    {{ $beritaAcara->diisi_pembimbing_at->isoFormat('D MMM Y, HH:mm') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Daftar Penguji & Lembar Catatan --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-group me-2"></i>Dewan Penguji & Lembar Catatan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Dosen</th>
                                        <th width="25%">Lembar Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jadwal->dosenPenguji()->wherePivot('status_kehadiran', 'Hadir')->get() as $index => $dosen)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        {{ $dosen->name }}
                                                        @if ($dosen->pivot->posisi === 'ketua')
                                                            <span class="badge bg-label-primary ms-1">Ketua</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $catatan = $beritaAcara
                                                        ->lembarCatatan()
                                                        ->where('dosen_id', $dosen->id)
                                                        ->first();
                                                @endphp
                                                @if ($catatan)
                                                    <span class="badge bg-success">
                                                        <i class="bx bx-check me-1"></i>Sudah Diisi
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="bx bx-time me-1"></i>Belum Diisi
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

            {{-- Right Column: PDF Preview --}}
            <div class="col-lg-6">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header bg-label-secondary">
                        <h5 class="mb-0">
                            <i class="bx bx-file-blank me-2"></i>Preview PDF Berita Acara
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="pdf-preview-container">
                            {{-- ✅ Update: gunakan data URL langsung di src --}}
                            <iframe src="{{ $pdfPreview }}" width="100%" height="800px" style="border: none;"
                                id="pdfPreview"></iframe>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="text-center text-muted small">
                            <i class="bx bx-info-circle me-1"></i>
                            Preview PDF yang akan digenerate setelah ditandatangani
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Tanda Tangan --}}
        <div class="card mt-4">
            <div class="card-body">
                <form action="{{ route('admin.berita-acara-sempro.sign-ketua', $beritaAcara) }}" method="POST"
                    id="formSignBA">
                    @csrf

                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="form-check">
                                <input class="form-check-input @error('confirmation') is-invalid @enderror"
                                    type="checkbox" id="confirmation" name="confirmation" value="1" required>
                                <label class="form-check-label" for="confirmation">
                                    <strong>Saya menyatakan bahwa:</strong>
                                    <ul class="mb-0 mt-2 small">
                                        <li>Data berita acara di atas sudah benar dan sesuai</li>
                                        <li>Saya bertanggung jawab atas tanda tangan digital ini</li>
                                        <li>Berita acara yang sudah ditandatangani tidak dapat diubah lagi</li>
                                    </ul>
                                </label>
                                @error('confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="submit" class="btn btn-success btn-lg w-100" id="btnSign" disabled>
                                <i class="bx bx-pen me-2"></i>Tandatangani Berita Acara
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .pdf-preview-container {
            background: #f5f5f5;
            min-height: 800px;
        }

        .sticky-top {
            position: sticky;
            z-index: 1020;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Enable/disable sign button based on checkbox
        const confirmationCheckbox = document.getElementById('confirmation');
        const btnSign = document.getElementById('btnSign');

        confirmationCheckbox.addEventListener('change', function() {
            btnSign.disabled = !this.checked;
        });

        // Form submission with confirmation
        document.getElementById('formSignBA').addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Konfirmasi Tanda Tangan',
                html: `
                    <div class="text-start">
                        <p class="mb-3">Anda akan menandatangani berita acara untuk:</p>
                        <div class="alert alert-info mb-3">
                            <strong>Mahasiswa:</strong> {{ $mahasiswa->name }} ({{ $mahasiswa->nim }})<br>
                            <strong>Tanggal Ujian:</strong> {{ $jadwal->tanggal_ujian->isoFormat('D MMMM Y') }}
                        </div>
                        <div class="alert alert-warning mb-0">
                            <i class="bx bx-info-circle me-1"></i>
                            <strong>Perhatian:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Berita acara yang sudah ditandatangani <strong>tidak dapat diubah</strong></li>
                                <li>PDF final akan digenerate secara otomatis</li>
                                <li>Status ujian akan berubah menjadi <strong>"Selesai"</strong></li>
                            </ul>
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="bx bx-pen me-1"></i> Ya, Tandatangani',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
                customClass: {
                    popup: 'swal-wide'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Memproses...',
                        html: 'Sedang menandatangani dan generate PDF berita acara',
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
    </script>
@endpush
