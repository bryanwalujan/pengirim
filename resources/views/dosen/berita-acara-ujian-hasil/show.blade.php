@extends('layouts.admin.app')

@section('title', 'Detail Berita Acara Ujian Hasil')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-9">
            <h4 class="fw-bold py-3 mb-4">
                <span class="text-muted fw-light">Dosen / Berita Acara /</span> Detail
            </h4>
        </div>
        <div class="col-md-3 text-end">
            <a href="{{ route('dosen.berita-acara-ujian-hasil.index') }}" class="btn btn-secondary mt-3">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Info Mahasiswa & Jadwal -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title text-primary"><i class="bx bx-user me-2"></i>Informasi Mahasiswa</h5>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td width="30%">Nama</td>
                                    <td>: <strong>{{ $beritaAcara->mahasiswa_name }}</strong></td>
                                </tr>
                                <tr>
                                    <td>NIM</td>
                                    <td>: {{ $beritaAcara->mahasiswa_nim }}</td>
                                </tr>
                                <tr>
                                    <td>Judul Skripsi</td>
                                    <td>: {{ $beritaAcara->judul_skripsi }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="card-title text-primary"><i class="bx bx-calendar me-2"></i>Informasi Ujian</h5>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td width="30%">Tanggal</td>
                                    <td>: {{ $beritaAcara->jadwalUjianHasil->tanggal_ujian?->translatedFormat('l, d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td>Waktu</td>
                                    <td>: {{ $beritaAcara->jadwalUjianHasil->jam_mulai }} - {{ $beritaAcara->jadwalUjianHasil->jam_selesai }}</td>
                                </tr>
                                <tr>
                                    <td>Ruangan</td>
                                    <td>: {{ $beritaAcara->ruangan ?? $beritaAcara->jadwalUjianHasil->ruangan }}</td>
                                </tr>
                                <tr>
                                    <td>Posisi Anda</td>
                                    <td>: <span class="badge bg-label-info">{{ $pengujiInfo->pivot->posisi ?? 'Penguji' }}</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Cards -->
    <div class="row mb-4">
        <!-- Card Penilaian -->
        <div class="col-md-6">
            <div class="card h-100 border-start border-4 {{ $myPenilaian ? 'border-success' : 'border-warning' }}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">1. Penilaian Ujian</h5>
                    @if($myPenilaian)
                        <span class="badge bg-success">Selesai</span>
                    @else
                        <span class="badge bg-warning">Belum Disi</span>
                    @endif
                </div>
                <div class="card-body">
                    <p class="card-text">
                        Anda wajib memberikan penilaian terhadap 5 kriteria: Kebaruan, Metode, Data/Software, Referensi, dan Penguasaan Materi.
                    </p>
                    
                    @if($myPenilaian)
                        <div class="alert alert-success d-flex align-items-center">
                            <i class="bx bx-check-circle me-2"></i>
                            <div>
                                <strong>Total Nilai: {{ $myPenilaian->total_nilai }} ({{ $myPenilaian->grade_letter }})</strong>
                            </div>
                        </div>
                    @endif

                    <div class="d-grid gap-2">
                        @if($beritaAcara->isMenungguTtdPenguji())
                            <a href="{{ route('dosen.berita-acara-ujian-hasil.penilaian', $beritaAcara) }}" 
                               class="btn {{ $myPenilaian ? 'btn-outline-primary' : 'btn-primary' }}">
                                <i class="bx bx-edit me-1"></i> {{ $myPenilaian ? 'Edit Penilaian' : 'Isi Penilaian' }}
                            </a>
                        @else
                            <button class="btn btn-secondary" disabled>Masa Penilaian Berakhir</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Lembar Koreksi (Khusus PS1 & PS2) -->
        @if($beritaAcara->isPembimbing(Auth::id()))
            <div class="col-md-6">
                <div class="card h-100 border-start border-4 {{ $myKoreksi ? 'border-success' : 'border-warning' }}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">2. Lembar Koreksi Skripsi</h5>
                        @if($myKoreksi)
                            <span class="badge bg-success">Selesai</span>
                        @else
                            <span class="badge bg-warning">Belum Disi</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            Sebagai Dosen Pembimbing, Anda juga wajib mengisi Lembar Koreksi Skripsi (Daftar perbaikan).
                        </p>

                        @if($myKoreksi)
                            <div class="alert alert-success d-flex align-items-center">
                                <i class="bx bx-check-circle me-2"></i>
                                <div>
                                    <strong>{{ $myKoreksi->total_koreksi }} item koreksi dicatat.</strong>
                                </div>
                            </div>
                        @endif

                        <div class="d-grid gap-2">
                            @if($beritaAcara->isMenungguTtdPenguji())
                                <a href="{{ route('dosen.berita-acara-ujian-hasil.koreksi', $beritaAcara) }}" 
                                   class="btn {{ $myKoreksi ? 'btn-outline-primary' : 'btn-primary' }}">
                                    <i class="bx bx-edit me-1"></i> {{ $myKoreksi ? 'Edit Lembar Koreksi' : 'Isi Lembar Koreksi' }}
                                </a>
                            @else
                                <button class="btn btn-secondary" disabled>Masa Koreksi Berakhir</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Placeholder for Non-Pembimbing -->
            <div class="col-md-6">
                <div class="card h-100 bg-light">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center text-muted">
                        <i class="bx bx-block display-4 mb-3"></i>
                        <h5>Lembar Koreksi</h5>
                        <p>Hanya wajib diisi oleh Dosen Pembimbing (PS1 & PS2).</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Status Tanda Tangan -->
    <div class="card mb-4">
        <h5 class="card-header">3. Status Tanda Tangan</h5>
        <div class="card-body">
            @if($beritaAcara->hasSignedByPenguji(Auth::id()))
                <div class="alert alert-success">
                    <i class="bx bx-check-double me-2"></i> Anda sudah menandatangani Berita Acara ini.
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="bx bx-time me-2"></i> Anda belum menandatangani Berita Acara ini.
                </div>
                
                @php
                    $canSign = $myPenilaian && (!$beritaAcara->isPembimbing(Auth::id()) || $myKoreksi);
                @endphp

                @if($canSign && $beritaAcara->isMenungguTtdPenguji())
                    <div class="text-end">
                        <form action="{{ route('admin.berita-acara-ujian-hasil.sign-penguji', $beritaAcara) }}" method="POST">
                            @csrf
                            <input type="hidden" name="passcode" value="123456"> <!-- Simplification for now, should prompt or be handled by modal -->
                            <button type="button" class="btn btn-success btn-lg" onclick="confirmSign(this.form)">
                                <i class="bx bx-pen me-2"></i> Tanda Tangani Berita Acara
                            </button>
                        </form>
                    </div>
                @elseif(!$canSign)
                    <div class="alert alert-secondary mb-0">
                        <i class="bx bx-info-circle me-1"></i> Mohon lengkapi 
                        <strong>Penilaian</strong> 
                        @if($beritaAcara->isPembimbing(Auth::id()))
                            dan <strong>Lembar Koreksi</strong>
                        @endif
                        terlebih dahulu sebelum menandatangani.
                    </div>
                @endif
            @endif

            <hr>
            
            <h6>Progress Penguji Lain:</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Nama Penguji</th>
                            <th>Posisi</th>
                            <th class="text-center">Penilaian</th>
                            <th class="text-center">Status TTD</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($beritaAcara->jadwalUjianHasil->dosenPenguji as $dosen)
                            @if($dosen->pivot->posisi !== 'Ketua Penguji')
                                <tr>
                                    <td>{{ $dosen->name }}</td>
                                    <td>{{ $dosen->pivot->posisi }}</td>
                                    <td class="text-center">
                                        @if($beritaAcara->hasPenilaianFrom($dosen->id))
                                            <span class="badge bg-success">Selesai</span>
                                        @else
                                            <span class="badge bg-secondary">Belum</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($beritaAcara->hasSignedByPenguji($dosen->id))
                                            <span class="badge bg-success">Sudah TTD</span>
                                        @else
                                            <span class="badge bg-secondary">Belum TTD</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Passcode Modal for Signing --}}
<div class="modal fade" id="signModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.berita-acara-ujian-hasil.sign-penguji', $beritaAcara) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Tanda Tangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        Anda akan menandatangani Berita Acara Ujian Hasil untuk mahasiswa <strong>{{ $beritaAcara->mahasiswa_name }}</strong>.
                        Pastikan nilai dan koreksi (jika ada) sudah benar.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Masukkan Passcode Anda</label>
                        <input type="password" name="passcode" class="form-control" required placeholder="Passcode...">
                        <div class="form-text">Default passcode: 123456</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Proses Tanda Tangan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function confirmSign(form) {
        var myModal = new bootstrap.Modal(document.getElementById('signModal'), {});
        myModal.show();
    }
</script>
@endpush
