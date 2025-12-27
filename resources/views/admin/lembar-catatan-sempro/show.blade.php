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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <span class="text-muted fw-light">Lembar Catatan /</span> Detail
                </h4>
                <p class="text-muted mb-0">{{ $mahasiswa->name }} ({{ $mahasiswa->nim }})</p>
            </div>
            <div class="btn-group">
                @if ($dosen->id === Auth::id() && !$beritaAcara->isSigned())
                    <a href="{{ route('admin.lembar-catatan-sempro.edit', $lembarCatatan) }}" class="btn btn-primary">
                        <i class="bx bx-edit me-1"></i> Edit
                    </a>
                @endif
                <a href="{{ route('admin.lembar-catatan-sempro.download-pdf', $lembarCatatan) }}"
                    class="btn btn-outline-primary">
                    <i class="bx bx-download me-1"></i> Download PDF
                </a>
                <a href="{{ route('admin.berita-acara-sempro.show', $beritaAcara) }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="row">
            {{-- Info & Nilai --}}
            <div class="col-md-4">
                {{-- Info Dosen --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Penguji</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="avatar avatar-xl mb-2">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    {{ strtoupper(substr($dosen->name, 0, 2)) }}
                                </span>
                            </div>
                            <h5 class="mb-0">{{ $dosen->name }}</h5>
                            <small class="text-muted">NIP: {{ $dosen->nip ?? '-' }}</small>
                        </div>
                        <hr>
                        <small class="text-muted">Diisi pada:</small>
                        <p class="mb-0">{{ $lembarCatatan->created_at->translatedFormat('d F Y H:i') }}</p>
                        @if ($lembarCatatan->updated_at != $lembarCatatan->created_at)
                            <small class="text-muted">Terakhir diupdate:</small>
                            <p class="mb-0">{{ $lembarCatatan->updated_at->translatedFormat('d F Y H:i') }}</p>
                        @endif
                    </div>
                </div>

                {{-- Penilaian --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Penilaian Aspek</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $getNilaiClass = function ($nilai) {
                                if ($nilai >= 80) {
                                    return 'success';
                                }
                                if ($nilai >= 60) {
                                    return 'warning';
                                }
                                return 'danger';
                            };
                        @endphp

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">Kebaruan Penelitian</small>
                                <strong
                                    class="text-{{ $lembarCatatan->nilai_kebaruan ? $getNilaiClass($lembarCatatan->nilai_kebaruan) : 'muted' }}">
                                    {{ $lembarCatatan->nilai_kebaruan ?? '-' }}
                                </strong>
                            </div>
                            @if ($lembarCatatan->nilai_kebaruan)
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-{{ $getNilaiClass($lembarCatatan->nilai_kebaruan) }}"
                                        style="width: {{ $lembarCatatan->nilai_kebaruan }}%"></div>
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">Metode Penelitian</small>
                                <strong
                                    class="text-{{ $lembarCatatan->nilai_metode ? $getNilaiClass($lembarCatatan->nilai_metode) : 'muted' }}">
                                    {{ $lembarCatatan->nilai_metode ?? '-' }}
                                </strong>
                            </div>
                            @if ($lembarCatatan->nilai_metode)
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-{{ $getNilaiClass($lembarCatatan->nilai_metode) }}"
                                        style="width: {{ $lembarCatatan->nilai_metode }}%"></div>
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">Ketersediaan Data</small>
                                <strong
                                    class="text-{{ $lembarCatatan->nilai_ketersediaan_data ? $getNilaiClass($lembarCatatan->nilai_ketersediaan_data) : 'muted' }}">
                                    {{ $lembarCatatan->nilai_ketersediaan_data ?? '-' }}
                                </strong>
                            </div>
                            @if ($lembarCatatan->nilai_ketersediaan_data)
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-{{ $getNilaiClass($lembarCatatan->nilai_ketersediaan_data) }}"
                                        style="width: {{ $lembarCatatan->nilai_ketersediaan_data }}%"></div>
                                </div>
                            @endif
                        </div>

                        <hr>

                        <div class="text-center">
                            <small class="text-muted d-block">Rata-rata</small>
                            <h3
                                class="mb-0 text-{{ $lembarCatatan->total_nilai ? $getNilaiClass($lembarCatatan->total_nilai) : 'muted' }}">
                                {{ $lembarCatatan->total_nilai ?? '-' }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Catatan --}}
            <div class="col-md-8">
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
