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
                        <span class="badge bg-label-primary">
                            <i class="bx bx-time-five me-1"></i>
                            {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }} WITA
                        </span>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold text-muted small">Judul Proposal</label>
                        <div class="text-wrap">{{ $pendaftaran->judul_skripsi }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Pengisian --}}
        <form action="{{ route('admin.berita-acara-sempro.store-fill-by-pembimbing', $beritaAcara) }}" method="POST"
            id="formFillBA">
            @csrf



            <div class="card mb-4">
                <div class="card-header bg-label-success">
                    <h5 class="mb-0">
                        <i class="bx bx-check-shield me-2"></i>1. Kesimpulan Kelayakan Seminar Proposal Skripsi
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Berikan kesimpulan apakah proposal layak dilanjutkan ke tahap penelitian:
                    </p>

                    <div class="form-check mb-3">
                        <input class="form-check-input @error('keputusan') is-invalid @enderror" type="radio"
                            name="keputusan" id="keputusan_ya" value="Ya"
                            {{ old('keputusan') === 'Ya' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="keputusan_ya">
                            <strong class="text-success">Ya</strong>
                            <div class="text-muted small">Proposal layak dilanjutkan tanpa syarat</div>
                        </label>
                    </div>

                    {{-- ✅ PERBAIKAN: Value harus PERSIS dengan enum database --}}
                    <div class="form-check mb-3">
                        <input class="form-check-input @error('keputusan') is-invalid @enderror" type="radio"
                            name="keputusan" id="keputusan_ya_perbaikan" value="Ya, dengan perbaikan"
                            {{ old('keputusan') === 'Ya, dengan perbaikan' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="keputusan_ya_perbaikan">
                            <strong class="text-warning">Ya, dengan perbaikan</strong>
                            <div class="text-muted small">Proposal layak dengan catatan harus ada perbaikan</div>
                        </label>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input @error('keputusan') is-invalid @enderror" type="radio"
                            name="keputusan" id="keputusan_tidak" value="Tidak"
                            {{ old('keputusan') === 'Tidak' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="keputusan_tidak">
                            <strong class="text-danger">Tidak</strong>
                            <div class="text-muted small">Proposal belum layak dan perlu revisi besar</div>
                        </label>
                    </div>

                    {{-- Warning untuk keputusan Tidak --}}
                    <div class="alert alert-danger d-none" id="alertTidakLayak" role="alert">
                        <h6 class="alert-heading mb-2">
                            <i class="bx bx-error-circle me-1"></i>Perhatian: Proposal Tidak Layak
                        </h6>
                        <p class="mb-2">Jika Anda memilih <strong>"Tidak"</strong>, maka:</p>
                        <ul class="mb-2">
                            <li>Berita acara akan ditandai sebagai <strong>DITOLAK</strong></li>
                            <li>Pendaftaran seminar proposal mahasiswa akan <strong>DITOLAK</strong></li>
                            <li>Mahasiswa <strong>HARUS membuat Komisi Proposal BARU</strong> dengan judul yang direvisi</li>
                            <li>Setelah komisi proposal baru disetujui (PA + Korprodi), mahasiswa baru bisa mendaftar seminar proposal lagi</li>
                            <li>Jadwal ujian sebelumnya akan dibatalkan</li>
                        </ul>
                        <p class="mb-0">
                            <i class="bx bx-info-circle me-1"></i>
                            <small>Pastikan keputusan ini sudah dipertimbangkan dengan matang bersama tim penguji.</small>
                        </p>
                    </div>

                    @error('keputusan')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-note me-2"></i>2. Catatan Tambahan (Opsional)
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
        document.addEventListener('DOMContentLoaded', function() {
            // ========== SHOW/HIDE WARNING ALERT ==========
            const radioTidak = document.getElementById('keputusan_tidak');
            const radioYa = document.getElementById('keputusan_ya');
            const radioYaPerbaikan = document.getElementById('keputusan_ya_perbaikan');
            const alertTidakLayak = document.getElementById('alertTidakLayak');

            function toggleAlert() {
                if (radioTidak && radioTidak.checked) {
                    alertTidakLayak.classList.remove('d-none');
                } else {
                    alertTidakLayak.classList.add('d-none');
                }
            }

            // Add event listeners
            if (radioTidak) radioTidak.addEventListener('change', toggleAlert);
            if (radioYa) radioYa.addEventListener('change', toggleAlert);
            if (radioYaPerbaikan) radioYaPerbaikan.addEventListener('change', toggleAlert);

            // Check on page load
            toggleAlert();

            // ========== FORM VALIDATION & CONFIRMATION ==========
            const form = document.getElementById('formFillBA');
            const btnSubmit = document.getElementById('btnSubmit');

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const keputusan = document.querySelector('input[name="keputusan"]:checked');

                // Validation
                if (!keputusan) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Form Belum Lengkap',
                        text: 'Mohon pilih Kesimpulan Kelayakan',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    });
                    return false;
                }

                // Log untuk debugging
                console.log('Form data:', {
                    keputusan: keputusan.value,
                    keputusan_length: keputusan.value.length
                });

                // Disable button to prevent double submit
                btnSubmit.disabled = true;
                btnSubmit.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';

                // Different confirmation based on keputusan
                let confirmTitle = 'Konfirmasi Submit';
                let confirmIcon = 'warning';
                let confirmHtml = '';
                let confirmButtonColor = '#28a745';

                if (keputusan.value === 'Tidak') {
                    confirmTitle = '⚠️ Konfirmasi Penolakan Proposal';
                    confirmIcon = 'error';
                    confirmButtonColor = '#dc3545';
                    confirmHtml = `
                    <div class="text-start">
                        <div class="alert alert-danger mb-3">
                            <strong>PERHATIAN:</strong> Anda akan menolak proposal mahasiswa ini!
                        </div>
                        <p>Data yang akan disimpan:</p>
                        <ul>
                            <li><strong>Kesimpulan:</strong> <span class="text-danger"><strong>${keputusan.value}</strong></span></li>
                        </ul>
                        <div class="alert alert-warning mb-0">
                            <p class="mb-2"><strong>Konsekuensi:</strong></p>
                            <ul class="mb-0">
                                <li>Berita acara akan ditandai sebagai <strong>DITOLAK</strong></li>
                                <li>Pendaftaran seminar proposal ditolak</li>
                                <li>Mahasiswa harus <strong>membuat Komisi Proposal BARU</strong> dengan judul yang direvisi</li>
                                <li>Setelah komisi baru disetujui, mahasiswa bisa daftar sempro lagi</li>
                            </ul>
                        </div>
                    </div>
                `;
                } else {
                    confirmHtml = `
                    <div class="text-start">
                        <p>Anda akan mengirimkan berita acara dengan data:</p>
                        <ul>
                            <li><strong>Kesimpulan:</strong> <span class="text-success"><strong>${keputusan.value}</strong></span></li>
                        </ul>
                        <p class="text-success mb-0">
                            <i class="bx bx-check-circle me-1"></i>
                            Setelah submit, berita acara akan selesai dan PDF akan ter-generate otomatis.
                        </p>
                    </div>
                `;
                }

                Swal.fire({
                    title: confirmTitle,
                    html: confirmHtml,
                    icon: confirmIcon,
                    showCancelButton: true,
                    confirmButtonText: keputusan.value === 'Tidak' ? 'Ya, Tolak Proposal' :
                        'Ya, Submit',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: confirmButtonColor,
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true,
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Mohon tunggu, sedang menyimpan data dan generate PDF',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit form
                        form.submit();
                    } else {
                        // Re-enable button if cancelled
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML =
                            '<i class="bx bx-check-double me-1"></i>Submit & Selesaikan BA';
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
        });
    </script>
@endpush
