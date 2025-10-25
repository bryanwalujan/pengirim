{{-- filepath: resources/views/admin/ukt/report.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Laporan Pembayaran UKT')

@php
    use App\Models\PembayaranUkt;
@endphp

@push('styles')
    <style>
        .badge {
            font-size: 0.85em;
            padding: 0.35em 0.65em;
        }

        .summary-card {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .summary-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .summary-value {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .progress {
            height: 0.5rem;
        }

        .year-card {
            transition: all 0.3s ease;
            height: 100%;
        }

        .year-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .active-year {
            border-left: 4px solid #696cff;
            background-color: rgba(105, 108, 255, 0.05);
        }

        .pagination .page-item.active .page-link {
            background-color: #696cff;
            border-color: #696cff;
        }

        .pagination .page-link {
            color: #696cff;
        }

        .summary-card .avatar-initial {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .alert-warning {
            background-color: rgba(255, 171, 0, 0.1);
            border-color: rgba(255, 171, 0, 0.2);
        }

        .filter-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="no-print">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">
                        <i class="bx bx-home-alt"></i> Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.pembayaran-ukt.index') }}">Pembayaran UKT</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Laporan</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="bx bx-chart text-primary"></i> Laporan Pembayaran UKT
                </h4>
                <p class="text-muted mb-0">Monitoring dan analisis pembayaran UKT mahasiswa</p>
            </div>
            <div class="d-flex gap-2">

                <div class="btn-group">
                    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bx bx-download me-1"></i> Export
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item"
                                href="{{ route('admin.pembayaran-ukt.export', [
                                    'tahun_ajaran' => request('tahun_ajaran_id'),
                                    'status' => request('status'),
                                    'search' => request('search'),
                                ]) }}"
                                onclick="return handleExport(this)">
                                <i class="bx bx-file me-2 text-success"></i>Export Excel (Filter Aktif)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.pembayaran-ukt.export') }}"
                                onclick="return handleExport(this)">
                                <i class="bx bx-file-blank me-2 text-warning"></i>Export Semua Data
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Tahun Ajaran Cards -->
        <div class="row mb-4">
            @forelse($tahunAjaranList as $tahun)
                <div class="col-md-4 mb-3">
                    <div class="card year-card {{ $tahun->status_aktif ? 'active-year' : '' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-1">{{ $tahun->tahun }}</h5>
                                    <span class="badge bg-{{ $tahun->semester == 'ganjil' ? 'primary' : 'info' }}">
                                        Semester {{ ucfirst($tahun->semester) }}
                                    </span>
                                    @if ($tahun->status_aktif)
                                        <span class="badge bg-success ms-1">Aktif</span>
                                    @endif
                                </div>
                                <a href="{{ route('admin.pembayaran-ukt.report', ['tahun_ajaran_id' => $tahun->id]) }}"
                                    class="btn btn-sm {{ $selectedTahunAjaran && $selectedTahunAjaran->id == $tahun->id ? 'btn-primary' : 'btn-outline-primary' }}">
                                    <i class="bx bx-show me-1"></i> Lihat
                                </a>
                            </div>
                            @php
                                $countBayar = PembayaranUkt::where('tahun_ajaran_id', $tahun->id)
                                    ->where('status', 'bayar')
                                    ->count();
                                $countBelumBayar = PembayaranUkt::where('tahun_ajaran_id', $tahun->id)
                                    ->where('status', 'belum_bayar')
                                    ->count();
                                $total = $countBayar + $countBelumBayar;
                                $percentage = $total > 0 ? round(($countBayar / $total) * 100) : 0;
                            @endphp
                            <div class="mt-3">
                                @if ($total > 0)
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small">Progress Pembayaran</span>
                                        <span class="small fw-bold">{{ $percentage }}%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar"
                                            style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <small class="text-success">
                                            <i class="bx bx-check-circle"></i> {{ $countBayar }} Lunas
                                        </small>
                                        <small class="text-warning">
                                            <i class="bx bx-time"></i> {{ $countBelumBayar }} Belum
                                        </small>
                                    </div>
                                @else
                                    <div class="alert alert-warning py-2 mb-0">
                                        <small>
                                            <i class="bx bx-info-circle"></i> Belum ada data pembayaran
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning">
                        <i class="bx bx-error-circle me-2"></i>
                        Tidak ada data tahun ajaran tersedia
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination untuk Tahun Ajaran -->
        @if ($tahunAjaranList->hasPages())
            <div class="row mb-4 no-print">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body py-2">
                            {{ $tahunAjaranList->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filter & Details Card -->
        <div class="card mb-4">
            <div class="card-body">
                <!-- Filter Header -->
                <div class="d-flex justify-content-between align-items-center mb-3 no-print">
                    <h5 class="card-title mb-0">
                        @if ($selectedTahunAjaran)
                            <span class="badge bg-label-primary me-2">
                                <i class="bx bx-calendar"></i>
                                {{ $selectedTahunAjaran->tahun }} - {{ ucfirst($selectedTahunAjaran->semester) }}
                            </span>
                        @else
                            <span class="badge bg-label-secondary me-2">
                                <i class="bx bx-list-ul"></i> Semua Tahun Ajaran
                            </span>
                        @endif
                        Detail Pembayaran
                    </h5>

                    <!-- Filter Form -->
                    <form action="{{ route('admin.pembayaran-ukt.report') }}" method="GET" class="d-flex gap-2">
                        @if ($selectedTahunAjaran)
                            <input type="hidden" name="tahun_ajaran_id" value="{{ $selectedTahunAjaran->id }}">
                        @endif

                        <select class="form-select form-select-sm" name="status" style="width: 150px;">
                            <option value="">Semua Status</option>
                            <option value="bayar" @selected(request('status') == 'bayar')>Sudah Bayar</option>
                            <option value="belum_bayar" @selected(request('status') == 'belum_bayar')>Belum Bayar</option>
                        </select>

                        <div class="input-group input-group-sm" style="width: 250px;">
                            <span class="input-group-text bg-white">
                                <i class="bx bx-search"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" placeholder="Cari NIM/Nama..."
                                name="search" value="{{ request('search') }}">
                        </div>

                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="bx bx-filter"></i> Filter
                        </button>

                        @if (request()->hasAny(['status', 'search']))
                            <a href="{{ route('admin.pembayaran-ukt.report', ['tahun_ajaran_id' => request('tahun_ajaran_id')]) }}"
                                class="btn btn-outline-secondary btn-sm">
                                <i class="bx bx-reset"></i>
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Summary Statistics -->
                <div class="row mb-4">
                    <!-- Total Mahasiswa -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card summary-card bg-gradient-primary text-white border-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white mb-2 opacity-75 small">Total Mahasiswa</h6>
                                        <div class="summary-value">{{ number_format($totalMahasiswa) }}</div>
                                        <small class="opacity-75">Terdaftar</small>
                                    </div>
                                    <div class="avatar">
                                        <span class="avatar-initial rounded" style="background: rgba(255,255,255,0.2);">
                                            <i class="bx bx-user fs-3"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sudah Bayar -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card summary-card bg-white border-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-2 text-muted small">Sudah Bayar</h6>
                                        <div class="summary-value text-success">{{ number_format($sudahBayar) }}</div>
                                        @if ($totalMahasiswa > 0)
                                            <small class="text-muted">{{ $percentagePaid }}% dari total</small>
                                            <div class="progress mt-2" style="height: 4px;">
                                                <div class="progress-bar bg-success"
                                                    style="width: {{ $percentagePaid }}%"></div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="avatar">
                                        <span class="avatar-initial rounded bg-label-success">
                                            <i class="bx bx-check-circle fs-3"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Belum Bayar -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card summary-card bg-white border-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-2 text-muted small">Belum Bayar</h6>
                                        <div class="summary-value text-warning">{{ number_format($belumBayar) }}</div>
                                        @if ($totalMahasiswa > 0)
                                            <small class="text-muted">{{ $percentageUnpaid }}% dari total</small>
                                            <div class="progress mt-2" style="height: 4px;">
                                                <div class="progress-bar bg-warning"
                                                    style="width: {{ $percentageUnpaid }}%"></div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="avatar">
                                        <span class="avatar-initial rounded bg-label-warning">
                                            <i class="bx bx-time fs-3"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Belum Ada Data -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card summary-card bg-white border-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-2 text-muted small">Belum Ada Data</h6>
                                        <div class="summary-value text-secondary">{{ number_format($belumAdaData) }}</div>
                                        @if ($totalMahasiswa > 0)
                                            <small class="text-muted">{{ $percentageNoData }}% dari total</small>
                                            <div class="progress mt-2" style="height: 4px;">
                                                <div class="progress-bar bg-secondary"
                                                    style="width: {{ $percentageNoData }}%"></div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="avatar">
                                        <span class="avatar-initial rounded bg-label-secondary">
                                            <i class="bx bx-question-mark fs-3"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table border-top">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="12%">NIM</th>
                                <th width="25%">Nama Mahasiswa</th>
                                <th width="18%">Tahun Ajaran</th>
                                <th width="12%">Status</th>
                                <th width="18%">Terakhir Update</th>
                                <th width="10%">Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pembayaran as $item)
                                <tr>
                                    <td>{{ $loop->iteration + ($pembayaran->currentPage() - 1) * $pembayaran->perPage() }}
                                    </td>
                                    <td>
                                        <span class="fw-medium">{{ $item->mahasiswa->nim }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ strtoupper(substr($item->mahasiswa->name, 0, 2)) }}
                                                </span>
                                            </div>
                                            {{ $item->mahasiswa->name }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-info">
                                            {{ $item->tahunAjaran->tahun }} - {{ ucfirst($item->tahunAjaran->semester) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($item->status == 'bayar')
                                            <span class="badge bg-success">
                                                <i class="bx bx-check-circle"></i> Lunas
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="bx bx-time"></i> Belum Bayar
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="bx bx-calendar me-1"></i>
                                            {{ $item->updated_at->format('d/m/Y H:i') }}
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            {{ $item->updated_at->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td>
                                        @if ($item->updatedBy)
                                            <small class="text-muted" title="{{ $item->updatedBy->name }}">
                                                {{ Str::limit($item->updatedBy->name, 15) }}
                                            </small>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bx bx-folder-open" style="font-size: 48px; color: #ccc;"></i>
                                        <p class="text-muted mt-2 mb-0">
                                            @if (request('search'))
                                                Tidak ada hasil untuk "{{ request('search') }}"
                                            @else
                                                Tidak ada data pembayaran UKT
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($pembayaran->hasPages())
                    <div class="card-footer border-top py-3 no-print">
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-2 mb-md-0">
                                <small class="text-muted">
                                    Menampilkan {{ $pembayaran->firstItem() }} - {{ $pembayaran->lastItem() }}
                                    dari {{ $pembayaran->total() }} data
                                </small>
                            </div>
                            <div class="col-md-6">
                                {{ $pembayaran->withQueryString()->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit on status change
            document.querySelector('select[name="status"]')?.addEventListener('change', function() {
                this.form.submit();
            });

        });

        // Export confirmation
        function confirmExport(element) {
            const url = new URL(element.href);
            const params = new URLSearchParams(url.search);

            let filterInfo = [];

            if (params.get('tahun_ajaran')) {
                const tahunSelect = document.querySelector('[name="tahun_ajaran"]');
                if (tahunSelect) {
                    const selectedOption = tahunSelect.options[tahunSelect.selectedIndex];
                    filterInfo.push('Tahun: ' + selectedOption.text);
                }
            }

            if (params.get('status')) {
                filterInfo.push('Status: ' + (params.get('status') === 'bayar' ? 'Sudah Bayar' : 'Belum Bayar'));
            }

            if (params.get('search')) {
                filterInfo.push('Pencarian: "' + params.get('search') + '"');
            }

            const filterText = filterInfo.length > 0 ?
                '\n\nFilter yang diterapkan:\n' + filterInfo.join('\n') :
                '\n\nSemua data akan diexport';

            Swal.fire({
                title: 'Export Data Pembayaran UKT',
                html: `
            <p>File Excel akan segera diunduh</p>
            <small class="text-muted">${filterText.replace(/\n/g, '<br>')}</small>
        `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: '<i class="bx bx-download"></i> Export',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#28a745',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return new Promise((resolve) => {
                        // Show loading
                        Swal.showLoading();

                        // Simulate download delay
                        setTimeout(() => {
                            resolve(true);
                        }, 500);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    // Open in new tab
                    window.open(element.href, '_blank');

                    // Show success message
                    Swal.fire({
                        title: 'Export Dimulai!',
                        text: 'File akan segera terunduh',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });

            return false; // Prevent default link behavior
        }

        function handleExport(element) {
            event.preventDefault();

            const url = element.href;

            // Show loading
            Swal.fire({
                title: 'Memproses Export',
                html: 'Mohon tunggu sebentar...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Create hidden iframe for download
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = url;
            document.body.appendChild(iframe);

            // Remove loading after delay
            setTimeout(() => {
                Swal.close();
                Swal.fire({
                    title: 'Export Berhasil!',
                    text: 'File Excel sedang diunduh',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });

                // Remove iframe after download
                setTimeout(() => {
                    document.body.removeChild(iframe);
                }, 5000);
            }, 2000);

            return false;
        }

        // Auto-refresh page after export (optional)
        window.addEventListener('focus', function() {
            if (document.exportInProgress) {
                document.exportInProgress = false;
                // Optional: refresh statistics
                location.reload();
            }
        });
    </script>
@endpush
