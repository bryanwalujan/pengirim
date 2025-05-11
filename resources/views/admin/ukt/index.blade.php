@extends('layouts.admin.app')

@section('title', 'Manajemen Pembayaran UKT')

@push('styles')
    <style>
        .dropdown-menu {
            padding: 0.5rem;
        }

        .dropdown-header {
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            color: #6c757d;
            font-weight: 600;
        }

        .dropdown-item.active {
            background-color: rgba(115, 103, 240, 0.1);
            color: #7367f0;
        }

        .dropdown-item:hover {
            background-color: rgba(115, 103, 240, 0.1);
        }

        .dropdown-divider {
            margin: 0.5rem 0;
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
                <li class="breadcrumb-item active" aria-current="page">Pembayaran UKT</li>
            </ol>
        </nav>
        <!-- /Breadcrumb -->

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <span class="text-muted">Daftar Pembayaran UKT</span>
            </h4>
            <div>
                <a href="{{ route('admin.pembayaran-ukt.import') }}" class="btn btn-outline-primary me-2">
                    <i class="bx bx-import me-1"></i> Import
                </a>
                <a href="{{ route('admin.pembayaran-ukt.export') }}" target="_blank"
                    class="btn-export btn btn-outline-success">
                    <i class="bx bx-export me-1"></i> Export
                </a>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.pembayaran-ukt.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                            <select class="form-select" id="tahun_ajaran" name="tahun_ajaran">
                                <!-- Pastikan name="tahun_ajaran" -->
                                <option value="">Semua Tahun Ajaran</option>
                                @foreach ($tahunAjaranList as $tahun)
                                    <option value="{{ $tahun->id }}" @selected(request('tahun_ajaran') == $tahun->id)>
                                        {{ $tahun->tahun }} - {{ ucfirst($tahun->semester) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Semua Status</option>
                                <option value="bayar" @selected(request('status') == 'bayar')>Bayar</option>
                                <option value="belum_bayar" @selected(request('status') == 'belum_bayar')>Belum Bayar</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="search" class="form-label">Cari NIM/Nama</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="search" name="search"
                                    placeholder="Cari NIM atau nama..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card -->
        <div class="card">
            <div class="table-responsive">
                <table class="table border-top">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Tahun Ajaran</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pembayaran as $item)
                            <tr>
                                <td>{{ $loop->iteration + ($pembayaran->currentPage() - 1) * $pembayaran->perPage() }}</td>
                                <td>{{ $item->mahasiswa->nim }}</td>
                                <td>{{ $item->mahasiswa->name }}</td>
                                <td>{{ $item->tahunAjaran->tahun }} - {{ ucfirst($item->tahunAjaran->semester) }}</td>
                                <td>
                                    @if ($item->status == 'bayar')
                                        <span class="badge bg-label-success">Bayar</span>
                                    @else
                                        <span class="badge bg-label-warning">Belum bayar</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <!-- Dropdown untuk ganti status -->
                                            <div class="dropdown-header">Ubah Status</div>
                                            <form action="{{ route('admin.pembayaran-ukt.update-status', $item->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" name="status" value="bayar"
                                                    class="dropdown-item {{ $item->status == 'bayar' ? 'active' : '' }}">
                                                    <i class="bx bx-check me-1"></i> Set Bayar
                                                </button>
                                                <button type="submit" name="status" value="belum_bayar"
                                                    class="dropdown-item {{ $item->status == 'belum_bayar' ? 'active' : '' }}">
                                                    <i class="bx bx-x me-1"></i> Set Belum Bayar
                                                </button>
                                            </form>
                                            <div class="dropdown-divider"></div>
                                            <!-- Tombol hapus -->
                                            <button type="button" class="dropdown-item text-danger delete-btn"
                                                data-id="{{ $item->id }}">
                                                <i class="bx bx-trash me-1"></i> Hapus
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
                                <td colspan="7" class="text-center">Tidak ada data pembayaran UKT</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($pembayaran->hasPages())
                <div class="card-footer border-top py-3">
                    {{ $pembayaran->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete confirmation
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const paymentId = this.getAttribute('data-id');

                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data pembayaran akan dihapus permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(`delete-form-${paymentId}`).submit();
                        }
                    });
                });
            });

            // Reset filter
            document.getElementById('resetFilter').addEventListener('click', function() {
                window.location.href = "{{ route('admin.pembayaran-ukt.index') }}";
            });
        });
    </script>
@endpush
