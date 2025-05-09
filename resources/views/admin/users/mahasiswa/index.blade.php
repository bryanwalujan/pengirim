@extends('layouts.admin.app')

@section('title', 'Daftar Mahasiswa')

@push('styles')
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
                <li class="breadcrumb-item breadcrumb-custom-icon">
                    <a href="javascript:void(0);">Manajemen Pengguna</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item breadcrumb-custom-icon active" aria-current="page">Mahasiswa</li>
            </ol>
        </nav>
        <!-- /Breadcrumb -->
        <h4 class="fw-bold py-3 mb-2" style="margin-top: -1.2rem">
            <span class="text-muted">Daftar Data Mahasiswa</span>
        </h4>
        <!-- Card -->
        <div class="card">
            <div class="card-header border-bottom">
                <div class="row align-items-center justify-content-end g-2">
                    <div class="col-auto">
                        <a href="{{ route('admin.users.mahasiswa.import') }}"
                            class="btn btn-outline-primary d-flex align-items-center">
                            <i class="bx bx-import d-flex d-sm-inline-flex"></i>
                            <span class="d-none d-sm-inline ms-2">Import</span>
                        </a>
                    </div>
                    <div class="col-auto me-auto">
                        <a href="{{ route('admin.users.mahasiswa.export') }}"
                            class="btn btn-outline-success d-flex align-items-center ms-2">
                            <i class="bx bx-export d-flex d-sm-inline-flex"></i>
                            <span class="d-none d-sm-inline ms-2">Export</span>
                        </a>
                    </div>
                    <!-- Search Column -->
                    <div class="col-4 col-sm-2 col-md-4 col-lg-3">
                        <form action="{{ route('admin.users.mahasiswa') }}" method="GET">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text" id="basic-addon-search31">
                                    <i class="bx bx-search"></i>
                                </span>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Cari nama, NIM, atau email..." aria-label="Search"
                                    aria-describedby="basic-addon-search31" value="{{ request('search') }}">
                            </div>
                        </form>
                    </div>
                    <!-- Button Column -->
                    <div class="col-auto text-end">
                        <a href="{{ route('admin.users.mahasiswa.create') }}"
                            class="btn btn-primary d-flex align-items-center"
                            style="min-width: 42px; justify-content: center;">
                            <i class="bx bx-plus d-flex d-sm-inline-flex"></i>
                            <span class="d-none d-sm-inline ms-2">Tambah Mahasiswa</span>
                        </a>
                    </div>

                </div>
            </div>
            <div class="table-responsive">
                <table class="table border-top">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>
                                Status UKT
                                @if ($tahunAjaranAktif)
                                    <small class="text-muted d-block">{{ $tahunAjaranAktif->tahun }} -
                                        {{ ucfirst($tahunAjaranAktif->semester) }}</small>
                                @endif
                            </th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                <td>{{ $user->nim }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @php
                                        $latestPayment = $user->pembayaranUkt->firstWhere(
                                            'tahun_ajaran_id',
                                            $tahunAjaranAktif->id ?? null,
                                        );
                                    @endphp

                                    @if ($tahunAjaranAktif)
                                        @if ($latestPayment)
                                            @if ($latestPayment->status == 'bayar')
                                                <span class="badge bg-label-success">Bayar</span>
                                                <small class="text-muted d-block">{{ $tahunAjaranAktif->tahun }} -
                                                    {{ ucfirst($tahunAjaranAktif->semester) }}</small>
                                            @else
                                                <span class="badge bg-label-warning">Belum Bayar</span>
                                                <small class="text-muted d-block">{{ $tahunAjaranAktif->tahun }} -
                                                    {{ ucfirst($tahunAjaranAktif->semester) }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-label-danger">Belum Ada Data</span>
                                            <small class="text-muted d-block">{{ $tahunAjaranAktif->tahun }} -
                                                {{ ucfirst($tahunAjaranAktif->semester) }}</small>
                                        @endif
                                    @else
                                        <span class="badge bg-label-secondary">Tahun Ajaran Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item text-info"
                                                href="{{ route('admin.users.mahasiswa.edit', $user->id) }}">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </a>

                                            <!-- Delete Button -->
                                            <button type="button" class="dropdown-item text-danger delete-btn"
                                                data-id="{{ $user->id }}">
                                                <i class="bx bx-trash me-1"></i> Hapus
                                            </button>

                                            <!-- Hidden Form -->
                                            <form id="delete-form-{{ $user->id }}"
                                                action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                                class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data mahasiswa</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($users->hasPages())
                <div class="card-footer border-top py-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-end mb-0">
                            {{-- Previous --}}
                            <li class="page-item {{ $users->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $users->previousPageUrl() ?? '#' }}" tabindex="-1">
                                    <i class="bx bx-chevrons-left icon-sm"></i>
                                </a>
                            </li>
                            {{-- First Page --}}
                            @if ($users->currentPage() > 2)
                                <li class="page-item">
                                    <a class="page-link" href="{{ $users->url(1) }}">1</a>
                                </li>
                            @endif

                            {{-- Dots if needed --}}
                            @if ($users->currentPage() > 3)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif

                            {{-- Current + 1 Before & After --}}
                            @for ($i = max(1, $users->currentPage() - 1); $i <= min($users->lastPage(), $users->currentPage() + 1); $i++)
                                <li class="page-item {{ $i == $users->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $users->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor

                            {{-- Dots if needed --}}
                            @if ($users->currentPage() < $users->lastPage() - 2)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif

                            {{-- Last Page --}}
                            @if ($users->currentPage() < $users->lastPage() - 1)
                                <li class="page-item">
                                    <a class="page-link"
                                        href="{{ $users->url($users->lastPage()) }}">{{ $users->lastPage() }}</a>
                                </li>
                            @endif

                            {{-- Next --}}
                            <li class="page-item {{ !$users->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $users->nextPageUrl() ?? '#' }}">
                                    <i class="bx bx-chevrons-right icon-sm"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            @endif

        </div>
        <!--/ Card -->
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tangkap semua tombol delete
            const deleteButtons = document.querySelectorAll('.delete-btn');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-id');

                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data mahasiswa akan dihapus permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(`delete-form-${userId}`).submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
