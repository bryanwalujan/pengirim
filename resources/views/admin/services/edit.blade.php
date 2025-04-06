@extends('layouts.admin.app')

@section('title', 'Edit Layanan')

@push('styles')
    <style>
        .icon-preview {
            font-size: 1.5rem;
            margin-right: 10px;
            vertical-align: middle;
        }

        .form-field-item {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            background: #f9f9f9;
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
                    <a href="{{ route('admin.services.index') }}">Daftar Layanan</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Edit Layanan</li>
            </ol>
        </nav>
        <!-- /Breadcrumb -->

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <span class="text-muted">Edit Layanan:</span> {{ $service->name }}
            </h4>
            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar
            </a>
        </div>

        <!-- Form Edit Layanan -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Form Edit Layanan</h5>
            </div>
            <div class="card-body pt-4">
                <form id="form-edit-layanan" action="{{ route('admin.services.update', $service->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Layanan <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $service->name) }}" required
                                    placeholder="Contoh: Surat Aktif Kuliah">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                    id="slug" name="slug" value="{{ old('slug', $service->slug) }}" required
                                    placeholder="Contoh: surat-aktif-kuliah">
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">URL identifier untuk layanan ini</small>
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="icon" class="form-label">Icon <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i id="icon-preview"
                                            class="{{ old('icon', $service->icon) }}"></i></span>
                                    <input type="text" class="form-control @error('icon') is-invalid @enderror"
                                        id="icon" name="icon" value="{{ old('icon', $service->icon) }}" required
                                        placeholder="Contoh: bx bx-file">
                                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                                        data-bs-target="#iconModal">
                                        Pilih Icon
                                    </button>
                                </div>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                        value="1" {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Aktifkan Layanan</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi Layanan</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                            rows="3" placeholder="Deskripsi singkat tentang layanan ini">{{ old('description', $service->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Form Fields Section -->
                    {{-- <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>Field Form Layanan</h5>
                            <a href="{{ route('admin.services.fields.create', $service->id) }}"
                                class="btn btn-sm btn-primary">
                                <i class="bx bx-plus me-1"></i> Tambah Field Baru
                            </a>
                        </div>

                        @forelse($service->formFields as $field)
                            <div class="form-field-item">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Label:</strong> {{ $field->field_label }}
                                    </div>
                                    <div class="col-md-2">
                                        <strong>Tipe:</strong> {{ ucfirst($field->field_type) }}
                                    </div>
                                    <div class="col-md-2">
                                        <strong>Wajib:</strong> {{ $field->is_required ? 'Ya' : 'Tidak' }}
                                    </div>
                                    <div class="col-md-3">
                                        @if ($field->options)
                                            <strong>Opsi:</strong> {{ $field->options }}
                                        @endif
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.services.fields.edit', [$service->id, $field->id]) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                            <form
                                                action="{{ route('admin.services.fields.destroy', [$service->id, $field->id]) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Hapus field ini?')">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info">
                                Belum ada field yang ditambahkan untuk layanan ini
                            </div>
                        @endforelse
                    </div> --}}

                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <button type="reset" class="btn btn-outline-secondary me-2">
                                <i class="bx bx-reset me-1"></i> Reset Perubahan
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!--/ Form Edit Layanan -->
    </div>

    <!-- Icon Modal -->
    <div class="modal fade" id="iconModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Icon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        @foreach (['bx bx-file', 'bx bx-book', 'bx bx-calendar', 'bx bx-certification', 'bx bx-clipboard', 'bx bx-collection', 'bx bx-credit-card', 'bx bx-envelope', 'bx bx-group', 'bx bx-id-card', 'bx bx-library', 'bx bx-notepad', 'bx bx-paperclip', 'bx bx-printer', 'bx bx-search', 'bx bx-task'] as $icon)
                            <div class="col-3 col-md-2 mb-3 text-center">
                                <button type="button" class="btn btn-icon btn-outline-primary icon-select"
                                    data-icon="{{ $icon }}" style="width: 100%; height: 100%;">
                                    <i class="{{ $icon }}"></i>
                                </button>
                                <small class="d-block mt-1">{{ $icon }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
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

            // Icon selection
            const iconInput = document.getElementById('icon');
            const iconPreview = document.getElementById('icon-preview');
            const iconSelectButtons = document.querySelectorAll('.icon-select');

            iconSelectButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const selectedIcon = this.getAttribute('data-icon');
                    iconInput.value = selectedIcon;
                    iconPreview.className = selectedIcon;
                    $('#iconModal').modal('hide');
                });
            });

            // Live icon preview
            iconInput.addEventListener('input', function() {
                iconPreview.className = this.value;
            });

            // Form validation
            const form = document.getElementById('form-edit-layanan');
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
