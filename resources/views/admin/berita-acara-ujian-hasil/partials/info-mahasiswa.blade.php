{{-- Student and Exam Information Card --}}
@props(['mahasiswa', 'jadwal', 'pendaftaran'])

<div class="card mb-4 shadow-sm border-0">
    <div class="card-header border-bottom p-4">
        <h5 class="mb-0 fw-bold"><i class="bx bx-info-circle me-2 text-warning"></i>Informasi Mahasiswa & Ujian</h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="p-3 rounded-3 border bg-light">
                    <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Data Mahasiswa</label>
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-warning">
                                <i class="bx bx-user"></i>
                            </span>
                        </div>
                        <div>
                            <div class="fw-bold fs-6">{{ $mahasiswa->name }}</div>
                            <div class="text-muted small">{{ $mahasiswa->nim }} • {{ $beritaAcara->mahasiswa_prodi ?? 'Teknik Informatika' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 rounded-3 border bg-light h-100">
                    <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Waktu & Tempat</label>
                    @if ($jadwal && $jadwal->tanggal_ujian)
                        <div class="d-flex align-items-start gap-3">
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-warning">
                                    <i class="bx bx-calendar"></i>
                                </span>
                            </div>
                            <div>
                                <div class="fw-bold small">
                                    {{ \Carbon\Carbon::parse($jadwal->tanggal_ujian)->isoFormat('dddd, D MMMM Y') }}
                                </div>
                                <div class="text-muted small mb-1">
                                    {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }} WITA
                                </div>
                                <span class="badge bg-label-warning rounded-pill">
                                    <i class="bx bx-buildings me-1"></i>
                                    {{ $beritaAcara->ruangan ?? ($jadwal?->ruangan ?? '-') }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="text-muted italic py-2 small">Informasi jadwal tidak tersedia</div>
                    @endif
                </div>
            </div>
            <div class="col-12">
                <div class="p-4 rounded-3 border bg-light">
                    <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Judul Skripsi</label>
                    <h6 class="fw-bold mb-0 leading-relaxed">
                        {{ $beritaAcara->judul_skripsi ?? ($pendaftaran?->judul_skripsi ?? '-') }}
                    </h6>
                </div>
            </div>
        </div>
    </div>
</div>
