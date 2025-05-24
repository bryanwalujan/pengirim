@extends('layouts.user.form')

@section('title', 'Pengajuan Surat Aktif Kuliah')

@push('style')
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
            --border-radius: 12px;
            --box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }

        .form-card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            background: white;
        }

        .form-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-header h4 {
            font-weight: 600;
            margin: 0;
            font-size: 1.5rem;
        }

        .form-section {
            padding: 0 2rem 1.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .section-title {
            display: flex;
            align-items: center;
            color: var(--dark-gray);
            font-weight: 600;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }

        .section-title i {
            margin-right: 0.75rem;
            color: var(--accent-color);
            font-size: 1.2rem;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--dark-gray);
        }

        .form-control,
        .form-select {
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            border: 1px solid #e0e0e0;
            transition: var(--transition);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15);
        }

        .btn-submit {
            background-color: var(--primary-color);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-submit:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .btn-back {
            background-color: white;
            border: 1px solid #e0e0e0;
            color: var(--dark-gray);
            padding: 0.75rem 2rem;
            transition: var(--transition);
        }

        .btn-back:hover {
            background-color: var(--light-gray);
            color: var(--dark-gray);
        }

        .file-upload-wrapper {
            position: relative;
            margin-bottom: 1rem;
        }

        .file-upload-label {
            display: block;
            padding: 1.5rem;
            border: 2px dashed #e0e0e0;
            border-radius: var(--border-radius);
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .file-upload-label:hover {
            border-color: var(--accent-color);
        }

        .file-upload-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-info {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 0.5rem;
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 1rem;
            }

            .form-section {
                padding: 0 1rem 1rem;
            }

            .form-header {
                padding: 1rem;
            }
        }

        /* Tambahkan ini di bagian style Anda */
        .list-group-item {
            transition: all 0.2s ease;
            border-radius: var(--border-radius) !important;
            margin-bottom: 0.5rem;
        }

        .list-group-item:hover {
            background-color: var(--light-gray);
        }

        .cancel-file {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border-radius: 50%;
            transition: var(--transition);
        }

        .cancel-file:hover {
            background-color: #ff4444;
            color: white;
        }

        .file-info {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 0.5rem;
            display: block;
        }
    </style>
@endpush

@section('form-content')
    <div class="form-container" data-aos="fade-up">
        <div class="form-card">
            <div class="form-header">
                <h4>Formulir Pengajuan Surat Aktif Kuliah</h4>
            </div>

            <form action="{{ route('user.surat-aktif-kuliah.store') }}" method="POST" enctype="multipart/form-data"
                id="surat-form">
                @csrf

                <!-- Informasi Mahasiswa Section -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="bi bi-person-circle"></i>
                        Informasi Mahasiswa
                    </h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="nama" value="{{ Auth::user()->name }}"
                                    readonly>
                                <label for="nama">Nama Lengkap</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="nim" value="{{ Auth::user()->nim }}"
                                    readonly>
                                <label for="nim">Nomor Induk Mahasiswa</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Akademik Section -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="bi bi-book-half"></i>
                        Informasi Akademik
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran"
                                    value="{{ $tahunAjaranAktif->tahun ?? '' }}" readonly>
                                <label for="tahun_ajaran">Tahun Ajaran</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="semester" name="semester"
                                    value="{{ $tahunAjaranAktif->semester ?? '' }}" readonly>
                                <label for="semester">Semester</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tujuan Pengajuan Section -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="bi bi-card-text"></i>
                        Tujuan Pengajuan
                    </h5>

                    <div class="mb-3">
                        <label for="tujuan_pengajuan" class="form-label">Tujuan Pengajuan Surat <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control @error('tujuan_pengajuan') is-invalid @enderror" id="tujuan_pengajuan"
                            name="tujuan_pengajuan" rows="3" required>{{ old('tujuan_pengajuan') }}</textarea>
                        <div class="form-text">Contoh: Untuk beasiswa, pengajuan KIP-Kuliah, atau melamar kerja.</div>
                        @error('tujuan_pengajuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Keterangan Tambahan Section -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="bi bi-chat-square-text"></i>
                        Keterangan Tambahan
                    </h5>

                    <div class="mb-3">
                        <label for="keterangan_tambahan" class="form-label">Keterangan Tambahan (Opsional)</label>
                        <textarea class="form-control @error('keterangan_tambahan') is-invalid @enderror" id="keterangan_tambahan"
                            name="keterangan_tambahan" rows="3">{{ old('keterangan_tambahan') }}</textarea>
                        <div class="form-text">Isi jika ada informasi penting seperti kebutuhan mendesak.</div>
                        @error('keterangan_tambahan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- File Upload Section -->
                <div class="form-section">
                    <h5 class="section-title">
                        <i class="bi bi-paperclip"></i>
                        Dokumen Pendukung
                    </h5>

                    <div class="file-upload-wrapper">
                        <label class="file-upload-label" for="file_pendukung_path">
                            <i class="bi bi-cloud-arrow-up" style="font-size: 2rem; color: var(--accent-color);"></i>
                            <div class="mt-2">Klik untuk mengunggah dokumen (Bisa multiple)</div>
                            <div class="file-info">Format: PDF, JPG, PNG (maks. 2MB per file)</div>
                        </label>
                        <input type="file" class="file-upload-input @error('file_pendukung_path') is-invalid @enderror"
                            id="file_pendukung_path" name="file_pendukung_path[]" multiple>
                        @error('file_pendukung_path')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div id="file-list" class="mt-3"></div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-section pt-0">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('user.surat-aktif-kuliah.index') }}" class="btn btn-back">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-submit text-white" id="submit-btn">
                            <i class="bi bi-send-check"></i> Ajukan Surat
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        AOS.init({
            duration: 700,
            once: true
        });

        // File upload preview with cancel button
        document.getElementById('file_pendukung_path').addEventListener('change', function(e) {
            const files = e.target.files;
            const fileList = document.getElementById('file-list');
            fileList.innerHTML = '';

            if (files && files.length > 0) {
                const list = document.createElement('div');
                list.className = 'list-group';
                let hasValidFiles = false;

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const fileSize = (file.size / (1024 * 1024)).toFixed(2);
                    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
                    const maxSize = 2 * 1024 * 1024; // 2MB

                    // Skip invalid files
                    if (!allowedTypes.includes(file.type) || file.size > maxSize) {
                        continue;
                    }

                    hasValidFiles = true;

                    const item = document.createElement('div');
                    item.className = 'list-group-item d-flex justify-content-between align-items-center';
                    item.dataset.index = i;
                    item.innerHTML = `
                        <div class="d-flex align-items-center">
                            <span class="me-2">${file.name}</span>
                            <small class="text-muted">${fileSize} MB</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger cancel-file" data-index="${i}">
                            <i class="bi bi-x"></i>
                        </button>
                    `;
                    list.appendChild(item);
                }

                // Only show clear all button if there are valid files
                if (hasValidFiles) {
                    const clearAllBtn = document.createElement('button');
                    clearAllBtn.type = 'button';
                    clearAllBtn.className = 'btn btn-sm btn-outline-secondary mt-2';
                    clearAllBtn.innerHTML = '<i class="bi bi-trash"></i> Hapus Semua';
                    clearAllBtn.addEventListener('click', function() {
                        document.getElementById('file_pendukung_path').value = '';
                        fileList.innerHTML = '';
                    });

                    list.appendChild(clearAllBtn);
                    fileList.appendChild(list);
                } else {
                    // Show error message if no valid files were selected
                    const errorMsg = document.createElement('div');
                    errorMsg.className = 'alert alert-danger mt-2';
                    errorMsg.textContent =
                        'Tidak ada file yang valid. Harap unggah file PDF, JPG, atau PNG (maks. 2MB)';
                    fileList.appendChild(errorMsg);
                }
            }
        });

        // Handle cancel button clicks
        document.addEventListener('click', function(e) {
            const cancelBtn = e.target.closest('.cancel-file');
            if (cancelBtn) {
                const index = parseInt(cancelBtn.dataset.index);
                const fileInput = document.getElementById('file_pendukung_path');

                if (fileInput.files && fileInput.files.length > index) {
                    // Create new FileList without the cancelled file
                    const dataTransfer = new DataTransfer();

                    // Add all files except the one being cancelled
                    for (let i = 0; i < fileInput.files.length; i++) {
                        if (i !== index) {
                            dataTransfer.items.add(fileInput.files[i]);
                        }
                    }

                    // Update file input
                    fileInput.files = dataTransfer.files;

                    // Trigger change event to update the UI
                    const event = new Event('change');
                    fileInput.dispatchEvent(event);
                }
            }
        });

        // Form submission handler
        document.getElementById('surat-form').addEventListener('submit', function(e) {
            // Optional: Add validation here if needed
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass"></i> Mengirim...';
        });
    </script>
@endpush
