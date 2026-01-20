{{-- filepath: /c:/laragon/www/eservice-app/resources/views/admin/pendaftaran-seminar-proposal/index.blade.php --}}

@extends('layouts.admin.app')

@section('title', 'Pendaftaran Seminar Proposal')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Pendaftaran Seminar Proposal</li>
            </ol>
        </nav>

        <h4 class="fw-bold py-3 mb-2" style="margin-top: -1.2rem">
            <span class="text-muted fw-light">Manajemen /</span> Pendaftaran Seminar Proposal
        </h4>

        {{-- ✅ PERBAIKAN: Statistics Cards - Conditional based on user role --}}
        @if (auth()->user()->isDosenWithApprovalAuthority())
            {{-- DOSEN VIEW: Simplified Statistics --}}
            <div class="row mb-4">
                @if (auth()->user()->isKoordinatorProdi())
                    {{-- Korprodi Statistics --}}
                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <i class="bx bx-hourglass rounded p-2 bg-warning" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                                <span class="fw-semibold d-block mb-1">Menunggu TTD Korprodi</span>
                                <h3 class="card-title mb-2">{{ $statistics['menunggu_ttd_kaprodi'] ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                @elseif(auth()->user()->isKetuaJurusan())
                    {{-- Kajur Statistics --}}
                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <i class="bx bx-hourglass rounded p-2 bg-primary" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                                <span class="fw-semibold d-block mb-1">Menunggu TTD Kajur</span>
                                <h3 class="card-title mb-2">{{ $statistics['menunggu_ttd_kajur'] ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Selesai (untuk semua dosen) --}}
                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="bx bx-check-circle rounded p-2 bg-success" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Selesai</span>
                            <h3 class="card-title mb-2">{{ $statistics['selesai'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- STAFF VIEW: Full Statistics --}}
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
                            <h3 class="card-title mb-2">{{ $statistics['total'] ?? 0 }}</h3>
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
                            <h3 class="card-title mb-2">{{ $statistics['pending'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="bx bx-user-check rounded p-2 bg-info" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Pembahas OK</span>
                            <h3 class="card-title mb-2">{{ $statistics['pembahas_ditentukan'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="bx bx-hourglass rounded p-2 bg-primary" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">TTD Korprodi</span>
                            <h3 class="card-title mb-2">{{ $statistics['menunggu_ttd_kaprodi'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="bx bx-hourglass rounded p-2 bg-secondary" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">TTD Kajur</span>
                            <h3 class="card-title mb-2">{{ $statistics['menunggu_ttd_kajur'] ?? 0 }}</h3>
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
                            <h3 class="card-title mb-2">{{ $statistics['selesai'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Main Card --}}
        <div class="card">
            {{-- Card Header with Filters --}}
            <div class="card-header border-bottom">
                <div class="row align-items-center g-3">
                    {{-- Search --}}
                    <div class="col-md-4">
                        <form action="{{ route('admin.pendaftaran-seminar-proposal.index') }}" method="GET">
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
                        <form action="{{ route('admin.pendaftaran-seminar-proposal.index') }}" method="GET"
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

                    {{-- Filter Status (hanya untuk staff) --}}
                    @if (!auth()->user()->isDosenWithApprovalAuthority())
                        <div class="col-md-3">
                            <form action="{{ route('admin.pendaftaran-seminar-proposal.index') }}" method="GET"
                                id="filterStatusForm">
                                <select name="status" class="form-select" onchange="this.form.submit()">
                                    <option value="">Semua Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>
                                    <option value="pembahas_ditentukan"
                                        {{ request('status') == 'pembahas_ditentukan' ? 'selected' : '' }}>
                                        Pembahas Ditentukan
                                    </option>
                                    <option value="menunggu_ttd_kaprodi"
                                        {{ request('status') == 'menunggu_ttd_kaprodi' ? 'selected' : '' }}>
                                        Menunggu TTD Korprodi
                                    </option>
                                    <option value="menunggu_ttd_kajur"
                                        {{ request('status') == 'menunggu_ttd_kajur' ? 'selected' : '' }}>
                                        Menunggu TTD Kajur
                                    </option>
                                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>
                                        Selesai
                                    </option>
                                </select>
                            </form>
                        </div>
                    @endif

                    {{-- Reset Filter --}}
                    <div class="col-md-2">
                        <a href="{{ route('admin.pendaftaran-seminar-proposal.index') }}"
                            class="btn btn-outline-secondary w-100">
                            <i class="bx bx-reset me-1"></i> Reset
                        </a>
                    </div>

                    {{-- Status Dosen Button (hanya untuk staff) --}}
                    @if (!auth()->user()->isDosenWithApprovalAuthority())
                        <div class="col-md-2">
                            <button type="button" class="btn btn-info w-100" data-bs-toggle="modal"
                                data-bs-target="#modalDosenStatus">
                                <i class="bx bx-list-ul me-1"></i> Status Dosen
                            </button>
                        </div>
                    @endif
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

                {{-- Info Banner untuk Dosen --}}
                @if (auth()->user()->isDosenWithApprovalAuthority())
                    <div class="alert alert-info mb-4">
                        <i class="bx bx-info-circle me-2"></i>
                        <strong>Info:</strong>
                        @if (auth()->user()->isKoordinatorProdi())
                            Anda melihat daftar pendaftaran yang memerlukan tanda tangan Korprodi atau sudah selesai.
                        @elseif(auth()->user()->isKetuaJurusan())
                            Anda melihat daftar pendaftaran yang memerlukan tanda tangan Kajur atau sudah selesai.
                        @endif
                    </div>
                @endif

                {{-- Table --}}
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="20%">Mahasiswa</th>
                                <th width="10%">Angkatan</th>
                                <th width="25%">Judul Skripsi</th>
                                <th width="15%">Status</th>
                                <th width="15%">Tanggal Pengajuan</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendaftaran as $item)
                                <tr>
                                    <td>{{ $loop->iteration + $pendaftaran->firstItem() - 1 }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold">{{ $item->user->name }}</span>
                                            <small class="text-muted">NIM: {{ $item->user->nim }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-primary">{{ $item->angkatan }}</span>
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 300px;"
                                            data-bs-toggle="tooltip" title="{{ strip_tags($item->judul_skripsi) }}">
                                            {!! Str::limit($item->judul_skripsi, 50, '...') !!}
                                        </span>
                                    </td>
                                    <td>{!! $item->status_badge !!}</td>
                                    <td>
                                        <small>{{ $item->created_at->format('d M Y') }}</small><br>
                                        <small class="text-muted">{{ $item->created_at->format('H:i') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.pendaftaran-seminar-proposal.show', $item) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="bx bx-show me-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="mb-3">
                                            <i class="bx bx-search-alt" style="font-size: 3rem; opacity: 0.3;"></i>
                                        </div>
                                        <p class="text-muted">
                                            @if (auth()->user()->isDosenWithApprovalAuthority())
                                                Tidak ada pendaftaran yang memerlukan persetujuan Anda saat ini.
                                            @else
                                                Tidak ada data pendaftaran seminar proposal
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if ($pendaftaran->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Menampilkan {{ $pendaftaran->firstItem() }} - {{ $pendaftaran->lastItem() }}
                            dari {{ $pendaftaran->total() }} data
                        </div>
                        {{ $pendaftaran->links() }}
                    </div>
                </div>
            @endif
        </div>

        {{-- Include Dosen Status Modal (hanya untuk staff) --}}
        @if (!auth()->user()->isDosenWithApprovalAuthority())
            @include('admin.pendaftaran-seminar-proposal.modals.dosen-status-modal')
        @endif
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
