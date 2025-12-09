{{-- filepath: resources/views/admin/jadwal-seminar-proposal/modals/schedule-modal.blade.php --}}
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleModalLabel">
                    <i class="bx bx-calendar-event me-2"></i>Penjadwalan Seminar Proposal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="scheduleForm" method="POST">
                @csrf
                @method('POST')
                <div class="modal-body">
                    {{-- Info Section --}}
                    <div class="alert alert-info mb-4">
                        <div class="d-flex">
                            <i class="bx bx-info-circle fs-4 me-2"></i>
                            <div>
                                <strong>Informasi</strong>
                                <p class="mb-0 small">Setelah jadwal disimpan, sistem akan otomatis mengirimkan undangan
                                    seminar proposal ke Dosen Pembimbing dan Pembahas via email.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Mahasiswa Info Card --}}
                    <div class="card mb-4 border">
                        <div class="card-body p-3">
                            <h6 class="card-title mb-3">
                                <i class="bx bx-user me-1"></i> Data Mahasiswa
                            </h6>
                            <div class="row g-2 small">
                                <div class="col-4 text-muted">Nama:</div>
                                <div class="col-8 fw-medium" id="modalMahasiswaNama">-</div>

                                <div class="col-4 text-muted">NIM:</div>
                                <div class="col-8 fw-medium" id="modalMahasiswaNim">-</div>

                                <div class="col-4 text-muted">Judul:</div>
                                <div class="col-8 fw-medium" id="modalMahasiswaJudul">-</div>

                                <div class="col-4 text-muted">SK Proposal:</div>
                                <div class="col-8">
                                    <span id="modalSkStatus" class="badge bg-label-success">
                                        <i class="bx bx-check me-1"></i>Sudah Upload
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Inputs --}}
                    <div class="row g-3">
                        {{-- Tanggal Ujian --}}
                        <div class="col-12">
                            <label for="tanggal" class="form-label fw-semibold">
                                <i class="bx bx-calendar me-1"></i> Tanggal Ujian
                                <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control @error('tanggal') is-invalid @enderror"
                                id="tanggal" name="tanggal" value="{{ old('tanggal') }}" min="{{ date('Y-m-d') }}"
                                required>
                            @error('tanggal')
                                <div class="invalid-feedback">
                                    <i class="bx bx-error-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                <i class="bx bx-info-circle me-1"></i>
                                Pilih tanggal pelaksanaan seminar proposal
                            </div>
                        </div>

                        {{-- Jam Mulai --}}
                        <div class="col-md-6">
                            <label for="jam_mulai" class="form-label fw-semibold">
                                <i class="bx bx-time me-1"></i> Jam Mulai
                                <span class="text-danger">*</span>
                            </label>
                            <input type="time" class="form-control @error('jam_mulai') is-invalid @enderror"
                                id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai') }}" required>
                            @error('jam_mulai')
                                <div class="invalid-feedback">
                                    <i class="bx bx-error-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Jam Selesai --}}
                        <div class="col-md-6">
                            <label for="jam_selesai" class="form-label fw-semibold">
                                <i class="bx bx-time-five me-1"></i> Jam Selesai
                                <span class="text-danger">*</span>
                            </label>
                            <input type="time" class="form-control @error('jam_selesai') is-invalid @enderror"
                                id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai') }}" required>
                            @error('jam_selesai')
                                <div class="invalid-feedback">
                                    <i class="bx bx-error-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                <i class="bx bx-info-circle me-1"></i>
                                Jam selesai harus lebih besar dari jam mulai
                            </div>
                        </div>

                        {{-- Ruangan --}}
                        <div class="col-12">
                            <label for="ruangan" class="form-label fw-semibold">
                                <i class="bx bx-door-open me-1"></i> Ruangan
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('ruangan') is-invalid @enderror"
                                id="ruangan" name="ruangan" value="{{ old('ruangan') }}"
                                placeholder="Contoh: Ruang Sidang 1" maxlength="100" required>
                            @error('ruangan')
                                <div class="invalid-feedback">
                                    <i class="bx bx-error-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                <i class="bx bx-info-circle me-1"></i>
                                Maksimal 100 karakter
                            </div>
                        </div>
                    </div>

                    {{-- Preview Jadwal --}}
                    <div class="mt-4 p-3 bg-light rounded border" id="schedulePreview" style="display: none;">
                        <h6 class="mb-3">
                            <i class="bx bx-show me-1"></i> Preview Jadwal
                        </h6>
                        <div class="row g-2 small">
                            <div class="col-4 text-muted">Tanggal:</div>
                            <div class="col-8 fw-medium" id="previewTanggal">-</div>

                            <div class="col-4 text-muted">Waktu:</div>
                            <div class="col-8 fw-medium" id="previewWaktu">-</div>

                            <div class="col-4 text-muted">Ruangan:</div>
                            <div class="col-8 fw-medium" id="previewRuangan">-</div>
                        </div>
                    </div>

                    {{-- Warning Bentrok (akan muncul via JS jika ada) --}}
                    <div class="alert alert-warning mt-3" id="bentrokWarning" style="display: none;">
                        <div class="d-flex">
                            <i class="bx bx-error-circle fs-4 me-2"></i>
                            <div>
                                <strong>Peringatan!</strong>
                                <p class="mb-0 small" id="bentrokMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveScheduleBtn">
                        <i class="bx bx-send me-1"></i> Simpan & Kirim Undangan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scheduleModal = document.getElementById('scheduleModal');
            const scheduleForm = document.getElementById('scheduleForm');
            const saveScheduleBtn = document.getElementById('saveScheduleBtn');
            const schedulePreview = document.getElementById('schedulePreview');
            const bentrokWarning = document.getElementById('bentrokWarning');

            // Elements
            const tanggalInput = document.getElementById('tanggal');
            const jamMulaiInput = document.getElementById('jam_mulai');
            const jamSelesaiInput = document.getElementById('jam_selesai');
            const ruanganInput = document.getElementById('ruangan');

            // Preview elements
            const previewTanggal = document.getElementById('previewTanggal');
            const previewWaktu = document.getElementById('previewWaktu');
            const previewRuangan = document.getElementById('previewRuangan');

            if (!scheduleModal) {
                console.error('❌ Modal #scheduleModal not found!');
                return;
            }

            // Initialize modal when shown
            scheduleModal.addEventListener('shown.bs.modal', function(event) {
                const button = event.relatedTarget;

                if (button) {
                    // Get data from button
                    const jadwalId = button.getAttribute('data-jadwal-id');
                    const mahasiswaNama = button.getAttribute('data-mahasiswa-nama');
                    const mahasiswaNim = button.getAttribute('data-mahasiswa-nim');
                    const mahasiswaJudul = button.getAttribute('data-mahasiswa-judul');

                    // Populate modal data
                    document.getElementById('modalMahasiswaNama').textContent = mahasiswaNama || '-';
                    document.getElementById('modalMahasiswaNim').textContent = mahasiswaNim || '-';
                    document.getElementById('modalMahasiswaJudul').textContent = mahasiswaJudul || '-';

                    // Set form action
                    const formAction = `{{ url('admin/jadwal-seminar-proposal') }}/${jadwalId}`;
                    scheduleForm.setAttribute('action', formAction);
                }

                // Reset preview
                schedulePreview.style.display = 'none';
                bentrokWarning.style.display = 'none';
            });

            // Live preview
            function updatePreview() {
                const tanggal = tanggalInput.value;
                const jamMulai = jamMulaiInput.value;
                const jamSelesai = jamSelesaiInput.value;
                const ruangan = ruanganInput.value;

                if (tanggal && jamMulai && jamSelesai && ruangan) {
                    // Format tanggal
                    const dateObj = new Date(tanggal);
                    const options = {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    };
                    const formattedDate = dateObj.toLocaleDateString('id-ID', options);

                    previewTanggal.textContent = formattedDate;
                    previewWaktu.textContent = `${jamMulai} - ${jamSelesai} WITA`;
                    previewRuangan.textContent = ruangan;

                    schedulePreview.style.display = 'block';

                    // Optional: Check bentrok via AJAX
                    checkBentrokRuangan(tanggal, jamMulai, jamSelesai, ruangan);
                } else {
                    schedulePreview.style.display = 'none';
                }
            }

            // Add event listeners
            tanggalInput.addEventListener('change', updatePreview);
            jamMulaiInput.addEventListener('change', updatePreview);
            jamSelesaiInput.addEventListener('change', updatePreview);
            ruanganInput.addEventListener('input', updatePreview);

            // Validate jam selesai > jam mulai
            jamSelesaiInput.addEventListener('change', function() {
                if (jamMulaiInput.value && jamSelesaiInput.value) {
                    if (jamSelesaiInput.value <= jamMulaiInput.value) {
                        jamSelesaiInput.setCustomValidity('Jam selesai harus lebih besar dari jam mulai');
                        jamSelesaiInput.classList.add('is-invalid');
                    } else {
                        jamSelesaiInput.setCustomValidity('');
                        jamSelesaiInput.classList.remove('is-invalid');
                    }
                }
            });

            // Check bentrok ruangan (optional)
            function checkBentrokRuangan(tanggal, jamMulai, jamSelesai, ruangan) {
                // TODO: Implement AJAX check if needed
                // For now, hide warning
                bentrokWarning.style.display = 'none';
            }

            // Form submission
            scheduleForm.addEventListener('submit', function(e) {
                // Disable button to prevent double submit
                saveScheduleBtn.disabled = true;
                saveScheduleBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';
            });

            // Reset modal on close
            scheduleModal.addEventListener('hidden.bs.modal', function() {
                scheduleForm.reset();
                schedulePreview.style.display = 'none';
                bentrokWarning.style.display = 'none';

                // Reset button
                saveScheduleBtn.disabled = false;
                saveScheduleBtn.innerHTML = '<i class="bx bx-send me-1"></i> Simpan & Kirim Undangan';

                // Clear validation
                scheduleForm.classList.remove('was-validated');
                const invalidInputs = scheduleForm.querySelectorAll('.is-invalid');
                invalidInputs.forEach(input => input.classList.remove('is-invalid'));
            });
        });
    </script>
@endpush
