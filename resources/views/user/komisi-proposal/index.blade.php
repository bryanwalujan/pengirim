@extends('layouts.user.app')

@section('title', 'Riwayat Pengajuan Persetujuan Komisi Proposal')

@push('styles')
    <!-- CSS Libraries -->

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fb;
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
            <h1 data-aos="fade-up">Riwayat Pengajuan Komisi Proposal</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan</a></li>
                    <li class="current">Komisi Proposal</li>
                </ol>
            </nav>
        </div>
    </div><!-- End Page Title -->

    <!-- Daftar Pengajuan Section -->
    <section id="services" class="daftar-surat section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    {{-- Alert Messages --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" data-aos="fade-down">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" data-aos="fade-down">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Status Information Card - Tampil jika tidak bisa mengajukan --}}
                    @if (!$canCreateStatus['can_create'] && $latestProposal)
                        <div class="card mb-4 border-{{ $latestProposal->status === 'approved' ? 'success' : ($latestProposal->status === 'rejected' ? 'danger' : 'warning') }}"
                            data-aos="fade-up" style="border-width: 2px !important;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start">
                                    <div class="me-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 60px; height: 60px; background: {{ $latestProposal->status === 'approved' ? '#d1fae5' : ($latestProposal->status === 'rejected' ? '#fee2e2' : '#fef3c7') }};">
                                            <i class="bi bi-{{ $latestProposal->status === 'approved' ? 'check-circle' : ($latestProposal->status === 'rejected' ? 'x-circle' : 'clock-history') }}"
                                                style="font-size: 2rem; color: {{ $latestProposal->status === 'approved' ? '#10b981' : ($latestProposal->status === 'rejected' ? '#ef4444' : '#f59e0b') }};"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="fw-bold mb-2">
                                            @if ($latestProposal->status === 'approved')
                                                <i class="bi bi-check-circle text-success me-1"></i> Pengajuan Sudah
                                                Disetujui
                                            @elseif($latestProposal->status === 'rejected')
                                                <i class="bi bi-x-circle text-danger me-1"></i> Pengajuan Terakhir Ditolak
                                            @else
                                                <i class="bi bi-clock-history text-warning me-1"></i> Pengajuan Sedang
                                                Diproses
                                            @endif
                                        </h5>
                                        <p class="mb-3 text-muted">{{ $canCreateStatus['reason'] }}</p>

                                        @if ($latestProposal->status === 'approved')
                                            <div class="alert alert-success mb-0">
                                                <small>
                                                    <strong><i class="bi bi-calendar-check me-1"></i> Disetujui
                                                        pada:</strong>
                                                    {{ $latestProposal->tanggal_persetujuan_korprodi->translatedFormat('d F Y, H:i') }}
                                                    WITA<br>
                                                    <strong><i class="bi bi-journal-text me-1"></i> Judul Skripsi:</strong>
                                                    {{ strip_tags($latestProposal->judul_skripsi) }}
                                                </small>
                                            </div>
                                        @elseif($latestProposal->status === 'rejected')
                                            <div class="alert alert-danger mb-3">
                                                <small>
                                                    <strong><i class="bi bi-info-circle me-1"></i> Alasan
                                                        Penolakan:</strong><br>
                                                    {{ $latestProposal->keterangan ?? 'Tidak ada keterangan' }}
                                                </small>
                                            </div>
                                            <a href="{{ route('user.komisi-proposal.create') }}"
                                                class="btn btn-primary btn-sm">
                                                <i class="bi bi-arrow-repeat me-1"></i> Ajukan Ulang Sekarang
                                            </a>
                                        @else
                                            <div class="alert alert-info mb-0">
                                                <small>
                                                    <strong><i class="bi bi-info-circle me-1"></i> Status:</strong>
                                                    @if ($latestProposal->status === 'pending')
                                                        Menunggu Persetujuan PA
                                                    @else
                                                        Menunggu Persetujuan Korprodi
                                                    @endif
                                                    <br>
                                                    <strong><i class="bi bi-calendar me-1"></i> Diajukan pada:</strong>
                                                    {{ $latestProposal->created_at->translatedFormat('d F Y, H:i') }}
                                                    WITA<br>
                                                    <strong><i class="bi bi-person-badge me-1"></i> Pembimbing
                                                        Akademik:</strong>
                                                    {{ $latestProposal->pembimbing->name ?? '-' }}
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Info untuk mahasiswa baru yang belum pernah mengajukan --}}
                        @if (!$latestProposal)
                            <div class="alert alert-info alert-dismissible fade show" role="alert" data-aos="fade-up">
                                <h5 class="alert-heading fw-bold">
                                    <i class="bi bi-info-circle-fill me-2"></i> Informasi Penting
                                </h5>
                                <p class="mb-2">Anda belum memiliki pengajuan komisi proposal. Silakan klik tombol
                                    <strong>"Buat Pengajuan Baru"</strong> untuk membuat pengajuan pertama Anda.
                                </p>
                                <hr>
                                <p class="mb-0">
                                    <small>
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        <strong>Catatan:</strong> Anda hanya dapat mengajukan komisi proposal
                                        <strong>sekali</strong>.
                                        Pastikan semua data yang Anda isi sudah benar sebelum mengirim.
                                    </small>
                                </p>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                    @endif

                    <div class="card" data-aos="fade-up">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <div>
                                    <h4 class="mb-0 fw-bold">Riwayat Pengajuan Anda</h4>
                                    <p class="mb-0 text-muted small">Total pengajuan: {{ $komisiProposals->count() }}</p>
                                </div>
                                {{-- Tombol hanya muncul jika boleh mengajukan --}}
                                @if ($canCreateStatus['can_create'])
                                    <a href="{{ route('user.komisi-proposal.create') }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-plus-circle me-2"></i> Buat Pengajuan Baru
                                    </a>
                                @endif
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
                                            <th width="10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($komisiProposals as $proposal)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    {{ $proposal->created_at->translatedFormat('d M Y') }}
                                                    <div class="small text-muted">
                                                        {{ $proposal->created_at->format('H:i') }} WITA
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="judul-skripsi"
                                                        title="{{ strip_tags($proposal->judul_skripsi) }}">
                                                        {{ Str::limit(strip_tags($proposal->judul_skripsi), 80) }}
                                                    </div>
                                                    <div class="pembimbing-info mt-1">
                                                        <i class="bi bi-person-badge"></i>
                                                        {{ $proposal->pembimbing->name }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="status-info">
                                                        @if ($proposal->status == 'pending')
                                                            <span class="badge bg-warning">Menunggu PA</span>
                                                            <span class="status-date">Sedang diproses</span>
                                                        @elseif($proposal->status == 'approved_pa')
                                                            <span class="badge bg-info">Menunggu Korprodi</span>
                                                            <span class="status-date">Disetujui PA</span>
                                                        @elseif($proposal->status == 'approved')
                                                            <span class="badge bg-success">Disetujui Lengkap</span>
                                                            <span class="status-date">
                                                                {{ $proposal->tanggal_persetujuan_korprodi->translatedFormat('d M Y') }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger">Ditolak</span>
                                                            <span class="status-date">
                                                                {{ $proposal->updated_at->translatedFormat('d M Y') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        @if ($proposal->status == 'approved' && $proposal->file_komisi)
                                                            <a href="{{ route('user.komisi-proposal.download', $proposal->id) }}"
                                                                target="_blank" class="action-btn btn btn-sm btn-success"
                                                                data-bs-toggle="tooltip" title="Download Dokumen">
                                                                <i class="bi bi-download"></i>
                                                            </a>
                                                        @elseif ($proposal->status == 'rejected')
                                                            <button type="button"
                                                                class="action-btn btn btn-sm btn-danger"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="Alasan: {{ $proposal->keterangan ?? 'Tidak ada keterangan' }}">
                                                                <i class="bi bi-info-circle"></i>
                                                            </button>
                                                        @else
                                                            <button class="action-btn btn btn-sm btn-secondary" disabled
                                                                data-bs-toggle="tooltip" title="Sedang Diproses">
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
                                                    <h5>Belum Ada Pengajuan Komisi Proposal</h5>
                                                    <p>Anda belum pernah membuat pengajuan komisi proposal.</p>
                                                    @if ($canCreateStatus['can_create'])
                                                        <a href="{{ route('user.komisi-proposal.create') }}"
                                                            class="btn btn-primary">
                                                            <i class="bi bi-plus-circle me-2"></i> Buat Pengajuan Pertama
                                                        </a>
                                                    @endif
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

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert:not(.alert-info)').fadeOut('slow');
            }, 5000);
        });
    </script>
@endpush
