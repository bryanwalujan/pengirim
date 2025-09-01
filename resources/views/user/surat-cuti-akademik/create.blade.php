@extends('layouts.user.form')

@section('title', 'Pengajuan Surat Cuti Akademik')

@push('styles')
    <style>
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

        .alert-info {
            background-color: #e7f5ff;
            border-color: #d0ebff;
            color: #1864ab;
            border-radius: 10px;
            padding: 1rem 1.5rem;
        }

        .alert-info ol {
            padding-left: 1.5rem;
        }

        .alert-info li {
            margin-bottom: 0.5rem;
        }
    </style>
@endpush

@section('form-content')
    <div class="form-container" data-aos="fade-up" data-aos-delay="100">
        <div class="form-card">
            <div class="form-header">
                <h4>Formulir Pengajuan Surat Cuti Akademik</h4>
            </div>

            <form action="{{ route('user.surat-cuti-akademik.store') }}" method="POST" enctype="multipart/form-data"
                id="surat-form">
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

                <!-- Alasan Pengajuan Section -->
                <div class="form-section" data-aos="fade-up" data-aos-delay="400">
                    <h5 class="section-title">
                        <i class="bi bi-card-text"></i>
                        Alasan Pengajuan
                    </h5>

                    <div class="mb-4">
                        <label for="alasan_pengajuan" class="form-label">Alasan Pengajuan Cuti Akademik <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control @error('alasan_pengajuan') is-invalid @enderror" id="alasan_pengajuan"
                            name="alasan_pengajuan" rows="4" required>{{ old('alasan_pengajuan') }}</textarea>
                        <div class="form-text mt-2">
                            Jelaskan alasan mengajukan cuti akademik secara detail dan jelas.
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
                            <h6 class="mb-0">Contoh Surat Cuti Akademik</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="surat-preview p-4"
                                style="font-family: 'Times New Roman', serif; font-size: 12pt; line-height: 1.5;">
                                <!-- Kop Surat -->
                                <div class="text-center mb-4">
                                    <h5 style="font-weight: bold; margin-bottom: 5px;">KEMENTERIAN PENDIDIKAN TINGGI, SAINS,
                                        DAN TEKNOLOGI</h5>
                                    <h5 style="font-weight: bold; margin-bottom: 5px;">UNIVERSITAS NEGERI MANADO</h5>
                                    <h6 style="font-weight: bold; margin-bottom: 5px;">FAKULTAS TEKNIK</h6>
                                    <h6 style="font-weight: bold; margin-bottom: 5px;">PROGRAM STUDI S1 TEKNIK INFORMATIKA
                                    </h6>
                                    <p style="margin-bottom: 0; font-size: 11pt;">
                                        Alamat : Kampus UNIMA Tondano 95618, Telp.(0431)7235580<br>
                                        Website : tl.unima.ac.id, Email : teknikinformatika@unima.ac.id
                                    </p>
                                </div>

                                <hr style="border-top: 2px solid #000; margin: 10px 0;">

                                <!-- Informasi Surat -->
                                <div class="mb-4">
                                    <table style="width: 100%; font-size: 12pt; border-collapse: collapse;">
                                        <tr>
                                            <td width="80">Nomor</td>
                                            <td width="10">:</td>
                                            <td>0449/UN41.2/TI/2025</td>
                                            <td width="200" style="text-align: right;">Tondano, 27 Februari 2025</td>
                                        </tr>
                                        <tr>
                                            <td>Lampiran</td>
                                            <td>:</td>
                                            <td>3 berkas</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Perihal</td>
                                            <td>:</td>
                                            <td>Permohonan Cuti Akademik</td>
                                            <td></td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Tujuan Surat -->
                                <p style="margin-bottom: 15px;">
                                    Kepada Yth.<br>
                                    Dekan Fakultas Teknik Universitas Negeri Manado di Tondano
                                </p>

                                <!-- Isi Surat -->
                                <div style="text-align: justify; margin-bottom: 20px;">
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
                                            <td>Delapan (VIII)</td>
                                        </tr>
                                        <tr>
                                            <td>Prodi</td>
                                            <td>:</td>
                                            <td>Teknik Informatika</td>
                                        </tr>
                                    </table>
                                    <p>
                                        Mahasiswa tersebut bermaksud untuk cuti akademik dalam semester yang sedang berjalan
                                        ini, semester
                                        VIII tahun ajaran {{ $tahunAjaranAktif->tahun ?? '' }} dengan alasan
                                        <span id="preview-alasan"
                                            class="highlight-text">{{ old('alasan_pengajuan') ? old('alasan_pengajuan') : '[Masukkan alasan pengajuan cuti akademik]' }}</span>.
                                        <br>
                                        Kiranya Dekan berkenan menerbitkan surat keterangan cuti akademik untuk mahasiswa
                                        tersebut.
                                    </p>

                                    <p style="margin-top: 10px;">
                                        Demikian permohonan ini, atasnya disampaikan terima kasih.
                                    </p>
                                </div>

                                <!-- Tanda Tangan -->
                                <table style="vertical-align: top; width: 100%; line-height: 0.8; margin-top: 30px;">
                                    <tr>
                                        <td style="width: 50%;">
                                            <p>Mengetahui,</p>
                                            <p>Pimpinan Jurusan PTIK,</p>
                                            <div
                                                style="height: 100px; display: flex; align-items: center; justify-content: center;">
                                                <!-- Space for signature -->
                                            </div>
                                            <p style="text-decoration: underline;">
                                                <strong>Arje C. Djamen, ST, MT</strong>
                                            </p>
                                            <p>NIP. 19870712 201012 1 006</p>
                                        </td>
                                        <td style="width: 50%; padding-left: 5rem;">
                                            <p>Koordinator Program Studi</p>
                                            <p>Teknik Informatika,</p>
                                            <div
                                                style="height: 100px; display: flex; align-items: center; justify-content: center;">
                                                <!-- Space for signature -->
                                            </div>
                                            <p style="text-decoration: underline;">
                                                <strong>Kristofel Santa, S,ST, M.MT</strong>
                                            </p>
                                            <p>NIP. 19870531 201504 1 003</p>
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
                        <div class="form-text mt-2">Isi jika ada informasi penting, seperti dokumen pendukung atau
                            penjelasan tambahan.</div>
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

                    <div class="alert alert-info mb-4">
                        <h6 class="fw-bold mb-2">Dokumen yang diperlukan:</h6>
                        <ol class="mb-0">
                            <li>
                                <strong>Surat Permohonan</strong> (sudah ditandatangani oleh Orang Tua dan Dosen Pembimbing
                                Akademik)
                                <small class="text-muted d-block">Format: PDF, maks. 2MB</small>
                            </li>
                            <li class="mt-2">
                                <strong>KHS/Transkrip Nilai</strong> (sudah ditandatangani oleh Koordinator Program Studi)
                                <small class="text-muted d-block">Format: PDF atau gambar, maks. 2MB</small>
                            </li>
                            <li class="mt-2">
                                <strong>Slip Uang Kuliah Tunggal (UKT)</strong> semester sebelumnya
                                <small class="text-muted d-block">Format: PDF atau gambar, maks. 2MB</small>
                            </li>
                        </ol>
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
                        <a href="{{ route('user.surat-cuti-akademik.index') }}" class="btn btn-back">
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

                // Only show clear all button if there are valid files
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
                    // Show error message if no valid files were selected
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

        // Form submission handler with SweetAlert
        document.getElementById('surat-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Mengirim...';

            // Simulate form submission
            this.submit();
        });

        // Preview alasan pengajuan
        document.addEventListener('DOMContentLoaded', function() {
            const alasanInput = document.getElementById('alasan_pengajuan');
            const previewAlasan = document.getElementById('preview-alasan');

            // Update preview saat input berubah
            alasanInput.addEventListener('input', function() {
                if (this.value.trim() === '') {
                    previewAlasan.innerHTML =
                        '<span class="highlight-text">Masukkan alasan pengajuan cuti akademik di form di atas</span>';
                } else {
                    previewAlasan.innerHTML = '<span class="highlight-text">' + this.value + '</span>';
                }
            });

            // Trigger input event untuk inisialisasi
            alasanInput.dispatchEvent(new Event('input'));
        });
    </script>
@endpush
