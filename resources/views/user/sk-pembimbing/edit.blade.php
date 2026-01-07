@extends('layouts.user.app')

@section('title', 'Edit Pengajuan SK Pembimbing')

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

        .current-file {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            background: #e8f5e9;
            border-radius: 0.375rem;
            margin-bottom: 0.5rem;
        }

        .current-file .file-icon {
            font-size: 1.5rem;
            margin-right: 0.75rem;
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
                <li class="breadcrumb-item">
                    <a href="{{ route('user.sk-pembimbing.show', $pengajuan) }}">Detail</a>
                </li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>

        <!-- Alert Alasan Ditolak -->
        @if ($pengajuan->isDokumenTidakValid() && $pengajuan->alasan_ditolak)
            <div class="alert alert-warning mb-4">
                <div class="d-flex">
                    <i class="bx bx-error-circle me-2 fs-4"></i>
                    <div>
                        <h6 class="alert-heading mb-1">Alasan Dokumen Tidak Valid:</h6>
                        <p class="mb-0">{{ $pengajuan->alasan_ditolak }}</p>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('user.sk-pembimbing.update', $pengajuan) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Judul Skripsi -->
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Judul Skripsi</h5>
                        </div>
                        <div class="card-body">
                            <textarea name="judul_skripsi" class="form-control @error('judul_skripsi') is-invalid @enderror" rows="2">{{ old('judul_skripsi', $pengajuan->judul_skripsi) }}</textarea>
                            @error('judul_skripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Surat Permohonan -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">Surat Permohonan</h6>
                        </div>
                        <div class="card-body">
                            @if ($pengajuan->file_surat_permohonan)
                                <div class="current-file">
                                    <i class="bx bxs-file-pdf text-danger file-icon"></i>
                                    <div class="flex-grow-1">
                                        <small class="text-muted">File saat ini</small>
                                        <div class="text-truncate" style="max-width: 150px;">
                                            {{ basename($pengajuan->file_surat_permohonan) }}
                                        </div>
                                    </div>
                                    <a href="{{ route('user.sk-pembimbing.view-document', [$pengajuan, 'permohonan']) }}"
                                        target="_blank" class="btn btn-sm btn-icon btn-outline-primary">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </div>
                            @endif

                            <label class="form-label small text-muted">Upload file baru (opsional)</label>
                            <input type="file" name="file_surat_permohonan"
                                class="form-control @error('file_surat_permohonan') is-invalid @enderror"
                                accept=".pdf,.jpg,.jpeg,.png">
                            @error('file_surat_permohonan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">PDF, JPG, PNG (Max: 2MB)</small>
                        </div>
                    </div>
                </div>

                <!-- Slip UKT -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">Slip UKT Terakhir</h6>
                        </div>
                        <div class="card-body">
                            @if ($pengajuan->file_slip_ukt)
                                <div class="current-file">
                                    <i class="bx bxs-file-pdf text-danger file-icon"></i>
                                    <div class="flex-grow-1">
                                        <small class="text-muted">File saat ini</small>
                                        <div class="text-truncate" style="max-width: 150px;">
                                            {{ basename($pengajuan->file_slip_ukt) }}
                                        </div>
                                    </div>
                                    <a href="{{ route('user.sk-pembimbing.view-document', [$pengajuan, 'ukt']) }}"
                                        target="_blank" class="btn btn-sm btn-icon btn-outline-primary">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </div>
                            @endif

                            <label class="form-label small text-muted">Upload file baru (opsional)</label>
                            <input type="file" name="file_slip_ukt"
                                class="form-control @error('file_slip_ukt') is-invalid @enderror"
                                accept=".pdf,.jpg,.jpeg,.png">
                            @error('file_slip_ukt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">PDF, JPG, PNG (Max: 2MB)</small>
                        </div>
                    </div>
                </div>

                <!-- Proposal Revisi -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">Proposal Revisi</h6>
                        </div>
                        <div class="card-body">
                            @if ($pengajuan->file_proposal_revisi)
                                <div class="current-file">
                                    <i class="bx bxs-file-pdf text-danger file-icon"></i>
                                    <div class="flex-grow-1">
                                        <small class="text-muted">File saat ini</small>
                                        <div class="text-truncate" style="max-width: 150px;">
                                            {{ basename($pengajuan->file_proposal_revisi) }}
                                        </div>
                                    </div>
                                    <a href="{{ route('user.sk-pembimbing.view-document', [$pengajuan, 'proposal']) }}"
                                        target="_blank" class="btn btn-sm btn-icon btn-outline-primary">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </div>
                            @endif

                            <label class="form-label small text-muted">Upload file baru (opsional)</label>
                            <input type="file" name="file_proposal_revisi"
                                class="form-control @error('file_proposal_revisi') is-invalid @enderror" accept=".pdf">
                            @error('file_proposal_revisi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">PDF only (Max: 10MB)</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body d-flex justify-content-between">
                    <a href="{{ route('user.sk-pembimbing.show', $pengajuan) }}" class="btn btn-outline-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Kembali
                    </a>
                    <div>
                        <button type="submit" name="action" value="save" class="btn btn-outline-primary me-2">
                            <i class="bx bx-save me-1"></i> Simpan
                        </button>
                        <button type="submit" name="action" value="resubmit" class="btn btn-primary">
                            <i class="bx bx-send me-1"></i> Submit Ulang
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
