{{-- filepath: resources/views/admin/berita-acara-sempro/fill-on-behalf.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Override Pembimbing - Isi Berita Acara')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @php
            $jadwal = $beritaAcara->jadwalSeminarProposal;
            $pendaftaran = $jadwal->pendaftaranSeminarProposal;
            $mahasiswa = $pendaftaran->user;
            $pembimbing = $pendaftaran->dosenPembimbing;
        @endphp

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">
                    <span class="text-muted fw-light">Berita Acara /</span> Override Pembimbing
                </h4>
                <p class="text-muted mb-0">
                    <i class="bx bx-user-check me-1"></i>
                    Mengisi atas nama Dosen Pembimbing
                </p>
            </div>
            <a href="{{ route('admin.berita-acara-sempro.show', $beritaAcara) }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>

        {{-- Alert Warning Override --}}
        <div class="alert alert-warning mb-4" role="alert">
            <h6 class="alert-heading mb-2">
                <i class="bx bx-error-circle me-2"></i>Perhatian: Override Pembimbing
            </h6>
            <div class="small">
                Anda akan mengisi berita acara <strong>atas nama dosen pembimbing</strong>:
                <strong>{{ $pembimbing->name }}</strong>
                <ul class="mb-0 ps-3 mt-2">
                    <li>Tindakan ini akan tercatat untuk keperluan <strong>audit trail</strong></li>
                    <li>Nama staff yang melakukan override akan dicatat di sistem</li>
                    <li>Pastikan Anda sudah berkoordinasi dengan dosen pembimbing yang bersangkutan</li>
                </ul>
            </div>
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
                        <label class="form-label fw-bold text-muted small">Dosen Pembimbing</label>
                        <div>{{ $pembimbing->name }}</div>
                        <small class="text-muted">NIP: {{ $pembimbing->nip }}</small>
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
        <form action="{{ route('admin.berita-acara-sempro.store-fill-on-behalf', $beritaAcara) }}" method="POST"
            id="formFillOnBehalf">
            @csrf

            {{-- Kesimpulan Kelayakan --}}
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
                            <li>Mahasiswa <strong>HARUS membuat Komisi Proposal BARU</strong></li>
                        </ul>
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
                        <i class="bx bx-note me-2"></i>2. Catatan Tambahan (Opsional)
                    </h5>
                </div>
                <div class="card-body">
                    <textarea name="catatan_tambahan" id="catatan_tambahan"
                        class="form-control @error('catatan_tambahan') is-invalid @enderror" rows="4"
                        placeholder="Tambahkan catatan khusus jika diperlukan..."
                        maxlength="1000">{{ old('catatan_tambahan') }}</textarea>
                    @error('catatan_tambahan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Maksimal 1000 karakter</div>
                </div>
            </div>

            {{-- Alasan Override (WAJIB) --}}
            <div class="card mb-4">
                <div class="card-header bg-label-warning">
                    <h5 class="mb-0">
                        <i class="bx bx-info-circle me-2"></i>3. Alasan Override (Wajib)
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Jelaskan alasan mengapa Anda melakukan override persetujuan pembimbing:
                    </p>
                    <textarea name="alasan_override" id="alasan_override"
                        class="form-control @error('alasan_override') is-invalid @enderror" rows="3"
                        placeholder="Contoh: Dosen pembimbing sedang cuti / Dosen tidak bisa akses sistem / Atas permintaan dosen pembimbing karena kondisi darurat"
                        maxlength="500" required>{{ old('alasan_override') }}</textarea>
                    @error('alasan_override')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Maksimal 500 karakter. Field ini wajib diisi untuk audit trail.</div>
                </div>
            </div>

            {{-- Konfirmasi --}}
            <div class="card mb-4">
                <div class="card-body">
                    <div class="form-check">
                        <input class="form-check-input @error('confirmation') is-invalid @enderror" 
                               type="checkbox" name="confirmation" id="confirmation" value="1" required>
                        <label class="form-check-label" for="confirmation">
                            Saya menyatakan bahwa tindakan override ini dilakukan <strong>atas nama dosen pembimbing 
                            {{ $pembimbing->name }}</strong> dan akan tercatat dalam sistem untuk keperluan audit.
                            Saya bertanggung jawab atas keputusan ini.
                        </label>
                    </div>
                    @error('confirmation')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bx bx-info-circle text-warning me-2"></i>
                            <small class="text-muted">
                                <strong>Override oleh:</strong> {{ Auth::user()->name }}
                            </small>
                        </div>
                        <div>
                            <a href="{{ route('admin.berita-acara-sempro.show', $beritaAcara) }}"
                                class="btn btn-outline-secondary me-2">
                                <i class="bx bx-x me-1"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-warning" id="btnSubmit">
                                <i class="bx bx-user-check me-1"></i>Override & Selesaikan BA
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
            // Show/hide warning alert
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

            if (radioTidak) radioTidak.addEventListener('change', toggleAlert);
            if (radioYa) radioYa.addEventListener('change', toggleAlert);
            if (radioYaPerbaikan) radioYaPerbaikan.addEventListener('change', toggleAlert);
            toggleAlert();

            // Form submission with SweetAlert confirmation
            const form = document.getElementById('formFillOnBehalf');
            const btnSubmit = document.getElementById('btnSubmit');

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const keputusan = document.querySelector('input[name="keputusan"]:checked');
                const alasanOverride = document.getElementById('alasan_override').value.trim();
                const confirmation = document.getElementById('confirmation').checked;

                // Validation
                if (!keputusan) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Form Belum Lengkap',
                        text: 'Mohon pilih Kesimpulan Kelayakan',
                        confirmButtonColor: '#3085d6'
                    });
                    return false;
                }

                if (!alasanOverride) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Form Belum Lengkap',
                        text: 'Alasan override wajib diisi',
                        confirmButtonColor: '#3085d6'
                    });
                    return false;
                }

                if (!confirmation) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Form Belum Lengkap',
                        text: 'Mohon centang checkbox konfirmasi',
                        confirmButtonColor: '#3085d6'
                    });
                    return false;
                }

                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

                let confirmTitle = '⚠️ Konfirmasi Override Pembimbing';
                let confirmIcon = 'warning';
                let confirmButtonColor = '#ffc107';
                let confirmHtml = '';

                if (keputusan.value === 'Tidak') {
                    confirmTitle = '⚠️ Konfirmasi Penolakan (Override)';
                    confirmIcon = 'error';
                    confirmButtonColor = '#dc3545';
                    confirmHtml = `
                        <div class="text-start">
                            <div class="alert alert-danger mb-3">
                                <strong>PERHATIAN:</strong> Anda akan menolak proposal mahasiswa ini via override!
                            </div>
                            <p><strong>Keputusan:</strong> <span class="text-danger">${keputusan.value}</span></p>
                            <p><strong>Alasan Override:</strong> ${alasanOverride}</p>
                        </div>
                    `;
                } else {
                    confirmHtml = `
                        <div class="text-start">
                            <p>Anda akan mengisi berita acara <strong>atas nama dosen pembimbing</strong>:</p>
                            <ul>
                                <li><strong>Keputusan:</strong> <span class="text-success">${keputusan.value}</span></li>
                                <li><strong>Alasan Override:</strong> ${alasanOverride}</li>
                            </ul>
                            <p class="text-warning mb-0">
                                <i class="bx bx-info-circle me-1"></i>
                                Tindakan ini akan tercatat untuk audit trail.
                            </p>
                        </div>
                    `;
                }

                Swal.fire({
                    title: confirmTitle,
                    html: confirmHtml,
                    icon: confirmIcon,
                    showCancelButton: true,
                    confirmButtonText: keputusan.value === 'Tidak' ? 'Ya, Tolak Proposal' : 'Ya, Override Sekarang',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: confirmButtonColor,
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true,
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Mohon tunggu, sedang menyimpan data dan generate PDF',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        form.submit();
                    } else {
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = '<i class="bx bx-user-check me-1"></i>Override & Selesaikan BA';
                    }
                });
            });
        });
    </script>
@endpush
