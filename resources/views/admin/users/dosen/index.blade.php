@extends('layouts.admin.app')

@section('title', 'Daftar Dosen')

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
                <li class="breadcrumb-item breadcrumb-custom-icon active" aria-current="page">Dosen</li>
            </ol>
        </nav>
        <!-- /Breadcrumb -->
        <h4 class="fw-bold py-3 mb-2" style="margin-top: -1.2rem">
            <span class="text-muted">Daftar Data Dosen</span>
        </h4>
        <!-- Card -->
        <div class="card">
            <div class="card-header border-bottom">
                <div class="row align-items-center justify-content-end g-2">
                    <!-- Search Column -->
                    <div class="col-4 col-md-4 col-lg-3">
                        <form action="{{ route('admin.users.dosen') }}" method="GET">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text" id="basic-addon-search31">
                                    <i class="bx bx-search"></i>
                                </span>
                                <input type="text" name="search" class="form-control" placeholder="Search..."
                                    value="{{ request('search') }}" aria-label="Search..."
                                    aria-describedby="basic-addon-search31">
                            </div>
                        </form>
                    </div>
                    <!-- Button Column -->
                    <div class="col-auto text-end">
                        <a href="{{ route('admin.users.dosen.create') }}" class="btn btn-primary d-flex align-items-center"
                            style="min-width: 42px; justify-content: center;">
                            <i class="bx bx-plus d-flex d-sm-inline-flex"></i>
                            <span class="d-none d-sm-inline ms-2">Tambah Dosen</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table border-top">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Email</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                <td>{{ $user->nip }}</td>
                                <td>{{ $user->name }}</td>
                                <td>
                                    @if($user->jabatan)
                                        <span class="badge bg-label-info">{{ ucwords($user->jabatan) }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item text-info"
                                                href="{{ route('admin.users.dosen.edit', $user->id) }}">
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
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bx bx-user-x text-muted" style="font-size: 4rem;"></i>
                                        <h5 class="mt-3 text-muted">Tidak ada data dosen ditemukan</h5>
                                        <p class="text-muted mb-0">Silakan tambahkan data dosen baru.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($users->hasPages())
                <div class="card-footer border-top py-3">
                    {{ $users->links() }}
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
                        text: "Data Dosen akan dihapus permanen!",
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
