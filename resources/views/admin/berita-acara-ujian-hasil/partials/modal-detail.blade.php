{{-- Modal: Detail Penilaian & Koreksi --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header border-bottom p-4 bg-label-primary">
                <h5 class="modal-title fw-bold">
                    <i class="bx bx-detail me-2 text-primary"></i>Detail Penilaian & Koreksi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small text-uppercase">Dosen Penguji</label>
                    <input type="text" class="form-control bg-light border-0 fw-bold" id="detail_dosen_name" readonly>
                    <small class="text-muted" id="detail_posisi"></small>
                </div>

                {{-- Penilaian Section --}}
                <div id="penilaianSection" class="mb-4">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">
                        <i class="bx bx-bar-chart-alt-2 me-2 text-warning"></i>Penilaian
                    </h6>
                    <div id="penilaianContent" class="p-3 bg-light rounded">
                        <div class="text-center text-muted py-3">
                            <i class="bx bx-info-circle fs-4"></i>
                            <p class="mb-0 mt-2">Belum ada data penilaian</p>
                        </div>
                    </div>
                </div>

                {{-- Lembar Koreksi Section --}}
                <div id="koreksiSection" class="mb-4">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">
                        <i class="bx bx-edit me-2 text-info"></i>Lembar Koreksi Skripsi
                    </h6>
                    <div id="koreksiContent" class="p-3 bg-light rounded">
                        <div class="text-center text-muted py-3">
                            <i class="bx bx-info-circle fs-4"></i>
                            <p class="mb-0 mt-2">Belum ada lembar koreksi</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top p-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
