{{-- filepath: resources/views/admin/surat-cuti-akademik/index.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Daftar Surat Cuti Akademik')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">
                @if (auth()->user()->hasRole('dosen'))
                    @if (str_contains(auth()->user()->jabatan, 'Koordinator Program Studi'))
                        <span class="text-muted">Daftar Surat Menunggu Persetujuan Korprodi</span>
                    @else
                        <span class="text-muted">Daftar Surat Menunggu Persetujuan Pimpinan</span>
                    @endif
                @else
                    <span class="text-muted">Daftar Pengajuan Surat Cuti Akademik</span>
                @endif
            </h4>

            <!-- Statistics Badge (Optional) -->
            @if (!auth()->user()->hasRole('dosen') && isset($statistics))
                <div class="d-none d-md-flex gap-2">
                    <span class="badge bg-label-primary">Total: {{ $statistics['total'] }}</span>
                    <span class="badge bg-label-warning">Diajukan: {{ $statistics['diajukan'] }}</span>
                    <span class="badge bg-label-success">Disetujui: {{ $statistics['disetujui'] }}</span>
                </div>
            @endif
        </div>

        <div class="card">
            <!-- Enhanced Filter Section -->
            <div class="card-header border-bottom">
                <div class="row g-3">
                    <!-- Status Filter - Hanya untuk staff/admin -->
                    @if (!auth()->user()->hasRole('dosen'))
                        <div class="col-12 col-md-4 col-lg-3">
                            <label class="form-label small text-muted mb-1">Status Surat</label>
                            <select class="form-select form-select-sm" id="statusFilter"
                                onchange="filterByStatus(this.value)">
                                <option value="all" {{ !$status || $status === 'all' ? 'selected' : '' }}>Semua Status
                                </option>
                                <option value="diajukan" {{ $status === 'diajukan' ? 'selected' : '' }}>
                                    Diajukan @if (isset($statistics))
                                        ({{ $statistics['diajukan'] }})
                                    @endif
                                </option>
                                <option value="diproses" {{ $status === 'diproses' ? 'selected' : '' }}>
                                    Diproses @if (isset($statistics))
                                        ({{ $statistics['diproses'] }})
                                    @endif
                                </option>
                                <option value="disetujui" {{ $status === 'disetujui' ? 'selected' : '' }}>
                                    Disetujui @if (isset($statistics))
                                        ({{ $statistics['disetujui'] }})
                                    @endif
                                </option>
                                <option value="siap_diambil" {{ $status === 'siap_diambil' ? 'selected' : '' }}>
                                    Siap Diambil @if (isset($statistics))
                                        ({{ $statistics['siap_diambil'] }})
                                    @endif
                                </option>
                            </select>
                        </div>
                    @endif

                    <!-- Enhanced Search -->
                    <div class="col-12 col-md-8 col-lg-6">
                        <label class="form-label small text-muted mb-1">Pencarian</label>
                        <form action="{{ route('admin.surat-cuti-akademik.index') }}" method="GET" id="searchForm">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white">
                                    <i class="bx bx-search"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" name="search" id="searchInput"
                                    value="{{ $search ?? '' }}" placeholder="Cari nama, NIM, nomor surat, tahun ajaran..."
                                    aria-label="Search" />
                                @if (!auth()->user()->hasRole('dosen'))
                                    <input type="hidden" name="status" value="{{ $status }}" id="hiddenStatus">
                                @else
                                    <input type="hidden" name="status" value="{{ $status }}">
                                @endif

                                @if ($search)
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSearch()">
                                        <i class="bx bx-x"></i>
                                    </button>
                                @endif

                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="bx bx-search me-1"></i>Cari
                                </button>
                            </div>

                            <!-- Search Tips -->
                            <small class="text-muted d-block mt-1">
                                <i class="bx bx-info-circle"></i>
                                Tip: Ketik nama mahasiswa, NIM, nomor surat, atau tahun ajaran
                            </small>
                        </form>
                    </div>

                    <!-- Quick Actions -->
                    <div class="col-12 col-md-12 col-lg-3">
                        <label class="form-label small text-muted mb-1">Aksi Cepat</label>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary w-100" onclick="refreshPage()">
                                <i class="bx bx-refresh"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Search Result Info -->
                @if ($search)
                    <div class="mt-3">
                        <div class="alert alert-info alert-dismissible fade show mb-0" role="alert">
                            <i class="bx bx-search me-2"></i>
                            Menampilkan hasil pencarian untuk: <strong>"{{ $search }}"</strong>
                            <span class="badge bg-primary ms-2">{{ $surats->total() }} hasil</span>
                            <button type="button" class="btn-close" onclick="clearSearch()" aria-label="Close"></button>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table border-top">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Mahasiswa</th>
                            <th width="20%">No. Surat</th>
                            <th width="15%">Tahun/Semester</th>
                            <th width="20%">Alasan</th>
                            <th width="10%">Status</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($surats as $surat)
                            <tr>
                                <td>{{ ($surats->currentPage() - 1) * $surats->perPage() + $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold">{{ $surat->mahasiswa->name }}</span>
                                        <small class="text-muted">
                                            <i class="bx bx-id-card"></i> {{ $surat->mahasiswa->nim }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium">{{ $surat->nomor_surat ?? '-' }}</span>
                                        @if ($surat->tanggal_surat)
                                            <small class="text-muted">
                                                <i class="bx bx-calendar"></i>
                                                {{ $surat->tanggal_surat->format('d M Y') }}
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="badge bg-label-info">{{ $surat->tahun_ajaran }}</span>
                                        <small class="text-muted mt-1">{{ ucfirst($surat->semester) }}</small>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-truncate d-block" style="max-width: 180px;"
                                        title="{{ $surat->alasan_pengajuan }}">
                                        {{ Str::limit($surat->alasan_pengajuan, 40) }}
                                    </small>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match ($surat->status ?? 'diajukan') {
                                            'diajukan' => 'warning',
                                            'diproses' => 'info',
                                            'disetujui' => 'success',
                                            'siap_diambil' => 'primary',
                                            default => 'secondary',
                                        };

                                        $statusLabel = match ($surat->status ?? 'diajukan') {
                                            'diajukan' => 'Diajukan',
                                            'diproses' => 'Diproses',
                                            'disetujui' => 'Disetujui',
                                            'siap_diambil' => 'Siap Diambil',
                                            default => ucfirst($surat->status ?? 'diajukan'),
                                        };
                                    @endphp
                                    <span class="badge bg-label-{{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item text-info"
                                                href="{{ route('admin.surat-cuti-akademik.show', $surat->id) }}">
                                                <i class="bx bx-show me-1"></i> Detail
                                            </a>
                                            @if (auth()->user()->hasRole('staff') && in_array($surat->status, ['diajukan']))
                                                <form id="delete-form-{{ $surat->id }}"
                                                    action="{{ route('admin.surat-cuti-akademik.destroy', $surat->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="dropdown-item text-danger delete-btn"
                                                        data-form-id="delete-form-{{ $surat->id }}">
                                                        <i class="bx bx-trash me-1"></i> Hapus
                                                    </button>
                                                </form>
                                            @endif
                                            @if (in_array($surat->status, ['siap_diambil']))
                                                <a class="dropdown-item text-success"
                                                    href="{{ route('admin.surat-cuti-akademik.download', $surat->id) }}">
                                                    <i class="bx bx-download me-1"></i> Unduh
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bx bx-folder-open" style="font-size: 48px; color: #ccc;"></i>
                                        <p class="mt-2 mb-0 text-muted">
                                            @if ($search)
                                                Tidak ada hasil untuk pencarian "{{ $search }}"
                                            @elseif(auth()->user()->hasRole('dosen'))
                                                Tidak ada surat yang menunggu persetujuan Anda
                                            @else
                                                Tidak ada pengajuan surat cuti akademik
                                            @endif
                                        </p>
                                        @if ($search)
                                            <button type="button" class="btn btn-sm btn-outline-primary mt-2"
                                                onclick="clearSearch()">
                                                <i class="bx bx-x-circle me-1"></i>Hapus Filter
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($surats->hasPages())
                <div class="card-footer border-top py-3">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <small class="text-muted">
                                Menampilkan {{ $surats->firstItem() }} - {{ $surats->lastItem() }}
                                dari {{ $surats->total() }} data
                            </small>
                        </div>
                        <div class="col-md-6">
                            {{ $surats->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Filter by status
        function filterByStatus(status) {
            const searchValue = document.getElementById('searchInput').value;

            let url = '{{ route('admin.surat-cuti-akademik.index') }}';
            const params = new URLSearchParams();

            if (status && status !== 'all') {
                params.append('status', status);
            }

            if (searchValue) {
                params.append('search', searchValue);
            }

            const queryString = params.toString();
            window.location.href = queryString ? `${url}?${queryString}` : url;
        }

        // Clear search
        function clearSearch() {
            const statusValue = document.getElementById('statusFilter') ?
                document.getElementById('statusFilter').value : '';
            let url = '{{ route('admin.surat-cuti-akademik.index') }}';

            if (statusValue && statusValue !== 'all') {
                url += '?status=' + statusValue;
            }

            window.location.href = url;
        }

        // Refresh page
        function refreshPage() {
            window.location.reload();
        }

        // Search on Enter key
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('searchForm').submit();
            }
        });

        // SweetAlert for Delete Confirmation
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const formId = this.getAttribute('data-form-id');
                    const form = document.getElementById(formId);

                    Swal.fire({
                        title: 'Konfirmasi Hapus',
                        text: 'Apakah Anda yakin ingin menghapus pengajuan ini? Tindakan ini tidak dapat dibatalkan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Show loading state on form submit
            document.getElementById('searchForm').addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-1"></span>Mencari...';
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        /* Search input focus */
        #searchInput:focus {
            border-color: #696cff;
            box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.25);
        }

        /* Table hover effect */
        .table tbody tr:hover {
            background-color: rgba(105, 108, 255, 0.05);
        }

        /* Badge animations */
        .badge {
            transition: all 0.3s ease;
        }

        .badge:hover {
            transform: scale(1.05);
        }

        /* Dropdown button hover */
        .dropdown-toggle:hover {
            background-color: rgba(105, 108, 255, 0.1);
            border-radius: 50%;
        }

        /* Empty state icon */
        .bx-folder-open {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        /* Responsive table text */
        @media (max-width: 768px) {
            .table td {
                font-size: 0.875rem;
            }

            .table .text-truncate {
                max-width: 100px !important;
            }
        }
    </style>
@endpush
