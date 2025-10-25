@extends('layouts.admin.app')

@section('title', 'Manajemen Pembayaran UKT')

@push('styles')
    <style>
        .stats-card {
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .filter-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            background: #f8f9fa;
            border-radius: 20px;
            font-size: 12px;
        }

        .search-highlight {
            background-color: #fff3cd;
            padding: 2px 4px;
            border-radius: 3px;
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
                <li class="breadcrumb-item active">Pembayaran UKT</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Pembayaran UKT</h4>
                <p class="text-muted mb-0">Kelola data pembayaran UKT mahasiswa</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.pembayaran-ukt.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Tambah Data
                </a>
                {{-- Update the export dropdown in header --}}
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bx bx-download me-1"></i> Import/Export
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <h6 class="dropdown-header">Import Data</h6>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.pembayaran-ukt.download-template') }}">
                                <i class="bx bx-file me-2 text-info"></i>Download Template
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.pembayaran-ukt.import') }}">
                                <i class="bx bx-import me-2 text-primary"></i>Import Data Excel
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <h6 class="dropdown-header">Export Data</h6>
                        </li>

                        <li>
                            <a class="dropdown-item"
                                href="{{ route('admin.pembayaran-ukt.export', [
                                    'tahun_ajaran' => request('tahun_ajaran'),
                                    'status' => request('status'),
                                    'search' => request('search'),
                                ]) }}"
                                onclick="return handleExport(this)">
                                <i class="bx bx-export me-2 text-success"></i>Export Sesuai Filter
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

        <!-- Statistics Cards -->
        @if (isset($statistics))
            <div class="row mb-4">
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card stats-card border-0 shadow-sm" onclick="filterByStatus('all')">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-1 small">Total Data</p>
                                    <h3 class="mb-0 fw-bold">{{ number_format($statistics['total']) }}</h3>
                                </div>
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="bx bx-list-ul bx-md"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card stats-card border-0 shadow-sm" onclick="filterByStatus('bayar')">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-1 small">Sudah Bayar</p>
                                    <h3 class="mb-0 fw-bold text-success">{{ number_format($statistics['bayar']) }}</h3>
                                    @if ($statistics['total'] > 0)
                                        <small class="text-muted">
                                            {{ number_format(($statistics['bayar'] / $statistics['total']) * 100, 1) }}%
                                        </small>
                                    @endif
                                </div>
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="bx bx-check-circle bx-md"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card stats-card border-0 shadow-sm" onclick="filterByStatus('belum_bayar')">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-1 small">Belum Bayar</p>
                                    <h3 class="mb-0 fw-bold text-warning">{{ number_format($statistics['belum_bayar']) }}
                                    </h3>
                                    @if ($statistics['total'] > 0)
                                        <small class="text-muted">
                                            {{ number_format(($statistics['belum_bayar'] / $statistics['total']) * 100, 1) }}%
                                        </small>
                                    @endif
                                </div>
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-warning">
                                        <i class="bx bx-time-five bx-md"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filter Card -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.pembayaran-ukt.index') }}" method="GET" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Tahun Ajaran</label>
                            <select class="form-select form-select-sm" name="tahun_ajaran" id="tahunAjaranFilter">
                                <option value="">Semua Tahun Ajaran</option>
                                @foreach ($tahunAjaranList as $tahun)
                                    <option value="{{ $tahun->id }}" @selected(request('tahun_ajaran') == $tahun->id || (!request('tahun_ajaran') && $tahunAjaranAktif && $tahunAjaranAktif->id == $tahun->id))>
                                        {{ $tahun->tahun }} - {{ ucfirst($tahun->semester) }}
                                        @if ($tahun->status_aktif)
                                            (Aktif)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small text-muted">Status Pembayaran</label>
                            <select class="form-select form-select-sm" name="status" id="statusFilter">
                                <option value="">Semua Status</option>
                                <option value="bayar" @selected(request('status') == 'bayar')>Sudah Bayar</option>
                                <option value="belum_bayar" @selected(request('status') == 'belum_bayar')>Belum Bayar</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small text-muted">Pencarian</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white">
                                    <i class="bx bx-search"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" name="search"
                                    placeholder="Cari NIM atau nama mahasiswa..." value="{{ request('search') }}">
                                @if (request('search'))
                                    <button type="button" class="btn btn-outline-secondary" onclick="clearSearch()">
                                        <i class="bx bx-x"></i>
                                    </button>
                                @endif
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-search me-1"></i>Cari
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Active Filters -->
                @if (request()->hasAny(['tahun_ajaran', 'status', 'search']))
                    <div class="mt-3 d-flex flex-wrap gap-2 align-items-center">
                        <small class="text-muted">Filter aktif:</small>
                        @if (request('tahun_ajaran'))
                            <span class="filter-badge">
                                <i class="bx bx-calendar"></i>
                                {{ $tahunAjaranList->where('id', request('tahun_ajaran'))->first()->tahun ?? '' }}
                                <button type="button" class="btn-close btn-close-sm"
                                    onclick="removeFilter('tahun_ajaran')"></button>
                            </span>
                        @endif
                        @if (request('status'))
                            <span class="filter-badge">
                                <i class="bx bx-filter"></i>
                                {{ request('status') == 'bayar' ? 'Sudah Bayar' : 'Belum Bayar' }}
                                <button type="button" class="btn-close btn-close-sm"
                                    onclick="removeFilter('status')"></button>
                            </span>
                        @endif
                        @if (request('search'))
                            <span class="filter-badge">
                                <i class="bx bx-search"></i>
                                "{{ request('search') }}"
                                <button type="button" class="btn-close btn-close-sm"
                                    onclick="removeFilter('search')"></button>
                            </span>
                        @endif
                        <button type="button" class="btn btn-sm btn-link text-danger" onclick="clearAllFilters()">
                            <i class="bx bx-x"></i> Hapus Semua Filter
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="card shadow-sm">
            <!-- Bulk Actions (Optional) -->
            <div class="card-header border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <input type="checkbox" class="form-check-input" id="selectAll">
                        <label class="form-check-label ms-2" for="selectAll">Pilih Semua</label>
                    </div>
                    <div id="bulkActions" style="display: none;">
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-success" onclick="bulkUpdateStatus('bayar')">
                                <i class="bx bx-check"></i> Set Lunas
                            </button>
                            <button type="button" class="btn btn-sm btn-warning"
                                onclick="bulkUpdateStatus('belum_bayar')">
                                <i class="bx bx-x"></i> Set Belum Bayar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table border-top">
                    <thead>
                        <tr>
                            <th width="3%">
                                <input type="checkbox" class="form-check-input" id="selectAllTable">
                            </th>
                            <th width="5%">No</th>
                            <th width="15%">NIM</th>
                            <th width="25%">Nama Mahasiswa</th>
                            <th width="20%">Tahun Ajaran</th>
                            <th width="12%">Status</th>
                            <th width="15%">Terakhir Update</th>
                            <th width="5%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pembayaran as $item)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input row-checkbox"
                                        value="{{ $item->id }}">
                                </td>
                                <td>{{ $loop->iteration + ($pembayaran->currentPage() - 1) * $pembayaran->perPage() }}</td>
                                <td>
                                    <span class="fw-medium">{{ $item->mahasiswa->nim }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <div class="avatar-initial rounded bg-label-primary">
                                                {{ strtoupper(substr($item->mahasiswa->name, 0, 2)) }}
                                            </div>
                                        </div>
                                        <span>{{ $item->mahasiswa->name }}</span>
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
                                            <i class="bx bx-check-circle me-1"></i>Lunas
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="bx bx-time-five me-1"></i>Belum Bayar
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="bx bx-time me-1"></i>
                                        {{ $item->updated_at->diffForHumans() }}
                                    </small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu shadow-sm">
                                            <a class="dropdown-item"
                                                href="{{ route('admin.pembayaran-ukt.edit', $item->id) }}">
                                                <i class="bx bx-edit me-2"></i> Edit
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <div class="dropdown-header">Ubah Status Cepat</div>
                                            <form action="{{ route('admin.pembayaran-ukt.update-status', $item->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" name="status" value="bayar"
                                                    class="dropdown-item {{ $item->status == 'bayar' ? 'active' : '' }}">
                                                    <i class="bx bx-check-circle me-2"></i> Set Lunas
                                                </button>
                                                <button type="submit" name="status" value="belum_bayar"
                                                    class="dropdown-item {{ $item->status == 'belum_bayar' ? 'active' : '' }}">
                                                    <i class="bx bx-x-circle me-2"></i> Set Belum Bayar
                                                </button>
                                            </form>
                                            <div class="dropdown-divider"></div>
                                            <button type="button" class="dropdown-item text-danger delete-btn"
                                                data-id="{{ $item->id }}">
                                                <i class="bx bx-trash me-2"></i> Hapus
                                            </button>
                                            <form id="delete-form-{{ $item->id }}"
                                                action="{{ route('admin.pembayaran-ukt.destroy', $item->id) }}"
                                                method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bx bx-folder-open" style="font-size: 48px; color: #ccc;"></i>
                                    <p class="text-muted mt-2 mb-0">
                                        @if (request('search'))
                                            Tidak ada hasil untuk pencarian "{{ request('search') }}"
                                        @else
                                            Tidak ada data pembayaran UKT
                                        @endif
                                    </p>
                                    @if (request()->hasAny(['search', 'status']))
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                                            onclick="clearAllFilters()">
                                            Hapus Filter
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($pembayaran->hasPages())
                <div class="card-footer border-top py-3">
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
@endsection

@push('scripts')
    <script>
        // Filter functions
        function filterByStatus(status) {
            const form = document.getElementById('filterForm');
            const statusSelect = document.getElementById('statusFilter');
            statusSelect.value = status === 'all' ? '' : status;
            form.submit();
        }

        function clearSearch() {
            const form = document.getElementById('filterForm');
            form.querySelector('input[name="search"]').value = '';
            form.submit();
        }

        function removeFilter(filterName) {
            const form = document.getElementById('filterForm');
            const input = form.querySelector(`[name="${filterName}"]`);
            if (input) {
                input.value = '';
                form.submit();
            }
        }

        function clearAllFilters() {
            window.location.href = '{{ route('admin.pembayaran-ukt.index') }}';
        }

        // Bulk actions
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('selectAll');
            const selectAllTable = document.getElementById('selectAllTable');
            const checkboxes = document.querySelectorAll('.row-checkbox');
            const bulkActions = document.getElementById('bulkActions');

            function updateBulkActions() {
                const checked = document.querySelectorAll('.row-checkbox:checked').length;
                bulkActions.style.display = checked > 0 ? 'block' : 'none';
            }

            [selectAll, selectAllTable].forEach(checkbox => {
                if (checkbox) {
                    checkbox.addEventListener('change', function() {
                        checkboxes.forEach(cb => cb.checked = this.checked);
                        updateBulkActions();
                    });
                }
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkActions);
            });

            // Delete confirmation
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    Swal.fire({
                        title: 'Konfirmasi Hapus',
                        text: 'Data pembayaran akan dihapus permanen. Lanjutkan?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(`delete-form-${id}`).submit();
                        }
                    });
                });
            });
        });

        function bulkUpdateStatus(status) {
            const checked = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);

            if (checked.length === 0) {
                Swal.fire('Peringatan', 'Pilih minimal satu data', 'warning');
                return;
            }

            const statusLabel = status === 'bayar' ? 'Lunas' : 'Belum Bayar';

            Swal.fire({
                title: 'Konfirmasi Update Massal',
                text: `Ubah ${checked.length} data menjadi status "${statusLabel}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ubah',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('admin.pembayaran-ukt.bulk-update-status') }}';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    checked.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });

                    const statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.name = 'status';
                    statusInput.value = status;
                    form.appendChild(statusInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function handleExport(element) {
            event.preventDefault();

            const url = element.href;

            Swal.fire({
                title: 'Memproses Export',
                html: 'File Excel sedang dipersiapkan...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = url;
            document.body.appendChild(iframe);

            setTimeout(() => {
                Swal.close();
                Swal.fire({
                    title: 'Export Berhasil!',
                    text: 'File Excel berhasil diunduh',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });

                setTimeout(() => {
                    document.body.removeChild(iframe);
                }, 5000);
            }, 2000);

            return false;
        }
    </script>
@endpush
