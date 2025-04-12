@extends('layouts.user.app')

@section('title', 'Daftar Surat Aktif Kuliah')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/datatables/media/css/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/select2/dist/css/select2.min.css') }}">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .page-title {
            background: #f8f9fa;
            padding: 2rem 0;
        }

        .page-title h1 {
            font-weight: 600;
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

        .breadcrumbs a {
            color: #0d6efd;
            text-decoration: none;
        }

        .card {
            border-radius: 1rem;
            border: none;
        }

        .card-header {
            border-bottom: 1px solid #dee2e6;
            background-color: #fff;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
            padding: 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .btn-primary {
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.35rem 0.65rem;
            border-radius: 50rem;
        }

        .badge-diajukan {
            background-color: #fff3cd;
            color: #664d03;
        }

        .badge-diproses {
            background-color: #cfe2ff;
            color: #084298;
        }

        .badge-disetujui {
            background-color: #d1e7dd;
            color: #0a3622;
        }

        .badge-ditolak {
            background-color: #f8d7da;
            color: #58151c;
        }

        .badge-selesai {
            background-color: #e2e3e5;
            color: #2b2f32;
        }

        .card-surat {
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 0.75rem;
        }

        .card-surat:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .action-btn {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background 0.3s;
        }

        .action-btn:hover {
            background: rgba(13, 110, 253, 0.1);
        }

        .table th,
        .table td {
            vertical-align: middle !important;
        }

        .form-label {
            font-weight: 500;
        }

        .select2-container--default .select2-selection--single {
            border-radius: 0.5rem;
            height: 38px;
            padding: 6px 12px;
            border-color: #ced4da;
        }

        .text-muted {
            font-size: 0.875rem;
        }
    </style>
@endpush


@section('main')
    <!-- Page Title -->
    <div class="page-title light-background">
        <div class="container">
            <h1>Daftar Surat Aktif Kuliah</h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan</a></li>
                    <li class="current">Surat Aktif Kuliah</li>
                </ol>
            </nav>
        </div>
    </div><!-- End Page Title -->

    <!-- Daftar Surat Section -->
    <section id="daftar-surat" class="daftar-surat section">
        <div class="container" data-aos="fade-up">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card shadow">
                        <div class="card-header bg-white border-bottom-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Riwayat Pengajuan</h4>
                                <a href="{{ route('user.surat-aktif-kuliah.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i> Ajukan Baru
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Filter Section -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Filter Status</label>
                                    <select class="form-select select2" id="filter-status">
                                        <option value="">Semua Status</option>
                                        <option value="diajukan">Diajukan</option>
                                        <option value="diproses">Diproses</option>
                                        <option value="disetujui">Disetujui</option>
                                        <option value="ditolak">Ditolak</option>
                                        <option value="selesai">Selesai</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Filter Tahun</label>
                                    <select class="form-select select2" id="filter-tahun">
                                        <option value="">Semua Tahun</option>
                                        @foreach (range(date('Y'), date('Y') - 3) as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Filter Semester</label>
                                    <select class="form-select select2" id="filter-semester">
                                        <option value="">Semua Semester</option>
                                        <option value="ganjil">Ganjil</option>
                                        <option value="genap">Genap</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Table -->
                            <div class="table-responsive">
                                <table class="table table-hover" id="surat-table">
                                    <thead>
                                        <tr>
                                            <th>No. Surat</th>
                                            <th>Tahun/Semester</th>
                                            <th>Tanggal Pengajuan</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($surats as $surat)
                                            <tr>
                                                <td>
                                                    {{ $surat->nomor_surat ?? 'Belum ada nomor' }}
                                                    @if ($surat->nomor_surat)
                                                        <small class="text-muted d-block">Tgl. Surat:
                                                            {{ $surat->tanggal_surat->format('d/m/Y') }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $surat->tahun_ajaran }}
                                                    <small class="text-muted d-block">Semester:
                                                        {{ ucfirst($surat->semester) }}</small>
                                                </td>
                                                <td>
                                                    {{ $surat->created_at->format('d F Y') }}
                                                    <small
                                                        class="text-muted d-block">{{ $surat->created_at->format('H:i') }}</small>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusClass = match ($surat->status->status ?? '') {
                                                            'diajukan' => 'badge-diajukan',
                                                            'diproses' => 'badge-diproses',
                                                            'disetujui' => 'badge-disetujui',
                                                            'ditolak' => 'badge-ditolak',
                                                            'selesai' => 'badge-selesai',
                                                            default => 'badge-diajukan',
                                                        };
                                                    @endphp
                                                    <span class="status-badge {{ $statusClass }}">
                                                        {{ $surat->status->status ?? 'diajukan' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('user.surat-aktif-kuliah.show', $surat->id) }}"
                                                            class="action-btn btn btn-sm btn-info" title="Detail">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        @if ($surat->file_surat_path)
                                                            <a href="{{ route('user.surat-aktif-kuliah.download', $surat->id) }}"
                                                                class="action-btn btn btn-sm btn-success" title="Download">
                                                                <i class="bi bi-download"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <img src="{{ asset('img/empty-state.svg') }}" alt="Empty state"
                                                        style="height: 150px;" class="mb-3">
                                                    <h5>Belum ada pengajuan surat</h5>
                                                    <p class="text-muted">Anda belum pernah mengajukan surat aktif kuliah
                                                    </p>
                                                    <a href="{{ route('user.surat-aktif-kuliah.create') }}"
                                                        class="btn btn-primary mt-2">
                                                        <i class="bi bi-plus-circle me-1"></i> Ajukan Sekarang
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if ($surats->hasPages())
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div class="text-muted small">
                                        Menampilkan {{ $surats->firstItem() }} sampai {{ $surats->lastItem() }} dari
                                        {{ $surats->total() }} entri
                                    </div>
                                    <div>
                                        {{ $surats->links() }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- End Daftar Surat Section -->
@endsection

@push('scripts')
    <!-- JS Libraries -->
    <script src="{{ asset('library/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('library/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Initialize select2
            $('.select2').select2({
                placeholder: 'Pilih filter',
                allowClear: true
            });

            // Initialize DataTable
            $('#surat-table').DataTable({
                searching: false,
                paging: false,
                info: false,
                ordering: false,
                responsive: true,
                language: {
                    emptyTable: "Tidak ada data yang tersedia"
                }
            });

            // Filter functionality
            $('#filter-status, #filter-tahun, #filter-semester').change(function() {
                // Implement your filter logic here
                // This can be AJAX based or simple table filtering
                console.log('Filter changed:', $(this).val());
            });
        });
    </script>
@endpush
