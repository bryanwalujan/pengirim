{{-- filepath: resources/views/admin/sk-pembimbing/show.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Detail Pengajuan SK Pembimbing')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.sk-pembimbing.index') }}">SK Pembimbing</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </nav>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bx bx-error-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            {{-- Main Content --}}
            <div class="col-lg-8">
                {{-- Status Card --}}
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bx bx-info-circle me-2"></i>Status Pengajuan</h5>
                        {!! $pengajuan->status_badge !!}
                    </div>
                    <div class="card-body">
                        <div class="alert alert-{{ $pengajuan->isSelesai() ? 'success' : ($pengajuan->isDitolak() || $pengajuan->isDokumenTidakValid() ? 'danger' : 'info') }} mb-0">
                            <i class="bx bx-{{ $pengajuan->isSelesai() ? 'check-circle' : 'info-circle' }} me-2"></i>
                            {{ $pengajuan->workflow_message }}
                        </div>

                        @if($pengajuan->alasan_ditolak)
                            <div class="alert alert-danger mt-3 mb-0">
                                <strong><i class="bx bx-x-circle me-1"></i>Alasan:</strong><br>
                                {{ $pengajuan->alasan_ditolak }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Data Mahasiswa --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bx bx-user me-2"></i>Data Mahasiswa</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Nama Mahasiswa</label>
                                <p class="fw-semibold mb-0">{{ $pengajuan->mahasiswa->name ?? '-' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">NIM</label>
                                <p class="fw-semibold mb-0">{{ $pengajuan->mahasiswa->nim ?? '-' }}</p>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted">Judul Skripsi</label>
                                <p class="fw-semibold mb-0">{{ $pengajuan->judul_skripsi }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Dokumen --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bx bx-file me-2"></i>Dokumen Pendukung</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @if($pengajuan->file_surat_permohonan)
                                <a href="{{ route('admin.sk-pembimbing.view-document', [$pengajuan, 'permohonan']) }}" 
                                   target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span><i class="bx bx-file-blank me-2"></i>Surat Permohonan</span>
                                    <span class="badge bg-primary"><i class="bx bx-show"></i></span>
                                </a>
                            @endif
                            @if($pengajuan->file_slip_ukt)
                                <a href="{{ route('admin.sk-pembimbing.view-document', [$pengajuan, 'ukt']) }}" 
                                   target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span><i class="bx bx-file-blank me-2"></i>Slip UKT</span>
                                    <span class="badge bg-primary"><i class="bx bx-show"></i></span>
                                </a>
                            @endif
                            @if($pengajuan->file_proposal_revisi)
                                <a href="{{ route('admin.sk-pembimbing.view-document', [$pengajuan, 'proposal']) }}" 
                                   target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span><i class="bx bx-file-blank me-2"></i>Proposal Revisi</span>
                                    <span class="badge bg-primary"><i class="bx bx-show"></i></span>
                                </a>
                            @endif
                            @if($pengajuan->file_surat_sk)
                                <a href="{{ route('admin.sk-pembimbing.download-sk', $pengajuan) }}" 
                                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center list-group-item-success">
                                    <span><i class="bx bx-file-blank me-2"></i>SK Pembimbing (Final)</span>
                                    <span class="badge bg-success"><i class="bx bx-download"></i></span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Pembimbing Skripsi --}}
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bx bx-user-check me-2"></i>Pembimbing Skripsi</h5>
                        @if(auth()->user()->hasRole('staff') && $pengajuan->canAssignPsBy(auth()->user()))
                            <a href="{{ route('admin.sk-pembimbing.assign-pembimbing', $pengajuan) }}" class="btn btn-sm btn-primary">
                                <i class="bx bx-edit me-1"></i>Ubah PS
                            </a>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($pengajuan->hasPembimbingAssigned())
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Pembimbing 1 (PS1)</label>
                                    <p class="fw-semibold mb-0">{{ $pengajuan->dosenPembimbing1->name ?? '-' }}</p>
                                    <small class="text-muted">{{ $pengajuan->dosenPembimbing1->nip ?? '' }}</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Pembimbing 2 (PS2)</label>
                                    <p class="fw-semibold mb-0">{{ $pengajuan->dosenPembimbing2->name ?? 'Tidak ada' }}</p>
                                    @if($pengajuan->dosenPembimbing2)
                                        <small class="text-muted">{{ $pengajuan->dosenPembimbing2->nip ?? '' }}</small>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="bx bx-info-circle me-2"></i>Pembimbing skripsi belum ditentukan.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                {{-- Actions --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bx bx-cog me-2"></i>Aksi</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            {{-- Staff Actions --}}
                            @if(auth()->user()->hasRole('staff'))
                                @if($pengajuan->isMenungguVerifikasi())
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#verifyModal">
                                        <i class="bx bx-check-circle me-1"></i>Verifikasi Dokumen
                                    </button>
                                @endif

                                @if($pengajuan->canAssignPsBy(auth()->user()))
                                    <a href="{{ route('admin.sk-pembimbing.assign-pembimbing', $pengajuan) }}" class="btn btn-primary">
                                        <i class="bx bx-user-plus me-1"></i>Tentukan Pembimbing
                                    </a>
                                @endif

                                @if(!$pengajuan->isSelesai() && !$pengajuan->isDitolak())
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                        <i class="bx bx-x-circle me-1"></i>Tolak Pengajuan
                                    </button>
                                @endif
                            @endif

                            {{-- Kajur Sign --}}
                            @if($pengajuan->canBeSignedByKajur(auth()->user()))
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#signKajurModal">
                                    <i class="bx bx-pen me-1"></i>Tanda Tangan (Kajur)
                                </button>
                            @endif

                            {{-- Korprodi Sign --}}
                            @if($pengajuan->canBeSignedByKorprodi(auth()->user()))
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#signKorprodiModal">
                                    <i class="bx bx-pen me-1"></i>Tanda Tangan (Korprodi)
                                </button>
                            @endif

                            {{-- Download SK --}}
                            @if($pengajuan->isSelesai() && $pengajuan->file_surat_sk)
                                <a href="{{ route('admin.sk-pembimbing.download-sk', $pengajuan) }}" class="btn btn-success">
                                    <i class="bx bx-download me-1"></i>Download SK
                                </a>
                            @endif

                            <a href="{{ route('admin.sk-pembimbing.index') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Audit Trail --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bx bx-history me-2"></i>Riwayat</h5>
                    </div>
                    <div class="card-body">
                        <ul class="timeline timeline-dashed">
                            <li class="timeline-item">
                                <span class="timeline-indicator timeline-indicator-primary">
                                    <i class="bx bx-file"></i>
                                </span>
                                <div class="timeline-event">
                                    <div class="timeline-header">
                                        <small class="text-muted">{{ $pengajuan->created_at->format('d M Y H:i') }}</small>
                                    </div>
                                    <p class="mb-0">Pengajuan dibuat</p>
                                </div>
                            </li>

                            @if($pengajuan->verified_at)
                                <li class="timeline-item">
                                    <span class="timeline-indicator timeline-indicator-success">
                                        <i class="bx bx-check"></i>
                                    </span>
                                    <div class="timeline-event">
                                        <div class="timeline-header">
                                            <small class="text-muted">{{ $pengajuan->verified_at->format('d M Y H:i') }}</small>
                                        </div>
                                        <p class="mb-0">Diverifikasi oleh {{ $pengajuan->verifiedByUser->name ?? '-' }}</p>
                                    </div>
                                </li>
                            @endif

                            @if($pengajuan->ps_assigned_at)
                                <li class="timeline-item">
                                    <span class="timeline-indicator timeline-indicator-info">
                                        <i class="bx bx-user-check"></i>
                                    </span>
                                    <div class="timeline-event">
                                        <div class="timeline-header">
                                            <small class="text-muted">{{ $pengajuan->ps_assigned_at->format('d M Y H:i') }}</small>
                                        </div>
                                        <p class="mb-0">PS ditentukan oleh {{ $pengajuan->psAssignedByUser->name ?? '-' }}</p>
                                    </div>
                                </li>
                            @endif

                            @if($pengajuan->ttd_kajur_at)
                                <li class="timeline-item">
                                    <span class="timeline-indicator timeline-indicator-success">
                                        <i class="bx bx-pen"></i>
                                    </span>
                                    <div class="timeline-event">
                                        <div class="timeline-header">
                                            <small class="text-muted">{{ $pengajuan->ttd_kajur_at->format('d M Y H:i') }}</small>
                                        </div>
                                        <p class="mb-0">TTD Kajur oleh {{ $pengajuan->ttdKajurUser->name ?? '-' }}</p>
                                    </div>
                                </li>
                            @endif

                            @if($pengajuan->ttd_korprodi_at)
                                <li class="timeline-item">
                                    <span class="timeline-indicator timeline-indicator-success">
                                        <i class="bx bx-pen"></i>
                                    </span>
                                    <div class="timeline-event">
                                        <div class="timeline-header">
                                            <small class="text-muted">{{ $pengajuan->ttd_korprodi_at->format('d M Y H:i') }}</small>
                                        </div>
                                        <p class="mb-0">TTD Korprodi oleh {{ $pengajuan->ttdKorprodiUser->name ?? '-' }}</p>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                {{-- Info --}}
                @if($pengajuan->nomor_surat)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bx bx-detail me-2"></i>Info Surat</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>No. Surat:</strong> {{ $pengajuan->nomor_surat }}</p>
                            <p class="mb-2"><strong>Tanggal:</strong> {{ $pengajuan->tanggal_surat?->format('d M Y') ?? '-' }}</p>
                            <p class="mb-0"><strong>Kode:</strong> <code>{{ $pengajuan->verification_code }}</code></p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Verify Modal --}}
    <div class="modal fade" id="verifyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.sk-pembimbing.verify', $pengajuan) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Verifikasi Dokumen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Status Dokumen</label>
                            <select name="is_valid" class="form-select" required>
                                <option value="1">Dokumen Valid</option>
                                <option value="0">Dokumen Tidak Valid</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan (Opsional)</label>
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan untuk mahasiswa..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Verifikasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.sk-pembimbing.reject', $pengajuan) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tolak Pengajuan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="bx bx-error me-2"></i>Pengajuan yang ditolak tidak dapat diproses kembali.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="alasan_ditolak" class="form-control" rows="3" required placeholder="Jelaskan alasan penolakan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Sign Kajur Modal --}}
    <div class="modal fade" id="signKajurModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.sk-pembimbing.sign-kajur', $pengajuan) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tanda Tangan Ketua Jurusan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-2"></i>
                            Dengan menandatangani, Anda menyetujui pengajuan SK Pembimbing ini sebagai Ketua Jurusan.
                        </div>
                        <p><strong>Mahasiswa:</strong> {{ $pengajuan->mahasiswa->name ?? '-' }}</p>
                        <p><strong>PS1:</strong> {{ $pengajuan->dosenPembimbing1->name ?? '-' }}</p>
                        <p class="mb-0"><strong>PS2:</strong> {{ $pengajuan->dosenPembimbing2->name ?? 'Tidak ada' }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success"><i class="bx bx-pen me-1"></i>Tanda Tangan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Sign Korprodi Modal --}}
    <div class="modal fade" id="signKorprodiModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.sk-pembimbing.sign-korprodi', $pengajuan) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tanda Tangan Koordinator Prodi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-2"></i>
                            Dengan menandatangani, Anda menyetujui dan menyelesaikan pengajuan SK Pembimbing ini.
                        </div>
                        <p><strong>Mahasiswa:</strong> {{ $pengajuan->mahasiswa->name ?? '-' }}</p>
                        <p><strong>PS1:</strong> {{ $pengajuan->dosenPembimbing1->name ?? '-' }}</p>
                        <p class="mb-0"><strong>PS2:</strong> {{ $pengajuan->dosenPembimbing2->name ?? 'Tidak ada' }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success"><i class="bx bx-pen me-1"></i>Tanda Tangan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .timeline { position: relative; padding-left: 1.5rem; list-style: none; }
    .timeline-item { position: relative; padding-bottom: 1rem; }
    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: -1.25rem;
        top: 1.5rem;
        bottom: 0;
        width: 2px;
        background: #e0e0e0;
    }
    .timeline-indicator {
        position: absolute;
        left: -1.75rem;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.5rem;
        color: #fff;
    }
    .timeline-indicator-primary { background: #696cff; }
    .timeline-indicator-success { background: #71dd37; }
    .timeline-indicator-info { background: #03c3ec; }
    .timeline-indicator-warning { background: #ffab00; }
</style>
@endpush
