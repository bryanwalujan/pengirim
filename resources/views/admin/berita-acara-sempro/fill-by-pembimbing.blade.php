{{-- filepath: resources/views/admin/berita-acara-sempro/fill-by-pembimbing.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Isi Berita Acara - Dosen Pembimbing')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @php
            $jadwal = $beritaAcara->jadwalSeminarProposal;
            $pendaftaran = $jadwal->pendaftaranSeminarProposal;
            $mahasiswa = $pendaftaran->user;
        @endphp

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <span class="text-muted fw-light">Berita Acara /</span> Isi Berita Acara
                </h4>
                <p class="text-muted mb-0">
                    <i class="bx bx-edit me-1"></i>
                    Sebagai Dosen Pembimbing/Ketua Penguji
                </p>
            </div>
            <a href="{{ route('admin.berita-acara-sempro.show', $beritaAcara) }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>

        {{-- Alert Instruksi --}}
        <div class="alert alert-primary alert-dismissible mb-4" role="alert">
            <h6 class="alert-heading mb-1">
                <i class="bx bx-info-circle me-2"></i>Instruksi Pengisian
            </h6>
            <div class="small">
                Sebagai <strong>Dosen Pembimbing/Ketua Penguji</strong>, Anda diminta untuk:
                <ol class="mb-0 ps-3 mt-2">
                    <li>Menilai <strong>Catatan Kejadian</strong> selama seminar berlangsung</li>
                    <li>Memberikan <strong>Kesimpulan Kelayakan</strong> proposal mahasiswa</li>
                    <li>Menambahkan catatan tambahan jika diperlukan</li>
                </ol>
                <div class="mt-2 alert alert-success mb-0">
                    <i class="bx bx-check-double me-1"></i>
                    <small><strong>Perhatian:</strong> Setelah Anda submit, berita acara akan <strong>langsung
                            selesai</strong> dan PDF akan ter-generate otomatis.</small>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        {{-- Info Mahasiswa --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bx bx-user me-2"></i>Informasi Mahasiswa
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted small">Nama Mahasiswa</label>
                        <div>{{ $mahasiswa->name }}</div>
                        <small class="text-muted">NIM: {{ $mahasiswa->nim }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted small">Tanggal Ujian</label>
                        <div>{{ $jadwal->tanggal_ujian->isoFormat('dddd, D MMMM Y') }}</div>
                        <small class="text-muted">{{ $jadwal->waktu_mulai }} - {{ $jadwal->waktu_selesai }}</small>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold text-muted small">Judul Proposal</label>
                        <div class="text-wrap">{{ $pendaftaran->judul_skripsi }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Pengisian --}}
        <form action="{{ route('admin.berita-acara-sempro.store-fill-pembimbing', $beritaAcara) }}" method="POST"
            id="formFillBA">
            @csrf

            {{-- Catatan Kejadian --}}
            <div class="card mb-4">
                <div class="card-header bg-label-primary">
                    <h5 class="mb-0">
                        <i class="bx bx-clipboard me-2"></i>1. Catatan Kejadian Selama Seminar
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Pilih kondisi yang sesuai dengan jalannya seminar proposal:
                    </p>

                    <div class="form-check mb-3">
                        <input class="form-check-input @error('catatan_kejadian') is-invalid @enderror" type="radio"
                            name="catatan_kejadian" id="catatan_lancar" value="Lancar"
                            {{ old('catatan_kejadian') === 'Lancar' ? 'checked' : '' }}>
                        <label class="form-check-label" for="catatan_lancar">
                            <strong>Lancar</strong>
                            <div class="text-muted small">Seminar berjalan dengan baik tanpa kendala berarti</div>
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input @error('catatan_kejadian') is-invalid @enderror" type="radio"
                            name="catatan_kejadian" id="catatan_ada_perbaikan"
                            value="Ada beberapa perbaikan yang harus diubah"
                            {{ old('catatan_kejadian') === 'Ada beberapa perbaikan yang harus diubah' ? 'checked' : '' }}>
                        <label class="form-check-label" for="catatan_ada_perbaikan">
                            <strong>Ada beberapa perbaikan yang harus diubah</strong>
                            <div class="text-muted small">Terdapat beberapa poin yang perlu diperbaiki</div>
                        </label>
                    </div>

                    @error('catatan_kejadian')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Kesimpulan Kelayakan --}}
            <div class="card mb-4">
                <div class="card-header bg-label-success">
                    <h5 class="mb-0">
                        <i class="bx bx-check-shield me-2"></i>2. Kesimpulan Kelayakan Seminar Proposal Skripsi
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Berikan kesimpulan apakah proposal layak dilanjutkan ke tahap penelitian:
                    </p>

                    <div class="form-check mb-3">
                        <input class="form-check-input @error('keputusan') is-invalid @enderror" type="radio"
                            name="keputusan" id="keputusan_ya" value="Ya"
                            {{ old('keputusan') === 'Ya' ? 'checked' : '' }}>
                        <label class="form-check-label" for="keputusan_ya">
                            <strong class="text-success">Ya</strong>
                            <div class="text-muted small">Proposal layak dilanjutkan tanpa syarat</div>
                        </label>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input @error('keputusan') is-invalid @enderror" type="radio"
                            name="keputusan" id="keputusan_ya_perbaikan" value="Ya, dengan perbaikan"
                            {{ old('keputusan') === 'Ya, dengan perbaikan' ? 'checked' : '' }}>
                        <label class="form-check-label" for="keputusan_ya_perbaikan">
                            <strong class="text-warning">Ya, dengan perbaikan</strong>
                            <div class="text-muted small">Proposal layak dengan catatan harus ada perbaikan</div>
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input @error('keputusan') is-invalid @enderror" type="radio"
                            name="keputusan" id="keputusan_tidak" value="Tidak"
                            {{ old('keputusan') === 'Tidak' ? 'checked' : '' }}>
                        <label class="form-check-label" for="keputusan_tidak">
                            <strong class="text-danger">Tidak</strong>
                            <div class="text-muted small">Proposal belum layak dan perlu revisi besar</div>
                        </label>
                    </div>

                    @error('keputusan')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Catatan Tambahan --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-note me-2"></i>3. Catatan Tambahan (Opsional)
                    </h5>
                </div>
                <div class="card-body">
                    <textarea name="catatan_tambahan" id="catatan_tambahan"
                        class="form-control @error('catatan_tambahan') is-invalid @enderror" rows="5"
                        placeholder="Tambahkan catatan khusus jika diperlukan, misalnya: poin-poin perbaikan yang harus dilakukan, saran untuk mahasiswa, dll.">{{ old('catatan_tambahan', $beritaAcara->catatan_tambahan) }}</textarea>
                    @error('catatan_tambahan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Maksimal 1000 karakter
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bx bx-info-circle text-success me-2"></i>
                            <small class="text-muted">
                                <strong>Info:</strong> Setelah submit, berita acara akan langsung selesai dan PDF akan
                                ter-generate otomatis.
                            </small>
                        </div>
                        <div>
                            <a href="{{ route('admin.berita-acara-sempro.show', $beritaAcara) }}"
                                class="btn btn-outline-secondary me-2">
                                <i class="bx bx-x me-1"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-success" id="btnSubmit">
                                <i class="bx bx-check-double me-1"></i>Submit & Selesaikan BA
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
        // Form validation before submit
        document.getElementById('formFillBA').addEventListener('submit', function(e) {
            const catatanKejadian = document.querySelector('input[name="catatan_kejadian"]:checked');
            const keputusan = document.querySelector('input[name="keputusan"]:checked');

            if (!catatanKejadian || !keputusan) {
                e.preventDefault();

                Swal.fire({
                    icon: 'error',
                    title: 'Form Belum Lengkap',
                    text: 'Mohon lengkapi semua pilihan yang diperlukan (Catatan Kejadian dan Kesimpulan Kelayakan)',
                    confirmButtonText: 'OK'
                });

                return false;
            }

            // Confirmation before submit
            e.preventDefault();

            Swal.fire({
                title: 'Konfirmasi Submit',
                html: `
                    <div class="text-start">
                        <p>Anda akan mengirimkan berita acara dengan data:</p>
                        <ul>
                            <li><strong>Catatan Kejadian:</strong> ${catatanKejadian.value}</li>
                            <li><strong>Kesimpulan:</strong> ${keputusan.value}</li>
                        </ul>
                        <p class="text-warning mb-0">
                            <i class="bx bx-info-circle me-1"></i>
                            Setelah submit, berita acara akan dikirim ke Ketua Penguji untuk ditandatangani.
                        </p>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Submit',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });

        // Character counter
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
