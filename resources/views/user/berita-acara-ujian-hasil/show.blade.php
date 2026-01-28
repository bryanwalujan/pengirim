@extends('layouts.user.app')

@section('title', 'Detail Hasil Ujian')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <a href="{{ route('user.berita-acara-ujian-hasil.index') }}" class="btn btn-label-secondary">
            <i class="bx bx-arrow-back me-1"></i>Kembali
        </a>
    </div>

    <!-- Header & Summary -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="card-title text-primary mb-1">{{ $beritaAcara->judul_skripsi }}</h4>
                        <p class="text-muted mb-3">{{ $beritaAcara->mahasiswa_name }} ({{ $beritaAcara->mahasiswa_nim }})</p>
                        
                        <div class="d-flex gap-2">
                            {!! $beritaAcara->status_badge !!}
                            <span class="badge bg-label-info">
                                <i class="bx bx-calendar me-1"></i> 
                                {{ $beritaAcara->jadwalUjianHasil->tanggal_ujian?->translatedFormat('d F Y') }}
                            </span>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="display-5 fw-bold text-primary">{{ number_format($penilaianSummary['average_nilai'], 2) }}</div>
                        <span class="text-muted">Rata-rata Nilai Akhir</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Penilaian -->
        <div class="card mb-4">
            <h5 class="card-header"><i class="bx bx-bar-chart-alt-2 me-2"></i>Detail Penilaian Penguji</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nama Penguji</th>
                            <th>Posisi</th>
                            <th class="text-center">Nilai</th>
                            <th class="text-center">Grade</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penilaianSummary['details'] as $detail)
                            <tr>
                                <td>{{ $detail['dosen_name'] }}</td>
                                <td><span class="badge bg-label-secondary">{{ $detail['posisi'] }}</span></td>
                                <td class="text-center fw-bold">{{ $detail['total_nilai'] }}</td>
                                <td class="text-center"><span class="badge bg-primary">{{ $detail['grade'] }}</span></td>
                                <td><small>{{ Str::limit($detail['catatan'], 50) ?: '-' }}</small></td>
                            </tr>
                        @endforeach
                        
                        @if(empty($penilaianSummary['details']))
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">Belum ada penilaian yang masuk.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar / Lembar Koreksi -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <h5 class="card-header bg-warning text-white"><i class="bx bx-edit me-2"></i>Lembar Koreksi</h5>
            <div class="card-body mt-3">
                <p class="small text-muted">Daftar perbaikan yang harus dilakukan berdasarkan masukan dari Pembimbing Skripsi.</p>
                
                @forelse($lembarKoreksis as $koreksi)
                    <div class="accordion mb-3" id="accordionKoreksi{{ $koreksi->id }}">
                        <div class="accordion-item shadow-none border">
                            <h2 class="accordion-header" id="heading{{ $koreksi->id }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#collapse{{ $koreksi->id }}">
                                    <div class="d-flex flex-column text-start">
                                        <span class="fw-bold">{{ $koreksi->dosen->name }}</span>
                                        <small class="text-muted">
                                            {{ $koreksi->total_koreksi }} item perbaikan
                                        </small>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse{{ $koreksi->id }}" class="accordion-collapse collapse" 
                                 data-bs-parent="#accordionKoreksi{{ $koreksi->id }}">
                                <div class="accordion-body p-0">
                                    <ul class="list-group list-group-flush">
                                        @foreach($koreksi->koreksi_collection as $item)
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between">
                                                    <strong>Hal. {{ $item['halaman'] }}</strong>
                                                    {{-- <span class="badge bg-label-secondary">#{{ $item['no'] }}</span> --}}
                                                </div>
                                                <p class="mb-0 mt-1 small text-justify">{{ $item['catatan'] }}</p>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-secondary">
                        Belum ada lembar koreksi yang diisi oleh pembimbing.
                    </div>
                @endforelse
            </div>
        </div>

        @if($beritaAcara->status === 'selesai' && $beritaAcara->file_path)
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Dokumen Berita Acara</h5>
                    <p class="card-text">Unduh dokumen berita acara yang telah ditandatangani.</p>
                    <a href="{{ route('admin.berita-acara-ujian-hasil.download-pdf', $beritaAcara) }}" class="btn btn-primary w-100">
                        <i class="bx bxs-file-pdf me-1"></i> Download PDF
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
