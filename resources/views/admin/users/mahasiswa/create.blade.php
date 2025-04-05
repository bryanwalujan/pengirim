@extends('layouts.admin.app')

@section('title', 'Tambah Mahasiswa Baru')

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
                    <a href="{{ route('admin.users.mahasiswa') }}">Mahasiswa</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item breadcrumb-custom-icon active" aria-current="page">Tambah Baru</li>
            </ol>
        </nav>
        <!-- /Breadcrumb -->

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <span class="text-muted">Tambah Mahasiswa Baru</span>
            </h4>
            <a href="{{ route('admin.users.mahasiswa') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar
            </a>
        </div>

        <!-- Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Form Tambah Mahasiswa</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.mahasiswa.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <!-- NIM Field -->
                        <div class="col-md-6 mb-3">
                            <label for="nim" class="form-label required">NIM <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nim') is-invalid @enderror" id="nim"
                                name="nim" value="{{ old('nim') }}" placeholder="Contoh: 123456789" required>
                            @error('nim')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">NIM akan digunakan sebagai username</small>
                        </div>

                        <!-- Name Field -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label required">Nama Lengkap <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email Field -->
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label required">Email <span
                                    class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}" placeholder="Contoh: mahasiswa@unima.ac.id"
                                required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label required">Password <span
                                    class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" placeholder="Minimal 8 karakter" required>
                                <span class="input-group-text cursor-pointer toggle-password">
                                    <i class="bx bx-hide"></i>
                                </span>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Gunakan kombinasi huruf, angka, dan simbol</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 text-end mt-3">
                            <button type="reset" class="btn btn-outline-secondary me-2">
                                <i class="bx bx-reset"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save"></i> Simpan Data
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            document.querySelectorAll('.toggle-password').forEach(function(button) {
                button.addEventListener('click', function() {
                    const input = this.previousElementSibling;
                    const icon = this.querySelector('i');

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.replace('bx-hide', 'bx-show');
                    } else {
                        input.type = 'password';
                        icon.classList.replace('bx-show', 'bx-hide');
                    }
                });
            });
        });
    </script>
@endpush
