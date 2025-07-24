@extends('layouts.user.app')

@section('title', 'Riwayat Pengajuan Persetujuan Komisi Hasil')

@push('styles')
    <!-- CSS Libraries -->
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

        .btn-primary {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            border: none;
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.2);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #3a0ca3, #4361ee);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
        }

        .table th,
        .table td {
            vertical-align: middle;
            padding: 1rem !important;
        }

        .table th {
            background: #f8fafc;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #5a6a85;
            border-bottom-width: 1px;
        }

        .table td {
            font-size: 0.875rem;
            color: #2c3e50;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
        }

        .empty-state img {
            max-width: 200px;
            margin-bottom: 1.5rem;
            opacity: 0.8;
        }

        .empty-state h5 {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #6c757d;
            margin-bottom: 1.5rem;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        .table-responsive {
            border-radius: 10px;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.03);
        }

        .badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.4rem 0.8rem;
            border-radius: 50rem;
        }

        .badge.bg-warning {
            background-color: #ffc107 !important;
            color: #212529;
        }

        .badge.bg-success {
            background-color: #198754 !important;
        }

        .badge.bg-danger {
            background-color: #dc3545 !important;
        }

        .badge.bg-info {
            background-color: #0dcaf0 !important;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
            color: #5a6a85;
            background: rgba(90, 106, 133, 0.1);
        }

        .action-btn:hover {
            background: rgba(67, 97, 238, 0.2);
            color: #4361ee;
            transform: scale(1.1);
        }

        .judul-skripsi {
            font-weight: 500;
            color: #2c3e50;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .judul-skripsi:hover {
            color: #4361ee;
        }

        .pembimbing-info {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .status-info {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .status-date {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .tooltip-inner {
            max-width: 300px;
            padding: 0.5rem 1rem;
        }

        .progress-tracker {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 1rem 0;
        }

        .progress-tracker::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 4px;
            background: #e9ecef;
            transform: translateY(-50%);
            z-index: 1;
        }

        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .progress-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
        }

        .progress-dot.active {
            background: #4361ee;
            color: white;
        }

        .progress-label {
            font-size: 0.7rem;
            text-align: center;
            color: #6c757d;
        }

        .progress-label.active {
            color: #4361ee;
            font-weight: 500;
        }

        @media (max-width: 767.98px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .card-header .btn {
                width: 100%;
            }

            .progress-tracker {
                flex-wrap: wrap;
                gap: 1rem;
            }

            .progress-tracker::before {
                display: none;
            }

            .progress-step {
                flex: 1 0 30%;
            }
        }
    </style>
@endpush

@section('main')
    <!-- Page Title -->
    <div class="page-title light-background">
        <div class="container">
            <h1 data-aos="fade-up">Riwayat Pengajuan Komisi Hasil</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan</a></li>
                    <li class="current">Komisi Hasil</li>
                </ol>
            </nav>
        </div>
    </div><!-- End Page Title -->

    <!-- Daftar Pengajuan Section -->
    <section id="services" class="daftar-surat section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card" data-aos="fade-up">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <div>
                                    <h4 class="mb-0 fw-bold">Riwayat Pengajuan Anda</h4>
                                    <p class="mb-0 text-muted small">Total pengajuan: {{ $komisiHasils->count() }}</p>
                                </div>
                                <a href="{{ route('user.komisi-hasil.create') }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i> Buat Pengajuan Baru
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="20%">Tanggal Pengajuan</th>
                                            <th width="45%">Judul & Pembimbing</th>
                                            <th width="20%">Status</th>
                                            <th width="25%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($komisiHasils as $hasil)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($hasil->created_at)->translatedFormat('d M Y') }}
                                                    <div class="small text-muted">
                                                        {{ $hasil->created_at->format('H:i') }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="judul-skripsi" title="{{ $hasil->judul_skripsi }}">
                                                        {!! $hasil->judul_skripsi !!}
                                                    </div>
                                                    <div class="pembimbing-info mt-1">
                                                        <i class="bi bi-person-badge"></i>
                                                        Pembimbing 1: {{ $hasil->pembimbing1->name ?? 'N/A' }} <br>
                                                        <i class="bi bi-person-badge"></i>
                                                        Pembimbing 2: {{ $hasil->pembimbing2->name ?? 'N/A' }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="status-info">
                                                        @if ($hasil->status == 'pending')
                                                            <span class="badge bg-warning">Pending</span>
                                                            <span class="status-date">Menunggu persetujuan</span>
                                                        @elseif($hasil->status == 'approved')
                                                            <span class="badge bg-success">Approved</span>
                                                            <span class="status-date">
                                                                Disetujui:
                                                                {{ $hasil->updated_at->translatedFormat('d M Y') }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger">Rejected</span>
                                                            <span class="status-date">
                                                                Ditolak:
                                                                {{ $hasil->updated_at->translatedFormat('d M Y') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        @if ($hasil->status == 'approved' && $hasil->file_komisi_hasil)
                                                            <a href="{{ asset('storage/' . $hasil->file_komisi_hasil) }}"
                                                                target="_blank" class="action-btn btn btn-sm btn-success"
                                                                title="Unduh Surat Persetujuan">
                                                                <i class="bi bi-download"></i>
                                                            </a>
                                                        @elseif ($hasil->status == 'rejected')
                                                            <button type="button"
                                                                class="action-btn btn btn-sm btn-secondary"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="Alasan: {{ $hasil->keterangan ?? 'Tidak ada keterangan' }}">
                                                                <i class="bi bi-info-circle"></i>
                                                            </button>
                                                        @else
                                                            <button class="action-btn btn btn-sm btn-secondary" disabled
                                                                title="Dalam Proses">
                                                                <i class="bi bi-clock-history"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="empty-state">
                                                    <p><i class="bi bi-emoji-frown" style="font-size: 2rem;"></i></p>
                                                    <h5>Belum Ada Pengajuan Komisi Hasil</h5>
                                                    <p>Anda belum pernah membuat pengajuan komisi hasil. Yuk, ajukan
                                                        sekarang!</p>
                                                    <a href="{{ route('user.komisi-hasil.create') }}"
                                                        class="btn btn-primary">
                                                        <i class="bi bi-plus-circle me-2"></i> Buat Pengajuan Baru
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- End Daftar Pengajuan Section -->
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();

            // AOS init
            AOS.init({
                duration: 400,
                once: true
            });
        });
    </script>
@endpush
