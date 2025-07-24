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
                <li class="breadcrumb-item breadcrumb-custom-icon active" aria-current="page">Manajemen Peminjaman Proyektor
                </li>
            </ol>
        </nav>
        <h4 class="fw-bold py-3 mb-2" style="margin-top: -1.2rem">
            <span class="text-muted">Data Peminjaman Proyektor</span>
        </h4>

        <div class="card">
            <div class="card-header border-bottom">
                <div class="row align-items-center justify-content-between g-2">
                    <div class="col-12 col-md-4">
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
                    <div class="col-12 col-md-4">
                        <form action="{{ route('admin.peminjaman-proyektor.index') }}" method="GET">
                            <div class="input-group">
                                <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                                    <option value="">Semua Status</option>
                                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>
                                        Dipinjam</option>
                                    <option value="dikembalikan"
                                        {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
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
                                <th>Nama Peminjam</th>
                                <th>NIM</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($peminjaman as $item)
                                <tr>
                                    <td>{{ $loop->iteration + $peminjaman->firstItem() - 1 }}</td>
                                    <td><strong>{{ $item->user->name ?? 'N/A' }}</strong></td>
                                    <td>{{ $item->user->nim ?? 'N/A' }}</td>
                                    <td>{{ $item->tanggal_pinjam->format('d M Y, H:i') }}</td>
                                    <td>
                                        @if ($item->tanggal_kembali)
                                            {{ $item->tanggal_kembali->format('d M Y, H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->status == 'dipinjam')
                                            <span class="badge bg-label-warning me-1">Dipinjam</span>
                                        @else
                                            <span class="badge bg-label-success me-1">Dikembalikan</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data untuk ditampilkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($peminjaman->hasPages())
                <div class="card-footer d-flex justify-content-end">
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            <li class="page-item {{ $peminjaman->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $peminjaman->previousPageUrl() }}" aria-label="Previous">
                                    <i class="bx bx-chevrons-left icon-sm"></i>
                                </a>
                            </li>
                            @foreach ($peminjaman->getUrlRange(1, $peminjaman->lastPage()) as $page => $url)
                                <li class="page-item {{ $page == $peminjaman->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach
                            <li class="page-item {{ $peminjaman->hasMorePages() ? '' : 'disabled' }}">
                                <a class="page-link" href="{{ $peminjaman->nextPageUrl() }}" aria-label="Next">
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
