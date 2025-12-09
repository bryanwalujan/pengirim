{{-- filepath: resources/views/admin/jadwal-seminar-proposal/modals/delete-modal.blade.php --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="deleteModalLabel">
                    <i class="bx bx-trash me-2"></i>Hapus Jadwal Seminar Proposal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.jadwal-seminar-proposal.destroy', $jadwal) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bx bx-error-circle text-danger" style="font-size: 4rem;"></i>
                    </div>

                    <div class="alert alert-danger border-0">
                        <div class="d-flex align-items-start">
                            <i class="bx bx-error-circle fs-4 me-2"></i>
                            <div>
                                <strong class="d-block mb-2">Peringatan!</strong>
                                <p class="mb-2">Tindakan ini akan:</p>
                                <ul class="mb-0">
                                    <li>Menghapus data jadwal seminar proposal</li>
                                    <li>Menghapus file SK Proposal</li>
                                    <li>Reset status ke "Menunggu SK"</li>
                                </ul>
                                <p class="mb-0 mt-2 small text-danger">
                                    <strong>Mahasiswa harus upload SK baru!</strong>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Data yang akan dihapus:</label>
                        <div class="p-3 bg-light rounded">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="text-muted" width="35%">Mahasiswa</td>
                                    <td width="5%">:</td>
                                    <td class="fw-semibold">{{ $jadwal->pendaftaranSeminarProposal->user->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">NIM</td>
                                    <td>:</td>
                                    <td class="fw-semibold">{{ $jadwal->pendaftaranSeminarProposal->user->nim }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status</td>
                                    <td>:</td>
                                    <td>{!! $jadwal->status_badge !!}</td>
                                </tr>
                                @if ($jadwal->hasJadwal())
                                    <tr>
                                        <td class="text-muted">Jadwal</td>
                                        <td>:</td>
                                        <td class="small">{{ $jadwal->jadwal_lengkap }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmDelete" required>
                        <label class="form-check-label" for="confirmDelete">
                            Saya memahami bahwa tindakan ini tidak dapat dibatalkan
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bx bx-trash me-1"></i>Ya, Hapus Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
