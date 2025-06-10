@extends('layouts.user.app')

@section('title', 'Peminjaman Laboratorium Komputer')

@section('main')
    {{-- Page Title (tidak diubah sesuai permintaan) --}}
    <div class="page-title light-background">
        <div class="container">
            <h1 data-aos="fade-up">Peminjaman Lab Komputer</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan</a></li>
                    <li class="current">Peminjaman Lab </li>
                </ol>
            </nav>
        </div>
    </div><!-- End Page Title -->

    <section id="services" class="peminjaman-laboratorium">
        <div class="container" data-aos="fade-up">

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

            <div class="row g-4">
                {{-- Kolom Kiri: Status & Form Peminjaman --}}
                <div class="col-lg-6">
                    {{-- Status Card --}}
                    <div class="card border-0 shadow-lg mb-4 overflow-hidden">
                        @if ($peminjamanAktif)
                            {{-- Sedang Dipinjam --}}
                            <div class="card-body p-0">
                                <div class="row g-0">
                                    <div
                                        class="col-md-4 bg-warning bg-opacity-10 d-flex align-items-center justify-content-center p-4">
                                        <div class="text-center py-4">
                                            <i class="bi bi-exclamation-triangle-fill text-warning"
                                                style="font-size: 3.5rem;"></i>
                                            <h3 class="mt-3 fw-bold text-warning">Sedang Dipinjam</h3>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="p-4 p-lg-5">
                                            <h4 class="fw-bold mb-3">Laboratorium sedang digunakan</h4>
                                            <p class="text-muted mb-4">Dipinjam oleh:
                                                <strong>{{ $peminjamanAktif->user->name }}</strong><br>
                                                Sampai pukul:
                                                <strong>{{ \Carbon\Carbon::parse($peminjamanAktif->jam_selesai)->format('H:i') }}
                                                    WITA</strong>
                                            </p>

                                            <div class="alert alert-warning d-flex align-items-center">
                                                <i class="bi bi-info-circle-fill me-3"></i>
                                                <div>Anda tidak dapat meminjam laboratorium saat sedang digunakan oleh orang
                                                    lain.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Tersedia --}}
                            <div class="card-body p-0">
                                <div class="row g-0">
                                    <div
                                        class="col-md-4 bg-primary bg-opacity-10 d-flex align-items-center justify-content-center p-4">
                                        <div class="text-center py-4">
                                            <i class="bi bi-check-circle-fill text-primary" style="font-size: 3.5rem;"></i>
                                            <h3 class="mt-3 fw-bold text-primary">Tersedia</h3>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="p-4 p-lg-5">
                                            <h4 class="fw-bold mb-3">Laboratorium siap dipinjam</h4>
                                            <p class="text-muted mb-4">Laboratorium komputer saat ini tersedia untuk Anda
                                                pinjam. Silahkan isi form peminjaman di bawah ini.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Form Peminjaman --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="card-title mb-0 fw-bold">
                                <i class="bi bi-pencil-square text-primary me-2"></i>Form Peminjaman
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('user.peminjaman-laboratorium.store') }}" method="POST"
                                id="borrow-form">
                                @csrf
                                <fieldset {{ $peminjamanAktif ? 'disabled' : '' }}>
                                    <div class="mb-3">
                                        <label for="tanggal_peminjaman" class="form-label">Tanggal Peminjaman</label>
                                        <input type="date" class="form-control" id="tanggal_peminjaman"
                                            name="tanggal_peminjaman"
                                            value="{{ old('tanggal_peminjaman', now()->format('Y-m-d')) }}" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="jam_mulai" class="form-label">Jam Mulai</label>
                                            <input type="time" class="form-control" id="jam_mulai" name="jam_mulai"
                                                value="{{ old('jam_mulai') }}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="jam_selesai" class="form-label">Jam Selesai</label>
                                            <input type="time" class="form-control" id="jam_selesai" name="jam_selesai"
                                                value="{{ old('jam_selesai') }}" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="keperluan" class="form-label">Keperluan</label>
                                        <textarea class="form-control" id="keperluan" name="keperluan" rows="4"
                                            placeholder="Contoh: Mengerjakan tugas akhir, praktikum, dll." required>{{ old('keperluan') }}</textarea>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="bi bi-send-fill me-2"></i>Ajukan Peminjaman
                                        </button>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Riwayat Peminjaman --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 fw-bold">
                                    <i class="bi bi-clock-history text-primary me-2"></i>Riwayat Peminjaman
                                </h5>
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">
                                    Total: {{ $peminjaman->count() }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">No</th>
                                            <th>Tanggal</th>
                                            <th>Waktu</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center pe-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($peminjaman as $item)
                                            <tr>
                                                <td class="ps-4">{{ $peminjaman->firstItem() + $loop->index }}</td>
                                                <td>
                                                    <div class="fw-semibold">
                                                        {{ \Carbon\Carbon::parse($item->tanggal_peminjaman)->translatedFormat('d M Y') }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <small
                                                        class="text-muted">{{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }}
                                                        -
                                                        {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }}</small>
                                                </td>
                                                <td class="text-center">
                                                    @if ($item->status == 'diajukan')
                                                        <span
                                                            class="badge rounded-pill bg-warning bg-opacity-15 text-black py-2 px-3">
                                                            <i class="bi bi-hourglass-split me-1"></i> Sedang Digunakan
                                                        </span>
                                                    @else
                                                        <span
                                                            class="badge rounded-pill bg-success bg-opacity-15 text-white py-2 px-3">
                                                            <i class="bi bi-check-circle me-1"></i> Selesai
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center pe-4">
                                                    @if ($item->status == 'diajukan')
                                                        <form class="form-konfirmasi-selesai"
                                                            action="{{ route('user.peminjaman-laboratorium.update', $item->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-check-lg me-1"></i> Selesai
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
                                                        <p class="text-muted mb-0">Anda belum memiliki riwayat peminjaman
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <!-- Pagination -->
                                @if ($peminjaman->hasPages())
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination justify-content-end mb-3">
                                            {{-- Previous --}}
                                            <li class="page-item {{ $peminjaman->onFirstPage() ? 'disabled' : '' }}">
                                                <a class="page-link" href="{{ $peminjaman->previousPageUrl() ?? '#' }}"
                                                    tabindex="-1">
                                                    <i class="bi bi-chevron-double-left"></i>
                                                </a>
                                            </li>
                                            {{-- First Page --}}
                                            @if ($peminjaman->currentPage() > 2)
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $peminjaman->url(1) }}">1</a>
                                                </li>
                                            @endif
                                            {{-- Dots if needed --}}
                                            @if ($peminjaman->currentPage() > 3)
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            @endif
                                            {{-- Current + 1 Before & After --}}
                                            @for ($i = max(1, $peminjaman->currentPage() - 1); $i <= min($peminjaman->lastPage(), $peminjaman->currentPage() + 1); $i++)
                                                <li
                                                    class="page-item {{ $i == $peminjaman->currentPage() ? 'active' : '' }}">
                                                    <a class="page-link"
                                                        href="{{ $peminjaman->url($i) }}">{{ $i }}</a>
                                                </li>
                                            @endfor
                                            {{-- Dots if needed --}}
                                            @if ($peminjaman->currentPage() < $peminjaman->lastPage() - 2)
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            @endif
                                            {{-- Last Page --}}
                                            @if ($peminjaman->currentPage() < $peminjaman->lastPage() - 1)
                                                <li class="page-item">
                                                    <a class="page-link"
                                                        href="{{ $peminjaman->url($peminjaman->lastPage()) }}">{{ $peminjaman->lastPage() }}</a>
                                                </li>
                                            @endif
                                            {{-- Next --}}
                                            <li class="page-item {{ !$peminjaman->hasMorePages() ? 'disabled' : '' }}">
                                                <a class="page-link" href="{{ $peminjaman->nextPageUrl() ?? '#' }}">
                                                    <i class="bi bi-chevron-double-right"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                @endif
                            </div>
                        </div>
                    </div>
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


        /* Pagination Styling */
        .pagination .page-link {
            border-radius: 8px;
            margin: 0 3px;
            color: #0d6efd;
            border: 1px solid #dee2e6;
            padding: 0.5rem 0.75rem;
            transition: all 0.3s ease;
        }

        .pagination .page-link:hover {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }

        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: transparent;
            border-color: #dee2e6;
            cursor: not-allowed;
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

@push('scripts')
    <script>
        // Borrowing Confirmation
        document.getElementById('borrow-form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Peminjaman',
                text: 'Apakah Anda yakin ingin mengajukan peminjaman laboratorium?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Ajukan Peminjaman',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });

        // Returning Confirmation
        document.querySelectorAll('.form-konfirmasi-selesai').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Selesai?',
                    text: "Apakah Anda yakin sudah selesai menggunakan laboratorium?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, sudah selesai!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    </script>
@endpush
