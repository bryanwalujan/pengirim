{{-- Modal: Approve On Behalf of Penguji --}}
<div class="modal fade" id="approveOnBehalfModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <form action="" method="POST" id="approveOnBehalfForm">
                @csrf
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold">
                        <i class="bx bx-user-check me-2 text-warning"></i>Approve Atas Nama Dosen
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="dosen_id" id="modal_dosen_id">

                    <div class="alert bg-label-warning border-0 mb-4 d-flex gap-3">
                        <i class="bx bx-info-circle fs-4"></i>
                        <div class="small">
                            <strong>Pemberitahuan:</strong> Fitur ini digunakan hanya jika dosen bersangkutan memberikan
                            mandat atau berhalangan mengakses sistem secara teknis.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase">Dosen Penguji</label>
                        <input type="text" class="form-control bg-light border-0 fw-bold" id="modal_dosen_name" readonly>
                    </div>

                    <div id="lembarKoreksiSection" class="mb-4" style="display:none;">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">Lembar Koreksi Skripsi (Input Data)</label>
                        <div class="p-3 bg-light border rounded mb-3">
                            <small class="text-muted d-block mb-3"><i class="bx bx-pencil me-1"></i>Masukkan hasil koreksi dari pembimbing.</small>
                            <table class="table table-bordered bg-white" id="koreksiTable">
                                <thead>
                                    <tr class="table-light">
                                        <th width="15%" class="x-small text-uppercase">Halaman</th>
                                        <th class="x-small text-uppercase">Catatan Koreksi</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="koreksiTableBody">
                                    {{-- Rows added via JS --}}
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2 fw-bold" onclick="addKoreksiRow()">
                                <i class="bx bx-plus me-1"></i>Tambah Baris Koreksi
                            </button>
                        </div>
                    </div>

                    {{-- Penilaian Section (Staff Override) --}}
                    <div id="penilaianSection" class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">
                            <i class="bx bx-star me-1"></i>Penilaian Atas Nama Dosen (Opsional)
                        </label>
                        <div class="p-3 bg-light border rounded">
                            <div class="alert alert-info border-0 py-2 px-3 mb-3">
                                <small><i class="bx bx-info-circle me-1"></i>Masukkan nilai mutu langsung (skala 0.00 - 4.00). Nilai komponen akan dihitung otomatis.</small>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nilai_mutu" class="form-label small fw-bold">Nilai Mutu (0.00 - 4.00)</label>
                                    <input type="text" class="form-control" id="nilai_mutu" name="nilai_mutu"
                                        placeholder="Contoh: 3.50" maxlength="4"
                                        oninput="validateAndClampNilaiMutu(this)"
                                        onblur="formatNilaiMutu(this)"
                                        onkeypress="return isValidNilaiMutuKey(event)">
                                    <small class="text-muted">Format: X.XX (contoh: 3.50, 2.75, 4.00)</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Preview Grade</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <span id="gradePreview" class="badge bg-secondary fs-6 px-3 py-2">-</span>
                                        <small id="gradeDescription" class="text-muted">Masukkan nilai</small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="catatan_penilaian" class="form-label small fw-bold">Catatan Penilaian (Opsional)</label>
                                    <textarea class="form-control" id="catatan_penilaian" name="catatan_penilaian" rows="2"
                                        placeholder="Catatan tambahan untuk penilaian..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="alasan" class="form-label fw-bold text-muted small text-uppercase">Alasan Persetujuan</label>
                        <textarea class="form-control border rounded" id="alasan" name="alasan" rows="3"
                            placeholder="Berikan alasan singkat..." maxlength="500"></textarea>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="confirmation" id="confirmation" required>
                        <label class="form-check-label text-muted small" for="confirmation">
                            Saya menyatakan bahwa persetujuan ini dilakukan secara sah dan akan tercatat secara permanen dalam sistem log audit.
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-top p-4">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-white px-4 fw-bold">Konfirmasi & Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>
