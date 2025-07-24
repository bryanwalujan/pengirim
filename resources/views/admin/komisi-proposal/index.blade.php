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
                <li class="breadcrumb-item breadcrumb-custom-icon active" aria-current="page">Manajemen Komisi Proposal</li>
            </ol>
        </nav>
        <h4 class="fw-bold py-3 mb-2" style="margin-top: -1.2rem">
            <span class="text-muted">Data Pengajuan Komisi Proposal</span>
        </h4>

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
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                                        Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                        Rejected</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success my-2">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger my-2">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Mahasiswa</th>
                                <th>NIM</th>
                                <th>Judul Proposal</th>
                                <th>Tgl. Pengajuan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($komisiProposals as $proposal)
                                <tr>
                                    <td>{{ $loop->iteration + $komisiProposals->firstItem() - 1 }}</td>
                                    <td>{{ $proposal->user->name }}</td>
                                    <td><span class="btn btn-sm btn-outline-primary">{{ $proposal->user->nim }}</span></td>
                                    <td>{!! Str::limit($proposal->judul_skripsi, 50, '...') !!}</td>
                                    <td>{{ $proposal->created_at->translatedFormat('d M Y, H:i') }} WITA</td>
                                    <td>
                                        @if ($proposal->status == 'pending')
                                            <span class="badge bg-label-warning me-1">Pending</span>
                                        @elseif($proposal->status == 'approved')
                                            <span class="badge bg-label-success me-1">Approved</span>
                                        @else
                                            <span class="badge bg-label-danger me-1">Rejected</span>
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
                                    <td colspan="7" class="text-center">Tidak ada data pengajuan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($komisiProposals->hasPages())
                <div class="card-footer d-flex justify-content-end">
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            <li class="page-item {{ $komisiProposals->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $komisiProposals->previousPageUrl() }}"
                                    aria-label="Previous">
                                    <i class="bx bx-chevrons-left icon-sm"></i>
                                </a>
                            </li>
                            @foreach ($komisiProposals->getUrlRange(1, $komisiProposals->lastPage()) as $page => $url)
                                <li class="page-item {{ $page == $komisiProposals->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Detail Komisi Proposal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <!-- Content will be loaded via AJAX -->
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('#detailModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var modal = $(this);

            $.get('/admin/komisi-proposal/' + id, function(data) {
                modal.find('#modalBody').html(data);
            });
        });
    </script>
@endpush
