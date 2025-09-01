@extends('layouts.user.app')

@section('title', 'Daftar Surat Aktif Kuliah')

@push('styles')
    <style>

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

        .btn-outline-primary {
            border-color: #4361ee;
            color: #4361ee;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: #4361ee;
            color: white;
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

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .select2-container--default .select2-selection--single {
            border-radius: 8px;
            height: 42px;
            padding: 8px 12px;
            border-color: #e0e0e0;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px;
            color: #2c3e50;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px;
        }

        .text-muted {
            font-size: 0.8rem;
            color: #6c757d !important;
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

        .search-box {
            position: relative;
            max-width: 300px;
        }

        .search-box .form-control {
            padding-left: 40px;
            border-radius: 8px;
            border-color: #e0e0e0;
            height: 42px;
        }

        .search-box .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .filter-section {
            background: #f8fafc;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .filter-section .row {
            align-items: flex-end;
        }

        .filter-section .btn-reset {
            height: 42px;
            display: flex;
            align-items: center;
        }

        .table-responsive {
            border-radius: 10px;
            /* overflow: hidden; */
        }

        .table {
            margin-bottom: 0;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.03);
        }

        .pagination .page-item.active .page-link {
            background-color: #4361ee;
            border-color: #4361ee;
        }

        .pagination .page-link {
            color: #4361ee;
            border-color: #e0e0e0;
        }

        .pagination .page-link:hover {
            background-color: #f8f9fa;
        }

        .card-header h4 {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0;
        }

        .card-header .btn {
            font-size: 0.875rem;
        }

        .badge-count {
            position: absolute;
            top: -8px;
            right: -8px;
            font-size: 0.6rem;
            min-width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #dc3545;
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        @media (max-width: 767.98px) {
            .filter-section .col-md-4 {
                margin-bottom: 1rem;
            }

            .search-box {
                max-width: 100%;
                margin-bottom: 1rem;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .card-header .btn {
                width: 100%;
            }
        }

        .btn-copy-code {
            width: 28px;
            height: 28px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            margin-left: 8px;
            background: rgba(67, 97, 238, 0.1);
            border: 1px solid rgba(67, 97, 238, 0.2);
            color: #4361ee;
            transition: all 0.3s ease;
        }

        .btn-copy-code:hover {
            background: rgba(67, 97, 238, 0.2);
            transform: scale(1.05);
        }

        .btn-copy-code i {
            font-size: 0.875rem;
        }

        .tracking-code-container {
            display: flex;
            align-items: center;
        }
    </style>
@endpush

@section('main')
    <!-- Page Title -->
    <div class="page-title light-background">
        <div class="container">
            <h1 data-aos="fade-up">Daftar Surat Aktif Kuliah</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan</a></li>
                    <li class="current">Surat Aktif Kuliah</li>
                </ol>
            </nav>
        </div>
    </div><!-- End Page Title -->

    <!-- Daftar Surat Section -->
    <section id="services" class="daftar-surat section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card" data-aos="fade-up">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <h4 class="mb-0 fw-bold">Riwayat Pengajuan</h4>
                                <a href="{{ route('user.surat-aktif-kuliah.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i> Ajukan Baru
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Di bagian filter section -->
                            <div class="filter-section">
                                <form id="filter-form" method="GET" action="{{ route('user.surat-aktif-kuliah.index') }}">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="search-box">
                                                <i class="bi bi-search search-icon"></i>
                                                <input type="text" class="form-control" id="search-input" name="search"
                                                    placeholder="Cari..." value="{{ request('search') }}">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Table -->
                            <div class="table-responsive">
                                <table class="table table-hover" id="surat-table">
                                    <thead>
                                        <tr>
                                            <th width="15%">No. Surat</th>
                                            <th width="15%">Kode Tracking</th>
                                            <th width="15%">Tahun/Semester</th>
                                            <th width="20%">Tujuan Pengajuan</th>
                                            <th width="15%">Tanggal Pengajuan</th>
                                            <th width="20%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($surats as $surat)
                                            <tr>
                                                <td>
                                                    @if ($surat->nomor_surat)
                                                        <span class="d-block">{{ $surat->nomor_surat }}</span>
                                                        <small class="text-muted">Tgl. Surat:
                                                            {{ optional($surat->tanggal_surat)->format('d/m/Y') }}</small>
                                                    @else
                                                        <span class="text-muted">Belum ada nomor</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="tracking-code-container">
                                                        <span class="tracking-code"
                                                            title="Klik tombol disamping untuk menyalin">
                                                            {{ $surat->tracking_code }}
                                                        </span>
                                                        <button class="btn-copy-code"
                                                            onclick="copyTrackingCode('{{ $surat->tracking_code }}', this)">
                                                            <i class="bi bi-clipboard"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="d-block">{{ $surat->tahun_ajaran }}</span>
                                                    <small class="text-muted">Semester:
                                                        {{ ucfirst($surat->semester) }}</small>
                                                </td>
                                                <td>
                                                    {{ Str::limit($surat->tujuan_pengajuan, 30) }}
                                                    @if ($surat->keterangan_tambahan)
                                                        <small class="text-muted d-block"
                                                            title="{{ $surat->keterangan_tambahan }}">
                                                            {{ Str::limit($surat->keterangan_tambahan, 30) }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="d-block">{{ $surat->created_at->format('d F Y') }}</span>
                                                    <small
                                                        class="text-muted">{{ $surat->created_at->format('H:i') }}</small>
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <a href="{{ route('user.surat-aktif-kuliah.show', $surat->id) }}"
                                                            class="action-btn btn btn-sm btn-info" title="Detail">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        @if ($surat->file_surat_path && ($surat->status === 'sudah_diambil' || $surat->status === 'disetujui'))
                                                            <a href="{{ route('user.surat-aktif-kuliah.download', $surat->id) }}"
                                                                class="action-btn btn btn-sm btn-primary" title="Download">
                                                                <i class="bi bi-download"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="empty-state">
                                                    <p><i class="bi bi-emoji-frown"></i></p>
                                                    <h5>Belum Ada Pengajuan Surat</h5>
                                                    <p>Anda belum pernah mengajukan surat aktif kuliah. Yuk, ajukan
                                                        sekarang!</p>
                                                    <a href="{{ route('user.surat-aktif-kuliah.create') }}"
                                                        class="btn btn-primary">
                                                        <i class="bi bi-plus-circle me-2"></i> Ajukan Sekarang
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if ($surats->hasPages())
                                <div class="mt-4">
                                    {{ $surats->links() }}
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
    <script>
        function copyTrackingCode(code, button) {
            // Buat elemen input sementara
            const tempInput = document.createElement('input');
            tempInput.value = code;
            document.body.appendChild(tempInput);

            // Pilih teks
            tempInput.select();
            tempInput.setSelectionRange(0, 99999); // Untuk mobile

            try {
                // Jalankan perintah salin
                const successful = document.execCommand('copy');

                if (successful) {
                    // Ubah ikon sementara
                    const icon = button.querySelector('i');
                    icon.classList.remove('bi-clipboard');
                    icon.classList.add('bi-check');

                    // Tampilkan tooltip/alert
                    Swal.fire({
                        icon: 'success',
                        title: 'Disalin!',
                        text: 'Kode tracking telah disalin ke clipboard.',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    // Kembalikan ikon setelah 2 detik
                    setTimeout(() => {
                        icon.classList.remove('bi-check');
                        icon.classList.add('bi-clipboard');
                    }, 2000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal menyalin kode tracking',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            } catch (err) {
                console.error('Error saat menyalin teks:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat menyalin',
                    showConfirmButton: false,
                    timer: 1500
                });
            }

            // Hapus elemen input sementara
            document.body.removeChild(tempInput);
        }
        // AOS init
        AOS.init({
            duration: 400,
            once: true
        });
    </script>
@endpush
