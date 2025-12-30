{{-- filepath: resources/views/admin/lembar-catatan-sempro/show.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Detail Lembar Catatan')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @php
            $beritaAcara = $lembarCatatan->beritaAcara;
            $jadwal = $beritaAcara->jadwalSeminarProposal;
            $pendaftaran = $jadwal->pendaftaranSeminarProposal;
            $mahasiswa = $pendaftaran->user;
            $dosen = $lembarCatatan->dosen;
        @endphp

        {{-- Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1">
                                    <i class="bx bx-file-blank me-2"></i>Detail Lembar Catatan Penguji
                                </h4>
                                <p class="text-muted mb-0">
                                    <i class="bx bx-user me-1"></i>{{ $mahasiswa->name }} ({{ $mahasiswa->nim }})
                                </p>
                            </div>
                            <div class="btn-group">
                                @if ($dosen->id === Auth::id() && !$beritaAcara->isSigned())
                                    <a href="{{ route('admin.lembar-catatan-sempro.edit', $lembarCatatan) }}" class="btn btn-warning">
                                        <i class="bx bx-edit me-1"></i> Edit
                                    </a>
                                @endif
                                <a href="{{ route('admin.berita-acara-sempro.show', $beritaAcara) }}" class="btn btn-primary">
                                    <i class="bx bx-arrow-back me-1"></i> Kembali ke Berita Acara
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Catatan --}}
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Catatan Revisi</h5>
                    </div>
                    <div class="card-body">
                        @forelse($lembarCatatan->formatted_catatan as $judul => $isi)
                            @if ($isi)
                                <div class="mb-4">
                                    <h6 class="text-primary">
                                        <i class="bx bx-book-bookmark me-1"></i>
                                        {{ $judul }}
                                    </h6>
                                    <div class="alert alert-secondary mb-0">
                                        {!! nl2br(e($isi)) !!}
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle me-2"></i>
                                Tidak ada catatan revisi yang diisi.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
