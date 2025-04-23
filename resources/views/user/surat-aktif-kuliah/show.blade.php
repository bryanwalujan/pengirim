@extends('layouts.user.app')

@section('title', 'Detail Surat Aktif Kuliah')

@section('main')
    <div class="page-title light-background">
        <div class="container">
            <h1>Detail Surat Aktif Kuliah</h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.surat-aktif-kuliah.index') }}">Surat Aktif Kuliah</a></li>
                    <li class="current">Detail Surat</li>
                </ol>
            </nav>
        </div>
    </div>

    <section id="services" class="detail-surat section">
        <div class="container" data-aos="fade-up">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0 text-center">Detail Pengajuan Surat Aktif Kuliah</h4>
                        </div>
                        <div class="card-body">
                            @php
                                $statusClass = match ($surat->status ?? 'diajukan') {
                                    'disetujui', 'siap_diambil', 'sudah_diambil' => 'success',
                                    'ditolak' => 'danger',
                                    default => 'warning',
                                };
                            @endphp
                            <div class="alert alert-{{ $statusClass }} text-center">
                                <h5 class="alert-heading">Status:
                                    <strong class="text-uppercase">{{ $surat->status ?? 'diajukan' }}</strong>
                                </h5>
                                @if ($surat->status()->first()?->catatan_admin)
                                    <hr>
                                    <p class="mb-0"><strong>Catatan Admin:</strong>
                                        {{ $surat->status()->first()->catatan_admin }}</p>
                                @endif
                            </div>
                            @if ($surat->status === 'siap_diambil')
                                <div class="alert alert-info mt-4">
                                    <form action="{{ route('user.surat-aktif-kuliah.confirm-taken', $surat->id) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        <p>Jika Anda sudah mengambil surat ini, silakan konfirmasi:</p>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle"></i> Konfirmasi Sudah Diambil
                                        </button>
                                    </form>
                                </div>
                            @endif
                            @if ($surat->status === 'diajukan' && auth()->user()->hasRole('staff'))
                                <div class="mb-3">
                                    <label class="form-label">Nomor Surat</label>
                                    <div class="input-group">
                                        <input type="text" name="nomor_surat" class="form-control"
                                            value="{{ old('nomor_surat', explode('/', $surat->nomor_surat)[0] ?? '') }}"
                                            placeholder="Contoh: 001">
                                        <span class="input-group-text">/UN41.2/TI/{{ date('Y') }}</span>
                                    </div>
                                    <small class="text-muted">Biarkan kosong untuk generate otomatis</small>
                                </div>
                            @endif

                            <div class="mb-4">
                                <h5 class="section-title border-bottom pb-2">Informasi Mahasiswa</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" value="{{ $surat->mahasiswa->name }}"
                                            readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">NIM</label>
                                        <input type="text" class="form-control"
                                            value="{{ $surat->mahasiswa->nim ?? '-' }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Program Studi</label>
                                        <input type="text" class="form-control"
                                            value="{{ $surat->mahasiswa->prodi ?? '-' }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Semester</label>
                                        <input type="text" class="form-control"
                                            value="{{ $surat->mahasiswa->semester ?? '-' }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5 class="section-title border-bottom pb-2">Detail Pengajuan</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tahun Ajaran</label>
                                        <input type="text" class="form-control"
                                            value="{{ $surat->tahun_ajaran ?? '-' }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Semester</label>
                                        <input type="text" class="form-control"
                                            value="{{ ucfirst($surat->semester ?? '-') }}" readonly>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Tujuan Pengajuan</label>
                                        <textarea class="form-control" rows="3" readonly>{{ $surat->tujuan_pengajuan ?? '-' }}</textarea>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Keterangan Tambahan</label>
                                        <textarea class="form-control" rows="2" readonly>{{ $surat->keterangan_tambahan ?? '-' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            @if ($surat->file_pendukung_path)
                                <div class="mb-4">
                                    <h5 class="section-title border-bottom pb-2">Dokumen Pendukung</h5>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-file-earmark-text me-2 fs-4"></i>
                                        <a href="{{ Storage::url($surat->file_pendukung_path) }}" target="_blank"
                                            class="text-decoration-none">
                                            Lihat Dokumen Pendukung
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <div class="mb-4">
                                <h5 class="section-title border-bottom pb-2">File Surat</h5>
                                @if ($surat->file_surat_path && in_array($surat->status, ['siap_diambil', 'sudah_diambil']))
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-file-earmark-pdf me-2 fs-4"></i>
                                        <a href="{{ route('user.surat-aktif-kuliah.download', $surat->id) }}"
                                            class="text-decoration-none">
                                            Unduh Surat Aktif Kuliah
                                        </a>
                                    </div>
                                @else
                                    <p class="text-muted">File surat belum tersedia. Silakan tunggu hingga status berubah
                                        menjadi "Siap Diambil" atau "Sudah Diambil".</p>
                                @endif
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('user.surat-aktif-kuliah.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                                </a>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        .section-title {
            color: #4154f1;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .timeline {
            position: relative;
            padding-left: 1rem;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
            padding-left: 1.5rem;
            border-left: 2px solid #dee2e6;
        }

        .timeline-item:last-child {
            border-left: 2px solid transparent;
        }

        .timeline-marker {
            position: absolute;
            left: -7px;
            top: 0;
            z-index: 1;
        }

        .timeline-content {
            margin-left: 0.5rem;
        }
    </style>
@endpush
