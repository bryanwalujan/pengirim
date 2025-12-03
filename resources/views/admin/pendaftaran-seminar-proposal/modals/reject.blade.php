<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel">
                    <i class="bx bx-x-circle me-2"></i>Tolak Pendaftaran
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.pendaftaran-seminar-proposal.reject', $pendaftaran) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning mb-3">
                        <div class="d-flex">
                            <i class="bx bx-error fs-4 me-2"></i>
                            <div>
                                <strong>Perhatian!</strong>
                                <p class="mb-0 small">Pendaftaran yang ditolak tidak dapat diproses lebih lanjut.
                                    Mahasiswa perlu mengajukan ulang pendaftaran.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Informasi Pendaftaran</label>
                        <div class="bg-lighter rounded p-3">
                            <div class="row g-2 small">
                                <div class="col-4 text-muted">Mahasiswa:</div>
                                <div class="col-8 fw-medium">{{ $pendaftaran->user->name }}</div>
                                <div class="col-4 text-muted">NIM:</div>
                                <div class="col-8 fw-medium">{{ $pendaftaran->user->nim }}</div>
                                <div class="col-4 text-muted">Status:</div>
                                <div class="col-8">{!! $pendaftaran->status_badge !!}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alasan_penolakan" class="form-label fw-semibold">
                            Alasan Penolakan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="alasan_penolakan" name="alasan_penolakan" rows="4" required minlength="10"
                            maxlength="1000" placeholder="Jelaskan alasan penolakan secara detail (minimal 10 karakter)..."></textarea>
                        <div class="form-text">
                            <span id="charCount">0</span>/1000 karakter
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small text-muted">Alasan Umum:</label>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-reason"
                                data-reason="Dokumen transkrip nilai tidak valid atau tidak terbaca dengan jelas.">
                                Transkrip Invalid
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-reason"
                                data-reason="File proposal penelitian tidak sesuai format atau tidak lengkap.">
                                Proposal Tidak Lengkap
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-reason"
                                data-reason="Slip pembayaran UKT tidak valid atau belum lunas.">
                                UKT Bermasalah
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-reason"
                                data-reason="Surat permohonan tidak sesuai dengan format yang ditentukan.">
                                Format Surat Salah
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-danger" id="confirmRejectBtn">
                        <i class="bx bx-x-circle me-1"></i> Tolak Pendaftaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('alasan_penolakan');
            const charCount = document.getElementById('charCount');
            const quickReasons = document.querySelectorAll('.quick-reason');

            // Character counter
            textarea.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });

            // Quick reason buttons
            quickReasons.forEach(btn => {
                btn.addEventListener('click', function() {
                    textarea.value = this.dataset.reason;
                    charCount.textContent = textarea.value.length;
                    textarea.focus();
                });
            });
        });
    </script>
@endpush
