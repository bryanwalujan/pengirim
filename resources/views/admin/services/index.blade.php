@extends('layouts.admin.app')

@section('title', 'Daftar Layanan')

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
                <li class="breadcrumb-item breadcrumb-custom-icon active" aria-current="page">Manajemen Layanan</li>
            </ol>
        </nav>
        <!-- /Breadcrumb -->

        <h4 class="fw-bold py-3 mb-2" style="margin-top: -1.2rem">
            <span class="text-muted">Daftar Layanan E-Service</span>
        </h4>

        <!-- Card -->
        <div class="card">
            <div class="card-header border-bottom">
                <div class="row align-items-center justify-content-end g-2">
                    <!-- Search Column -->
                    <div class="col-4 col-md-4 col-lg-3">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text" id="basic-addon-search31">
                                <i class="bx bx-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="Cari layanan..."
                                wire:model.debounce.300ms="search" aria-label="Search..."
                                aria-describedby="basic-addon-search31" />
                        </div>
                    </div>
                    <!-- Button Column -->
                    <div class="col-auto text-end">
                        <a href="{{ route('admin.services.create') }}" class="btn btn-primary d-flex align-items-center"
                            style="min-width: 42px; justify-content: center;">
                            <i class="bx bx-plus d-flex d-sm-inline-flex"></i>
                            <span class="d-none d-sm-inline ms-2">Tambah Layanan</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table border-top">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Layanan</th>
                            <th>Deskripsi</th>
                            <th>Icon</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($services as $service)
                            <tr>
                                <td>{{ $loop->iteration + ($services->currentPage() - 1) * $services->perPage() }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bx {{ $service->icon }} me-2"></i>
                                        <span>{{ $service->name }}</span>
                                    </div>
                                </td>
                                <td>{!! Str::limit($service->description, 70) !!}</td>
                                <td><code>{{ $service->icon }}</code></td>
                                <td>
                                    @if ($service->is_active)
                                        <span class="badge bg-label-success">Aktif</span>
                                    @else
                                        <span class="badge bg-label-secondary">Nonaktif</span>
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
                                                href="{{ route('admin.services.edit', $service->id) }}">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </a>

                                            <form action="{{ route('admin.services.destroy', $service->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger delete-btn">
                                                    <i class="bx bx-trash me-1"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada layanan yang ditambahkan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($services->hasPages())
                <div class="card-footer border-top py-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-end mb-0">
                            {{-- Previous Page Link --}}
                            <li class="page-item prev {{ $services->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $services->previousPageUrl() }}">
                                    <i class="bx bx-chevrons-left icon-sm"></i>
                                </a>
                            </li>

                            {{-- Pagination Elements --}}
                            @foreach ($services->getUrlRange(1, $services->lastPage()) as $page => $url)
                                <li class="page-item {{ $services->currentPage() == $page ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach

                            {{-- Next Page Link --}}
                            <li class="page-item next {{ !$services->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $services->nextPageUrl() }}">
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
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Layanan yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
