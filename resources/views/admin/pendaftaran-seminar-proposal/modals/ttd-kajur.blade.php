@if ($pendaftaran->suratUsulan && $pendaftaran->status === 'menunggu_ttd_kajur')
    <div class="modal fade" id="ttdKajurModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">
                        <i class="bx bx-pen me-2"></i>Tanda Tangan Kajur
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.pendaftaran-seminar-proposal.ttd-kajur', $pendaftaran) }}" 
                    method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            <i class="bx bx-info-circle me-2"></i>
                            <strong>Status Persetujuan:</strong><br>
                            Kaprodi telah menandatangani surat pada: 
                            <strong>{{ $pendaftaran->suratUsulan->ttd_kaprodi_at->format('d M Y H:i') }}</strong>
                        </div>

                        <div class="alert alert-warning mb-3">
                            <i class="bx bx-shield-quarter me-2"></i>
                            <strong>Tanda Tangan Terakhir:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Ini adalah tanda tangan final untuk surat ini</li>
                                <li>QR Code akan di-generate untuk verifikasi publik</li>
                                <li>Mahasiswa dapat langsung mengunduh surat</li>
                                <li>Proses tidak dapat dibatalkan setelah TTD</li>
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
                            Dengan menandatangani, proses persetujuan <strong>SELESAI</strong>. 
                            Surat resmi dapat diunduh oleh mahasiswa.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bx bx-x me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-pen me-1"></i> Ya, Tanda Tangani & Selesaikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif