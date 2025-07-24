@extends('layouts.user.app')

@section('title', 'Detail Pendaftaran Ujian Hasil')

@push('styles')
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fb;
        }

        .page-title h1 {
            font-weight: 600;
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }

        .breadcrumbs ol {
            margin-bottom: 0;
            padding-left: 0;
            list-style: none;
            display: flex;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .card {
            border-radius: 1rem;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            background: #fff;
            overflow: hidden;
        }

        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            background: #fff;
            padding: 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-footer {
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            background: #fff;
            padding: 1.5rem;
        }

        .btn-secondary {
            background: #fff;
            border: 2px solid #e0e0e0;
            color: #343a40;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #f8f9fa;
            border-color: #4895ef;
            color: #343a40;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            border: none;
            border-radius: 50px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.2);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #3a0ca3, #4361ee);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
        }

        .list-group-item {
            border: none;
            padding: 1rem 1.5rem;
            background: #f8fafc;
            margin-bottom: 0.5rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .list-group-item:hover {
            background: #f1f5f9;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .detail-label {
            font-weight: 600;
            color: #5a6a85;
            font-size: 0.875rem;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-size: 1rem;
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        hr {
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            margin: 1.5rem 0;
        }

        h5 {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        @media (max-width: 767.98px) {
            .card-header {
                padding: 1rem;
            }

            .card-body {
                padding: 1rem;
            }

            .card-footer {
                padding: 1rem;
            }
        }
    </style>
@endpush

@section('main')
    <!-- Page Title -->
    <div class="page-title light-background">
        <div class="container">
            <h1 data-aos="fade-up">Detail Pendaftaran Ujian Hasil</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan</a></li>
                    <li><a href="{{ route('user.pendaftaran-ujian-hasil.index') }}">Riwayat Pendaftaran</a></li>
                    <li class="current">Detail</li>
                </ol>
            </nav>
        </div>
    </div><!-- End Page Title -->

    <section id="services" class="daftar-surat section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card" data-aos="fade-up">
                        <div class="card-header">
                            <h4 class="mb-0 fw-bold">Detail Pendaftaran: {{ $pendaftaran_ujian_hasil->nama }}
                                ({{ $pendaftaran_ujian_hasil->nim }})</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="detail-label">Nama Mahasiswa</div>
                                    <div class="detail-value">{{ $pendaftaran_ujian_hasil->nama }}</div>
                                    <div class="detail-label">NIM</div>
                                    <div class="detail-value">{{ $pendaftaran_ujian_hasil->nim }}</div>
                                    <div class="detail-label">Angkatan</div>
                                    <div class="detail-value">{{ $pendaftaran_ujian_hasil->angkatan }}</div>
                                    <div class="detail-label">IPK</div>
                                    <div class="detail-value">{{ $pendaftaran_ujian_hasil->ipk }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-label">Tanggal Pengajuan</div>
                                    <div class="detail-value">
                                        {{ $pendaftaran_ujian_hasil->created_at->translatedFormat('l, d F Y H:i') }} WITA
                                    </div>
                                    <div class="detail-label">Dosen Pembimbing Akademik</div>
                                    <div class="detail-value">
                                        {{ $pendaftaran_ujian_hasil->dosenPa->name ?? 'Belum Dipilih' }}</div>
                                    <div class="detail-label">Dosen Pembimbing 1</div>
                                    <div class="detail-value">
                                        {{ $pendaftaran_ujian_hasil->dosenPembimbing1->name ?? 'Belum Dipilih' }}</div>
                                    <div class="detail-label">Dosen Pembimbing 2</div>
                                    <div class="detail-value">
                                        {{ $pendaftaran_ujian_hasil->dosenPembimbing2->name ?? 'Belum Dipilih' }}</div>
                                </div>
                            </div>

                            <hr>

                            <div class="detail-label">Judul Skripsi</div>
                            <div class="detail-value">{{ $pendaftaran_ujian_hasil->judul_skripsi }}</div>

                            <hr>

                            <h5>Berkas Terlampir</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Transkrip Nilai
                                    <a href="{{ Storage::url($pendaftaran_ujian_hasil->transkrip_nilai) }}" target="_blank"
                                        class="btn btn-sm btn-primary">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Berkas
                                    </a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    File Skripsi
                                    <a href="{{ Storage::url($pendaftaran_ujian_hasil->file_skripsi) }}" target="_blank"
                                        class="btn btn-sm btn-primary">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Berkas
                                    </a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Surat Komisi Hasil
                                    <a href="{{ Storage::url($pendaftaran_ujian_hasil->komisi_hasil) }}" target="_blank"
                                        class="btn btn-sm btn-primary">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Berkas
                                    </a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Surat Permohonan Ujian Hasil
                                    <a href="{{ Storage::url($pendaftaran_ujian_hasil->surat_permohonan_hasil) }}"
                                        target="_blank" class="btn btn-sm btn-primary">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Berkas
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('user.pendaftaran-ujian-hasil.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // AOS init
        AOS.init({
            duration: 400,
            once: true
        });
    </script>
@endpush
