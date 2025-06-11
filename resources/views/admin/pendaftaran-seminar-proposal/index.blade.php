@extends('layouts.admin.app')

@section('title', 'Pendaftaran Seminar Proposal')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item breadcrumb-custom-icon active" aria-current="page">Manajemen Pendaftaran Seminar
                    Proposal
                </li>
            </ol>
        </nav>
        <h4 class="fw-bold py-3 mb-2" style="margin-top: -1.2rem">
            <span class="text-muted">Data Pendaftaran Seminar Proposal</span>
        </h4>

        <div class="card">
            <div class="card-header border-bottom">
                <div class="row align-items-center justify-content-between g-2">
                    <div class="col-12 col-md-4">
                        <form action="{{ route('admin.pendaftaran-seminar-proposal.index') }}" method="GET">
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
                        <form action="{{ route('admin.pendaftaran-seminar-proposal.index') }}" method="GET">
                            <div class="input-group">
                                <select name="angkatan" id="angkatan" class="form-select" onchange="this.form.submit()">
                                    <option value="">Semua Angkatan</option>
                                    @foreach ($uniqueAngkatan as $year)
                                        <option value="{{ $year }}"
                                            {{ request('angkatan') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success mb-2">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger mb-2">
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
                                <th>Angkatan</th>
                                <th>Judul Proposal</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($pendaftaran as $item)
                                <tr>
                                    <td>{{ $loop->iteration + $pendaftaran->firstItem() - 1 }}</td>
                                    <td><strong>{{ $item->user->name }}</strong></td>
                                    <td>{{ $item->user->nim }}</td>
                                    <td><span class="badge bg-label-primary me-1">{{ $item->angkatan }}</span></td>
                                    <td>{{ Str::limit($item->judul_skripsi, 40, '...') }}</td>
                                    <td>{{ $item->created_at->format('d M Y, H:i') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                            data-bs-target="#detailModal" data-id="{{ $item->id }}">
                                            <i class="bx bx-show-alt me-1"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data untuk ditampilkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($pendaftaran->hasPages())
                <div class="card-footer d-flex justify-content-end">
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            <li class="page-item {{ $pendaftaran->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $pendaftaran->previousPageUrl() }}" aria-label="Previous">
                                    <i class="bx bx-chevrons-left icon-sm"></i>
                                </a>
                            </li>
                            @foreach ($pendaftaran->getUrlRange(1, $pendaftaran->lastPage()) as $page => $url)
                                <li class="page-item {{ $page == $pendaftaran->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach
                            <li class="page-item {{ $pendaftaran->hasMorePages() ? '' : 'disabled' }}">
                                <a class="page-link" href="{{ $pendaftaran->nextPageUrl() }}" aria-label="Next">
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
                    <h5 class="modal-title" id="modalTitle">Detail Pendaftaran</h5>
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

            $.get('/admin/pendaftaran-seminar-proposal/' + id, function(data) {
                modal.find('#modalBody').html(data);
            });
        });
    </script>
@endpush
