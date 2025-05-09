@extends('layouts.admin.app')

@section('title', 'Manajemen Tahun Ajaran')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item breadcrumb-custom-icon active" aria-current="page">Tahun Ajaran</li>
            </ol>
        </nav>
        <!-- /Breadcrumb -->

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <span class="text-muted">Daftar Tahun Ajaran</span>
            </h4>
            <a href="{{ route('admin.tahun-ajaran.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> Tambah Baru
            </a>
        </div>

        <!-- Card -->
        <div class="card">
            <div class="table-responsive">
                <table class="table border-top">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tahunAjaran as $tahun)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $tahun->tahun }}</td>
                                <td>{{ ucfirst($tahun->semester) }}</td>
                                <td>
                                    @if ($tahun->status_aktif)
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
                                            <a class="dropdown-item"
                                                href="{{ route('admin.tahun-ajaran.edit', $tahun->id) }}">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </a>
                                            @if (!$tahun->status_aktif)
                                                <a class="dropdown-item text-success" href="#"
                                                    onclick="event.preventDefault(); document.getElementById('activate-form-{{ $tahun->id }}').submit();">
                                                    <i class="bx bx-check-circle me-1"></i> Aktifkan
                                                </a>
                                                <form id="activate-form-{{ $tahun->id }}"
                                                    action="{{ route('admin.tahun-ajaran.activate', $tahun->id) }}"
                                                    method="POST" class="d-none">
                                                    @csrf
                                                </form>
                                            @endif
                                            <button type="button" class="dropdown-item text-danger delete-btn"
                                                data-id="{{ $tahun->id }}">
                                                <i class="bx bx-trash me-1"></i> Hapus
                                            </button>
                                            <form id="delete-form-{{ $tahun->id }}"
                                                action="{{ route('admin.tahun-ajaran.destroy', $tahun->id) }}"
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
                                <td colspan="5" class="text-center">Tidak ada data tahun ajaran</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete confirmation
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const tahunId = this.getAttribute('data-id');

                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Tahun ajaran akan dihapus permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(`delete-form-${tahunId}`).submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
