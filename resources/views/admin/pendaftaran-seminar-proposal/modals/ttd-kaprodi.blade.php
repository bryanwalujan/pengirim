{{-- filepath: /c:/laragon/www/eservice-app/resources/views/admin/pendaftaran-seminar-proposal/modals/ttd-kaprodi.blade.php --}}

{{-- ✅ PERBAIKAN: Tambahkan kondisi untuk dosen --}}
@if (
    $pendaftaran->suratUsulan &&
        $pendaftaran->status === 'menunggu_ttd_kaprodi' &&
        (auth()->user()->isKoordinatorProdi() || auth()->user()->hasRole('staff')))

    <div class="modal fade" id="ttdKaprodiModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white">
                        <i class="bx bx-pen me-2"></i>Tanda Tangan Kaprodi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.pendaftaran-seminar-proposal.ttd-kaprodi', $pendaftaran) }}"
                    method="POST">
                    @csrf
                    <div class="modal-body">
                        {{-- ✅ Info untuk dosen vs staff --}}
                        @if (auth()->user()->isKoordinatorProdi())
                            <div class="alert alert-info mb-3">
                                <i class="bx bx-info-circle me-2"></i>
                                <strong>Informasi:</strong><br>
                                Anda akan menandatangani surat ini sebagai <strong>Koordinator Program Studi</strong>.
                            </div>
                        @else
                            <div class="alert alert-warning mb-3">
                                <i class="bx bx-shield-alt me-2"></i>
                                <strong>Staff Override:</strong><br>
                                Anda akan menandatangani atas nama Koordinator Program Studi.
                                Tindakan ini akan tercatat dalam sistem.
                            </div>
                        @endif

                        <div class="alert alert-warning mb-3">
                            <i class="bx bx-shield-quarter me-2"></i>
                            <strong>Keamanan:</strong>
                            <ul class="mb-0 mt-2">
                                <li>QR Code akan di-generate untuk verifikasi</li>
                                <li>Tanda tangan digital tidak dapat dibatalkan</li>
                                <li>Dokumen akan tersimpan secara permanen</li>
                            </ul>
                        </div>

                        <!-- Surat Info -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nomor Surat</label>
                            <input type="text" class="form-control"
                                value="{{ $pendaftaran->suratUsulan->nomor_surat }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Mahasiswa</label>
                            <input type="text" class="form-control"
                                value="{{ $pendaftaran->user->name }} ({{ $pendaftaran->user->nim }})" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul Skripsi</label>
                            <textarea class="form-control" rows="3" readonly>{{ $pendaftaran->judul_skripsi }}</textarea>
                        </div>

                        <div class="alert alert-success mb-0">
                            <i class="bx bx-check-circle me-2"></i>
                            Setelah TTD, surat akan diteruskan ke <strong>Kajur</strong> untuk persetujuan final.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="bx bx-pen me-1"></i> Ya, Tanda Tangani
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
