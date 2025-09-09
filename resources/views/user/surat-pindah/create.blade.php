@extends('layouts.user.form')

@section('title', 'Pengajuan Surat Pindah')

@push('styles')
    <style>
        /* Keep all the existing styles */
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
            --border-radius: 15px;
            --box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        .form-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2.5rem 1rem;
        }

        .form-card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            background: linear-gradient(145deg, #ffffff, #f9f9f9);
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            text-align: center;
            border-bottom: 4px solid var(--accent-color);
        }

        .form-header h4 {
            font-weight: 700;
            margin: 0;
            font-size: 1.75rem;
            letter-spacing: 0.5px;
        }

        .form-section {
            padding: 1rem 2.5rem 2rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            background: rgba(255, 255, 255, 0.5);
            border-radius: 10px;
        }

        .section-title {
            display: flex;
            align-items: center;
            color: var(--dark-gray);
            font-weight: 700;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
            text-transform: uppercase;
        }

        .section-title i {
            margin-right: 0.75rem;
            color: var(--accent-color);
            font-size: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark-gray);
            font-size: 0.95rem;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            padding: 0.85rem 1.25rem;
            border: 1px solid #e0e0e0;
            transition: var(--transition);
            background: #fff;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.03);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.2);
        }

        .form-floating>label {
            padding: 0.85rem 1.25rem;
            color: #6c757d;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 0.85rem 2.5rem;
            font-weight: 600;
            border-radius: 50px;
            transition: var(--transition);
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }

        .btn-back {
            background: #fff;
            border: 2px solid #e0e0e0;
            color: var(--dark-gray);
            padding: 0.85rem 2.5rem;
            border-radius: 50px;
            transition: var(--transition);
        }

        .btn-back:hover {
            background: var(--light-gray);
            border-color: var(--accent-color);
            color: var(--dark-gray);
            transform: translateY(-3px);
        }

        .file-upload-wrapper {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .file-upload-label {
            display: block;
            padding: 2rem;
            border: 2px dashed var(--accent-color);
            border-radius: var(--border-radius);
            text-align: center;
            cursor: pointer;
            background: rgba(72, 149, 239, 0.05);
            transition: var(--transition);
        }

        .file-upload-label:hover {
            background: rgba(72, 149, 239, 0.1);
            border-color: var(--primary-color);
            transform: scale(1.01);
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
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 0.5rem;
            display: block;
        }

        .list-group-item {
            transition: var(--transition);
            border-radius: 10px !important;
            margin-bottom: 0.75rem;
            background: #fff;
            border: 1px solid #e0e0e0;
        }

        .list-group-item:hover {
            background: var(--light-gray);
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .cancel-file {
            width: 28px;
            height: 28px;
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
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 1.5rem 0.5rem;
            }

            .form-section {
                padding: 1rem 1.5rem 1.5rem;
            }

            .form-header {
                padding: 1.5rem;
            }

            .btn-submit,
            .btn-back {
                padding: 0.75rem 1.5rem;
            }
        }

        .highlight-text {
            background-color: #fff3cd;
            padding: 2px 4px;
            border-radius: 4px;
            font-weight: 500;
            color: #856404;
        }

        .surat-preview {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }

        .preview-content {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            text-align: justify;
        }

        .preview-content table {
            width: 100%;
            border-collapse: collapse;
        }

        .preview-content td {
            padding: 2px 0;
            vertical-align: top;
        }

        .preview-content .underline {
            text-decoration: underline;
        }

        .preview-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .preview-header h5 {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .preview-header h6 {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .preview-header p {
            margin-bottom: 0;
            font-size: 12px;
        }

        .preview-divider {
            border-top: 2px solid #000;
            margin: 10px 0;
        }

        /* Required Docs Container */
        .required-docs-container {
            background: rgba(255, 255, 255, 0.7);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 10px;
            border: 1px solid rgba(67, 97, 238, 0.15);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .required-docs-content {
            padding: 0 0.5rem;
        }

        .required-docs-list {
            list-style-type: none;
            padding-left: 0;
            margin-bottom: 0;
        }

        .required-docs-list li {
            padding: 0.5rem 0;
            display: flex;
            align-items: flex-start;
            font-size: 0.9rem;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.08);
        }

        .required-docs-list li:last-child {
            border-bottom: none;
        }

        .doc-type {
            font-weight: 600;
            color: var(--dark-gray);
            min-width: 190px;
            display: inline-block;
        }


        @media (max-width: 576px) {
            .required-docs-list li {
                flex-direction: column;
            }

            .doc-type {
                min-width: 100%;
                margin-bottom: 0.25rem;
            }
        }
    </style>
@endpush

@section('form-content')
    <div class="form-container" data-aos="fade-up" data-aos-delay="100">
        <div class="form-card">
            <div class="form-header">
                <h4>Formulir Pengajuan Surat Pindah</h4>
            </div>

            <form action="{{ route('user.surat-pindah.store') }}" method="POST" enctype="multipart/form-data" id="surat-form">
                @csrf

                <!-- Informasi Mahasiswa Section -->
                <div class="form-section" data-aos="fade-up" data-aos-delay="200">
                    <h5 class="section-title">
                        <i class="bi bi-person-circle"></i>
                        Informasi Mahasiswa
                    </h5>

                    <div class="row g-4">
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
                <div class="form-section" data-aos="fade-up" data-aos-delay="300">
                    <h5 class="section-title">
                        <i class="bi bi-book-half"></i>
                        Informasi Akademik
                    </h5>
                    <div class="row g-4">
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

                <!-- Informasi Pindah Section -->
                <div class="form-section" data-aos="fade-up" data-aos-delay="400">
                    <h5 class="section-title">
                        <i class="bi bi-building"></i>
                        Informasi Pindah
                    </h5>

                    <div class="mb-4">
                        <label for="universitas_tujuan" class="form-label">Universitas Tujuan <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('universitas_tujuan') is-invalid @enderror"
                            id="universitas_tujuan" name="universitas_tujuan" value="{{ old('universitas_tujuan') }}"
                            required>
                        @error('universitas_tujuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="alasan_pengajuan" class="form-label">Alasan Pengajuan <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control @error('alasan_pengajuan') is-invalid @enderror" id="alasan_pengajuan"
                            name="alasan_pengajuan" rows="4" required>{{ old('alasan_pengajuan') }}</textarea>
                        <div class="form-text mt-2">
                            Jelaskan alasan Anda ingin pindah ke universitas lain.
                        </div>
                        @error('alasan_pengajuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Preview Surat Section -->
                <div class="form-section" data-aos="fade-up" data-aos-delay="450">
                    <h5 class="section-title">
                        <i class="bi bi-eye"></i>
                        Preview Surat
                    </h5>

                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">Contoh Surat Pindah</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="preview-content p-4">
                                <!-- Kop Surat -->
                                <div class="preview-header">
                                    <h5>KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</h5>
                                    <h5>UNIVERSITAS NEGERI MANADO</h5>
                                    <h6>FAKULTAS TEKNIK</h6>
                                    <h6>PROGRAM STUDI S1 TEKNIK INFORMATIKA</h6>
                                    <p>
                                        Alamat : Kampus UNIMA Tondano 95618, Telp.(0431)7233580<br>
                                        Website : tt.unima.ac.id, Email : teknikinformatika@unima.ac.id
                                    </p>
                                </div>

                                <div class="preview-divider"></div>

                                <!-- Informasi Surat -->
                                <div style="margin-bottom: 30px;">
                                    <table>
                                        <tr>
                                            <td width="80">Nomor</td>
                                            <td width="10">:</td>
                                            <td>3108/UN41.2/TI/2024</td>
                                            <td width="150" style="text-align: right;">Tondano, 10 Desember 2024</td>
                                        </tr>
                                        <tr>
                                            <td>Lampiran</td>
                                            <td>:</td>
                                            <td>1 berkas</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Perihal</td>
                                            <td>:</td>
                                            <td>Permohonan Pindah Universitas</td>
                                            <td></td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Tujuan Surat -->
                                <p style="margin-bottom: 10px;">
                                    Kepada Yth.<br>
                                    Dekan Fakultas Teknik Universitas Negeri Manado di Tondano
                                </p>

                                <!-- Isi Surat -->
                                <div style="margin-bottom: 20px;">
                                    <p style="margin-bottom: 2px">
                                        Pimpinan Program Studi S1 Teknik Informatika menerangkan bahwa:
                                    </p>
                                    <table style="margin-left: 40px; margin-bottom: 10px;">
                                        <tr>
                                            <td width="110">Nama</td>
                                            <td width="10">:</td>
                                            <td>{{ Auth::user()->name }}</td>
                                        </tr>
                                        <tr>
                                            <td>NIM</td>
                                            <td>:</td>
                                            <td>{{ Auth::user()->nim }}</td>
                                        </tr>
                                        <tr>
                                            <td>Semester</td>
                                            <td>:</td>
                                            <td>V (Lima)</td>
                                        </tr>
                                        <tr>
                                            <td>Jurusan/Prodi</td>
                                            <td>:</td>
                                            <td>S1 Teknik Informatika</td>
                                        </tr>
                                    </table>
                                    <p id="preview-alasan">
                                        Mahasiswa tersebut bermaksud akan pindah dari Universitas Negeri Manado ke <span
                                            class="highlight-text">{{ old('universitas_tujuan', '[Universitas Tujuan]') }}</span>
                                        dengan alasan <span
                                            class="highlight-text">{{ old('alasan_pengajuan', '[Alasan Pengajuan]') }}</span>.<br>
                                        Kiranya Dekan berkenan menerbitkan surat keterangan pindah untuk mahasiswa tersebut.
                                    </p>

                                    <p style="margin-top: -10px;">
                                        Demikian permohonan ini, atasnya disampaikan terima kasih.
                                    </p>
                                </div>

                                <!-- Tanda Tangan -->
                                <table style="vertical-align: top; width: 100%; line-height: 0.8;">
                                    <tr>
                                        <td>
                                            <p>Mengetahui,</p>
                                            <p>Pimpinan Jurusan PTIK,</p>
                                            <div style="height: 100px;"></div>
                                            <p class="underline">
                                                <strong>[Nama Penandatangan]</strong>
                                            </p>
                                            <p>NIP. [NIP]</p>
                                        </td>
                                        <td style="padding-left: 8rem;">
                                            <p>Koordinator Program Studi</p>
                                            <p>Teknik Informatika,</p>
                                            <div style="height: 100px;"></div>
                                            <p class="underline">
                                                <strong>[Nama Penandatangan]</strong>
                                            </p>
                                            <p>NIP. [NIP]</p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Keterangan Tambahan Section -->
                <div class="form-section" data-aos="fade-up" data-aos-delay="500">
                    <h5 class="section-title">
                        <i class="bi bi-chat-square-text"></i>
                        Keterangan Tambahan
                    </h5>

                    <div class="mb-4">
                        <label for="keterangan_tambahan" class="form-label">Keterangan Tambahan (Opsional)</label>
                        <textarea class="form-control @error('keterangan_tambahan') is-invalid @enderror" id="keterangan_tambahan"
                            name="keterangan_tambahan" rows="4">{{ old('keterangan_tambahan') }}</textarea>
                        <div class="form-text mt-2">Isi jika ada informasi penting tambahan.</div>
                        @error('keterangan_tambahan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- File Upload Section -->
                <div class="form-section" data-aos="fade-up" data-aos-delay="600">
                    <h5 class="section-title">
                        <i class="bi bi-paperclip"></i>
                        Dokumen Pendukung
                    </h5>

                    <!-- Dokumen yang dibutuhkan -->
                    <div class="required-docs-container mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-info-circle-fill me-2"
                                style="color: var(--accent-color); font-size: 1.25rem;"></i>
                            <h6 class="mb-0 fw-semibold" style="color: var(--secondary-color);">Dokumen Wajib</h6>
                        </div>
                        <div class="required-docs-content">
                            <p class="mb-2" style="font-size: 0.9rem; color: #6c757d;">Silakan unggah dokumen berikut
                                (format PDF/JPG/PNG, maks. 2MB per file):</p>
                            <ul class="required-docs-list">
                                <li>
                                    <span class="doc-type">👉 Surat permohonan (Mengetahui Orang Tua dan Dosen PA)</span>
                                </li>
                                <li>
                                    <span class="doc-type">👉 Surat Keterangan Diterima di Universitas (Diambil di
                                        Universitas yang Dituju)</span>
                                </li>
                                <li>
                                    <span class="doc-type">👉 Slip Uang Kuliahh Tunggal (UKT) Semester Sebelumnya</span>
                                </li>
                                <li>
                                    <span class="doc-type">👉 Transkrip Nilai</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="file-upload-wrapper">
                        <label class="file-upload-label" for="file_pendukung_path">
                            <i class="bi bi-cloud-arrow-up" style="font-size: 2.5rem; color: var(--accent-color);"></i>
                            <div class="mt-3 fw-semibold">Klik untuk mengunggah dokumen pendukung</div>
                            <div class="file-info">Format: PDF, JPG, PNG (maks. 2MB per file)</div>
                        </label>
                        <input type="file" class="file-upload-input @error('file_pendukung_path') is-invalid @enderror"
                            id="file_pendukung_path" name="file_pendukung_path[]" multiple>
                        @error('file_pendukung_path')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div id="file-list" class="mt-4"></div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-section pt-0" data-aos="fade-up" data-aos-delay="700">
                    <div class="d-flex justify-content-between gap-3">
                        <a href="{{ route('user.surat-pindah.index') }}" class="btn btn-back">
                            <i class="bi bi-arrow-left me-2"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-submit text-white" id="submit-btn">
                            <i class="bi bi-send-check me-2"></i> Ajukan Surat
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
            duration: 400,
            once: true,
            offset: 100
        });

        // Update preview saat input berubah
        document.addEventListener('DOMContentLoaded', function() {
            const universitasInput = document.getElementById('universitas_tujuan');
            const alasanInput = document.getElementById('alasan_pengajuan');
            const previewAlasan = document.getElementById('preview-alasan');

            function updatePreview() {
                const universitas = universitasInput.value.trim() === '' ? '[Universitas Tujuan]' : universitasInput
                    .value;
                const alasan = alasanInput.value.trim() === '' ? '[Alasan Pengajuan]' : alasanInput.value;

                previewAlasan.innerHTML = `
                    Mahasiswa tersebut bermaksud akan pindah dari Universitas Negeri Manado ke <span class="highlight-text">${universitas}</span>
                    dengan alasan <span class="highlight-text">${alasan}</span>.<br>
                    Kiranya Dekan berkenan menerbitkan surat keterangan pindah untuk mahasiswa tersebut.
                `;
            }

            universitasInput.addEventListener('input', updatePreview);
            alasanInput.addEventListener('input', updatePreview);

            // Trigger initial update
            updatePreview();
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

                    if (!allowedTypes.includes(file.type) || file.size > maxSize) {
                        continue;
                    }

                    hasValidFiles = true;

                    const item = document.createElement('div');
                    item.className = 'list-group-item d-flex justify-content-between align-items-center';
                    item.dataset.index = i;
                    item.innerHTML = `
                        <div class="d-flex align-items-center">
                            <i class="bi bi-file-earmark-text me-2 text-primary"></i>
                            <span class="me-3">${file.name}</span>
                            <small class="text-muted">${fileSize} MB</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger cancel-file" data-index="${i}">
                            <i class="bi bi-x"></i>
                        </button>
                    `;
                    list.appendChild(item);
                }

                if (hasValidFiles) {
                    const clearAllBtn = document.createElement('button');
                    clearAllBtn.type = 'button';
                    clearAllBtn.className = 'btn btn-sm btn-outline-danger mt-3';
                    clearAllBtn.innerHTML = '<i class="bi bi-trash me-2"></i> Hapus Semua';
                    clearAllBtn.addEventListener('click', function() {
                        document.getElementById('file_pendukung_path').value = '';
                        fileList.innerHTML = '';
                    });

                    list.appendChild(clearAllBtn);
                    fileList.appendChild(list);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Tidak Valid',
                        text: 'Harap unggah file PDF, JPG, atau PNG (maks. 2MB)',
                        confirmButtonColor: '#4361ee'
                    });
                    document.getElementById('file_pendukung_path').value = '';
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
                    const dataTransfer = new DataTransfer();

                    for (let i = 0; i < fileInput.files.length; i++) {
                        if (i !== index) {
                            dataTransfer.items.add(fileInput.files[i]);
                        }
                    }

                    fileInput.files = dataTransfer.files;
                    const event = new Event('change');
                    fileInput.dispatchEvent(event);
                }
            }
        });

        // Form submission handler with SweetAlert
        document.getElementById('surat-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Mengirim...';

            this.submit();
        });
    </script>
@endpush
