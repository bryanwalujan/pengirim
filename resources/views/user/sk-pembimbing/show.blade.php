@extends('layouts.user.app')

@section('title', 'Detail Pengajuan SK Pembimbing')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('user.sk-pembimbing.index') }}">SK Pembimbing</a>
            </li>
            <li class="breadcrumb-item active">Detail Pengajuan</li>
        </ol>
    </nav>

    <!-- Status Alert -->
    @if($pengajuan->isDokumenTidakValid())
        <div class="alert alert-danger mb-4">
            <div class="d-flex align-items-start">
                <i class="bx bx-error-circle me-2 fs-4"></i>
                <div>
                    <h6 class="alert-heading mb-1">Dokumen Tidak Valid</h6>
                    <p class="mb-2">{{ $pengajuan->alasan_ditolak }}</p>
                    <a href="{{ route('user.sk-pembimbing.edit', $pengajuan) }}" class="btn btn-danger btn-sm">
                        <i class="bx bx-edit me-1"></i> Perbaiki Dokumen
                    </a>
                </div>
            </div>
        </div>
    @elseif($pengajuan->isDitolak())
        <div class="alert alert-danger mb-4">
            <div class="d-flex align-items-start">
                <i class="bx bx-x-circle me-2 fs-4"></i>
                <div>
                    <h6 class="alert-heading mb-1">Pengajuan Ditolak</h6>
                    <p class="mb-0">{{ $pengajuan->alasan_ditolak ?? 'Pengajuan Anda telah ditolak.' }}</p>
                </div>
            </div>
        </div>
    @elseif($pengajuan->isSelesai())
        <div class="alert alert-success mb-4">
            <div class="d-flex align-items-start">
                <i class="bx bx-check-circle me-2 fs-4"></i>
                <div>
                    <h6 class="alert-heading mb-1">SK Pembimbing Selesai!</h6>
                    <p class="mb-2">SK Pembimbing Skripsi Anda telah selesai dan dapat didownload.</p>
                    @if($pengajuan->file_surat_sk)
                        <a href="{{ route('user.sk-pembimbing.download-sk', $pengajuan) }}" class="btn btn-success btn-sm">
                            <i class="bx bx-download me-1"></i> Download SK Pembimbing
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8 mb-4">
            <!-- Detail Pengajuan -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Pengajuan</h5>
                    {!! $pengajuan->status_badge !!}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Judul Skripsi</label>
                            <p class="mb-0 fw-medium">{{ $pengajuan->judul_skripsi }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Tanggal Pengajuan</label>
                            <p class="mb-0">{{ $pengajuan->created_at->format('d F Y, H:i') }}</p>
                        </div>
                        @if($pengajuan->nomor_surat)
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small">Nomor Surat</label>
                                <p class="mb-0 fw-medium">{{ $pengajuan->nomor_surat }}</p>
                            </div>
                        @endif
                        @if($pengajuan->tanggal_surat)
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small">Tanggal Surat</label>
                                <p class="mb-0">{{ $pengajuan->tanggal_surat->format('d F Y') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Dosen Pembimbing -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-user-check me-2"></i>Dosen Pembimbing
                    </h5>
                </div>
                <div class="card-body">
                    @if($pengajuan->hasPembimbingAssigned())
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="avatar me-3">
                                        <span class="avatar-initial rounded bg-primary">
                                            <i class="bx bx-user"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <small class="text-muted">Pembimbing 1 (PS1)</small>
                                        <h6 class="mb-0">{{ $pengajuan->dosenPembimbing1->name }}</h6>
                                        <small class="text-muted">{{ $pengajuan->dosenPembimbing1->nip ?? '-' }}</small>
                                    </div>
                                </div>
                            </div>
                            @if($pengajuan->dosenPembimbing2)
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center p-3 bg-light rounded">
                                        <div class="avatar me-3">
                                            <span class="avatar-initial rounded bg-info">
                                                <i class="bx bx-user"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <small class="text-muted">Pembimbing 2 (PS2)</small>
                                            <h6 class="mb-0">{{ $pengajuan->dosenPembimbing2->name }}</h6>
                                            <small class="text-muted">{{ $pengajuan->dosenPembimbing2->nip ?? '-' }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bx bx-user-x text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2 mb-0">Dosen pembimbing belum ditentukan</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Dokumen yang Diupload -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-file me-2"></i>Dokumen yang Diupload
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <!-- Surat Permohonan -->
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div class="d-flex align-items-center">
                                <i class="bx bxs-file-pdf text-danger me-3 fs-3"></i>
                                <div>
                                    <h6 class="mb-0">Surat Permohonan</h6>
                                    <small class="text-muted">Surat permohonan tulis tangan</small>
                                </div>
                            </div>
                            <a href="{{ route('user.sk-pembimbing.view-document', [$pengajuan, 'permohonan']) }}" 
                               target="_blank"
                               class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-show me-1"></i> Lihat
                            </a>
                        </div>

                        <!-- Slip UKT -->
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div class="d-flex align-items-center">
                                <i class="bx bxs-file-pdf text-danger me-3 fs-3"></i>
                                <div>
                                    <h6 class="mb-0">Slip UKT Terakhir</h6>
                                    <small class="text-muted">Bukti pembayaran UKT</small>
                                </div>
                            </div>
                            <a href="{{ route('user.sk-pembimbing.view-document', [$pengajuan, 'ukt']) }}" 
                               target="_blank"
                               class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-show me-1"></i> Lihat
                            </a>
                        </div>

                        <!-- Proposal Revisi -->
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div class="d-flex align-items-center">
                                <i class="bx bxs-file-pdf text-danger me-3 fs-3"></i>
                                <div>
                                    <h6 class="mb-0">Proposal Revisi</h6>
                                    <small class="text-muted">Proposal yang sudah direvisi</small>
                                </div>
                            </div>
                            <a href="{{ route('user.sk-pembimbing.view-document', [$pengajuan, 'proposal']) }}" 
                               target="_blank"
                               class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-show me-1"></i> Lihat
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Timeline -->
        <div class="col-lg-4 mb-4">
            <!-- Progress Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-loader-circle me-2"></i>Progress
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $steps = [
                            ['status' => 'draft', 'label' => 'Draft', 'icon' => 'bx-edit'],
                            ['status' => 'menunggu_verifikasi', 'label' => 'Verifikasi Dokumen', 'icon' => 'bx-search'],
                            ['status' => 'ps_ditentukan', 'label' => 'Penentuan Pembimbing', 'icon' => 'bx-user-check'],
                            ['status' => 'menunggu_ttd_kajur', 'label' => 'TTD Ketua Jurusan', 'icon' => 'bx-pen'],
                            ['status' => 'menunggu_ttd_korprodi', 'label' => 'TTD Koordinator Prodi', 'icon' => 'bx-pen'],
                            ['status' => 'selesai', 'label' => 'Selesai', 'icon' => 'bx-check-circle'],
                        ];
                        
                        $currentIndex = array_search($pengajuan->status, array_column($steps, 'status'));
                        if ($currentIndex === false) $currentIndex = -1;
                    @endphp

                    <div class="timeline-vertical">
                        @foreach($steps as $index => $step)
                            @php
                                $isCompleted = $index < $currentIndex || $pengajuan->status === 'selesai';
                                $isCurrent = $index === $currentIndex;
                                $isPending = $index > $currentIndex;
                            @endphp
                            <div class="timeline-item {{ $isCompleted ? 'completed' : ($isCurrent ? 'current' : 'pending') }}">
                                <div class="timeline-marker">
                                    @if($isCompleted)
                                        <i class="bx bx-check"></i>
                                    @elseif($isCurrent)
                                        <i class="bx {{ $step['icon'] }}"></i>
                                    @else
                                        <span>{{ $index + 1 }}</span>
                                    @endif
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-0 {{ $isPending ? 'text-muted' : '' }}">{{ $step['label'] }}</h6>
                                    @if($isCurrent && !$pengajuan->isSelesai())
                                        <small class="text-primary">Sedang diproses</small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tracking History -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-history me-2"></i>Riwayat
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="timeline-simple">
                        @if($pengajuan->ttd_korprodi_at)
                            <li class="timeline-item">
                                <span class="timeline-point bg-success"></span>
                                <div class="timeline-event">
                                    <div class="fw-medium">Ditandatangani Korprodi</div>
                                    <small class="text-muted">{{ $pengajuan->ttd_korprodi_at->format('d M Y H:i') }}</small>
                                </div>
                            </li>
                        @endif
                        @if($pengajuan->ttd_kajur_at)
                            <li class="timeline-item">
                                <span class="timeline-point bg-success"></span>
                                <div class="timeline-event">
                                    <div class="fw-medium">Ditandatangani Kajur</div>
                                    <small class="text-muted">{{ $pengajuan->ttd_kajur_at->format('d M Y H:i') }}</small>
                                </div>
                            </li>
                        @endif
                        @if($pengajuan->ps_assigned_at)
                            <li class="timeline-item">
                                <span class="timeline-point bg-primary"></span>
                                <div class="timeline-event">
                                    <div class="fw-medium">Pembimbing Ditentukan</div>
                                    <small class="text-muted">{{ $pengajuan->ps_assigned_at->format('d M Y H:i') }}</small>
                                </div>
                            </li>
                        @endif
                        @if($pengajuan->verified_at)
                            <li class="timeline-item">
                                <span class="timeline-point bg-info"></span>
                                <div class="timeline-event">
                                    <div class="fw-medium">Dokumen Diverifikasi</div>
                                    <small class="text-muted">{{ $pengajuan->verified_at->format('d M Y H:i') }}</small>
                                </div>
                            </li>
                        @endif
                        <li class="timeline-item">
                            <span class="timeline-point bg-secondary"></span>
                            <div class="timeline-event">
                                <div class="fw-medium">Pengajuan Dibuat</div>
                                <small class="text-muted">{{ $pengajuan->created_at->format('d M Y H:i') }}</small>
                            </div>
                        </li>
                    </ul>
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
                        @if($pengajuan->canBeEditedByMahasiswa())
                            <a href="{{ route('user.sk-pembimbing.edit', $pengajuan) }}" class="btn btn-warning">
                                <i class="bx bx-edit me-1"></i> Edit Dokumen
                            </a>
                        @endif
                        @if($pengajuan->isSelesai() && $pengajuan->file_surat_sk)
                            <a href="{{ route('user.sk-pembimbing.download-sk', $pengajuan) }}" class="btn btn-success">
                                <i class="bx bx-download me-1"></i> Download SK
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .timeline-vertical {
        position: relative;
        padding-left: 30px;
    }
    .timeline-vertical::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e0e0e0;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    .timeline-marker {
        position: absolute;
        left: -30px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        background: #e0e0e0;
        color: #666;
    }
    .timeline-item.completed .timeline-marker {
        background: #71dd37;
        color: white;
    }
    .timeline-item.current .timeline-marker {
        background: #696cff;
        color: white;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(105, 108, 255, 0.4); }
        50% { box-shadow: 0 0 0 8px rgba(105, 108, 255, 0); }
    }
    
    .timeline-simple {
        list-style: none;
        padding: 0;
        margin: 0;
        position: relative;
    }
    .timeline-simple::before {
        content: '';
        position: absolute;
        left: 5px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e0e0e0;
    }
    .timeline-simple .timeline-item {
        position: relative;
        padding-left: 25px;
        padding-bottom: 1rem;
    }
    .timeline-simple .timeline-point {
        position: absolute;
        left: 0;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }
</style>
@endpush
@endsection