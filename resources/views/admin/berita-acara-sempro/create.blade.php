{{-- filepath: resources/views/admin/berita-acara-sempro/create.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Buat Berita Acara - Draft')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @php
            $pendaftaran = $jadwal->pendaftaranSeminarProposal;
            $mahasiswa = $pendaftaran->user;
            $pembimbing = $pendaftaran->dosenPembimbing;
        @endphp

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <span class="text-muted fw-light">Berita Acara /</span> Buat Draft
                </h4>
                <p class="text-muted mb-0">
                    <i class="bx bx-info-circle me-1"></i>
                    Draft akan diisi oleh Dosen Pembimbing
                </p>
            </div>
            <a href="{{ route('admin.jadwal-seminar-proposal.show', $jadwal) }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>

        {{-- Alert Info --}}
        <div class="alert alert-info alert-dismissible mb-4" role="alert">
            <h6 class="alert-heading mb-1">
                <i class="bx bx-info-circle me-2"></i>Informasi Workflow Berita Acara
            </h6>
            <div class="small">
                <ol class="mb-0 ps-3">
                    <li><strong>Staff</strong> membuat draft berita acara (bisa kapan saja setelah jadwal dibuat)</li>
                    <li><strong>Dosen Pembahas</strong> memberikan persetujuan (tidak terikat waktu ujian)</li>
                    <li><strong>Dosen Pembimbing</strong> mengisi catatan kejadian & kesimpulan kelayakan</li>
                    <li><strong>Ketua Penguji</strong> preview dan menandatangani berita acara</li>
                    <li>PDF final ter-generate dan status jadwal menjadi "Selesai"</li>
                </ol>
                <div class="mt-2 text-muted">
                    <i class="bx bx-calendar me-1"></i>
                    Jadwal ujian: <strong>{{ $jadwal->tanggal_ujian->translatedFormat('l, d F Y') }}</strong>
                    ({{ $jadwal->jam_formatted }})
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        {{-- Info Mahasiswa --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bx bx-user me-2"></i>Informasi Mahasiswa & Ujian
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted small">Mahasiswa</label>
                        <div>{{ $mahasiswa->name }}</div>
                        <small class="text-muted">NIM: {{ $mahasiswa->nim }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted small">Dosen Pembimbing</label>
                        <div>{{ $pembimbing->name }}</div>
                        <small class="text-muted">NIP: {{ $pembimbing->nip }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted small">Tanggal Ujian</label>
                        <div>{{ $jadwal->tanggal_ujian->isoFormat('dddd, D MMMM Y') }}</div>
                        <small class="text-muted">{{ $jadwal->waktu_mulai }} - {{ $jadwal->waktu_selesai }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted small">Ruangan</label>
                        <div>{{ $jadwal->ruangan }}</div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold text-muted small">Judul Proposal</label>
                        <div class="text-wrap">{{ $pendaftaran->judul_skripsi }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar Penguji --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bx bx-group me-2"></i>Pembimbing & Pembahas Ujian Seminar Proposal
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Dosen</th>
                                <th>Posisi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // ✅ Sort dosen: Ketua Pembahas pertama, kemudian Anggota Pembahas berurutan
                                $sortedDosen = $jadwal->dosenPenguji->sortBy(function($dosen) {
                                    if ($dosen->pivot->posisi === 'Ketua Pembahas') {
                                        return 0; // Ketua Pembahas di urutan pertama
                                    }
                                    // Extract angka dari "Anggota Pembahas 1", "Anggota Pembahas 2", dst
                                    preg_match('/\d+/', $dosen->pivot->posisi, $matches);
                                    return isset($matches[0]) ? (int)$matches[0] : 999;
                                });
                            @endphp
                            @foreach ($sortedDosen as $index => $dosen)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <div>{{ $dosen->name }}</div>
                                        <small class="text-muted">NIP: {{ $dosen->nip ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span
                                            class="badge {{ $dosen->pivot->posisi === 'Ketua Pembahas' ? 'bg-label-success' : 'bg-label-primary' }}">
                                            @if ($dosen->pivot->posisi === 'Ketua Pembahas')
                                                <i class="bx bx-star me-1"></i>
                                            @else
                                                <i class="bx bx-user me-1"></i>
                                            @endif
                                            {{ $dosen->pivot->posisi }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Form Create Draft --}}
        <form action="{{ route('admin.berita-acara-sempro.store', $jadwal) }}" method="POST">
            @csrf

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-note me-2"></i>Catatan Tambahan (Opsional)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <i class="bx bx-info-circle me-2"></i>
                        <strong>Catatan Kejadian</strong> dan <strong>Kesimpulan Kelayakan</strong>
                        akan diisi oleh <strong>{{ $pembimbing->name }}</strong> (Dosen Pembimbing)
                    </div>

                    <div class="mb-3">
                        <label for="catatan_tambahan" class="form-label">
                            Catatan Tambahan
                            <small class="text-muted">(Opsional)</small>
                        </label>
                        <textarea name="catatan_tambahan" id="catatan_tambahan"
                            class="form-control @error('catatan_tambahan') is-invalid @enderror" rows="4"
                            placeholder="Catatan tambahan jika diperlukan (misal: informasi teknis, kondisi khusus, dll)">{{ old('catatan_tambahan') }}</textarea>
                        @error('catatan_tambahan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Maksimal 1000 karakter
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bx bx-info-circle text-primary me-2"></i>
                            <small class="text-muted">
                                Setelah draft dibuat, sistem akan menunggu Dosen Pembimbing untuk mengisi berita acara.
                            </small>
                        </div>
                        <div>
                            <a href="{{ route('admin.jadwal-seminar-proposal.show', $jadwal) }}"
                                class="btn btn-outline-secondary me-2">
                                <i class="bx bx-x me-1"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>Buat Draft Berita Acara
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Character counter for catatan_tambahan
        const textarea = document.getElementById('catatan_tambahan');
        if (textarea) {
            textarea.addEventListener('input', function() {
                const maxLength = 1000;
                const currentLength = this.value.length;

                if (currentLength > maxLength) {
                    this.value = this.value.substring(0, maxLength);
                }
            });
        }
    </script>
@endpush
