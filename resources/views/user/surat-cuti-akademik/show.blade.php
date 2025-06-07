@extends('layouts.user.app')

@section('title', 'Detail Surat Cuti Akademik')

@push('styles')
    <style>
        .card {
            border-radius: 1.5rem;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            background: #fff;
        }

        .card-header {
            border-bottom: none;
            background: linear-gradient(135deg, #4361ee, #3f37c9);
            border-top-left-radius: 1.5rem;
            border-top-right-radius: 1.5rem;
            padding: 2rem;
        }

        .card-body {
            padding: 2.5rem;
        }

        .section-title {
            color: #343a40;
            font-weight: 700;
            margin-bottom: 1.5rem;
            font-size: 1.25rem;
            text-transform: uppercase;
            border-bottom: 2px solid #4361ee;
            padding-bottom: 0.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #343a40;
            font-size: 0.95rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.85rem 1.25rem;
            border: 1px solid #e0e0e0;
            background: #f8f9fa;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.03);
        }

        .alert {
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .alert-heading {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .btn-primary,
        .btn-secondary {
            border-radius: 50px;
            padding: 0.75rem 2rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4361ee, #3f37c9);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #3f37c9, #4361ee);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
            transform: translateY(-3px);
        }

        .btn-secondary {
            background: #fff;
            border: 2px solid #e0e0e0;
            color: #343a40;
        }

        .btn-secondary:hover {
            background: #f8f9fa;
            border-color: #4361ee;
            transform: translateY(-3px);
        }

        .progress {
            height: 30px;
            border-radius: 15px;
            background: #e9ecef;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .progress-bar {
            border-radius: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: width 0.5s ease;
        }

        .file-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .file-link:hover {
            background: #e9ecef;
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }
    </style>
@endpush

@section('main')
    <div class="page-title light-background">
        <div class="container">
            <h1 data-aos="fade-up">Detail Surat Cuti Akademik</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.surat-cuti-akademik.index') }}">Surat Cuti Akademik</a></li>
                    <li class="current">Detail Surat</li>
                </ol>
            </nav>
        </div>
    </div>

    <section id="services" class="detail-surat section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card" data-aos="fade-up">
                        <div class="card-header text-white">
                            <h4 class="mb-0 fw-bold text-center text-white">Detail Pengajuan Surat Cuti Akademik</h4>
                        </div>
                        <div class="card-body">
                            <!-- Informasi Mahasiswa -->
                            <div class="mb-5" data-aos="fade-up" data-aos-delay="100">
                                <h5 class="section-title">Informasi Mahasiswa</h5>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" value="{{ $surat->mahasiswa->name }}"
                                            readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">NIM</label>
                                        <input type="text" class="form-control"
                                            value="{{ $surat->mahasiswa->nim ?? '-' }}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Program Studi</label>
                                        <input type="text" class="form-control"
                                            value="{{ $surat->mahasiswa->prodi ?? 'S1 Teknik Informatika' }}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Semester</label>
                                        <input type="text" class="form-control" value="{{ $surat->semester ?? '-' }}"
                                            readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Pengajuan -->
                            <div class="mb-5" data-aos="fade-up" data-aos-delay="200">
                                <h5 class="section-title">Detail Pengajuan</h5>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Tahun Ajaran</label>
                                        <input type="text" class="form-control" value="{{ $surat->tahun_ajaran ?? '-' }}"
                                            readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Semester</label>
                                        <input type="text" class="form-control"
                                            value="{{ ucfirst($surat->semester ?? '-') }}" readonly>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Alasan Pengajuan</label>
                                        <textarea class="form-control" rows="4" readonly>{{ $surat->alasan_pengajuan ?? '-' }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Keterangan Tambahan</label>
                                        <textarea class="form-control" rows="3" readonly>{{ $surat->keterangan_tambahan ?? '-' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Dokumen Pendukung -->
                            @if ($surat->file_pendukung_path)
                                <div class="mb-5" data-aos="fade-up" data-aos-delay="300">
                                    <h5 class="section-title">Dokumen Pendukung</h5>
                                    <a href="{{ Storage::url($surat->file_pendukung_path) }}" target="_blank"
                                        class="file-link text-decoration-none">
                                        <i class="bi bi-file-earmark-text fs-4 text-primary"></i>
                                        Lihat Dokumen Pendukung
                                    </a>
                                </div>
                            @endif

                            <!-- File Surat -->
                            <div class="mb-5" data-aos="fade-up" data-aos-delay="400">
                                <h5 class="section-title">File Surat</h5>
                                @if ($surat->file_surat_path)
                                    @if ($surat->status === 'sudah_diambil')
                                        <a href="{{ route('user.surat-cuti-akademik.download', $surat->id) }}"
                                            class="file-link text-decoration-none">
                                            <i class="bi bi-file-earmark-pdf fs-4 text-success"></i>
                                            Unduh Surat Cuti Akademik
                                        </a>
                                    @elseif ($surat->status === 'siap_diambil')
                                        <div class="alert alert-warning">
                                            <p>Silakan konfirmasi penerimaan surat terlebih dahulu untuk mengunduh.</p>
                                            <form
                                                action="{{ route('user.surat-cuti-akademik.confirm-taken', $surat->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-check-circle me-2"></i> Konfirmasi Sudah Diambil
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <p class="text-muted">File surat belum tersedia. Silakan tunggu hingga status
                                            berubah menjadi "Siap Diambil".</p>
                                    @endif
                                @else
                                    <p class="text-muted">File surat belum tersedia. Silakan tunggu hingga status berubah
                                        menjadi "Siap Diambil".</p>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="d-flex justify-content-between mt-5" data-aos="fade-up" data-aos-delay="500">
                                <a href="{{ route('user.surat-cuti-akademik.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i> Kembali ke Daftar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        AOS.init({
            duration: 400,
            once: true
        });
    </script>
@endpush
