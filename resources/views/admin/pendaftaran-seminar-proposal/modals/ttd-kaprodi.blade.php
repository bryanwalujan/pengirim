{{-- filepath: /c:/laragon/www/eservice-app/resources/views/admin/pendaftaran-seminar-proposal/modals/ttd-kaprodi.blade.php --}}
@if ($pendaftaran->suratUsulan)
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
                        <div class="alert alert-warning mb-3">
                            <i class="bx bx-info-circle me-2"></i>
                            <strong>Perhatian:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Tanda tangan akan dibubuhkan pada surat</li>
                                <li>QR Code akan digenerate untuk verifikasi</li>
                                <li>Proses tidak dapat dibatalkan setelah ditandatangani</li>
                            </ul>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nomor Surat</label>
                            <input type="text" class="form-control"
                                value="{{ $pendaftaran->suratUsulan->nomor_surat }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Mahasiswa</label>
                            <input type="text" class="form-control" value="{{ $pendaftaran->user->name }}" readonly>
                        </div>

                        <p class="mb-0">Apakah Anda yakin ingin menandatangani surat ini sebagai
                            <strong>Kaprodi</strong>?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bx bx-pen me-1"></i> Ya, Tanda Tangani
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
