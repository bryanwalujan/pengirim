{{-- filepath: resources/views/admin/berita-acara-ujian-hasil/create.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Buat Berita Acara Ujian Hasil')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        
        {{-- Header Section --}}
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.jadwal-ujian-hasil.show', $jadwal) }}">Jadwal Ujian</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Buat Berita Acara</li>
            </ol>
        </nav>

        {{-- Header Section --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div class="me-3">
                <h4 class="fw-bold mb-1">Buat Berita Acara</h4>
                <p class="text-muted mb-0">Halaman pembuatan dokumen berita acara pelaksanaan ujian hasil.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.jadwal-ujian-hasil.show', $jadwal) }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
        </div>

        @php
            $pendaftaran = $jadwal->pendaftaranUjianHasil;
            $mahasiswa = $pendaftaran->user;
        @endphp

        <div class="row">
            {{-- Left Column: Main Content --}}
            <div class="col-lg-8">
                
                {{-- 1. Kartu Informasi (Read Only Context) --}}
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="avatar avatar-md me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-user fs-4"></i>
                                </span>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">Informasi Peserta Ujian</h5>
                                <small class="text-muted">Data mahasiswa dan jadwal pelaksanaan</small>
                            </div>
                        </div>

                        <div class="row g-4">
                            {{-- Data Mahasiswa --}}
                            <div class="col-md-6">
                                <label class="d-block text-uppercase text-muted fs-7 fw-bold mb-2">Nama Mahasiswa</label>
                                <div class="d-flex align-items-center">
                                    <div class="fw-semibold text-dark fs-6">{{ $mahasiswa->name }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="d-block text-uppercase text-muted fs-7 fw-bold mb-2">NIM</label>
                                <div class="font-monospace text-dark">{{ $mahasiswa->nim }}</div>
                            </div>
                            
                            {{-- Judul Skripsi --}}
                            <div class="col-12">
                                <label class="d-block text-uppercase text-muted fs-7 fw-bold mb-2">Judul Skripsi</label>
                                <div class="p-3 bg-light rounded text-secondary fst-italic border">
                                    "{{ $pendaftaran->judul_skripsi ?? $pendaftaran->komisiHasil->judul_skripsi ?? '-' }}"
                                </div>
                            </div>

                            <div class="col-12"><hr class="my-0"></div>

                            {{-- Data Jadwal --}}
                            <div class="col-md-6">
                                <label class="d-block text-uppercase text-muted fs-7 fw-bold mb-2">Waktu Pelaksanaan</label>
                                <div class="d-flex align-items-center text-dark">
                                    <i class="bx bx-calendar me-2 text-primary"></i>
                                    <span class="fw-medium">{{ \Carbon\Carbon::parse($jadwal->tanggal_ujian)->isoFormat('dddd, D MMMM Y') }}</span>
                                </div>
                                <div class="d-flex align-items-center text-dark mt-1 ms-4 ps-1">
                                    <small class="fw-bold">{{ $jadwal->waktu_mulai }} - {{ $jadwal->waktu_selesai }} WITA</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="d-block text-uppercase text-muted fs-7 fw-bold mb-2">Lokasi Terjadwal</label>
                                <div class="d-flex align-items-center text-dark">
                                    <i class="bx bx-map me-2 text-danger"></i>
                                    <span>{{ $jadwal->ruangan ?? 'Belum ditentukan' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Form Input Section --}}
                <form action="{{ route('admin.berita-acara-ujian-hasil.store', $jadwal) }}" method="POST">
                    @csrf
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom p-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-md me-3">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="bx bx-edit fs-4"></i>
                                    </span>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Input Data Berita Acara</h5>
                                    <small class="text-muted">Silahkan lengkapi detail pelaksanaan ujian berikut</small>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            {{-- Ruangan Input --}}
                            <div class="mb-4">
                                <label for="ruangan" class="form-label fw-bold">Konfirmasi Ruangan Ujian</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bx bx-building"></i></span>
                                    <input type="text" id="ruangan" name="ruangan" 
                                           class="form-control form-control-lg @error('ruangan') is-invalid @enderror" 
                                           value="{{ old('ruangan', $jadwal->ruangan ?? 'Ruangan Ujian Teknik Informatika') }}"
                                           placeholder="Masukkan nama ruangan...">
                                    @error('ruangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text text-muted">Sesuaikan jika pelaksanaan ujian pindah ruangan.</div>
                            </div>

                            {{-- Catatan Input --}}
                            <div class="mb-4">
                                <label for="catatan_tambahan" class="form-label fw-bold">Catatan Tambahan <span class="fw-normal text-muted fst-italic">(Opsional)</span></label>
                                <textarea id="catatan_tambahan" name="catatan_tambahan" 
                                          class="form-control @error('catatan_tambahan') is-invalid @enderror" 
                                          rows="4" 
                                          placeholder="Tuliskan catatan khusus terkait pelaksanaan ujian jika ada...">{{ old('catatan_tambahan') }}</textarea>
                                @error('catatan_tambahan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-primary d-flex align-items-center" role="alert">
                                <i class="bx bx-info-circle me-2 fs-5"></i>
                                <div>
                                    Setelah dibuat, berita acara akan masuk ke status <strong>Draft</strong> dan perlu disetujui oleh dosen penguji.
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-light p-4 d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="bx bx-check-circle me-1"></i> Simpan & Buat Berita Acara
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Right Column: Sidebar --}}
            <div class="col-lg-4">
                {{-- Tim Penguji Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="card-title m-0 fw-bold text-uppercase fs-7 text-muted">Tim Penguji</h6>
                    </div>
                    <ul class="list-group list-group-flush">
                        @forelse ($jadwal->dosenPenguji()->orderByRaw("CASE WHEN posisi = 'Ketua Penguji' THEN 1 WHEN posisi = 'Penguji 1' THEN 2 WHEN posisi = 'Penguji 2' THEN 3 ELSE 4 END")->get() as $penguji)
                            <li class="list-group-item d-flex align-items-center py-3">
                                <div class="avatar avatar-sm me-3 flex-shrink-0">
                                    <div class="avatar-initial rounded-circle bg-label-{{ $penguji->pivot->posisi === 'Ketua Penguji' ? 'primary' : 'secondary' }}">
                                        {{ substr($penguji->name, 0, 1) }}
                                    </div>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold text-truncate">{{ $penguji->name }}</div>
                                    <div class="small text-muted">{{ $penguji->pivot->posisi }}</div>
                                </div>
                                @if($penguji->pivot->posisi === 'Ketua Penguji')
                                    <i class="bx bxs-star text-warning" data-bs-toggle="tooltip" title="Ketua Penguji"></i>
                                @endif
                            </li>
                        @empty
                            <li class="list-group-item text-center py-4 text-muted">
                                <i class="bx bx-user-x fs-3 mb-1"></i>
                                <div>Belum ada penguji</div>
                            </li>
                        @endforelse
                    </ul>
                </div>

                {{-- Help/Workflow Card --}}
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body p-4 position-relative overflow-hidden">
                        <div class="position-absolute top-0 end-0 opacity-10 me-n3 mt-n3">
                            <i class="bx bx-file mx-2" style="font-size: 8rem;"></i>
                        </div>
                        <h5 class="card-title text-white fw-bold mb-3"><i class="bx bx-bulb me-2"></i>Alur Proses</h5>
                        <ul class="list-unstyled mb-0 position-relative" style="z-index: 1;">
                            <li class="mb-2 d-flex">
                                <i class="bx bx-check-circle me-2 mt-1"></i>
                                <span>Buat draft berita acara (Langkah saat ini)</span>
                            </li>
                            <li class="mb-2 d-flex">
                                <i class="bx bx-time-five me-2 mt-1 opacity-75"></i>
                                <span class="opacity-75">Persetujuan Dosen Penguji</span>
                            </li>
                            <li class="mb-2 d-flex">
                                <i class="bx bx-pen me-2 mt-1 opacity-75"></i>
                                <span class="opacity-75">Keputusan & TTD Ketua Penguji</span>
                            </li>
                            <li class="d-flex">
                                <i class="bx bxs-file-pdf me-2 mt-1 opacity-75"></i>
                                <span class="opacity-75">Selesai & Generate PDF</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
