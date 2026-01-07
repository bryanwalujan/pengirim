@extends('layouts.user.app')

@section('title', 'SK Pembimbing Skripsi')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1 text-white">
                                    <i class="bx bx-file me-2"></i>SK Pembimbing Skripsi
                                </h4>
                                <p class="mb-0 opacity-75">
                                    Kelola pengajuan SK Pembimbing Skripsi Anda
                                </p>
                            </div>
                            @if ($canCreateNew)
                                <a href="{{ route('user.sk-pembimbing.create') }}" class="btn btn-light">
                                    <i class="bx bx-plus me-1"></i> Ajukan SK Baru
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert jika tidak bisa mengajukan -->
        @if (!$canCreateNew && $reason)
            <div class="alert alert-warning alert-dismissible mb-4" role="alert">
                <i class="bx bx-info-circle me-2"></i>
                {{ $reason }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Daftar Pengajuan -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Riwayat Pengajuan</h5>
                <span class="badge bg-primary">{{ $pengajuans->total() }} Pengajuan</span>
            </div>
            <div class="card-body">
                @forelse($pengajuans as $pengajuan)
                    <div
                        class="card mb-3 border {{ $pengajuan->isSelesai() ? 'border-success' : ($pengajuan->isDitolak() || $pengajuan->isDokumenTidakValid() ? 'border-danger' : 'border-primary') }}">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar avatar-lg me-3 flex-shrink-0">
                                            @if ($pengajuan->isSelesai())
                                                <span class="avatar-initial rounded bg-success">
                                                    <i class="bx bx-check-circle"></i>
                                                </span>
                                            @elseif($pengajuan->isDitolak() || $pengajuan->isDokumenTidakValid())
                                                <span class="avatar-initial rounded bg-danger">
                                                    <i class="bx bx-x-circle"></i>
                                                </span>
                                            @else
                                                <span class="avatar-initial rounded bg-primary">
                                                    <i class="bx bx-loader-circle bx-spin"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ Str::limit($pengajuan->judul_skripsi, 60) }}</h6>
                                            <div class="d-flex flex-wrap gap-2 mb-2">
                                                {!! $pengajuan->status_badge !!}
                                                @if ($pengajuan->nomor_surat)
                                                    <span class="badge bg-label-dark">
                                                        <i class="bx bx-hash me-1"></i>{{ $pengajuan->nomor_surat }}
                                                    </span>
                                                @endif
                                            </div>
                                            <small class="text-muted">
                                                <i class="bx bx-calendar me-1"></i>
                                                Diajukan: {{ $pengajuan->created_at->format('d M Y H:i') }}
                                            </small>
                                            @if ($pengajuan->hasPembimbingAssigned())
                                                <div class="mt-2">
                                                    <small class="text-muted d-block">
                                                        <i class="bx bx-user me-1"></i>PS1:
                                                        {{ $pengajuan->dosenPembimbing1->name ?? '-' }}
                                                    </small>
                                                    @if ($pengajuan->dosenPembimbing2)
                                                        <small class="text-muted d-block">
                                                            <i class="bx bx-user me-1"></i>PS2:
                                                            {{ $pengajuan->dosenPembimbing2->name }}
                                                        </small>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <div class="btn-group">
                                        <a href="{{ route('user.sk-pembimbing.show', $pengajuan) }}"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="bx bx-show me-1"></i> Detail
                                        </a>
                                        @if ($pengajuan->canBeEditedByMahasiswa())
                                            <a href="{{ route('user.sk-pembimbing.edit', $pengajuan) }}"
                                                class="btn btn-outline-warning btn-sm">
                                                <i class="bx bx-edit me-1"></i> Edit
                                            </a>
                                        @endif
                                        @if ($pengajuan->isSelesai() && $pengajuan->file_surat_sk)
                                            <a href="{{ route('user.sk-pembimbing.download-sk', $pengajuan) }}"
                                                class="btn btn-success btn-sm">
                                                <i class="bx bx-download me-1"></i> Download SK
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mt-3">
                                @php
                                    $progress = match ($pengajuan->status) {
                                        'draft' => 10,
                                        'menunggu_verifikasi' => 25,
                                        'dokumen_tidak_valid' => 25,
                                        'ps_ditentukan' => 50,
                                        'menunggu_ttd_kajur' => 65,
                                        'menunggu_ttd_korprodi' => 80,
                                        'selesai' => 100,
                                        'ditolak' => 0,
                                        default => 0,
                                    };
                                    $progressClass = match (true) {
                                        $pengajuan->isSelesai() => 'bg-success',
                                        $pengajuan->isDitolak() || $pengajuan->isDokumenTidakValid() => 'bg-danger',
                                        default => 'bg-primary',
                                    };
                                @endphp
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar {{ $progressClass }}" role="progressbar"
                                        style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}"
                                        aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <small class="text-muted mt-1 d-block">
                                    {{ $pengajuan->workflow_message }}
                                </small>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <img src="{{ asset('assets/img/illustrations/empty.svg') }}" alt="No data"
                            style="max-width: 200px;" class="mb-3">
                        <h5 class="text-muted">Belum ada pengajuan</h5>
                        <p class="text-muted mb-3">Anda belum memiliki pengajuan SK Pembimbing Skripsi</p>
                        @if ($canCreateNew)
                            <a href="{{ route('user.sk-pembimbing.create') }}" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i> Ajukan Sekarang
                            </a>
                        @endif
                    </div>
                @endforelse

                <!-- Pagination -->
                @if ($pengajuans->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $pengajuans->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
