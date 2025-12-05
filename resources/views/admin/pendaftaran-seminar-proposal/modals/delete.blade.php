{{-- Modal Hapus Pendaftaran - HANYA UNTUK STAFF --}}
@if(auth()->user()->hasRole('staff'))
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
                    {{-- Extra Warning untuk data selesai/sudah ada surat --}}
                    @if($pendaftaran->status === 'selesai')
                        <div class="alert alert-danger border-danger mb-3">
                            <div class="d-flex align-items-start">
                                <i class="bx bx-error-circle fs-3 me-2 text-danger"></i>
                                <div>
                                    <strong class="d-block mb-1">⚠️ DATA SUDAH SELESAI!</strong>
                                    <small>
                                        Pendaftaran ini sudah selesai diproses dan memiliki surat resmi yang sudah ditandatangani.
                                        @if($pendaftaran->suratUsulan)
                                            <br><strong>Nomor Surat:</strong> {{ $pendaftaran->suratUsulan->nomor_surat }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    @elseif($pendaftaran->suratUsulan)
                        <div class="alert alert-warning border-warning mb-3">
                            <div class="d-flex align-items-start">
                                <i class="bx bx-file fs-3 me-2 text-warning"></i>
                                <div>
                                    <strong class="d-block mb-1">⚠️ SURAT SUDAH DIGENERATE!</strong>
                                    <small>
                                        Surat usulan sudah digenerate untuk pendaftaran ini.
                                        <br><strong>Nomor Surat:</strong> {{ $pendaftaran->suratUsulan->nomor_surat }}
                                        @if($pendaftaran->suratUsulan->ttd_kaprodi_at)
                                            <br><span class="text-success"><i class="bx bx-check"></i> TTD Kaprodi: {{ $pendaftaran->suratUsulan->ttd_kaprodi_at->format('d M Y H:i') }}</span>
                                        @endif
                                        @if($pendaftaran->suratUsulan->ttd_kajur_at)
                                            <br><span class="text-success"><i class="bx bx-check"></i> TTD Kajur: {{ $pendaftaran->suratUsulan->ttd_kajur_at->format('d M Y H:i') }}</span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Info Mahasiswa --}}
                    <div class="bg-light rounded p-3 mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    <i class="bx bx-user"></i>
                                </span>
                            </div>
                            <div>
                                <strong>{{ $pendaftaran->user->name }}</strong>
                                <br><small class="text-muted">NIM: {{ $pendaftaran->user->nim }}</small>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Status:</small>
                            <span>{!! $pendaftaran->status_badge !!}</span>
                        </div>
                    </div>

                    {{-- Daftar yang akan dihapus --}}
                    <div class="alert alert-danger mb-3">
                        <i class="bx bx-error-circle me-2"></i>
                        <strong>Tindakan ini akan menghapus:</strong>
                    </div>
                    <ul class="mb-3">
                        <li>Data pendaftaran seminar proposal</li>
                        <li>Penentuan dosen pembahas ({{ $pendaftaran->proposalPembahas->count() }} pembahas)</li>
                        @if($pendaftaran->suratUsulan)
                            <li><strong class="text-danger">Surat usulan: {{ $pendaftaran->suratUsulan->nomor_surat }}</strong></li>
                            <li>File PDF surat usulan</li>
                            <li>QR Code tanda tangan digital</li>
                        @endif
                        <li>File transkrip nilai</li>
                        <li>File proposal penelitian</li>
                        <li>File surat permohonan</li>
                        <li>File slip UKT</li>
                    </ul>

                    <p class="text-danger fw-bold mb-3">
                        <i class="bx bx-error me-1"></i>
                        Tindakan ini TIDAK DAPAT dibatalkan!
                    </p>

                    {{-- Konfirmasi Ketik untuk data selesai --}}
                    @if($pendaftaran->status === 'selesai' || $pendaftaran->suratUsulan)
                        <div class="mb-3">
                            <label class="form-label text-danger fw-bold">
                                Ketik "HAPUS" untuk konfirmasi:
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="confirmDeleteInput" 
                                   placeholder="Ketik HAPUS"
                                   autocomplete="off"
                                   required>
                            <small class="text-muted">Konfirmasi diperlukan karena data memiliki surat resmi.</small>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn"
                        @if($pendaftaran->status === 'selesai' || $pendaftaran->suratUsulan) disabled @endif>
                        <i class="bx bx-trash me-1"></i> Hapus Permanen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script untuk validasi konfirmasi --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmInput = document.getElementById('confirmDeleteInput');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    
    if (confirmInput && confirmBtn) {
        confirmInput.addEventListener('input', function() {
            if (this.value.toUpperCase() === 'HAPUS') {
                confirmBtn.disabled = false;
                confirmBtn.classList.remove('btn-secondary');
                confirmBtn.classList.add('btn-danger');
            } else {
                confirmBtn.disabled = true;
                confirmBtn.classList.remove('btn-danger');
                confirmBtn.classList.add('btn-secondary');
            }
        });
    }
});
</script>
@endpush
@endif