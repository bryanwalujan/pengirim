{{-- filepath: resources/views/admin/komisi-proposal/index.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Komisi Proposal')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item breadcrumb-custom-icon active" aria-current="page">Manajemen Komisi Proposal
                </li>
            </ol>
        </nav>
        <h4 class="fw-bold py-3 mb-2" style="margin-top: -1.2rem">
            <span class="text-muted">Data Pengajuan Komisi Proposal</span>
        </h4>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-md-3 col-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0 text-muted">Total</h6>
                                <h3 class="mb-0">{{ $statistics['total'] ?? 0 }}</h3>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-file bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0 text-muted">Menunggu PA</h6>
                                <h3 class="mb-0">{{ $statistics['pending'] ?? 0 }}</h3>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="bx bx-time bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0 text-muted">Menunggu Korprodi</h6>
                                <h3 class="mb-0">{{ $statistics['approved_pa'] ?? 0 }}</h3>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="bx bx-hourglass bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0 text-muted">Disetujui</h6>
                                <h3 class="mb-0">{{ $statistics['approved'] ?? 0 }}</h3>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="bx bx-check-circle bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-bottom">
                <div class="row align-items-center justify-content-between g-2">
                    <div class="col-12 col-md-4">
                        <form action="{{ route('admin.komisi-proposal.index') }}" method="GET">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search"
                                    placeholder="Cari nama atau NIM..." value="{{ request('search') }}">
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="bx bx-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-12 col-md-4">
                        <form action="{{ route('admin.komisi-proposal.index') }}" method="GET">
                            <div class="input-group">
                                <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                                    <option value="">Semua Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                        Menunggu PA
                                    </option>
                                    <option value="approved_pa" {{ request('status') == 'approved_pa' ? 'selected' : '' }}>
                                        Menunggu Korprodi
                                    </option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                                        Disetujui Lengkap
                                    </option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                        Ditolak
                                    </option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bx bx-check-circle me-1"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bx bx-x-circle me-1"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive text-nowrap">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Mahasiswa</th>
                                <th>NIM</th>
                                <th>Pembimbing Akademik</th>
                                <th>Judul Proposal</th>
                                <th>Tgl. Pengajuan</th>
                                <th>Status</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($komisiProposals as $proposal)
                                <tr>
                                    <td>{{ $loop->iteration + $komisiProposals->firstItem() - 1 }}</td>
                                    <td>
                                        <strong>{{ $proposal->user->name }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-primary">{{ $proposal->user->nim }}</span>
                                    </td>
                                    <td>
                                        <small>
                                            {{ $proposal->pembimbing->name ?? '-' }}<br>
                                            <span class="text-muted">{{ $proposal->pembimbing->jabatan ?? '-' }}</span>
                                        </small>
                                    </td>
                                    <td>
                                        <small>{!! Str::limit($proposal->judul_skripsi, 50, '...') !!}</small>
                                    </td>
                                    <td>
                                        <small>{{ $proposal->created_at->translatedFormat('d M Y') }}<br>
                                            <span class="text-muted">{{ $proposal->created_at->format('H:i') }}
                                                WITA</span>
                                        </small>
                                    </td>
                                    <td>
                                        @if ($proposal->status == 'pending')
                                            <span class="badge bg-label-warning">
                                                <i class="bx bx-time-five me-1"></i> Menunggu PA
                                            </span>
                                        @elseif($proposal->status == 'approved_pa')
                                            <span class="badge bg-label-info">
                                                <i class="bx bx-hourglass me-1"></i> Menunggu Korprodi
                                            </span>
                                        @elseif($proposal->status == 'approved')
                                            <span class="badge bg-label-success">
                                                <i class="bx bx-check-circle me-1"></i> Disetujui Lengkap
                                            </span>
                                        @else
                                            <span class="badge bg-label-danger">
                                                <i class="bx bx-x-circle me-1"></i> Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                            data-bs-target="#detailModal" data-id="{{ $proposal->id }}">
                                            <i class="bx bx-show-alt me-1"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bx bx-file bx-lg mb-2"></i>
                                            <p class="mb-0">Tidak ada data pengajuan komisi proposal.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($komisiProposals->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Menampilkan {{ $komisiProposals->firstItem() }} - {{ $komisiProposals->lastItem() }} dari
                        {{ $komisiProposals->total() }} data
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            {{-- Previous Page Link --}}
                            <li class="page-item {{ $komisiProposals->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $komisiProposals->previousPageUrl() }}"
                                    aria-label="Previous">
                                    <i class="bx bx-chevrons-left icon-sm"></i>
                                </a>
                            </li>

                            {{-- Page Numbers --}}
                            @php
                                $start = max($komisiProposals->currentPage() - 2, 1);
                                $end = min($start + 4, $komisiProposals->lastPage());
                                $start = max($end - 4, 1);
                            @endphp

                            @if ($start > 1)
                                <li class="page-item">
                                    <a class="page-link" href="{{ $komisiProposals->url(1) }}">1</a>
                                </li>
                                @if ($start > 2)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                            @endif

                            @for ($page = $start; $page <= $end; $page++)
                                <li class="page-item {{ $page == $komisiProposals->currentPage() ? 'active' : '' }}">
                                    <a class="page-link"
                                        href="{{ $komisiProposals->url($page) }}">{{ $page }}</a>
                                </li>
                            @endfor

                            @if ($end < $komisiProposals->lastPage())
                                @if ($end < $komisiProposals->lastPage() - 1)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                                <li class="page-item">
                                    <a class="page-link"
                                        href="{{ $komisiProposals->url($komisiProposals->lastPage()) }}">{{ $komisiProposals->lastPage() }}</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            <li class="page-item {{ $komisiProposals->hasMorePages() ? '' : 'disabled' }}">
                                <a class="page-link" href="{{ $komisiProposals->nextPageUrl() }}" aria-label="Next">
                                    <i class="bx bx-chevrons-right icon-sm"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="bx bx-file-blank me-2"></i>Detail Komisi Proposal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <!-- Content will be loaded via AJAX -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Memuat data...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Load modal content via AJAX
            $('#detailModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var modal = $(this);

                console.log('Loading modal for komisi ID:', id);

                // Reset modal body dengan loading indicator
                modal.find('#modalBody').html(`
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Memuat data...</p>
                    </div>
                `);

                // Load content via AJAX
                $.ajax({
                    url: '/admin/komisi-proposal/' + id,
                    method: 'GET',
                    success: function(data) {
                        console.log('Modal content loaded successfully');
                        modal.find('#modalBody').html(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading modal:', error);
                        modal.find('#modalBody').html(`
                            <div class="alert alert-danger">
                                <i class="bx bx-error-circle me-1"></i>
                                <strong>Error!</strong> Gagal memuat data. Silakan coba lagi.
                            </div>
                        `);
                    }
                });
            });

            // Clear modal content on hide
            $('#detailModal').on('hidden.bs.modal', function() {
                console.log('Modal closed, clearing content');
                $(this).find('#modalBody').html(`
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Memuat data...</p>
                    </div>
                `);
            });

            // AUTO-OPEN MODAL dari notification
            @if (request()->has('open'))
                const komisiId = {{ request('open') }};
                console.log('Auto-opening modal for komisi ID:', komisiId);

                // Tunggu sebentar agar halaman fully loaded
                setTimeout(function() {
                    // Find button dengan data-id yang sesuai
                    const modalButton = $(`button[data-bs-target="#detailModal"][data-id="${komisiId}"]`);

                    if (modalButton.length) {
                        modalButton.trigger('click');
                    } else {
                        console.error('Modal button not found for komisi ID:', komisiId);
                    }
                }, 500);
            @endif

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>
@endpush
