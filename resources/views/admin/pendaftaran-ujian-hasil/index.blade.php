@extends('layouts.admin.app')

@section('title', 'Pendaftaran Ujian Hasil')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Pendaftaran Ujian Hasil</li>
            </ol>
        </nav>

        <h4 class="fw-bold py-3 mb-2" style="margin-top: -1.2rem">
            <span class="text-muted fw-light">Manajemen /</span> Pendaftaran Ujian Hasil
        </h4>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-lg-2 col-md-4 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class="bx bx-list-ul rounded p-2 bg-primary" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Total</span>
                        <h3 class="card-title mb-2">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class="bx bx-time-five rounded p-2 bg-warning" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Pending</span>
                        <h3 class="card-title mb-2">{{ $stats['pending'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class="bx bx-check-circle rounded p-2 bg-success" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Selesai</span>
                        <h3 class="card-title mb-2">{{ $stats['selesai'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <i class="bx bx-x-circle rounded p-2 bg-danger" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Ditolak</span>
                        <h3 class="card-title mb-2">{{ $stats['ditolak'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Card --}}
        <div class="card">
            {{-- Card Header with Filters --}}
            <div class="card-header border-bottom">
                <div class="row align-items-center g-3">
                    {{-- Search --}}
                    <div class="col-md-4">
                        <form action="{{ route('admin.pendaftaran-ujian-hasil.index') }}" method="GET">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                                <input type="text" class="form-control" name="search"
                                    placeholder="Cari nama atau NIM..." value="{{ request('search') }}">
                                <button class="btn btn-outline-primary" type="submit">Cari</button>
                            </div>
                        </form>
                    </div>

                    {{-- Filter Angkatan --}}
                    <div class="col-md-3">
                        <form action="{{ route('admin.pendaftaran-ujian-hasil.index') }}" method="GET"
                            id="filterAngkatanForm">
                            {{-- Preserve status filter if exists --}}
                            @if (request('status'))
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            @endif
                            <select name="angkatan" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Angkatan</option>
                                @foreach ($uniqueAngkatan as $year)
                                    <option value="{{ $year }}"
                                        {{ request('angkatan') == $year ? 'selected' : '' }}>
                                        Angkatan {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    {{-- Filter Status --}}
                    <div class="col-md-3">
                        <form action="{{ route('admin.pendaftaran-ujian-hasil.index') }}" method="GET"
                            id="filterStatusForm">
                            {{-- Preserve angkatan filter if exists --}}
                            @if (request('angkatan'))
                                <input type="hidden" name="angkatan" value="{{ request('angkatan') }}">
                            @endif
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                    Pending
                                </option>
                                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>
                                    Selesai
                                </option>
                                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>
                                    Ditolak
                                </option>
                            </select>
                        </form>
                    </div>

                    {{-- Reset Filter --}}
                    <div class="col-md-2">
                        <a href="{{ route('admin.pendaftaran-ujian-hasil.index') }}"
                            class="btn btn-outline-secondary w-100">
                            <i class="bx bx-reset me-1"></i> Reset
                        </a>
                    </div>

                    {{-- Status Dosen Button --}}
                    <div class="col-md-2">
                        <button type="button" class="btn btn-warning w-100" data-bs-toggle="modal"
                            data-bs-target="#modalDosenStatus">
                            <i class="bx bx-list-ul me-1"></i> Status Dosen
                        </button>
                    </div>
                </div>
            </div>

            {{-- Card Body --}}
            <div class="card-body">
                {{-- Alert Messages --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Table --}}
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="30%">Mahasiswa</th>
                                <th width="15%">Angkatan</th>
                                <th width="20%">Status</th>
                                <th width="15%">Tanggal Pengajuan</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendaftaranUjianHasils as $item)
                                <tr>
                                    <td>{{ $loop->iteration + $pendaftaranUjianHasils->firstItem() - 1 }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold">{{ $item->user->name }}</span>
                                            <small class="text-muted">NIM: {{ $item->user->nim }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-primary">{{ $item->angkatan }}</span>
                                    </td>

                                    <td>{!! $item->status_badge !!}</td>
                                    <td>
                                        <small>{{ $item->created_at->format('d M Y') }}</small><br>
                                        <small class="text-muted">{{ $item->created_at->format('H:i') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            {{-- Detail Button --}}
                                            <a href="{{ route('admin.pendaftaran-ujian-hasil.show', $item) }}"
                                                class="btn btn-sm btn-info" data-bs-toggle="tooltip"
                                                title="Lihat Detail">
                                                <i class="bx bx-show me-1"></i> Detail
                                            </a>

                                            {{-- Delete Button (Staff only) --}}
                                            @can('role', 'staff')
                                                <form action="{{ route('admin.pendaftaran-ujian-hasil.destroy', $item) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus pendaftaran ujian hasil dari {{ $item->user->name }}? Tindakan ini tidak dapat dibatalkan.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        data-bs-toggle="tooltip" title="Hapus Pendaftaran">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="mb-3">
                                            <i class="bx bx-search-alt" style="font-size: 3rem; opacity: 0.3;"></i>
                                        </div>
                                        <p class="text-muted">Tidak ada data pendaftaran ujian hasil</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if ($pendaftaranUjianHasils->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Menampilkan {{ $pendaftaranUjianHasils->firstItem() }} - {{ $pendaftaranUjianHasils->lastItem() }}
                            dari {{ $pendaftaranUjianHasils->total() }} data
                        </div>
                        {{ $pendaftaranUjianHasils->links() }}
                    </div>
                </div>
            @endif
        </div>

        {{-- Include Dosen Status Modal --}}
        @include('admin.pendaftaran-ujian-hasil.dosen-status-modal')
    </div>
@endsection

@push('scripts')
    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush
