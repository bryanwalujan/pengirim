@extends('layouts.admin.app')

@section('title', 'Detail Surat Aktif Kuliah')

@push('styles')
    <style>
        .form-label {
            font-weight: 500;
        }

        .timeline {
            position: relative;
            padding-left: 1rem;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
            padding-left: 1.5rem;
            border-left: 2px solid #dee2e6;
        }

        .timeline-item:last-child {
            border-left: 2px solid transparent;
        }

        .timeline-marker {
            position: absolute;
            left: -7px;
            top: 0;
            z-index: 1;
        }

        .timeline-content {
            margin-left: 0.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.surat-aktif-kuliah.index') }}">Surat Aktif Kuliah</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                </li>
                <li class="breadcrumb-item breadcrumb-custom-icon active" aria-current="page">Detail</li>
            </ol>
        </nav>
        <!-- /Breadcrumb -->

        <h4 class="fw-bold py-3 mb-2" style="margin-top: -1.2rem">
            <span class="text-muted">Detail Pengajuan Surat Aktif Kuliah</span>
        </h4>

        <!-- Card -->
        <div class="card">
            <div class="card-header border-bottom mb-4">
                <h5 class="card-title mb-0">Informasi Pengajuan</h5>
            </div>
            <div class="card-body">
                <!-- Status -->
                @php
                    $alertClass = match ($surat->status ?? 'diajukan') {
                        'disetujui', 'siap_diambil', 'sudah_diambil' => 'success',
                        'ditolak' => 'danger',
                        default => 'warning',
                    };
                @endphp
                <div class="alert alert-{{ $alertClass }} mb-4">
                    <h6 class="alert-heading mb-1">Status: <strong
                            class="text-uppercase">{{ str_replace('_', ' ', $surat->status ?? 'Diajukan') }}</strong></h6>
                    @if ($surat->status()->first()?->catatan_admin)
                        <p class="mb-0"><strong>Catatan Admin:</strong> {{ $surat->status()->first()->catatan_admin }}</p>
                    @endif
                </div>

                <!-- Informasi Surat -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Informasi Surat</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Surat</label>
                            <input type="text" class="form-control"
                                value="{{ $surat->nomor_surat ?? 'Belum ada nomor surat' }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Surat</label>
                            <input type="text" class="form-control"
                                value="{{ $surat->tanggal_surat ? $surat->tanggal_surat->format('d F Y') : 'Belum ada tanggal' }}"
                                readonly>
                        </div>
                    </div>
                </div>

                <!-- Informasi Mahasiswa -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Informasi Mahasiswa</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" value="{{ $surat->mahasiswa->name }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIM</label>
                            <input type="text" class="form-control" value="{{ $surat->mahasiswa->nim ?? '-' }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tahun Ajaran</label>
                            <input type="text" class="form-control" value="{{ $surat->tahun_ajaran ?? '-' }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Semester</label>
                            <input type="text" class="form-control" value="{{ ucfirst($surat->semester ?? '-') }}"
                                readonly>
                        </div>
                    </div>
                </div>

                <!-- Detail Pengajuan -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3">Detail Pengajuan</h6>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Tujuan Pengajuan</label>
                            <textarea class="form-control" rows="3" readonly>{{ $surat->tujuan_pengajuan ?? '-' }}</textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Keterangan Tambahan</label>
                            <textarea class="form-control" rows="2" readonly>{{ $surat->keterangan_tambahan ?? '-' }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Dokumen Pendukung -->
                @if ($surat->file_pendukung_path)
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Dokumen Pendukung</h6>
                        <div class="d-flex align-items-center">
                            <i class="bx bx-file me-2"></i>
                            <a href="{{ Storage::url($surat->file_pendukung_path) }}" target="_blank"
                                class="text-decoration-none">
                                Lihat Dokumen Pendukung
                            </a>
                        </div>
                    </div>
                @endif

                <!-- File Surat -->
                @if ($surat->file_surat_path)
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">File Surat</h6>
                        <div class="d-flex align-items-center">
                            <i class="bx bx-file me-2"></i>
                            <a href="{{ Storage::url($surat->file_surat_path) }}" target="_blank"
                                class="text-decoration-none">
                                Lihat File Surat
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!-- /Card -->

        <!-- Status Update Form -->
        @if (!in_array($surat->status, ['sudah_diambil', 'ditolak']))
            <div class="card mt-4">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Update Status Pengajuan</h5>
                </div>
                <div class="card-body">
                    @include('admin.surat-aktif-kuliah.status-form')
                </div>
            </div>
        @endif

        <!-- Timeline -->
        <div class="card mt-4">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Riwayat Status</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @forelse ($surat->trackings->sortByDesc('created_at') as $tracking)
                        <div class="timeline-item">
                            <div class="timeline-marker">
                                @php
                                    $timelineClass = match ($tracking->aksi) {
                                        'disetujui', 'siap_diambil', 'sudah_diambil' => 'text-success',
                                        'ditolak' => 'text-danger',
                                        default => 'text-primary',
                                    };
                                @endphp
                                <i class="bx bx-circle {{ $timelineClass }}"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="text-capitalize">{{ str_replace('_', ' ', $tracking->aksi) }}</h6>
                                <p class="text-muted small mb-1">
                                    {{ $tracking->created_at->format('d F Y H:i') }}
                                </p>
                                @if ($tracking->keterangan)
                                    <p class="mb-0">{{ $tracking->keterangan }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">Belum ada riwayat status.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <!-- /Card -->
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusForm = document.querySelector('#status-form');
            if (statusForm) {
                statusForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Status pengajuan akan diperbarui.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, update!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            }
        });
    </script>
@endpush
