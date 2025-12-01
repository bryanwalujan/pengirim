{{-- filepath: /c:/laragon/www/eservice-app/resources/views/admin/pendaftaran-seminar-proposal/modals/reset-pembahas.blade.php --}}
@if ($pendaftaran->isPembahasDitentukan())
    <div class="modal fade" id="resetPembahasModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">
                        <i class="bx bx-reset me-2"></i>Reset Pembahas
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.pendaftaran-seminar-proposal.reset-pembahas', $pendaftaran) }}"
                    method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-danger mb-3">
                            <i class="bx bx-error me-2"></i>
                            <strong>Peringatan:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Semua pembahas akan dihapus:
                                    <ul class="mt-1">
                                        <li>Pembahas 1: {{ $pendaftaran->getPembahas1()?->dosen->name ?? '-' }}</li>
                                        <li>Pembahas 2: {{ $pendaftaran->getPembahas2()?->dosen->name ?? '-' }}</li>
                                        <li>Pembahas 3: {{ $pendaftaran->getPembahas3()?->dosen->name ?? '-' }}</li>
                                    </ul>
                                </li>
                                @if ($pendaftaran->suratUsulan)
                                    <li class="text-danger"><strong>Surat usulan akan dihapus!</strong></li>
                                @endif
                                <li>Status akan kembali ke "Pending"</li>
                                <li>Anda harus menentukan pembahas baru</li>
                            </ul>
                        </div>

                        <p class="mb-0">Apakah Anda yakin ingin mereset penentuan pembahas?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bx bx-reset me-1"></i> Ya, Reset Pembahas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
