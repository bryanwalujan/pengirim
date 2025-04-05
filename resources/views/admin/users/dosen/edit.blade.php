@extends('layouts.admin.app')

@section('title', 'Edit Data Dosen')

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

        .profile-pic-wrapper {
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
            position: relative;
        }

        .profile-pic {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #e9ecef;
        }

        .upload-btn {
            position: absolute;
            bottom: 0;
            right: 0;
            background: #fff;
            border-radius: 50%;
            padding: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
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
                <li class="breadcrumb-item active" aria-current="page">Edit Data</li>
            </ol>
        </nav>
        <!-- /Breadcrumb -->

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <span class="text-muted">Edit Data Dosen</span>
            </h4>
            <a href="{{ route('admin.users.dosen') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar
            </a>
        </div>

        <!-- Form Edit Dosen -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Form Edit Data</h5>
            </div>
            <div class="card-body pt-4">
                <form id="form-edit-dosen" action="{{ route('admin.users.dosen.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="text-center mb-4">
                                <div class="profile-pic-wrapper">
                                    <img src="{{ $user->foto ? asset('storage/' . $user->foto) : asset('assets/img/avatars/default-avatar.png') }}"
                                        class="profile-pic" id="profile-pic-preview" alt="Foto Profil">
                                    <button type="button" class="upload-btn"
                                        onclick="document.getElementById('foto').click()">
                                        <i class="bx bx-camera"></i>
                                    </button>
                                    <input type="file" id="foto" name="foto" accept="image/*"
                                        style="display: none;">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Lengkap <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $user->name) }}" required
                                    placeholder="Masukkan nama lengkap">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="nidn" class="form-label">NIDN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nidn') is-invalid @enderror"
                                    id="nidn" name="nidn" value="{{ old('nidn', $user->nidn) }}" required
                                    placeholder="Masukkan NIDN (10 digit)" pattern="[0-9]{10}"
                                    title="NIDN harus 10 digit angka">
                                @error('nidn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $user->email) }}" required
                                    placeholder="Masukkan email aktif">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                    id="username" name="username" value="{{ old('username', $user->username) }}"
                                    placeholder="Masukkan username">
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Password Field (Optional) -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
                                    <span class="input-group-text cursor-pointer toggle-password">
                                        <i class="bx bx-hide"></i>
                                    </span>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Minimal 8 karakter</small>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12 text-end">
                                <button type="reset" class="btn btn-outline-secondary me-2">
                                    <i class="bx bx-reset me-1"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                </form>
            </div>
        </div>
        <!--/ Form Edit Dosen -->
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');

            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.classList.toggle('bx-hide');
                this.classList.toggle('bx-show');
            });

            // Preview foto profil
            const fotoInput = document.getElementById('foto');
            const fotoPreview = document.getElementById('profile-pic-preview');

            fotoInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        fotoPreview.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Form validation
            const form = document.getElementById('form-edit-dosen');
            form.addEventListener('submit', function(e) {
                let valid = true;

                // Validate NIDN format (10 digits)
                const nidn = document.getElementById('nidn').value;
                if (!/^\d{10}$/.test(nidn)) {
                    alert('NIDN harus terdiri dari 10 digit angka');
                    valid = false;
                }

                // Validate password if filled
                const password = document.getElementById('password').value;
                if (password && password.length < 8) {
                    alert('Password harus minimal 8 karakter jika diisi');
                    valid = false;
                }

                if (!valid) {
                    e.preventDefault();
                }
            });
        });
    </script>
@endpush
