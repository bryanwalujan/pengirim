@extends('layouts.admin.app')

@section('title', 'Tambah Layanan Baru')

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
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.services.index') }}">Daftar Layanan</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Baru</li>
            </ol>
        </nav>
        <!-- /Breadcrumb -->

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <span class="text-muted">Tambah Layanan Baru</span>
            </h4>
            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar
            </a>
        </div>

        <!-- Form Tambah Layanan -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Form Data Layanan</h5>
            </div>
            <div class="card-body pt-4">
                <form id="form-layanan" action="{{ route('admin.services.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Layanan <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required
                                    placeholder="Contoh: Surat Aktif Kuliah">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                    id="slug" name="slug" value="{{ old('slug') }}" required
                                    placeholder="Contoh: surat-aktif-kuliah" readonly>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Slug akan otomatis terisi berdasarkan nama layanan</small>
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="icon" class="form-label">Icon <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i id="icon-preview" class="bx bx-file"></i></span>
                                    <input type="text" class="form-control @error('icon') is-invalid @enderror"
                                        id="icon" name="icon" value="{{ old('icon', 'bx-file') }}" required
                                        placeholder="Contoh: bx-file">
                                </div>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Gunakan class icon dari Boxicons</small>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                        value="1"
                                        {{ old('is_active', $service->is_active ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Aktifkan Layanan</label>
                                </div>
                                <small class="text-muted">Nonaktifkan jika layanan tidak tersedia sementara</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi Layanan</label>
                        <trix-editor class="form-control @error('description') is-invalid @enderror" id="description"
                            name="description" rows="3"
                            placeholder="Deskripsi singkat tentang layanan ini">{{ old('description') }}</trix-editor>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <button type="reset" class="btn btn-outline-secondary me-2">
                                <i class="bx bx-reset me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan Layanan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!--/ Form Tambah Layanan -->
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Generate slug from name
            const nameInput = document.getElementById('name');
            const slugInput = document.getElementById('slug');

            nameInput.addEventListener('input', function() {
                const name = this.value.trim().toLowerCase();
                const slug = name.replace(/\s+/g, '-').replace(/[^\w-]+/g, '');
                slugInput.value = slug;
            });

            // Form validation
            const form = document.getElementById('form-layanan');
            form.addEventListener('submit', function(e) {
                let valid = true;

                // Validate name
                const name = document.getElementById('name').value;
                if (name.length < 3) {
                    alert('Nama layanan minimal 3 karakter');
                    valid = false;
                }

                if (!valid) {
                    e.preventDefault();
                }
            });
        });
    </script>
@endpush
