@extends('layouts.admin.app')

@section('title', 'Detail Pendaftaran Ujian Hasil')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Detail Pendaftaran: {{ $pendaftaranUjianHasil->nama }} ({{ $pendaftaranUjianHasil->nim }})
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Angkatan:</strong> {{ $pendaftaranUjianHasil->angkatan }}</p>
                    <p><strong>IPK:</strong> {{ $pendaftaranUjianHasil->ipk }}</p>
                    <p><strong>Dosen P.A:</strong> {{ $pendaftaranUjianHasil->dosenPa->name }}</p>
                    <p><strong>Dosen Pembimbing 1:</strong> {{ $pendaftaranUjianHasil->dosenPembimbing1->name }}</p>
                    <p><strong>Dosen Pembimbing 2:</strong> {{ $pendaftaranUjianHasil->dosenPembimbing2->name }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Tanggal Pengajuan:</strong>
                        {{ $pendaftaranUjianHasil->created_at->translatedFormat('d F Y, H:i') }}</p>
                    <p><strong>Judul Skripsi:</strong></p>
                    <p>{{ $pendaftaranUjianHasil->judul_skripsi }}</p>
                </div>
            </div>
            <hr>
            <h5>Berkas Terlampir:</h5>
            <ul>
                <li><a href="{{ Storage::url($pendaftaranUjianHasil->transkrip_nilai) }}" target="_blank">Lihat Transkrip
                        Nilai</a></li>
                <li><a href="{{ Storage::url($pendaftaranUjianHasil->file_skripsi) }}" target="_blank">Lihat File
                        Skripsi</a></li>
                <li><a href="{{ Storage::url($pendaftaranUjianHasil->komisi_hasil) }}" target="_blank">Lihat Surat Komisi
                        Hasil</a></li>
                <li><a href="{{ Storage::url($pendaftaranUjianHasil->surat_permohonan_hasil) }}" target="_blank">Lihat
                        Surat Permohonan Hasil</a></li>
            </ul>
        </div>
        <div class="card-footer">
            <a href="{{ route('admin.pendaftaran-ujian-hasil.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
@endsection
