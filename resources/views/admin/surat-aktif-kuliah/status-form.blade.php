<form id="status-form" action="{{ route('admin.surat-aktif-kuliah.update-status', $surat->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="status" class="form-label">Status Pengajuan <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                <option value="">Pilih Status</option>
                <option value="diajukan" {{ old('status', $surat->status) === 'diajukan' ? 'selected' : '' }}>Diajukan
                </option>
                <option value="diproses" {{ old('status', $surat->status) === 'diproses' ? 'selected' : '' }}>Diproses
                </option>
                <option value="disetujui" {{ old('status', $surat->status) === 'disetujui' ? 'selected' : '' }}>
                    Disetujui</option>
                <option value="ditolak" {{ old('status', $surat->status) === 'ditolak' ? 'selected' : '' }}>Ditolak
                </option>
                <option value="siap_diambil" {{ old('status', $surat->status) === 'siap_diambil' ? 'selected' : '' }}>
                    Siap Diambil</option>
                <option value="sudah_diambil" {{ old('status', $surat->status) === 'sudah_diambil' ? 'selected' : '' }}>
                    Sudah Diambil</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="penandatangan_id" class="form-label">Penandatangan <span id="penandatangan_required"
                    class="text-danger" style="display: none;">*</span></label>
            <select name="penandatangan_id" id="penandatangan_id"
                class="form-select @error('penandatangan_id') is-invalid @enderror">
                <option value="">Pilih Penandatangan</option>
                @foreach ($penandatangans as $penandatangan)
                    <option value="{{ $penandatangan->id }}"
                        {{ old('penandatangan_id', $surat->penandatangan_id) == $penandatangan->id ? 'selected' : '' }}>
                        {{ $penandatangan->name }}
                    </option>
                @endforeach
            </select>
            @error('penandatangan_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="jabatan_penandatangan" class="form-label">Jabatan Penandatangan <span id="jabatan_required"
                    class="text-danger" style="display: none;">*</span></label>
            <input type="text" name="jabatan_penandatangan" id="jabatan_penandatangan"
                class="form-control @error('jabatan_penandatangan') is-invalid @enderror"
                value="{{ old('jabatan_penandatangan', $surat->jabatan_penandatangan) }}"
                placeholder="Masukkan jabatan penandatangan">
            @error('jabatan_penandatangan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 mb-3">
            <label for="catatan_admin" class="form-label">Catatan Admin <span class="text-danger">*</span></label>
            <textarea name="catatan_admin" id="catatan_admin" class="form-control @error('catatan_admin') is-invalid @enderror"
                rows="4" required>{{ old('catatan_admin', $surat->status()->first()?->catatan_admin) }}</textarea>
            @error('catatan_admin')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 mb-3">
            <label for="catatan_internal" class="form-label">Catatan Internal (Opsional)</label>
            <textarea name="catatan_internal" id="catatan_internal"
                class="form-control @error('catatan_internal') is-invalid @enderror" rows="4">{{ old('catatan_internal', $surat->status()->first()?->catatan_internal) }}</textarea>
            @error('catatan_internal')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary">
                <i class="bx bx-save me-1"></i> Simpan Perubahan
            </button>
        </div>
    </div>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('status');
            const penandatanganSelect = document.getElementById('penandatangan_id');
            const jabatanInput = document.getElementById('jabatan_penandatangan');
            const penandatanganRequired = document.getElementById('penandatangan_required');
            const jabatanRequired = document.getElementById('jabatan_required');

            function toggleRequiredFields() {
                const requiresPenandatangan = ['disetujui', 'siap_diambil'].includes(statusSelect.value);
                penandatanganSelect.required = requiresPenandatangan;
                jabatanInput.required = requiresPenandatangan;
                penandatanganRequired.style.display = requiresPenandatangan ? 'inline' : 'none';
                jabatanRequired.style.display = requiresPenandatangan ? 'inline' : 'none';
            }

            statusSelect.addEventListener('change', toggleRequiredFields);
            toggleRequiredFields(); // Panggil saat load
        });
    </script>
@endpush
