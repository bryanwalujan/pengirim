{{-- filepath: resources/views/admin/ukt/edit.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Edit Pembayaran UKT')

@push('styles')
    <style>
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #696cff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .info-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #6c757d;
            width: 180px;
            flex-shrink: 0;
        }

        .info-value {
            color: #1e293b;
        }

        .status-option {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            height: 100%;
        }

        .status-option:hover {
            border-color: #696cff;
            background: #f8f9fa;
        }

        .status-option.selected {
            border-color: #696cff;
            background: rgba(105, 108, 255, 0.1);
        }

        .status-option input[type="radio"] {
            display: none;
        }

        .status-icon {
            font-size: 48px;
            margin-bottom: 12px;
        }

        .status-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .status-desc {
            font-size: 13px;
            color: #6c757d;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">
                        Dashboard
                    </a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.pembayaran-ukt.index') }}">Pembayaran UKT</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>

                </li>
                <li class="breadcrumb-item active">Edit Pembayaran</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">
                    <i class="bx bx-edit-alt"></i> Edit Pembayaran UKT
                </h4>
                <p class="text-muted mb-0">Ubah status pembayaran mahasiswa</p>
            </div>
            <a href="{{ route('admin.pembayaran-ukt.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back"></i> Kembali
            </a>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Form Edit Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.pembayaran-ukt.update', $pembayaran->id) }}" method="POST"
                            id="editForm">
                            @csrf
                            @method('PUT')

                            <!-- Info Mahasiswa -->
                            <div class="info-box">
                                <h6 class="mb-3">
                                    <i class="bx bx-info-circle"></i> Informasi Data
                                </h6>

                                <div class="info-row">
                                    <div class="info-label">NIM</div>
                                    <div class="info-value">{{ $pembayaran->mahasiswa->nim }}</div>
                                </div>

                                <div class="info-row">
                                    <div class="info-label">Nama Mahasiswa</div>
                                    <div class="info-value">{{ $pembayaran->mahasiswa->name }}</div>
                                </div>

                                <div class="info-row">
                                    <div class="info-label">Tahun Ajaran</div>
                                    <div class="info-value">
                                        {{ $pembayaran->tahunAjaran->tahun }} -
                                        {{ ucfirst($pembayaran->tahunAjaran->semester) }}
                                    </div>
                                </div>

                                @if ($pembayaran->updatedBy)
                                    <div class="info-row">
                                        <div class="info-label">Terakhir Diupdate</div>
                                        <div class="info-value">
                                            {{ $pembayaran->updatedBy->name }} -
                                            {{ $pembayaran->updated_at->format('d M Y, H:i') }}
                                        </div>
                                    </div>
                                @endif

                                <div class="info-row">
                                    <div class="info-label">Status Saat Ini</div>
                                    <div class="info-value">
                                        @if ($pembayaran->status == 'bayar')
                                            <span class="badge bg-success">Sudah Bayar</span>
                                        @else
                                            <span class="badge bg-warning">Belum Bayar</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Pilih Status -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Pilih Status Pembayaran <span class="text-danger">*</span>
                                </label>

                                <div class="row g-3">
                                    <!-- Sudah Bayar -->
                                    <div class="col-md-3">
                                        <label
                                            class="status-option {{ old('status', $pembayaran->status) == 'bayar' ? 'selected' : '' }}"
                                            data-status="bayar">
                                            <input type="radio" name="status" value="bayar"
                                                {{ old('status', $pembayaran->status) == 'bayar' ? 'checked' : '' }}
                                                required>
                                            <div class="status-icon text-success">
                                                <i class="bx bx-check-circle"></i>
                                            </div>
                                            <div class="status-title text-success">Sudah Bayar</div>
                                            <div class="status-desc">
                                                Mahasiswa telah melunasi pembayaran UKT
                                            </div>
                                        </label>
                                    </div>

                                    <!-- Belum Bayar -->
                                    <div class="col-md-3">
                                        <label
                                            class="status-option {{ old('status', $pembayaran->status) == 'belum_bayar' ? 'selected' : '' }}"
                                            data-status="belum_bayar">
                                            <input type="radio" name="status" value="belum_bayar"
                                                {{ old('status', $pembayaran->status) == 'belum_bayar' ? 'checked' : '' }}
                                                required>
                                            <div class="status-icon text-warning">
                                                <i class="bx bx-time-five"></i>
                                            </div>
                                            <div class="status-title text-warning">Belum Bayar</div>
                                            <div class="status-desc">
                                                Mahasiswa belum melakukan pembayaran UKT
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                @error('status')
                                    <div class="text-danger small mt-2">
                                        <i class="bx bx-error-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.pembayaran-ukt.index') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-x"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="bx bx-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('editForm');
            const statusOptions = document.querySelectorAll('.status-option');
            const radioButtons = document.querySelectorAll('input[name="status"]');

            // Handle status selection
            statusOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Remove selected class from all
                    statusOptions.forEach(opt => opt.classList.remove('selected'));

                    // Add selected class to clicked
                    this.classList.add('selected');

                    // Check the radio button
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;
                    }
                });
            });

            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const selectedStatus = document.querySelector('input[name="status"]:checked');

                if (!selectedStatus) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silakan pilih status pembayaran terlebih dahulu',
                        confirmButtonColor: '#696cff'
                    });
                    return;
                }

                const statusText = selectedStatus.value === 'bayar' ? 'Sudah Bayar' : 'Belum Bayar';
                const statusIcon = selectedStatus.value === 'bayar' ? 'success' : 'warning';

                Swal.fire({
                    title: 'Konfirmasi Perubahan',
                    html: `Ubah status pembayaran menjadi:<br><strong>${statusText}</strong>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#696cff',
                    cancelButtonColor: '#8592a3',
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Disable button and show loading
                        const submitBtn = document.getElementById('submitBtn');
                        submitBtn.disabled = true;
                        submitBtn.innerHTML =
                            '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

                        // Submit form
                        form.submit();
                    }
                });
            });
        });

        // Show notifications
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#696cff'
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: '<ul class="text-start">' +
                    @foreach ($errors->all() as $error)
                        '<li>{{ $error }}</li>' +
                    @endforeach
                '</ul>',
                confirmButtonColor: '#696cff'
            });
        @endif
    </script>
@endpush
