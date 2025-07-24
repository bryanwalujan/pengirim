{{-- /resources/views/admin/komisi-proposal/show.blade.php --}}

@extends('layouts.admin.app')

@section('title', 'Admin | Detail Komisi Proposal')

@section('content')
    <div class="row">
        {{-- Kolom Kiri: Detail Pengajuan --}}
        <div class="col-lg-7 col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Detail Pengajuan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th width="200px">Nama Mahasiswa</th>
                                <td>: {{ $komisiProposal->user->name }}</td>
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
                                <th>Pembimbing 1</th>
                                <td>: {{ $komisiProposal->pembimbing->name }}</td>
                            </tr>
                            <tr>
                                <th>Status Saat Ini</th>
                                <td>:
                                    @if ($komisiProposal->status == 'pending')
                                        <span class="badge bg-label-warning">Pending</span>
                                    @elseif($komisiProposal->status == 'approved')
                                        <span class="badge bg-label-success">Approved</span>
                                    @else
                                        <span class="badge bg-label-danger">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                            @if ($komisiProposal->keterangan)
                                <tr>
                                    <th style="vertical-align: top">Keterangan (Alasan Ditolak)</th>
                                    <td style="vertical-align: top">: {{ $komisiProposal->keterangan }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Aksi --}}
        <div class="col-lg-5 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Tindakan</h5>
                </div>
                <div class="card-body">
                    {{-- Form Update Status --}}
                    <form action="{{ route('admin.komisi-proposal.update-status', $komisiProposal->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="status" class="form-label">Ubah Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="approved" {{ $komisiProposal->status == 'approved' ? 'selected' : '' }}>
                                    Approve</option>
                                <option value="rejected" {{ $komisiProposal->status == 'rejected' ? 'selected' : '' }}>
                                    Reject</option>
                                <option value="pending" {{ $komisiProposal->status == 'pending' ? 'selected' : '' }}>
                                    Pending</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan (Wajib diisi jika menolak)</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3">{{ $komisiProposal->keterangan }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-warning w-100 mb-3">Update Status</button>
                    </form>

                    {{-- Tombol Generate PDF --}}
                    @if ($komisiProposal->status == 'approved')
                        <a href="{{ route('admin.komisi-proposal.pdf', $komisiProposal->id) }}" target="_blank"
                            class="btn btn-success w-100">
                            <i class="bx bxs-file-pdf me-1"></i> Generate & Unduh PDF
                        </a>
                    @else
                        <button class="btn btn-secondary w-100" disabled>
                            <i class="bx bxs-file-pdf me-1"></i> Generate PDF
                        </button>
                        <small class="text-muted d-block mt-1 text-center">Tombol akan aktif jika status "Approved".</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
