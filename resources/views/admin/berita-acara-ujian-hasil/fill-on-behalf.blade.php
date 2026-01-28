{{-- filepath: resources/views/admin/berita-acara-ujian-hasil/fill-on-behalf.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Tandatangani Atas Nama Ketua - Berita Acara Ujian Hasil')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Breadcrumb --}}
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.berita-acara-ujian-hasil.index') }}">Berita Acara</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Tandatangani Atas Nama Ketua</li>
            </ol>
        </nav>

        @php
            $jadwal = $beritaAcara->jadwalUjianHasil;
            $pendaftaran = $jadwal->pendaftaranUjianHasil;
            $mahasiswa = $pendaftaran->user;
            $ketuaPenguji = $jadwal->getKetuaPenguji();
        @endphp

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-edit me-2"></i>Tandatangani Atas Nama Ketua Penguji
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- Alert --}}
                        <div class="alert alert-danger mb-4">
                            <i class="bx bx-error-circle me-2"></i>
                            <strong>Override Mode:</strong> Anda akan menandatangani berita acara atas nama Ketua Penguji. 
                            Tindakan ini akan dicatat dalam sistem.
                        </div>

                        {{-- Ketua Penguji Info --}}
                        <div class="alert alert-secondary mb-4">
                            <strong>Ketua Penguji:</strong> {{ $ketuaPenguji?->name ?? 'Tidak diketahui' }}
                        </div>

                        {{-- Mahasiswa Info --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Nama Mahasiswa</label>
                                <p class="fw-semibold mb-0">{{ $mahasiswa->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">NIM</label>
                                <p class="mb-0">{{ $mahasiswa->nim }}</p>
                            </div>
                        </div>

                        <hr>

                        {{-- Form --}}
                        <form action="{{ route('admin.berita-acara-ujian-hasil.store-fill-on-behalf', $beritaAcara) }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label">Catatan Tambahan <small class="text-muted">(Opsional)</small></label>
                                <textarea name="catatan_tambahan" class="form-control" rows="3" 
                                    placeholder="Catatan atau keterangan tambahan">{{ old('catatan_tambahan') }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Alasan Override <span class="text-danger">*</span></label>
                                <textarea name="alasan_override" class="form-control @error('alasan_override') is-invalid @enderror" 
                                    rows="2" placeholder="Jelaskan alasan mengapa Anda menandatangani atas nama ketua penguji" required>{{ old('alasan_override') }}</textarea>
                                @error('alasan_override')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Alasan ini akan dicatat untuk keperluan audit.</small>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning">
                                    <i class="bx bx-check me-1"></i>Tandatangani Atas Nama Ketua
                                </button>
                                <a href="{{ route('admin.berita-acara-ujian-hasil.show', $beritaAcara) }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-x me-1"></i>Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
