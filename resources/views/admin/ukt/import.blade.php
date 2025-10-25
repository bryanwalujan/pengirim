{{-- filepath: resources/views/admin/ukt/import.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Import Pembayaran UKT')

@push('styles')
    <style>
        .import-steps {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .step-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 16px;
            padding: 12px;
            background: #fff;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .step-item:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .step-item:last-child {
            margin-bottom: 0;
        }

        .step-number {
            width: 32px;
            height: 32px;
            background: #696cff;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            flex-shrink: 0;
            margin-right: 12px;
        }

        .step-content {
            flex: 1;
        }

        .step-title {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .step-desc {
            font-size: 13px;
            color: #64748b;
            margin: 0;
        }

        .info-alert {
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
            border: none;
            border-left: 4px solid #0ea5e9;
            border-radius: 8px;
            margin-bottom: 24px;
        }

        .warning-alert {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: none;
            border-left: 4px solid #f59e0b;
            border-radius: 8px;
        }

        .file-upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 32px;
            text-align: center;
            transition: all 0.3s;
            background: #f8fafc;
            cursor: pointer;
        }

        .file-upload-area:hover {
            border-color: #696cff;
            background: rgba(105, 108, 255, 0.05);
        }

        .file-upload-area.dragover {
            border-color: #696cff;
            background: rgba(105, 108, 255, 0.1);
        }

        .file-upload-icon {
            font-size: 64px;
            color: #cbd5e1;
            margin-bottom: 16px;
        }

        .file-upload-area:hover .file-upload-icon {
            color: #696cff;
        }

        .file-info {
            display: none;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 12px;
        }

        .file-info.active {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .btn-download-template {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            border: none;
            color: #fff;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-download-template:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(34, 197, 94, 0.3);
            color: #fff;
        }

        .format-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            background: #f1f5f9;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            color: #475569;
            margin: 0 4px;
        }

        .required-label::after {
            content: " *";
            color: #ef4444;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">
                        <i class="bx bx-home-alt"></i> Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.pembayaran-ukt.index') }}">Pembayaran UKT</a>
                </li>
                <li class="breadcrumb-item active">Import Data</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">
                    <i class="bx bx-import"></i> Import Data Pembayaran UKT
                </h4>
                <p class="text-muted mb-0">Import data pembayaran dari file Excel</p>
            </div>
            <a href="{{ route('admin.pembayaran-ukt.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back"></i> Kembali
            </a>
        </div>

        <div class="row">
            <!-- Left Column - Instructions -->
            <div class="col-lg-5 mb-4">
                <!-- Download Template -->
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bx bx-file-blank" style="font-size: 64px; color: #22c55e;"></i>
                        </div>
                        <h5 class="mb-2">Template Excel</h5>
                        <p class="text-muted mb-3">
                            Download template Excel untuk format import yang benar
                        </p>
                        <a href="{{ route('admin.pembayaran-ukt.download-template') }}"
                            class="btn btn-download-template w-100" target="_blank">
                            <i class="bx bx-download me-2"></i>Download Template
                        </a>
                    </div>
                </div>

                <!-- Import Steps -->
                <div class="import-steps">
                    <h6 class="mb-3 fw-bold">
                        <i class="bx bx-list-check me-2"></i>Langkah-langkah Import
                    </h6>

                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <div class="step-title">Download Template</div>
                            <p class="step-desc">Download template Excel yang sudah disediakan</p>
                        </div>
                    </div>

                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <div class="step-title">Isi Data</div>
                            <p class="step-desc">Isi data mahasiswa sesuai format yang ada di template</p>
                        </div>
                    </div>

                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <div class="step-title">Pilih Tahun Ajaran</div>
                            <p class="step-desc">Pilih tahun ajaran yang sesuai dengan data import</p>
                        </div>
                    </div>

                    <div class="step-item">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <div class="step-title">Upload File</div>
                            <p class="step-desc">Upload file Excel yang sudah diisi</p>
                        </div>
                    </div>

                    <div class="step-item">
                        <div class="step-number">5</div>
                        <div class="step-content">
                            <div class="step-title">Proses Import</div>
                            <p class="step-desc">Klik tombol Import dan tunggu proses selesai</p>
                        </div>
                    </div>
                </div>

                <!-- Important Notes -->
                <div class="warning-alert alert">
                    <h6 class="alert-heading mb-3">
                        <i class="bx bx-error-circle me-2"></i>Perhatian Penting
                    </h6>
                    <ul class="mb-0 small">
                        <li class="mb-2">Pastikan format file adalah <strong>.xlsx</strong> atau <strong>.xls</strong>
                        </li>
                        <li class="mb-2">Maksimal ukuran file <strong>2MB</strong></li>
                        <li class="mb-2">Data duplikat akan diupdate otomatis</li>
                        <li>Hapus baris contoh sebelum import</li>
                    </ul>
                </div>
            </div>

            <!-- Right Column - Form -->
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bx bx-upload me-2"></i>Form Import Data
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Format Info -->
                        <div class="info-alert alert">
                            <h6 class="alert-heading mb-3">
                                <i class="bx bx-info-circle me-2"></i>Format Data Excel
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <strong>Kolom Wajib:</strong>
                                    <ul class="mb-0 mt-2 small">
                                        <li><span class="format-badge">NIM</span> Nomor Induk Mahasiswa</li>
                                        <li><span class="format-badge">Status</span> Status pembayaran</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <strong>Format Status:</strong>
                                    <ul class="mb-0 mt-2 small">
                                        <li><code class="text-success">bayar</code> untuk Sudah Bayar</li>
                                        <li><code class="text-warning">belum_bayar</code> untuk Belum Bayar</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Import Form -->
                        <form action="{{ route('admin.pembayaran-ukt.process-import') }}" method="POST"
                            enctype="multipart/form-data" id="importForm">
                            @csrf

                            <!-- Tahun Ajaran -->
                            <div class="mb-4">
                                <label for="tahun_ajaran_id" class="form-label fw-semibold required-label">
                                    Tahun Ajaran
                                </label>
                                <select class="form-select @error('tahun_ajaran_id') is-invalid @enderror"
                                    id="tahun_ajaran_id" name="tahun_ajaran_id" required>
                                    <option value="">-- Pilih Tahun Ajaran --</option>
                                    @foreach ($tahunAjaranList as $tahun)
                                        <option value="{{ $tahun->id }}"
                                            {{ old('tahun_ajaran_id', $tahunAjaranAktif?->id) == $tahun->id ? 'selected' : '' }}>
                                            {{ $tahun->tahun }} - {{ ucfirst($tahun->semester) }}
                                            @if ($tahun->status_aktif)
                                                <span class="badge bg-success">Aktif</span>
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('tahun_ajaran_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="bx bx-info-circle me-1"></i>
                                    Pilih tahun ajaran untuk data yang akan diimport
                                </div>
                            </div>

                            <!-- File Upload -->
                            <div class="mb-4">
                                <label for="file" class="form-label fw-semibold required-label">
                                    File Excel
                                </label>

                                <div class="file-upload-area" id="fileUploadArea">
                                    <input type="file" class="d-none @error('file') is-invalid @enderror"
                                        id="file" name="file" accept=".xlsx,.xls" required>

                                    <div class="file-upload-icon">
                                        <i class="bx bx-cloud-upload"></i>
                                    </div>

                                    <h6 class="mb-2">Klik untuk memilih file atau drag & drop</h6>
                                    <p class="text-muted small mb-0">
                                        Format: .xlsx, .xls (Maksimal 2MB)
                                    </p>
                                </div>

                                <!-- File Info Display -->
                                <div class="file-info" id="fileInfo">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-file text-success me-2" style="font-size: 24px;"></i>
                                        <div>
                                            <div class="fw-semibold" id="fileName"></div>
                                            <small class="text-muted" id="fileSize"></small>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="removeFile">
                                        <i class="bx bx-x"></i>
                                    </button>
                                </div>

                                @error('file')
                                    <div class="text-danger small mt-2">
                                        <i class="bx bx-error-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('admin.pembayaran-ukt.index') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-x"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                                    <i class="bx bx-upload me-2"></i>Import Data
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
            const form = document.getElementById('importForm');
            const fileInput = document.getElementById('file');
            const fileUploadArea = document.getElementById('fileUploadArea');
            const fileInfo = document.getElementById('fileInfo');
            const fileName = document.getElementById('fileName');
            const fileSize = document.getElementById('fileSize');
            const removeFileBtn = document.getElementById('removeFile');
            const submitBtn = document.getElementById('submitBtn');

            // Click to upload
            fileUploadArea.addEventListener('click', function() {
                fileInput.click();
            });

            // File selected
            fileInput.addEventListener('change', function(e) {
                handleFile(e.target.files[0]);
            });

            // Drag and drop
            fileUploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.add('dragover');
            });

            fileUploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('dragover');
            });

            fileUploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.remove('dragover');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    handleFile(files[0]);
                }
            });

            // Handle file display
            function handleFile(file) {
                if (!file) return;

                // Validate file type
                const allowedTypes = [
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                ];

                if (!allowedTypes.includes(file.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format File Salah',
                        text: 'File harus berformat .xlsx atau .xls',
                        confirmButtonColor: '#696cff'
                    });
                    fileInput.value = '';
                    return;
                }

                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar',
                        text: 'Ukuran file maksimal 2MB',
                        confirmButtonColor: '#696cff'
                    });
                    fileInput.value = '';
                    return;
                }

                // Display file info
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                fileInfo.classList.add('active');
                fileUploadArea.style.display = 'none';
            }

            // Remove file
            removeFileBtn.addEventListener('click', function() {
                fileInput.value = '';
                fileInfo.classList.remove('active');
                fileUploadArea.style.display = 'block';
            });

            // Format file size
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
            }

            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const tahunAjaran = document.getElementById('tahun_ajaran_id');

                if (!tahunAjaran.value) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silakan pilih tahun ajaran terlebih dahulu',
                        confirmButtonColor: '#696cff'
                    });
                    return;
                }

                if (!fileInput.files.length) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silakan pilih file Excel terlebih dahulu',
                        confirmButtonColor: '#696cff'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Import',
                    html: `
                <p>File akan diimport untuk:</p>
                <strong>${tahunAjaran.options[tahunAjaran.selectedIndex].text}</strong>
                <br><br>
                <small class="text-muted">Proses import mungkin membutuhkan beberapa saat</small>
            `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#696cff',
                    cancelButtonColor: '#8592a3',
                    confirmButtonText: '<i class="bx bx-upload"></i> Ya, Import',
                    cancelButtonText: 'Batal',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return new Promise((resolve) => {
                            setTimeout(() => resolve(), 500);
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Disable submit button
                        submitBtn.disabled = true;
                        submitBtn.innerHTML =
                            '<span class="spinner-border spinner-border-sm me-2"></span>Mengimport...';

                        // Show loading
                        Swal.fire({
                            title: 'Memproses Import',
                            html: 'Mohon tunggu, sedang memproses data...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

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
                html: '{{ session('success') }}',
                confirmButtonColor: '#696cff'
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

        @if (session('failures'))
            let failuresHtml = '<div class="text-start"><ul class="mb-0">';
            @foreach (session('failures') as $failure)
                failuresHtml += '<li>Baris {{ $failure->row() }}: {{ $failure->errors()[0] }}</li>';
            @endforeach
            failuresHtml += '</ul></div>';

            Swal.fire({
                icon: 'error',
                title: 'Import Gagal',
                html: failuresHtml,
                confirmButtonColor: '#696cff',
                width: '600px'
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: '<ul class="text-start mb-0">' +
                    @foreach ($errors->all() as $error)
                        '<li>{{ $error }}</li>' +
                    @endforeach
                '</ul>',
                confirmButtonColor: '#696cff'
            });
        @endif
    </script>
@endpush
