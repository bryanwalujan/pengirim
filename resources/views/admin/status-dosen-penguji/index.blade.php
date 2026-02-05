@extends('layouts.admin.app')

@section('title', 'Status Dosen Penguji')

@push('styles')
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <style>
        .dataTables_filter {
            margin-bottom: 1rem;
        }
        .dt-buttons {
            margin-bottom: 1rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Status Dosen Penguji</li>
            </ol>
        </nav>

        <h4 class="fw-bold py-3 mb-2" style="margin-top: -1.2rem">
            <span class="text-muted fw-light">Manajemen /</span> Status Dosen Penguji
        </h4>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="avatar mx-auto mb-3">
                            <span class="avatar-initial rounded p-2 bg-label-primary">
                                <i class="bx bx-user fs-4"></i>
                            </span>
                        </div>
                        <span class="d-block mb-1 text-muted small fw-semibold">Total Dosen</span>
                        <h3 class="card-title mb-0">{{ count($pengujiStatistics) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="avatar mx-auto mb-3">
                            <span class="avatar-initial rounded p-2 bg-label-success">
                                <i class="bx bx-task fs-4"></i>
                            </span>
                        </div>
                        <span class="d-block mb-1 text-muted small fw-semibold">Beban Aktif</span>
                        <h3 class="card-title mb-0">{{ collect($pengujiStatistics)->sum('beban_active') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="avatar mx-auto mb-3">
                            <span class="avatar-initial rounded p-2 bg-label-warning">
                                <i class="bx bx-history fs-4"></i>
                            </span>
                        </div>
                        <span class="d-block mb-1 text-muted small fw-semibold">Beban Historis</span>
                        <h3 class="card-title mb-0">{{ collect($pengujiStatistics)->sum('beban_replaced') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="avatar mx-auto mb-3">
                            <span class="avatar-initial rounded p-2 bg-label-info">
                                <i class="bx bx-bar-chart fs-4"></i>
                            </span>
                        </div>
                        <span class="d-block mb-1 text-muted small fw-semibold">Rata-rata Beban</span>
                        <h3 class="card-title mb-0">
                            {{ count($pengujiStatistics) > 0 ? number_format(collect($pengujiStatistics)->avg('total_beban'), 1) : 0 }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Nav Tabs --}}
        <div class="nav-align-top mb-4">
            <ul class="nav nav-pills mb-3" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-status" aria-controls="navs-pills-top-status" aria-selected="true">
                        <i class="bx bx-list-ul me-1"></i> Ringkasan Beban Dosen
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-history" aria-controls="navs-pills-top-history" aria-selected="false">
                        <i class="bx bx-history me-1"></i> Riwayat Penggantian & Ketidakhadiran
                    </button>
                </li>
            </ul>

            <div class="tab-content p-0 bg-transparent border-0 shadow-none">
                {{-- TAB 1: STATUS BEBAN --}}
                <div class="tab-pane fade show active" id="navs-pills-top-status" role="tabpanel">
                    <div class="card">
                        <div class="card-header border-bottom py-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <h5 class="m-0 text-primary fw-bold">
                                    <i class="bx bx-user-check me-2"></i>Status Beban Dosen Penguji
                                </h5>
                                <div class="d-flex align-items-center gap-2">
                                    <select id="filterStatusBeban" class="form-select form-select-sm" style="width: 150px;">
                                        <option value="">Semua Status</option>
                                        <option value="Rendah">Rendah</option>
                                        <option value="Sedang">Sedang</option>
                                        <option value="Tinggi">Tinggi</option>
                                        <option value="Kosong">Kosong</option>
                                    </select>
                                    <a href="{{ route('admin.pendaftaran-ujian-hasil.export-status-dosen') }}" class="btn btn-outline-success btn-sm">
                                        <i class="bx bx-export me-1"></i> Export Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-4">
                            <div class="table-responsive">
                                <table class="table table-hover border-top" id="tableDosenStatus">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="40%">Nama Dosen</th>
                                            <th width="15%" class="text-center">Total Beban</th>
                                            <th width="20%" class="text-center">Breakdown</th>
                                            <th width="20%" class="text-center">Status Beban</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pengujiStatistics as $stat)
                                            @php
                                                $maxBeban = collect($pengujiStatistics)->max('total_beban');
                                                $minBeban = collect($pengujiStatistics)->min('total_beban');
                                                $totalBeban = $stat['total_beban'];

                                                if ($totalBeban == 0) {
                                                    $badgeClass = 'bg-label-secondary';
                                                    $statusText = 'Kosong';
                                                    $iconClass = 'bx-minus-circle';
                                                } elseif ($totalBeban == $minBeban) {
                                                    $badgeClass = 'bg-label-success';
                                                    $statusText = 'Rendah';
                                                    $iconClass = 'bx-chevron-down';
                                                } elseif ($totalBeban == $maxBeban) {
                                                    $badgeClass = 'bg-label-danger';
                                                    $statusText = 'Tinggi';
                                                    $iconClass = 'bx-chevron-up';
                                                } else {
                                                    $badgeClass = 'bg-label-warning';
                                                    $statusText = 'Sedang';
                                                    $iconClass = 'bx-chevron-right';
                                                }
                                            @endphp
                                            <tr data-status-text="{{ $statusText }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <div class="d-flex justify-content-start align-items-center">
                                                        <div class="avatar avatar-sm me-3">
                                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                                {{ strtoupper(substr($stat['dosen']->name, 0, 1)) }}
                                                            </span>
                                                        </div>
                                                        <div class="d-flex flex-column">
                                                            <span class="fw-semibold text-dark">{{ $stat['dosen']->name }}</span>
                                                            <small class="text-muted">{{ $stat['dosen']->email }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-label-dark p-2">{{ $totalBeban }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="mb-1">
                                                        <span class="badge bg-label-primary p-2">
                                                            <i class="bx bx-check-double me-1"></i>Aktif: {{ $stat['beban_active'] }}
                                                        </span>
                                                    </div>
                                                    @if ($stat['beban_replaced'] > 0)
                                                        <span class="badge bg-label-danger p-2">
                                                            <i class="bx bx-x-circle me-1"></i>Digantikan: {{ $stat['beban_replaced'] }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge {{ $badgeClass }} p-2 status-badge">
                                                        <i class="bx {{ $iconClass }} me-1"></i>{{ $statusText }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 2: RIWAYAT PENGGANTIAN --}}
                <div class="tab-pane fade" id="navs-pills-top-history" role="tabpanel">
                    <div class="card">
                        <div class="card-header border-bottom py-3">
                            <h5 class="m-0 text-danger fw-bold">
                                <i class="bx bx-history me-2"></i>Detail Dosen Digantikan (Ujian Hasil)
                            </h5>
                        </div>
                        <div class="card-body pt-4">
                            @php
                                $replacedHistory = collect($pengujiStatistics)->flatMap(function($stat) {
                                    return collect($stat['history_replaced'])->map(function($item) use ($stat) {
                                        $item->dosen_name = $stat['dosen']->name;
                                        return $item;
                                    });
                                })->sortByDesc('tanggal');
                            @endphp

                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover border-top w-100" id="tableReplacedHistory">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Dosen</th>
                                            <th>Mahasiswa</th>
                                            <th class="text-center">Tanggal & Waktu</th>
                                            <th class="text-center">Ruangan</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($replacedHistory as $history)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <span class="fw-bold text-dark">{{ $history->dosen_name }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-semibold text-primary">{{ $history->mahasiswa_name }}</span>
                                                        <small class="text-muted">Ujian Hasil</small>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex flex-column">
                                                        <span>{{ \Carbon\Carbon::parse($history->tanggal)->translatedFormat('d F Y') }}</span>
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($history->jam_mulai)->format('H:i') }} WITA</small>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-label-info">{{ $history->ruangan ?? '-' }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-label-danger">
                                                        <i class="bx bx-user-x me-1"></i>Digantikan / Absen
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            {{-- DataTables handled empty --}}
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- DataTables JS --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Function to re-adjust column width on tab switch
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            });

            // Initialize Summary Table
            if ($('#tableDosenStatus').length) {
                const tableStatus = $('#tableDosenStatus').DataTable({
                    responsive: true,
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                        search: '',
                        searchPlaceholder: 'Cari dosen...'
                    },
                    pageLength: 25,
                    order: [
                        [2, 'desc']
                    ],
                    columnDefs: [{
                        orderable: false,
                        targets: [0]
                    }]
                });

                // Status Beban Filter
                $('#filterStatusBeban').on('change', function() {
                    const val = $(this).val();
                    tableStatus.column(4).search(val ? '^' + val + '$' : '', true, false).draw();
                });
            }

            // Initialize Replaced History Table
            if ($('#tableReplacedHistory').length) {
                $('#tableReplacedHistory').DataTable({
                    responsive: true,
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                        search: '',
                        searchPlaceholder: 'Cari riwayat...'
                    },
                    pageLength: 10,
                    order: [[3, 'desc']], // Sort by Date descending
                    columnDefs: [{ orderable: false, targets: [0] }]
                });
            }
        });
    </script>
@endpush
