{{-- filepath: /c:/laragon/www/eservice-app/resources/views/admin/pendaftaran-seminar-proposal/modals/generate-surat.blade.php --}}
@if (!$pendaftaran->suratUsulan)
    <div class="modal fade" id="generateSuratModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bx bx-file-blank me-2"></i>Generate Surat Usulan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.pendaftaran-seminar-proposal.generate-surat', $pendaftaran) }}"
                    method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            <i class="bx bx-info-circle me-2"></i>
                            <strong>Informasi:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Surat akan digenerate dengan nomor otomatis</li>
                                <li>Pembahas sudah ditentukan:
                                    <ul class="mt-1">
                                        <li>Pembahas 1: {{ $pendaftaran->getPembahas1()?->dosen->name ?? '-' }}</li>
                                        <li>Pembahas 2: {{ $pendaftaran->getPembahas2()?->dosen->name ?? '-' }}</li>
                                        <li>Pembahas 3: {{ $pendaftaran->getPembahas3()?->dosen->name ?? '-' }}</li>
                                    </ul>
                                </li>
                                <li>Status akan berubah menjadi "Menunggu TTD Kaprodi"</li>
                            </ul>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Mahasiswa</label>
                            <input type="text" class="form-control"
                                value="{{ $pendaftaran->user->name }} ({{ $pendaftaran->user->nim }})" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Judul Skripsi</label>
                            <textarea class="form-control" rows="3" readonly>{{ $pendaftaran->judul_skripsi }}</textarea>
                        </div>

                        <p class="mb-0">Apakah Anda yakin ingin generate surat usulan untuk mahasiswa ini?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bx bx-check me-1"></i> Ya, Generate Surat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
