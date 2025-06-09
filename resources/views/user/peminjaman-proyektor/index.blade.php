@extends('layouts.user.app')

@section('title', 'Peminjaman Proyektor')

@section('main')
    <!-- Page Title (unchanged as requested) -->
    <div class="page-title light-background">
        <div class="container">
            <h1 data-aos="fade-up">Peminjaman Proyektor</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.peminjaman-proyektor.index') }}">Layanan</a></li>
                    <li class="current">Peminjaman Proyektor</li>
                </ol>
            </nav>
        </div>
    </div><!-- End Page Title -->

    <section id="services" class="peminjaman-proyektor">
        <div class="container" data-aos="fade-up">
            <div class="section-header text-center mb-5">
                <h2 class="fw-bold">Layanan Peminjaman Proyektor</h2>
                <p class="lead text-muted">Pinjam dan kembalikan proyektor dengan mudah melalui sistem digital kami</p>
            </div>

            {{-- Notification Alerts --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill flex-shrink-0 me-2"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Main Card --}}
            <div class="card border-0 shadow-lg mb-5 overflow-hidden">
                @if ($isCurrentlyBorrowing)
                    {{-- Currently Borrowing Card --}}
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <div
                                class="col-md-4 bg-warning bg-opacity-10 d-flex align-items-center justify-content-center p-4">
                                <div class="text-center py-4">
                                    <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 3.5rem;"></i>
                                    <h3 class="mt-3 fw-bold text-warning">Proyektor Dipinjam</h3>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="p-4 p-lg-5">
                                    <h4 class="fw-bold mb-3">Anda sedang meminjam proyektor</h4>
                                    <p class="text-muted mb-4">Anda telah meminjam proyektor dan belum mengembalikannya.
                                        Harap kembalikan proyektor yang sedang Anda pinjam sebelum dapat melakukan
                                        peminjaman baru.</p>

                                    <div class="alert alert-warning d-flex align-items-center">
                                        <i class="bi bi-info-circle-fill me-3"></i>
                                        <div>Jika Anda merasa sudah mengembalikan, harap hubungi staff administrasi.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Available to Borrow Card --}}
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <div
                                class="col-md-4 bg-primary bg-opacity-10 d-flex align-items-center justify-content-center p-4">
                                <div class="text-center py-4">
                                    <i class="bi bi-projector-fill text-primary" style="font-size: 3.5rem;"></i>
                                    <h3 class="mt-3 fw-bold text-primary">Siap Meminjam</h3>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="p-4 p-lg-5">
                                    <h4 class="fw-bold mb-3">Pinjam Proyektor Sekarang</h4>
                                    <p class="text-muted mb-4">Dengan menekan tombol, Anda akan tercatat meminjam proyektor
                                        pada waktu saat ini.
                                        Mohon untuk mengambil proyektor di ruang administrasi dan mengembalikannya tepat
                                        waktu.</p>

                                    <form action="{{ route('user.peminjaman-proyektor.store') }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin meminjam proyektor sekarang?');">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-lg px-4 py-3">
                                            <i class="bi bi-hand-index-thumb-fill me-2"></i>
                                            Pinjam Proyektor Sekarang
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Borrowing History --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="bi bi-clock-history text-primary me-2"></i>Riwayat Peminjaman
                        </h5>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">
                            Total: {{ $peminjaman->total() }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">No</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Tanggal Kembali</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($peminjaman as $item)
                                    <tr>
                                        <td class="ps-4">{{ $loop->iteration + $peminjaman->firstItem() - 1 }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $item->tanggal_pinjam->translatedFormat('d M Y') }}
                                            </div>
                                            <small class="text-muted">{{ $item->tanggal_pinjam->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            @if ($item->tanggal_kembali)
                                                <div class="fw-semibold">
                                                    {{ $item->tanggal_kembali->translatedFormat('d M Y') }}</div>
                                                <small
                                                    class="text-muted">{{ $item->tanggal_kembali->format('H:i') }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($item->status == 'dipinjam')
                                                <span
                                                    class="badge rounded-pill bg-warning bg-opacity-15 text-black py-2 px-3">
                                                    <i class="bi bi-hourglass-split me-1"></i> Dipinjam
                                                </span>
                                            @else
                                                <span
                                                    class="badge rounded-pill bg-success bg-opacity-15 text-success py-2 px-3">
                                                    <i class="bi bi-check-circle me-1"></i> Dikembalikan
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center pe-4">
                                            @if ($item->status == 'dipinjam')
                                                <form
                                                    action="{{ route('user.peminjaman-proyektor.kembalikan', $item->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Penting: Pastikan Anda sudah mengembalikan proyektor secara fisik ke ruang administrasi sebelum melakukan konfirmasi ini. Lanjutkan?');">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-check-lg me-1"></i> Konfirmasi
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="bi bi-journal-x fs-1 text-muted mb-3"></i>
                                                <p class="text-muted mb-0">Anda belum memiliki riwayat peminjaman</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($peminjaman->hasPages())
                        <div class="card-footer bg-white border-0 py-3">
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center mb-0">
                                    {{-- Previous Page Link --}}
                                    <li class="page-item {{ $peminjaman->onFirstPage() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $peminjaman->previousPageUrl() }}"
                                            aria-label="Previous">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>

                                    {{-- Pagination Elements --}}
                                    @foreach ($peminjaman->getUrlRange(1, $peminjaman->lastPage()) as $page => $url)
                                        <li class="page-item {{ $page == $peminjaman->currentPage() ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                        </li>
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    <li class="page-item {{ !$peminjaman->hasMorePages() ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $peminjaman->nextPageUrl() }}" aria-label="Next">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        /* Modern Card Styling */
        .card {
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        /* Table Styling */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            border-bottom: 2px solid #f0f0f0;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            color: #6c757d;
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.03);
        }

        /* Button Styling */
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
            transform: translateY(-2px);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .card-body .row {
                flex-direction: column;
            }

            .col-md-4 {
                width: 100%;
                padding: 2rem !important;
            }

            .col-md-8 {
                width: 100%;
                padding: 2rem !important;
            }
        }
    </style>
@endpush
