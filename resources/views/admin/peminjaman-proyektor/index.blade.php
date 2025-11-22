@extends('layouts.admin.app')

@section('title', 'Peminjaman Proyektor')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item breadcrumb-custom-icon active" aria-current="page">
                    Manajemen Peminjaman Proyektor
                </li>
            </ol>
        </nav>

        <h4 class="fw-bold py-3 mb-4" style="margin-top: -1.2rem">
            <span class="text-muted">Data Peminjaman Proyektor</span>
        </h4>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="card-info">
                                <p class="card-text mb-1">Total Peminjaman</p>
                                <div class="d-flex align-items-end mb-2">
                                    <h4 class="mb-0 me-2">{{ $stats['total'] }}</h4>
                                </div>
                                <small class="text-muted">Semua data</small>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-primary rounded p-2">
                                    <i class="bx bx-list-ul bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="card-info">
                                <p class="card-text mb-1">Sedang Dipinjam</p>
                                <div class="d-flex align-items-end mb-2">
                                    <h4 class="mb-0 me-2 text-warning">{{ $stats['sedang_dipinjam'] }}</h4>
                                </div>
                                <small class="text-muted">Aktif saat ini</small>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-warning rounded p-2">
                                    <i class="bx bx-time-five bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="card-info">
                                <p class="card-text mb-1">Sudah Dikembalikan</p>
                                <div class="d-flex align-items-end mb-2">
                                    <h4 class="mb-0 me-2 text-success">{{ $stats['dikembalikan'] }}</h4>
                                </div>
                                <small class="text-muted">Total dikembalikan</small>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-success rounded p-2">
                                    <i class="bx bx-check-circle bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="card">
            <div class="card-header border-bottom">
                <div class="row align-items-center g-3">
                    {{-- Search --}}
                    <div class="col-12 col-md-3">
                        <form action="{{ route('admin.peminjaman-proyektor.index') }}" method="GET">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search"
                                    placeholder="Cari nama atau NIM..." value="{{ request('search') }}">
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="bx bx-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Filter Status --}}
                    <div class="col-12 col-md-3">
                        <form action="{{ route('admin.peminjaman-proyektor.index') }}" method="GET">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>
                                    Dipinjam
                                </option>
                                <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>
                                    Dikembalikan
                                </option>
                            </select>
                        </form>
                    </div>

                    {{-- Filter Proyektor --}}
                    <div class="col-12 col-md-3">
                        <form action="{{ route('admin.peminjaman-proyektor.index') }}" method="GET">
                            <input type="text" class="form-control" name="proyektor"
                                placeholder="Filter kode proyektor..." value="{{ request('proyektor') }}">
                        </form>
                    </div>

                    {{-- Reset Filter --}}
                    <div class="col-12 col-md-3 text-end">
                        <a href="{{ route('admin.peminjaman-proyektor.index') }}" class="btn btn-secondary">
                            <i class="bx bx-reset"></i> Reset Filter
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bx bx-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bx bx-error me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Peminjam</th>
                                <th>NIM</th>
                                <th>Kode Proyektor</th>
                                <th>Keperluan</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($peminjaman as $item)
                                <tr>
                                    <td>{{ $loop->iteration + $peminjaman->firstItem() - 1 }}</td>
                                    <td>
                                        <strong>{{ $item->user->name ?? 'N/A' }}</strong>
                                    </td>
                                    <td>{{ $item->user->nim ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-label-info">
                                            {{ $item->formatted_proyektor_code }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;"
                                            title="{{ $item->keperluan ?? 'Tidak disebutkan' }}">
                                            {{ $item->formatted_keperluan }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $item->tanggal_pinjam->format('d M Y') }}<br>
                                        <small class="text-muted">{{ $item->tanggal_pinjam->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        @if ($item->tanggal_kembali)
                                            {{ $item->tanggal_kembali->format('d M Y') }}<br>
                                            <small class="text-muted">{{ $item->tanggal_kembali->format('H:i') }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->status == 'dipinjam')
                                            <span class="badge bg-warning">
                                                <i class="bx bx-time-five"></i> Dipinjam
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="bx bx-check"></i> Dikembalikan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="javascript:void(0);"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#detailModal{{ $item->id }}">
                                                    <i class="bx bx-show me-1"></i> Detail
                                                </a>
                                                @if ($item->status == 'dipinjam')
                                                    <a class="dropdown-item" href="javascript:void(0);"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#returnModal{{ $item->id }}">
                                                        <i class="bx bx-check me-1"></i> Proses Pengembalian
                                                    </a>
                                                @endif
                                                <div class="dropdown-divider"></div>
                                                <form
                                                    action="{{ route('admin.peminjaman-proyektor.destroy', $item->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bx bx-trash me-1"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Detail Modal --}}
                                <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="bx bx-info-circle me-2"></i>Detail Peminjaman
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <table class="table table-sm table-borderless">
                                                    <tr>
                                                        <th style="width: 40%">Nama Peminjam:</th>
                                                        <td>{{ $item->user->name ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>NIM:</th>
                                                        <td>{{ $item->user->nim ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Email:</th>
                                                        <td>{{ $item->user->email ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Kode Proyektor:</th>
                                                        <td>
                                                            <span class="badge bg-label-info">
                                                                {{ $item->formatted_proyektor_code }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Keperluan:</th>
                                                        <td>{{ $item->formatted_keperluan }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tanggal Pinjam:</th>
                                                        <td>{{ $item->tanggal_pinjam->format('d M Y, H:i') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tanggal Kembali:</th>
                                                        <td>
                                                            @if ($item->tanggal_kembali)
                                                                {{ $item->tanggal_kembali->format('d M Y, H:i') }}
                                                            @else
                                                                <span class="text-muted">Belum dikembalikan</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Status:</th>
                                                        <td>
                                                            @if ($item->status == 'dipinjam')
                                                                <span class="badge bg-warning">Dipinjam</span>
                                                            @else
                                                                <span class="badge bg-success">Dikembalikan</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @if ($item->keterangan)
                                                        <tr>
                                                            <th>Keterangan:</th>
                                                            <td>{{ $item->keterangan }}</td>
                                                        </tr>
                                                    @endif
                                                </table>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Return Modal --}}
                                @if ($item->status == 'dipinjam')
                                    <div class="modal fade" id="returnModal{{ $item->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <form
                                                action="{{ route('admin.peminjaman-proyektor.update-status', $item->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            <i class="bx bx-check-circle me-2"></i>Proses Pengembalian
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="status" value="dikembalikan">
                                                        <div class="alert alert-info">
                                                            <strong>Peminjam:</strong> {{ $item->user->name }}<br>
                                                            <strong>Proyektor:</strong>
                                                            {{ $item->formatted_proyektor_code }}
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Keterangan</label>
                                                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Tambahkan catatan (opsional)"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="bx bx-check me-1"></i>Konfirmasi Pengembalian
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="bx bx-data fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">Tidak ada data untuk ditampilkan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if ($peminjaman->hasPages())
                <div class="card-footer border-top py-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-end mb-0">
                            {{-- Previous --}}
                            <li class="page-item {{ $peminjaman->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $peminjaman->previousPageUrl() ?? '#' }}" tabindex="-1">
                                    <i class="bx bx-chevrons-left icon-sm"></i>
                                </a>
                            </li>

                            {{-- First Page --}}
                            @if ($peminjaman->currentPage() > 2)
                                <li class="page-item">
                                    <a class="page-link" href="{{ $peminjaman->url(1) }}">1</a>
                                </li>
                            @endif

                            {{-- Dots if needed --}}
                            @if ($peminjaman->currentPage() > 3)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif

                            {{-- Current + 1 Before & After --}}
                            @for ($i = max(1, $peminjaman->currentPage() - 1); $i <= min($peminjaman->lastPage(), $peminjaman->currentPage() + 1); $i++)
                                <li class="page-item {{ $i == $peminjaman->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $peminjaman->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor

                            {{-- Dots if needed --}}
                            @if ($peminjaman->currentPage() < $peminjaman->lastPage() - 2)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif

                            {{-- Last Page --}}
                            @if ($peminjaman->currentPage() < $peminjaman->lastPage() - 1)
                                <li class="page-item">
                                    <a class="page-link"
                                        href="{{ $peminjaman->url($peminjaman->lastPage()) }}">{{ $peminjaman->lastPage() }}</a>
                                </li>
                            @endif

                            {{-- Next --}}
                            <li class="page-item {{ !$peminjaman->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $peminjaman->nextPageUrl() ?? '#' }}">
                                    <i class="bx bx-chevrons-right icon-sm"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Auto dismiss alerts
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
@endpush
