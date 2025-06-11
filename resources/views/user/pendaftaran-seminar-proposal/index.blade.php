@extends('layouts.user.app')

@section('title', 'Riwayat Pendaftaran Seminar Proposal')

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

        .empty-state i {
            font-size: 3rem;
            opacity: 0.7;
        }

        .empty-state p {
            color: #6c757d;
            margin-top: 1rem;
        }

        .table-responsive {
            border-radius: 10px;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.03);
        }

        @media (max-width: 767.98px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }
    </style>
@endpush

@section('main')
    <!-- Page Title -->
    <div class="page-title light-background">
        <div class="container">
            <h1 data-aos="fade-up">Riwayat Pendaftaran Seminar Proposal</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan</a></li>
                    <li class="current">Riwayat Pendaftaran</li>
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
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <h4 class="mb-0 fw-bold">Riwayat Pendaftaran Seminar Proposal</h4>
                                <a href="{{ route('user.pendaftaran-seminar-proposal.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i> Buat Pendaftaran Baru
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="10%">No</th>
                                            <th width="40%">Judul Proposal</th>
                                            <th width="30%">Dosen Pembimbing</th>
                                            <th width="20%">Tanggal Pengajuan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($pendaftaran as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->judul_skripsi }}</td>
                                                <td>{{ $item->dosenPembimbing->name ?? 'N/A' }}</td>
                                                <td>{{ $item->created_at->translatedFormat('d F Y, H:i') }} WITA</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="empty-state">
                                                    <i class="bi bi-folder2-open"></i>
                                                    <p>Anda belum pernah melakukan pendaftaran.</p>
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
