@extends('layouts.admin.app')

@section('title', 'Detail Pendaftaran Seminar Proposal')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Detail Pendaftaran</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Nama Mahasiswa</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">: {{ $pendaftaran->user->name }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">NIM</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">: {{ $pendaftaran->user->nim }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Angkatan</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">: {{ $pendaftaran->angkatan }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">IPK</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">: {{ $pendaftaran->ipk }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-4 col-form-label">Dosen Pembimbing</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">: {{ $pendaftaran->dosenPembimbing->name ?? 'Belum Dipilih' }}
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Judul Skripsi</label>
                        <div class="col-sm-8">
                            <p class="form-control-plaintext">: {{ $pendaftaran->judul_skripsi }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Dokumen Terlampir</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ Storage::url($pendaftaran->file_transkrip_nilai) }}" target="_blank"
                            class="list-group-item list-group-item-action">
                            <i class="bx bx-file me-2"></i>Lihat Transkrip Nilai
                        </a>
                        <a href="{{ Storage::url($pendaftaran->file_proposal_penelitian) }}" target="_blank"
                            class="list-group-item list-group-item-action">
                            <i class="bx bx-file me-2"></i>Lihat Proposal Penelitian
                        </a>
                        <a href="{{ Storage::url($pendaftaran->file_surat_permohonan) }}" target="_blank"
                            class="list-group-item list-group-item-action">
                            <i class="bx bx-file me-2"></i>Lihat Surat Permohonan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-3">
        <a href="{{ route('admin.pendaftaran-seminar-proposal.index') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar
        </a>
    </div>
@endsection
