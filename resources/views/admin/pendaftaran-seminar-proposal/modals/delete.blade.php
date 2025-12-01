{{-- filepath: /c:/laragon/www/eservice-app/resources/views/admin/pendaftaran-seminar-proposal/modals/delete.blade.php --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white">
                    <i class="bx bx-trash me-2"></i>Hapus Pendaftaran
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.pendaftaran-seminar-proposal.destroy', $pendaftaran) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="bx bx-error-circle me-2"></i>
                        <strong>Peringatan!</strong> Tindakan ini akan menghapus:
                    </div>
                    <ul>
                        <li>Data pendaftaran seminar proposal</li>
                        <li>Penentuan pembahas</li>
                        <li>Surat usulan</li>
                        <li>Semua file yang telah diupload</li>
                    </ul>
                    <p class="text-danger fw-bold mb-0">Tindakan ini TIDAK DAPAT dibatalkan!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bx bx-trash me-1"></i> Hapus Permanen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
