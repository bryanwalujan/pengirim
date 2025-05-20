@extends('layouts.admin.app')

@section('title', 'Tambah Dosen Baru')

@push('styles')
    <style>
        .password-toggle {
            position: relative;
        }

        .password-toggle .toggle-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #697a8d;
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
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.users.dosen') }}">Daftar Dosen</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Baru</li>
            </ol>
        </nav>
        <!-- /Breadcrumb -->

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <span class="text-muted">Tambah Dosen Baru</span>
            </h4>
            <a href="{{ route('admin.users.dosen') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar
            </a>
        </div>

        <!-- Form Tambah Dosen -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Form Data Dosen</h5>
            </div>
            <div class="card-body pt-4">
                <form id="form-dosen" action="{{ route('admin.users.dosen.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Lengkap <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required
                                    placeholder="Masukkan nama lengkap">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="nip" class="form-label">NIP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nip') is-invalid @enderror" id="nip"
                                    name="nip" value="{{ old('nip') }}" required placeholder="Masukkan NIP"
                                    title="NIP harus 10 digit angka">
                                @error('nip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}" required
                                    placeholder="Masukkan email aktif">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="jabatan" class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('jabatan') is-invalid @enderror"
                                    id="jabatan" name="jabatan" value="{{ old('jabatan') }}" required
                                    placeholder="Masukkan jabatan (contoh: Dosen, Ketua Program Studi)">
                                @error('jabatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <!-- Password Field -->
                            <div class="mb-3">
                                <label for="password" class="form-label required">Password <span
                                        class="text-danger">*</span></label>
                                <div class="input-group input-group-merge password-toggle">
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
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <button type="reset" class="btn btn-outline-secondary me-2">
                                <i class="bx bx-reset me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan Data
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!--/ Form Tambah Dosen -->
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            const togglePassword = document.querySelector('.toggle-password');
            const password = document.querySelector('#password');

            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.querySelector('i').classList.toggle('bx-hide');
                this.querySelector('i').classList.toggle('bx-show');
            });

            // Form validation
            const form = document.getElementById('form-dosen');
            form.addEventListener('submit', function(e) {
                let valid = true;

                // Validate password
                const password = document.getElementById('password').value;
                if (password.length < 8) {
                    alert('Password harus minimal 8 karakter');
                    valid = false;
                }

                // Validate NIP format (10 digits)
                const nip = document.getElementById('nip').value;
                if (!/^\d{10,20}$/.test(nip)) {
                    alert('NIP harus terdiri dari 10-20 digit angka');
                    valid = false;
                }

                // Validate jabatan
                const jabatan = document.getElementById('jabatan').value;
                if (!jabatan) {
                    alert('Jabatan harus dipilih');
                    valid = false;
                }

                if (!valid) {
                    e.preventDefault();
                }
            });
        });
    </script>
@endpush
