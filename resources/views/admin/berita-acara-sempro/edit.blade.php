{{-- filepath: /c:/laragon/www/eservice-app/resources/views/admin/berita-acara-sempro/edit.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Edit Berita Acara')

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
                    <span class="text-muted fw-light">Berita Acara /</span> Edit Draft
                </h4>
                <p class="text-muted mb-0">
                    <i class="bx bx-edit me-1"></i>
                    Ubah draft berita acara sebelum ditandatangani
                </p>
            </div>
            <a href="{{ route('admin.berita-acara-sempro.show', $beritaAcara) }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>

        {{-- Alert Info --}}
        <div class="alert alert-info alert-dismissible mb-4" role="alert">
            <h6 class="alert-heading mb-1">
                <i class="bx bx-info-circle me-2"></i>Informasi Edit Draft
            </h6>
            <div class="small">
                <ul class="mb-0 ps-3">
                    <li>Hanya <strong>Catatan Tambahan</strong> yang dapat diubah di halaman ini</li>
                    <li><strong>Catatan Kejadian</strong> dan <strong>Kesimpulan</strong> akan diisi oleh Dosen Pembimbing
                    </li>
                    <li>Setelah berita acara ditandatangani, tidak dapat diubah lagi</li>
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        {{-- Info Mahasiswa --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bx bx-user me-2"></i>Informasi Mahasiswa & Ujian
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted small">Mahasiswa</label>
                        <div>{{ $mahasiswa->name }}</div>
                        <small class="text-muted">NIM: {{ $mahasiswa->nim }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted small">Dosen Pembimbing</label>
                        <div>{{ $pembimbing->name }}</div>
                        <small class="text-muted">NIP: {{ $pembimbing->nip ?? '-' }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted small">Tanggal Ujian</label>
                        <div>{{ $jadwal->tanggal_ujian->isoFormat('dddd, D MMMM Y') }}</div>
                        <small class="text-muted">{{ $jadwal->waktu_mulai }} - {{ $jadwal->waktu_selesai }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted small">Ruangan</label>
                        <div>{{ $jadwal->ruangan }}</div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold text-muted small">Judul Proposal</label>
                        <div class="text-wrap">{{ $pendaftaran->judul_skripsi }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Current BA Content (Read-Only) --}}
        @if ($beritaAcara->isFilledByPembimbing())
            <div class="card mb-4">
                <div class="card-header bg-label-primary">
                    <h5 class="mb-0">
                        <i class="bx bx-clipboard me-2"></i>Isi Berita Acara (Tidak Dapat Diubah)
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
                    <div class="mb-0">
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
                </div>
            </div>
        @else
            <div class="alert alert-warning mb-4">
                <i class="bx bx-info-circle me-2"></i>
                <strong>Catatan Kejadian</strong> dan <strong>Kesimpulan</strong> belum diisi oleh Dosen Pembimbing.
            </div>
        @endif

        {{-- Form Edit --}}
        <form action="{{ route('admin.berita-acara-sempro.update', $beritaAcara) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-note me-2"></i>Catatan Tambahan (Dapat Diubah)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="catatan_tambahan" class="form-label">
                            Catatan Tambahan
                            <small class="text-muted">(Opsional)</small>
                        </label>
                        <textarea name="catatan_tambahan" id="catatan_tambahan"
                            class="form-control @error('catatan_tambahan') is-invalid @enderror" rows="4"
                            placeholder="Catatan tambahan jika diperlukan (misal: informasi teknis, kondisi khusus, dll)" maxlength="1000">{{ old('catatan_tambahan', $beritaAcara->catatan_tambahan) }}</textarea>
                        @error('catatan_tambahan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <span id="charCount">{{ strlen($beritaAcara->catatan_tambahan ?? '') }}</span> / 1000 karakter
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bx bx-info-circle text-primary me-2"></i>
                            <small class="text-muted">
                                Perubahan hanya berlaku untuk catatan tambahan
                            </small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.berita-acara-sempro.show', $beritaAcara) }}"
                                class="btn btn-outline-secondary">
                                <i class="bx bx-x me-1"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Character counter
        const textarea = document.getElementById('catatan_tambahan');
        const charCount = document.getElementById('charCount');

        if (textarea && charCount) {
            textarea.addEventListener('input', function() {
                const length = this.value.length;
                charCount.textContent = length;

                // Visual feedback saat mendekati limit
                if (length > 900) {
                    charCount.classList.add('text-warning');
                } else {
                    charCount.classList.remove('text-warning');
                }

                if (length >= 1000) {
                    charCount.classList.add('text-danger');
                    charCount.classList.remove('text-warning');
                }
            });
        }
    </script>
@endpush
