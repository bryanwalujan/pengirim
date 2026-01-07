@extends('layouts.user.app')

@section('title', 'Ajukan SK Pembimbing Skripsi')

@push('styles')
    <style>
        .upload-area {
            border: 2px dashed #d9dee3;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .upload-area:hover,
        .upload-area.dragover {
            border-color: #696cff;
            background-color: rgba(105, 108, 255, 0.05);
        }

        .upload-area.has-file {
            border-color: #71dd37;
            background-color: rgba(113, 221, 55, 0.05);
        }

        .file-preview {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            background: #f5f5f9;
            border-radius: 0.375rem;
            margin-top: 0.5rem;
        }

        .file-preview .file-icon {
            font-size: 2rem;
            margin-right: 1rem;
        }

        .file-preview .file-info {
            flex: 1;
        }

        .file-preview .file-remove {
            color: #ff3e1d;
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('user.sk-pembimbing.index') }}">SK Pembimbing</a>
                </li>
                <li class="breadcrumb-item active">Ajukan Baru</li>
            </ol>
        </nav>

        <!-- Info Card -->
        <div class="card mb-4 border-primary">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="avatar me-3">
                        <span class="avatar-initial rounded bg-primary">
                            <i class="bx bx-info-circle"></i>
                        </span>
                    </div>
                    <div>
                        <h6 class="mb-1">Informasi Pengajuan</h6>
                        <p class="mb-0 text-muted">
                            Data berikut diambil dari hasil Seminar Proposal Anda.
                            Pastikan dokumen yang diupload sudah sesuai dengan ketentuan.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('user.sk-pembimbing.store') }}" method="POST" enctype="multipart/form-data"
            id="formPengajuan">
            @csrf
            <input type="hidden" name="berita_acara_id" value="{{ $beritaAcara->id }}">

            <div class="row">
                <!-- Left Column - Data dari Sempro -->
                <div class="col-lg-5 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bx bx-data me-2"></i>Data dari Seminar Proposal
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Data Mahasiswa -->
                            <div class="mb-4">
                                <h6 class="text-primary mb-3">
                                    <i class="bx bx-user me-1"></i> Data Mahasiswa
                                </h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="text-muted" style="width: 100px;">Nama</td>
                                        <td><strong>{{ $mahasiswa->name }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">NIM</td>
                                        <td>{{ $mahasiswa->nim }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Email</td>
                                        <td>{{ $mahasiswa->email }}</td>
                                    </tr>
                                </table>
                            </div>

                            <hr>

                            <!-- Data Sempro -->
                            <div class="mb-4">
                                <h6 class="text-primary mb-3">
                                    <i class="bx bx-file me-1"></i> Data Seminar Proposal
                                </h6>
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Judul Skripsi</label>
                                    <p class="mb-0"><strong>{{ $dataFromSempro['judul_skripsi'] }}</strong></p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Dosen Pembimbing (Awal)</label>
                                    <p class="mb-0">
                                        <i class="bx bx-user-check text-success me-1"></i>
                                        {{ $dataFromSempro['dosen_pembimbing_awal']->name ?? '-' }}
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Tanggal Seminar</label>
                                    <p class="mb-0">
                                        {{ $beritaAcara->jadwalSeminarProposal->tanggal_seminar->format('d F Y') }}
                                    </p>
                                </div>
                                <div>
                                    <label class="form-label text-muted small">Hasil Seminar</label>
                                    <p class="mb-0">
                                        {!! $beritaAcara->keputusan_badge !!}
                                    </p>
                                </div>
                            </div>

                            <hr>

                            <!-- Judul Skripsi (Editable) -->
                            <div class="mb-3">
                                <label class="form-label" for="judul_skripsi">
                                    Judul Skripsi <span class="text-danger">*</span>
                                </label>
                                <textarea name="judul_skripsi" id="judul_skripsi" class="form-control @error('judul_skripsi') is-invalid @enderror"
                                    rows="3" placeholder="Judul skripsi dapat diubah jika ada revisi">{{ old('judul_skripsi', $dataFromSempro['judul_skripsi']) }}</textarea>
                                @error('judul_skripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    Judul dapat diubah jika ada perbaikan dari hasil seminar proposal.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Upload Dokumen -->
                <div class="col-lg-7 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bx bx-upload me-2"></i>Upload Dokumen
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Surat Permohonan -->
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="bx bx-envelope me-1"></i>
                                    Surat Permohonan (Tulis Tangan) <span class="text-danger">*</span>
                                </label>
                                <div class="upload-area" id="dropzone-permohonan" data-target="file_surat_permohonan">
                                    <i class="bx bx-cloud-upload text-primary" style="font-size: 3rem;"></i>
                                    <p class="mb-1">Drag & drop file atau <span class="text-primary">browse</span></p>
                                    <small class="text-muted">PDF, JPG, PNG (Max: 2MB)</small>
                                </div>
                                <input type="file" name="file_surat_permohonan" id="file_surat_permohonan"
                                    class="d-none @error('file_surat_permohonan') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                <div id="preview-permohonan"></div>
                                @error('file_surat_permohonan')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Slip UKT -->
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="bx bx-receipt me-1"></i>
                                    Slip UKT Terakhir <span class="text-danger">*</span>
                                </label>
                                <div class="upload-area" id="dropzone-ukt" data-target="file_slip_ukt">
                                    <i class="bx bx-cloud-upload text-primary" style="font-size: 3rem;"></i>
                                    <p class="mb-1">Drag & drop file atau <span class="text-primary">browse</span></p>
                                    <small class="text-muted">PDF, JPG, PNG (Max: 2MB)</small>
                                </div>
                                <input type="file" name="file_slip_ukt" id="file_slip_ukt"
                                    class="d-none @error('file_slip_ukt') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                <div id="preview-ukt"></div>
                                @error('file_slip_ukt')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Proposal Revisi -->
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="bx bx-book-open me-1"></i>
                                    Proposal Revisi <span class="text-danger">*</span>
                                </label>
                                <div class="upload-area" id="dropzone-proposal" data-target="file_proposal_revisi">
                                    <i class="bx bx-cloud-upload text-primary" style="font-size: 3rem;"></i>
                                    <p class="mb-1">Drag & drop file atau <span class="text-primary">browse</span></p>
                                    <small class="text-muted">PDF only (Max: 10MB)</small>
                                </div>
                                <input type="file" name="file_proposal_revisi" id="file_proposal_revisi"
                                    class="d-none @error('file_proposal_revisi') is-invalid @enderror" accept=".pdf">
                                <div id="preview-proposal"></div>
                                @error('file_proposal_revisi')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Catatan -->
                            <div class="alert alert-info mb-0">
                                <div class="d-flex">
                                    <i class="bx bx-bulb me-2 mt-1"></i>
                                    <div>
                                        <strong>Catatan:</strong>
                                        <ul class="mb-0 ps-3 mt-1">
                                            <li>Surat permohonan harus ditulis tangan dengan jelas</li>
                                            <li>Slip UKT yang diupload adalah slip pembayaran semester terakhir</li>
                                            <li>Proposal revisi adalah proposal yang sudah diperbaiki sesuai hasil seminar
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between">
                            <a href="{{ route('user.sk-pembimbing.index') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-arrow-back me-1"></i> Kembali
                            </a>
                            <div>
                                <button type="submit" name="action" value="draft"
                                    class="btn btn-outline-primary me-2">
                                    <i class="bx bx-save me-1"></i> Simpan Draft
                                </button>
                                <button type="submit" name="action" value="submit" class="btn btn-primary">
                                    <i class="bx bx-send me-1"></i> Submit Pengajuan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // File upload handling
            const uploadAreas = document.querySelectorAll('.upload-area');

            uploadAreas.forEach(area => {
                const targetId = area.dataset.target;
                const input = document.getElementById(targetId);
                const previewId = 'preview-' + targetId.replace('file_', '').replace('_', '-');
                const preview = document.getElementById(previewId.replace('surat-permohonan', 'permohonan')
                    .replace('slip-ukt', 'ukt').replace('proposal-revisi', 'proposal'));

                // Click to upload
                area.addEventListener('click', () => input.click());

                // Drag & Drop
                area.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    area.classList.add('dragover');
                });

                area.addEventListener('dragleave', () => {
                    area.classList.remove('dragover');
                });

                area.addEventListener('drop', (e) => {
                    e.preventDefault();
                    area.classList.remove('dragover');
                    if (e.dataTransfer.files.length) {
                        input.files = e.dataTransfer.files;
                        handleFileSelect(input, area, preview);
                    }
                });

                // File selected
                input.addEventListener('change', () => {
                    handleFileSelect(input, area, preview);
                });
            });

            function handleFileSelect(input, area, preview) {
                const file = input.files[0];
                if (!file) return;

                // Validate file size
                const maxSize = input.accept.includes('.pdf') && !input.accept.includes('.jpg') ? 10 * 1024 * 1024 :
                    2 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('File terlalu besar. Maksimal ' + (maxSize / 1024 / 1024) + 'MB');
                    input.value = '';
                    return;
                }

                area.classList.add('has-file');

                // Show preview
                const fileSize = (file.size / 1024).toFixed(1);
                const sizeUnit = fileSize > 1024 ? (file.size / 1024 / 1024).toFixed(1) + ' MB' : fileSize + ' KB';

                preview.innerHTML = `
            <div class="file-preview">
                <i class="bx ${file.type === 'application/pdf' ? 'bxs-file-pdf text-danger' : 'bxs-file-image text-primary'} file-icon"></i>
                <div class="file-info">
                    <div class="fw-medium">${file.name}</div>
                    <small class="text-muted">${sizeUnit}</small>
                </div>
                <i class="bx bx-x file-remove" onclick="removeFile('${input.id}')"></i>
            </div>
        `;
            }

            // Remove file function
            window.removeFile = function(inputId) {
                const input = document.getElementById(inputId);
                const area = document.querySelector(`[data-target="${inputId}"]`);
                const previewId = 'preview-' + inputId.replace('file_', '').replace('_', '-');
                const preview = document.getElementById(previewId.replace('surat-permohonan', 'permohonan')
                    .replace('slip-ukt', 'ukt').replace('proposal-revisi', 'proposal'));

                input.value = '';
                area.classList.remove('has-file');
                preview.innerHTML = '';
            };
        });
    </script>
@endpush
