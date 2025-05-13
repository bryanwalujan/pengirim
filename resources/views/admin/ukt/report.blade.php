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
        }

        .summary-value {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .progress {
            height: 0.5rem;
        }

        .active-year {
            border-left: 4px solid #696cff;
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
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.pembayaran-ukt.index') }}">Pembayaran UKT</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Laporan</li>
            </ol>
        </nav>
        <!-- /Breadcrumb -->

        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted">Laporan Pembayaran UKT</span>
        </h4>

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
                                    class="btn btn-sm btn-outline-primary">
                                    Lihat Laporan
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
                                        <span>Progress Pembayaran</span>
                                        <span>{{ $percentage }}%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar"
                                            style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <small class="text-success">
                                            <i class="bx bx-check-circle"></i> {{ $countBayar }} Bayar
                                        </small>
                                        <small class="text-warning">
                                            <i class="bx bx-time"></i> {{ $countBelumBayar }} Belum
                                        </small>
                                    </div>
                                @else
                                    <div class="alert alert-warning py-1 my-2">
                                        <small><i class="bx bx-info-circle"></i> Belum ada data pembayaran</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning">
                        Tidak ada data tahun ajaran tersedia
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination untuk Tahun Ajaran -->
        @if ($tahunAjaranList->hasPages())
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body py-2">
                            {{ $tahunAjaranList->withQueryString()->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        @if ($selectedTahunAjaran)
                            <span class="badge bg-label-primary me-2">
                                {{ $selectedTahunAjaran->tahun }} - {{ ucfirst($selectedTahunAjaran->semester) }}
                            </span>
                        @endif
                        Detail Pembayaran
                    </h5>

                    <form action="{{ route('admin.pembayaran-ukt.report') }}" method="GET" class="d-flex gap-2">
                        <select class="form-select form-select-sm" id="status" name="status" style="width: 150px;">
                            <option value="">Semua Status</option>
                            <option value="bayar" @selected(request('status') == 'bayar')>Bayar</option>
                            <option value="belum_bayar" @selected(request('status') == 'belum_bayar')>Belum Bayar</option>
                        </select>
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <input type="text" class="form-control" placeholder="Cari mahasiswa..." name="search"
                                value="{{ request('search') }}">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="bx bx-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Summary Cards -->

                <div class="col-auto mb-3">
                    <div class="card summary-card bg-white text-dark">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-2 fw-bold btn btn-sm btn-outline-primary">Total Mahasiswa</h6>
                                    <div class="summary-value">{{ number_format($totalMahasiswa) }}</div>
                                    @if ($totalMahasiswa == 0)
                                        <small class="text-danger"><i class="bx bx-error"></i> Data mahasiswa
                                            kosong</small>
                                    @endif
                                </div>
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="bx bx-user fs-4"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card summary-card bg-white text-dark">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-2 fw-bold btn btn-sm btn-outline-success">Sudah Bayar</h6>
                                        <div class="summary-value">{{ number_format($sudahBayar) }}</div>
                                        @if ($totalMahasiswa > 0)
                                            <small>{{ $percentagePaid }}% dari total</small>
                                            <div class="progress mt-2">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                    style="width: {{ $percentagePaid }}%"
                                                    aria-valuenow="{{ $percentagePaid }}" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                        @else
                                            <small class="text-danger"><i class="bx bx-error"></i> Tidak ada
                                                data</small>
                                        @endif
                                    </div>
                                    <div class="avatar">
                                        <span class="avatar-initial rounded bg-label-success">
                                            <i class="bx bx-check-circle fs-4"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card summary-card bg-white text-dark">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-2 fw-bold btn btn-sm btn-outline-warning">Belum Bayar</h6>
                                        <div class="summary-value">{{ number_format($belumBayar) }}</div>
                                        @if ($totalMahasiswa > 0)
                                            <small>{{ $percentageUnpaid }}% dari total</small>
                                            <div class="progress mt-2">
                                                <div class="progress-bar bg-warning" role="progressbar"
                                                    style="width: {{ $percentageUnpaid }}%"
                                                    aria-valuenow="{{ $percentageUnpaid }}" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                        @else
                                            <small class="text-danger"><i class="bx bx-error"></i> Tidak ada
                                                data</small>
                                        @endif
                                    </div>
                                    <div class="avatar">
                                        <span class="avatar-initial rounded bg-label-warning">
                                            <i class="bx bx-time fs-4"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card summary-card bg-white text-dark">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-2 fw-bold btn btn-sm btn-outline-secondary">Belum Ada Data</h6>
                                        <div class="summary-value">{{ number_format($belumAdaData) }}</div>
                                        @if ($totalMahasiswa > 0)
                                            <small>{{ $percentageNoData }}% dari total</small>
                                            <div class="progress mt-2">
                                                <div class="progress-bar bg-secondary" role="progressbar"
                                                    style="width: {{ $percentageNoData }}%"
                                                    aria-valuenow="{{ $percentageNoData }}" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                        @else
                                            <small class="text-danger"><i class="bx bx-error"></i> Tidak ada
                                                data</small>
                                        @endif
                                    </div>
                                    <div class="avatar">
                                        <span class="avatar-initial rounded bg-label-secondary">
                                            <i class="bx bx-question-mark fs-4"></i>
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
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Tahun Ajaran</th>
                                <th>Status</th>
                                <th>Terakhir Diupdate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pembayaran as $item)
                                <tr>
                                    <td>{{ $loop->iteration + ($pembayaran->currentPage() - 1) * $pembayaran->perPage() }}
                                    </td>
                                    <td>{{ $item->mahasiswa->nim }}</td>
                                    <td>{{ $item->mahasiswa->name }}</td>
                                    <td>{{ $item->tahunAjaran->tahun }} - {{ ucfirst($item->tahunAjaran->semester) }}</td>
                                    <td>
                                        @if ($item->status == 'bayar')
                                            <span class="badge bg-label-success">Bayar</span>
                                        @else
                                            <span class="badge bg-label-warning">Belum Bayar</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $item->updated_at->format('d/m/Y H:i') }}
                                        @if ($item->updated_by)
                                            <small class="text-muted d-block">Oleh:
                                                {{ $item->updatedBy->name ?? 'Admin' }}</small>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data pembayaran UKT</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($pembayaran->hasPages())
                    <div class="card-footer border-top py-3">
                        {{ $pembayaran->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Reset filter button
            document.querySelectorAll('.reset-filter').forEach(button => {
                button.addEventListener('click', function() {
                    window.location.href = "{{ route('admin.pembayaran-ukt.report') }}";
                });
            });
        });
    </script>
@endpush
