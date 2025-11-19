@extends('layouts.admin.app')

@section('title', 'Detail Komisi Proposal')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">
                        <i class="bx bx-home"></i>
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.komisi-proposal.index') }}">Komisi Proposal</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Detail Pengajuan</li>
            </ol>
        </nav>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bx bx-check-circle me-1"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bx bx-error-circle me-1"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            {{-- Kolom Kiri: Detail Pengajuan --}}
            <div class="col-lg-7 col-md-12 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-file me-2"></i>Detail Pengajuan
                        </h5>
                        <a href="{{ route('admin.komisi-proposal.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th width="200px">Nama Mahasiswa</th>
                                    <td>: <strong>{{ $komisiProposal->user->name }}</strong></td>
                                </tr>
                                <tr>
                                    <th>NIM</th>
                                    <td>: {{ $komisiProposal->user->nim }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Pengajuan</th>
                                    <td>: {{ $komisiProposal->created_at->translatedFormat('l, d F Y') }}</td>
                                </tr>
                                <tr>
                                    <th style="vertical-align: top">Judul Skripsi</th>
                                    <td style="vertical-align: top">: {{ $komisiProposal->judul_skripsi }}</td>
                                </tr>
                                <tr>
                                    <th>Dosen Pembimbing</th>
                                    <td>: {{ $komisiProposal->pembimbing->name }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>: {!! $komisiProposal->status_badge !!}</td>
                                </tr>

                                @if ($komisiProposal->penandatangan_pa_id)
                                    <tr>
                                        <th>Disetujui PA oleh</th>
                                        <td>: {{ $komisiProposal->penandatanganPA->name ?? '-' }}</td>
                                    </tr>
                                @endif

                                @if ($komisiProposal->penandatangan_korprodi_id)
                                    <tr>
                                        <th>Disetujui Korprodi oleh</th>
                                        <td>: {{ $komisiProposal->penandatanganKorprodi->name ?? '-' }}</td>
                                    </tr>
                                @endif

                                @if ($komisiProposal->keterangan)
                                    <tr>
                                        <th style="vertical-align: top">Keterangan</th>
                                        <td style="vertical-align: top">
                                            <div class="alert alert-warning mb-0">
                                                <i class="bx bx-info-circle me-1"></i>
                                                {{ $komisiProposal->keterangan }}
                                            </div>
                                        </td>
                                    </tr>
                                @endif

                                @if ($komisiProposal->file_komisi)
                                    <tr>
                                        <th>File Komisi</th>
                                        <td>
                                            : <a href="{{ route('admin.komisi-proposal.download', $komisiProposal->id) }}"
                                                class="btn btn-sm btn-primary" target="_blank">
                                                <i class="bx bx-download me-1"></i>Download PDF
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Aksi Approval --}}
            <div class="col-lg-5 col-md-12 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-check-shield me-2"></i>Tindakan Persetujuan
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $user = auth()->user();
                            $canApprovePA =
                                $komisiProposal->canBeApprovedByPA() &&
                                $user->hasRole('dosen') &&
                                $user->jabatan === 'Pembimbing Akademik';
                            $canApproveKorprodi =
                                $komisiProposal->canBeApprovedByKorprodi() &&
                                $user->hasRole('dosen') &&
                                $user->jabatan === 'Koordinator Prodi';
                        @endphp

                        {{-- Approval PA --}}
                        @if ($canApprovePA)
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-1"></i>
                                <strong>Menunggu Persetujuan PA</strong><br>
                                <small>Komisi proposal ini memerlukan persetujuan Anda sebagai Pembimbing Akademik.</small>
                            </div>

                            <form action="{{ route('admin.komisi-proposal.approve-pa', $komisiProposal->id) }}"
                                method="POST" id="approvePAForm">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 mb-2">
                                    <i class="bx bx-check-circle me-1"></i>Setujui sebagai PA
                                </button>
                            </form>

                            <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal"
                                data-bs-target="#rejectPAModal">
                                <i class="bx bx-x-circle me-1"></i>Tolak sebagai PA
                            </button>

                            {{-- Approval Korprodi --}}
                        @elseif ($canApproveKorprodi)
                            <div class="alert alert-warning">
                                <i class="bx bx-info-circle me-1"></i>
                                <strong>Menunggu Persetujuan Korprodi</strong><br>
                                <small>Komisi proposal ini memerlukan persetujuan Anda sebagai Koordinator Prodi.</small>
                            </div>

                            <form action="{{ route('admin.komisi-proposal.approve-korprodi', $komisiProposal->id) }}"
                                method="POST" id="approveKorprodiForm">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 mb-2">
                                    <i class="bx bx-check-circle me-1"></i>Setujui sebagai Korprodi
                                </button>
                            </form>

                            <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal"
                                data-bs-target="#rejectKorprodiModal">
                                <i class="bx bx-x-circle me-1"></i>Tolak sebagai Korprodi
                            </button>

                            {{-- Status Informasi --}}
                        @else
                            <div class="alert alert-secondary">
                                <i class="bx bx-info-circle me-1"></i>
                                @if ($komisiProposal->status === 'pending')
                                    <strong>Status: Menunggu Persetujuan</strong><br>
                                    <small>Komisi proposal sedang menunggu persetujuan dari PA/Korprodi.</small>
                                @elseif ($komisiProposal->status === 'disetujui_pa')
                                    <strong>Status: Disetujui PA</strong><br>
                                    <small>Menunggu persetujuan Koordinator Prodi.</small>
                                @elseif ($komisiProposal->status === 'approved')
                                    <strong>Status: Disetujui</strong><br>
                                    <small>Komisi proposal telah disetujui lengkap.</small>
                                @else
                                    <strong>Status: {{ ucfirst($komisiProposal->status) }}</strong>
                                @endif
                            </div>
                        @endif

                        {{-- Download PDF jika sudah approved --}}
                        @if ($komisiProposal->status === 'approved' && $komisiProposal->file_komisi)
                            <hr class="my-3">
                            <a href="{{ route('admin.komisi-proposal.download', $komisiProposal->id) }}"
                                class="btn btn-primary w-100" target="_blank">
                                <i class="bx bx-download me-1"></i>Download PDF Komisi
                            </a>
                        @endif

                        {{-- Delete Button (hanya untuk admin) --}}
                        @if ($user->hasRole('admin') && $komisiProposal->canBeDeleted())
                            <hr class="my-3">
                            <form action="{{ route('admin.komisi-proposal.destroy', $komisiProposal->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus komisi proposal ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="bx bx-trash me-1"></i>Hapus Komisi Proposal
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Reject PA --}}
    <div class="modal fade" id="rejectPAModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Komisi Proposal (PA)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.komisi-proposal.approve-pa', $komisiProposal->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="reject" value="1">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="keterangan" rows="4" required placeholder="Jelaskan alasan penolakan..."></textarea>
                            <small class="text-muted">Alasan penolakan wajib diisi.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bx bx-x-circle me-1"></i>Tolak Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Reject Korprodi --}}
    <div class="modal fade" id="rejectKorprodiModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Komisi Proposal (Korprodi)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.komisi-proposal.approve-korprodi', $komisiProposal->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="reject" value="1">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="keterangan" rows="4" required placeholder="Jelaskan alasan penolakan..."></textarea>
                            <small class="text-muted">Alasan penolakan wajib diisi.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bx bx-x-circle me-1"></i>Tolak Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Auto-hide alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Confirmation for approve
        $('#approvePAForm, #approveKorprodiForm').on('submit', function(e) {
            return confirm('Apakah Anda yakin ingin menyetujui komisi proposal ini?');
        });
    </script>
@endpush
