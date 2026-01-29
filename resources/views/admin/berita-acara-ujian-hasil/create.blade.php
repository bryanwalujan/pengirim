{{-- filepath: resources/views/admin/berita-acara-ujian-hasil/create.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Buat Berita Acara Ujian Hasil')

@push('styles')
<style>
    .rounded-xl { border-radius: 0.75rem !important; }
    .rounded-2xl { border-radius: 1rem !important; }
    .x-small { font-size: 0.7rem !important; }
    .fs-small { font-size: 0.8rem !important; }
    .leading-relaxed { line-height: 1.6 !important; }

    /* Card Hover Effects */
    .card { transition: all 0.3s ease; }
    
    /* Sneat specific overrides for premium feel */
    .bg-label-amber { background-color: #fff2e0 !important; color: #ffab00 !important; }
    .bg-label-primary-subtle { background-color: #e7e7ff !important; color: #696cff !important; }
</style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        
        {{-- Breadcrumb --}}
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style2 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.jadwal-ujian-hasil.show', $jadwal) }}">Jadwal Ujian</a>
                    </li>
                    <li class="breadcrumb-item active fw-bold" aria-current="page">Buat Berita Acara</li>
                </ol>
            </nav>
        </div>

        {{-- Header Section --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar avatar-lg">
                    <span class="avatar-initial rounded-2 bg-label-primary">
                        <i class="bx bx-file bx-sm"></i>
                    </span>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">Buat Berita Acara</h4>
                    <p class="text-muted mb-0">Inisialisasi dokumen berita acara pelaksanaan ujian hasil</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.jadwal-ujian-hasil.show', $jadwal) }}" class="btn btn-secondary text-nowrap">
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
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header border-bottom p-4">
                        <h5 class="mb-0 fw-bold"><i class="bx bx-info-circle me-2 text-primary"></i>Informasi Peserta Ujian</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="p-3 rounded-3 border bg-light h-100">
                                    <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Data Mahasiswa</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar">
                                            <span class="avatar-initial rounded bg-primary text-white">
                                                <i class="bx bx-user"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-bold fs-6">{{ $mahasiswa->name }}</div>
                                            <div class="text-muted small">{{ $mahasiswa->nim }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded-3 border bg-light h-100">
                                    <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Waktu & Tempat</label>
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="avatar">
                                            <span class="avatar-initial rounded bg-label-warning text-warning">
                                                <i class="bx bx-calendar"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-bold small">{{ \Carbon\Carbon::parse($jadwal->tanggal_ujian)->isoFormat('dddd, D MMMM Y') }}</div>
                                            <div class="text-muted small mb-1">
                                                {{ $jadwal->waktu_mulai }} - {{ $jadwal->waktu_selesai }} WITA
                                            </div>
                                            <span class="badge bg-white text-dark border shadow-sm rounded-pill x-small">
                                                <i class="bx bx-buildings me-1"></i> {{ $jadwal->ruangan ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-4 rounded-3 border bg-light">
                                    <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Judul Skripsi</label>
                                    <h6 class="fw-bold mb-0 leading-relaxed text-dark">
                                        {{ $pendaftaran->judul_skripsi ?? $pendaftaran->komisiHasil->judul_skripsi ?? '-' }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Form Input Section --}}
                <form action="{{ route('admin.berita-acara-ujian-hasil.store', $jadwal) }}" method="POST">
                    @csrf
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header border-bottom p-4">
                            <h5 class="mb-0 fw-bold"><i class="bx bx-edit me-2 text-warning"></i>Input Data Berita Acara</h5>
                        </div>

                        <div class="card-body p-4">
                            {{-- Ruangan Input --}}
                            <div class="mb-4">
                                <label for="ruangan" class="form-label fw-bold text-uppercase text-muted small">Konfirmasi Ruangan Ujian</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light border-end-0 ps-3"><i class="bx bx-building"></i></span>
                                    <input type="text" id="ruangan" name="ruangan" 
                                           class="form-control form-control-lg border-start-0 ps-2 @error('ruangan') is-invalid @enderror" 
                                           value="{{ old('ruangan', $jadwal->ruangan ?? 'Ruangan Ujian Teknik Informatika') }}"
                                           placeholder="Masukkan nama ruangan...">
                                    @error('ruangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text text-muted small ms-1">Sesuaikan jika pelaksanaan ujian pindah ruangan.</div>
                            </div>

                            {{-- Catatan Input --}}
                            <div class="mb-4">
                                <label for="catatan_tambahan" class="form-label fw-bold text-uppercase text-muted small">Catatan Tambahan <span class="fw-normal text-muted fst-italic text-lowercase">(Opsional)</span></label>
                                <textarea id="catatan_tambahan" name="catatan_tambahan" 
                                          class="form-control @error('catatan_tambahan') is-invalid @enderror" 
                                          rows="4" 
                                          placeholder="Tuliskan catatan khusus terkait pelaksanaan ujian jika ada...">{{ old('catatan_tambahan') }}</textarea>
                                @error('catatan_tambahan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-warning d-flex align-items-start gap-3 p-3 rounded-3" role="alert">
                                <i class="bx bx-info-circle fs-4 mt-1"></i>
                                <div>
                                    <div class="fw-bold mb-1">Konfirmasi Pembuatan</div>
                                    <div class="small">Setelah dibuat, berita acara akan masuk ke status <strong>"Menunggu TTD Penguji"</strong>. Pastikan data pendaftaran sudah benar sebelum melanjutkan.</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-light p-4 d-flex justify-content-end gap-2 border-top">
                            <button type="submit" class="btn btn-warning btn-lg px-4 fw-bold shadow-sm text-white">
                                <i class="bx bx-save me-1"></i> Simpan & Buat Berita Acara
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Right Column: Sidebar --}}
            <div class="col-lg-4">
                {{-- Tim Penguji Card --}}
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header border-bottom p-4">
                        <h5 class="mb-0 fw-bold"><i class="bx bx-group me-2 text-primary"></i>Tim Penguji</h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        @forelse ($jadwal->dosenPenguji()->orderByRaw("CASE WHEN posisi = 'Ketua Penguji' THEN 1 WHEN posisi = 'Penguji 1' THEN 2 WHEN posisi = 'Penguji 2' THEN 3 ELSE 4 END")->get() as $penguji)
                            @php
                                $isKetua = $penguji->pivot->posisi === 'Ketua Penguji';
                            @endphp
                            <li class="list-group-item d-flex align-items-center py-3 {{ $isKetua ? 'bg-label-primary-subtle' : '' }}">
                                <div class="avatar avatar-sm me-3 flex-shrink-0">
                                    <span class="avatar-initial rounded-circle {{ $isKetua ? 'bg-primary text-white' : 'bg-label-secondary text-secondary' }}">
                                        {{ strtoupper(substr($penguji->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-bold text-dark text-truncate">{{ $penguji->name }}</div>
                                    <div class="x-small text-uppercase text-muted fw-bold">{{ $penguji->pivot->posisi }}</div>
                                </div>
                                @if($isKetua)
                                    <i class="bx bxs-star text-warning" data-bs-toggle="tooltip" title="Ketua Penguji"></i>
                                @endif
                            </li>
                        @empty
                            <li class="list-group-item text-center py-5 text-muted">
                                <i class="bx bx-user-minus fs-1 mb-2"></i>
                                <div>Belum ada penguji</div>
                            </li>
                        @endforelse
                    </ul>
                </div>

                {{-- Workflow Card --}}
                <div class="card shadow-sm border-0 bg-primary text-white overflow-hidden">
                    <div class="card-header border-bottom border-white border-opacity-25 p-4">
                        <h5 class="mb-0 fw-bold text-white"><i class="bx bx-map me-2"></i>Alur Proses</h5>
                    </div>
                    <div class="card-body p-4 position-relative">
                        {{-- Background Decor --}}
                        <div class="position-absolute top-0 end-0 opacity-10 me-n3 mt-n3">
                            <i class="bx bx-file" style="font-size: 8rem;"></i>
                        </div>

                        <div class="workflow-timeline position-relative" style="z-index: 1;">
                            {{-- Step 1 --}}
                            <div class="d-flex mb-4">
                                <div class="me-3">
                                    <div class="avatar avatar-xs">
                                        <span class="avatar-initial rounded-circle bg-white text-primary fw-bold">1</span>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-white mb-1">Inisialisasi (Saat Ini)</h6>
                                    <div class="small text-white">Staff membuat draft berita acara.</div>
                                </div>
                            </div>
                            
                            {{-- Step 2 --}}
                            <div class="d-flex mb-4">
                                <div class="me-3">
                                    <div class="avatar avatar-xs">
                                        <span class="avatar-initial rounded-circle bg-white text-primary fw-bold">2</span>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-white mb-1">Validasi Penguji</h6>
                                    <div class="small text-white">Semua dosen penguji menandatangani dokumen secara digital.</div>
                                </div>
                            </div>

                            {{-- Step 3 --}}
                            <div class="d-flex mb-4">
                                <div class="me-3">
                                    <div class="avatar avatar-xs">
                                        <span class="avatar-initial rounded-circle bg-white text-primary fw-bold">3</span>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-white mb-1">Penyelesaian</h6>
                                    <div class="small text-white">Ketua Penguji mengisi hasil akhir dan mengesahkan dokumen.</div>
                                </div>
                            </div>

                            {{-- Step 4 --}}
                            <div class="d-flex">
                                <div class="me-3">
                                    <div class="avatar avatar-xs">
                                        <span class="avatar-initial rounded-circle bg-white text-primary fw-bold">4</span>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-white mb-1">Arsip PDF</h6>
                                    <div class="small text-white">Dokumen final digenerate dan diarsipkan.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
