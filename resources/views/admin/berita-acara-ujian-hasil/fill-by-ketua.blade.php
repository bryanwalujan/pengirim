{{-- filepath: resources/views/admin/berita-acara-ujian-hasil/fill-by-ketua.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Tandatangani - Berita Acara Ujian Hasil')

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
                <li class="breadcrumb-item active" aria-current="page">Tandatangani</li>
            </ol>
        </nav>

        @php
            $jadwal = $beritaAcara->jadwalUjianHasil;
            $pendaftaran = $jadwal->pendaftaranUjianHasil;
            $mahasiswa = $pendaftaran->user;
        @endphp

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-pen me-2"></i>Tandatangani Berita Acara
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- Alert --}}
                        <div class="alert alert-info mb-4">
                            <i class="bx bx-info-circle me-2"></i>
                            Dengan menandatangani, Anda menyatakan bahwa ujian hasil skripsi telah dilaksanakan sesuai prosedur.
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
                            <div class="col-12">
                                <label class="form-label text-muted">Judul Skripsi</label>
                                <p class="mb-0">{{ $beritaAcara->judul_skripsi ?? '-' }}</p>
                            </div>
                        </div>

                        <hr>

                        {{-- Penguji Signatures --}}
                        <div class="mb-4">
                            <label class="form-label text-muted">TTD Penguji</label>
                            <div class="row">
                                @foreach ($beritaAcara->ttd_dosen_penguji ?? [] as $sig)
                                    <div class="col-md-6 mb-2">
                                        <div class="d-flex align-items-center p-2 rounded bg-success-subtle">
                                            <i class="bx bx-check-circle text-success me-2"></i>
                                            <div>
                                                <strong>{{ $sig['dosen_name'] }}</strong>
                                                <small class="d-block text-muted">{{ $sig['posisi'] }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <hr>

                        {{-- Form --}}
                        <form action="{{ route('admin.berita-acara-ujian-hasil.store-fill-by-ketua', $beritaAcara) }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label">Catatan Tambahan <small class="text-muted">(Opsional)</small></label>
                                <textarea name="catatan_tambahan" class="form-control @error('catatan_tambahan') is-invalid @enderror" 
                                    rows="4" placeholder="Catatan atau keterangan tambahan">{{ old('catatan_tambahan') }}</textarea>
                                @error('catatan_tambahan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-warning mb-4">
                                <i class="bx bx-error me-2"></i>
                                <strong>Perhatian:</strong> Setelah menandatangani, berita acara akan menjadi final dan PDF akan digenerate otomatis.
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-check me-1"></i>Tandatangani
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
