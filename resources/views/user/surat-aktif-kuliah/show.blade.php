@extends('layouts.user.app')

@section('title', 'Detail Surat Aktif Kuliah')

@section('main')
    <!-- Page Title -->
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
    </div><!-- End Page Title -->

    <!-- Detail Section -->
    <section id="detail-surat" class="detail-surat section">
        <div class="container" data-aos="fade-up">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0 text-center">Detail Pengajuan Surat Aktif Kuliah</h4>
                        </div>
                        <div class="card-body">
                            <!-- Status Surat -->
                            {{-- <div
                                class="alert alert-{{ $surat->status->status == 'disetujui' ? 'success' : ($surat->status->status == 'ditolak' ? 'danger' : 'warning') }} text-center">
                                <h5 class="alert-heading">Status:
                                    <strong class="text-uppercase">{{ $surat->status->status }}</strong>
                                </h5>
                                @if ($surat->status->catatan_admin)
                                    <hr>
                                    <p class="mb-0"><strong>Catatan Admin:</strong> {{ $surat->status->catatan_admin }}
                                    </p>
                                @endif
                            </div> --}}


                            <!-- Informasi Surat -->
                            <div class="mb-4">
                                <h5 class="section-title border-bottom pb-2">Informasi Surat</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nomor Surat</label>
                                        <input type="text" class="form-control"
                                            value="{{ $surat->nomor_surat ?? 'Belum ada nomor surat' }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Surat</label>
                                        <input type="text" class="form-control"
                                            value="{{ $surat->tanggal_surat ? $surat->tanggal_surat->format('d F Y') : 'Belum ada tanggal' }}"
                                            readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Mahasiswa -->
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
                                        <input type="text" class="form-control" value="{{ $surat->mahasiswa->nim }}"
                                            readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Program Studi</label>
                                        <input type="text" class="form-control" value="{{ $surat->mahasiswa->prodi }}"
                                            readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Semester</label>
                                        <input type="text" class="form-control" value="{{ $surat->mahasiswa->semester }}"
                                            readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Pengajuan -->
                            <div class="mb-4">
                                <h5 class="section-title border-bottom pb-2">Detail Pengajuan</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tahun Ajaran</label>
                                        <input type="text" class="form-control" value="{{ $surat->tahun_ajaran }}"
                                            readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Semester</label>
                                        <input type="text" class="form-control" value="{{ ucfirst($surat->semester) }}"
                                            readonly>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Tujuan Pengajuan</label>
                                        <textarea class="form-control" rows="3" readonly>{{ $surat->tujuan_pengajuan }}</textarea>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Keterangan Tambahan</label>
                                        <textarea class="form-control" rows="2" readonly>{{ $surat->keterangan_tambahan ?? '-' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Dokumen Pendukung -->
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

                            <!-- Tombol Aksi -->
                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('user.surat-aktif-kuliah.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                                </a>

                                @if ($surat->file_surat_path)
                                    <a href="{{ route('user.surat-aktif-kuliah.download', $surat->id) }}"
                                        class="btn btn-primary">
                                        <i class="bi bi-download"></i> Download Surat
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Tracking Surat -->
                    <div class="card shadow mt-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Riwayat Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @foreach ($surat->trackings->sortByDesc('created_at') as $tracking)
                                    <div class="timeline-item">
                                        <div class="timeline-marker">
                                            <i
                                                class="bi bi-circle-fill {{ $tracking->aksi == 'disetujui' ? 'text-success' : ($tracking->aksi == 'ditolak' ? 'text-danger' : 'text-primary') }}"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="text-capitalize">{{ $tracking->aksi }}</h6>
                                            <p class="text-muted small mb-1">
                                                {{ $tracking->created_at->format('d F Y H:i') }}</p>
                                            @if ($tracking->keterangan)
                                                <p class="mb-0">{{ $tracking->keterangan }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- End Detail Section -->
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
