<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary bg-gradient p-3">
                <h5 class="modal-title text-white" id="scheduleModalLabel">
                    <i class="bx bx-calendar-event me-2"></i>Penjadwalan Seminar Proposal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="scheduleForm" method="POST">
                @csrf
                @method('POST')
                <div class="modal-body">
                    {{-- Info Section --}}
                    <div class="alert alert-info border-0 shadow-sm mb-4">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <i class="bx bx-info-circle fs-3 me-2"></i>
                            </div>
                            <div class="flex-grow-1">
                                <strong class="d-block mb-1">Informasi Penting</strong>
                                <p class="mb-0 small">Setelah jadwal disimpan, sistem akan otomatis mengirimkan undangan
                                    seminar proposal ke <strong>Dosen Pembimbing</strong> dan <strong>Dosen
                                        Pembahas</strong> via email.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Mahasiswa Info Card --}}
                    <div class="card border-primary border-opacity-25 shadow-sm mb-4">
                        <div class="card-header bg-primary bg-opacity-10 border-0">
                            <h6 class="mb-0 text-primary fw-semibold">
                                <i class="bx bx-user me-2"></i>Data Mahasiswa
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless table-sm mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted" style="width: 30%;">
                                                <i class="bx bx-user-circle me-1"></i>Nama
                                            </td>
                                            <td class="text-muted" style="width: 5%;">:</td>
                                            <td class="fw-semibold" id="modalMahasiswaNama">-</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">
                                                <i class="bx bx-id-card me-1"></i>NIM
                                            </td>
                                            <td class="text-muted">:</td>
                                            <td class="fw-semibold" id="modalMahasiswaNim">-</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted align-top">
                                                <i class="bx bx-book-content me-1"></i>Judul Skripsi
                                            </td>
                                            <td class="text-muted align-top">:</td>
                                            <td class="fw-medium lh-base" id="modalMahasiswaJudul">-</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">
                                                <i class="bx bx-file me-1"></i>Status SK
                                            </td>
                                            <td class="text-muted">:</td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <i class="bx bx-check-circle me-1"></i>Sudah Upload
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Form Inputs --}}
                    <div class="card border shadow-sm">
                        <div class="card-header bg-light border-0 p-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="bx bx-calendar-plus me-2"></i>Detail Penjadwalan
                            </h6>
                        </div>
                        <div class="card-body mt-3">
                            <div class="row g-3">
                                {{-- Tanggal Ujian --}}
                                <div class="col-12">
                                    <label for="tanggal" class="form-label fw-semibold">
                                        <i class="bx bx-calendar text-primary me-1"></i>Tanggal Ujian
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                        class="form-control form-control-lg @error('tanggal') is-invalid @enderror"
                                        id="tanggal" name="tanggal" value="{{ old('tanggal') }}"
                                        min="{{ date('Y-m-d') }}" required>
                                    @error('tanggal')
                                        <div class="invalid-feedback">
                                            <i class="bx bx-error-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="bx bx-info-circle me-1"></i>
                                        Pilih tanggal pelaksanaan seminar proposal (minimal hari ini)
                                    </div>
                                </div>

                                {{-- Jam Mulai --}}
                                <div class="col-md-6">
                                    <label for="jam_mulai" class="form-label fw-semibold">
                                        <i class="bx bx-time text-success me-1"></i>Jam Mulai
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="time"
                                        class="form-control form-control-lg @error('jam_mulai') is-invalid @enderror"
                                        id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai', '09:00') }}"
                                        required>
                                    @error('jam_mulai')
                                        <div class="invalid-feedback">
                                            <i class="bx bx-error-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="bx bx-info-circle me-1"></i>Format: HH:MM (WITA)
                                    </div>
                                </div>

                                {{-- Jam Selesai --}}
                                <div class="col-md-6">
                                    <label for="jam_selesai" class="form-label fw-semibold">
                                        <i class="bx bx-time-five text-danger me-1"></i>Jam Selesai
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="time"
                                        class="form-control form-control-lg @error('jam_selesai') is-invalid @enderror"
                                        id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai', '15:00') }}"
                                        required>
                                    @error('jam_selesai')
                                        <div class="invalid-feedback">
                                            <i class="bx bx-error-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="bx bx-info-circle me-1"></i>Harus lebih besar dari jam mulai
                                    </div>
                                </div>

                                {{-- Ruangan --}}
                                <div class="col-12">
                                    <label for="ruangan" class="form-label fw-semibold">
                                        <i class="bx bx-door-open text-warning me-1"></i>Ruangan
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control form-control-lg @error('ruangan') is-invalid @enderror"
                                        id="ruangan" name="ruangan"
                                        value="{{ old('ruangan', 'Ruangan Ujian Prodi Teknik Informatika Unima') }}"
                                        placeholder="Contoh: Ruangan Ujian Prodi Teknik Informatika Unima"
                                        maxlength="100" required>
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
                        </div>
                    </div>

                    {{-- Preview Jadwal --}}
                    <div class="mt-4 p-4 bg-light border border-2 border-primary border-opacity-25 rounded-3 shadow-sm"
                        id="schedulePreview" style="display: none;">
                        <h6 class="mb-3 text-primary fw-semibold border-bottom border-primary border-opacity-25 pb-2">
                            <i class="bx bx-show me-2"></i>Preview Jadwal Seminar
                        </h6>
                        <div class="row-column g-3">
                            <div class="col-md-4">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="bg-primary bg-opacity-10 rounded p-2">
                                            <i class="bx bx-calendar text-primary fs-4"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <small class="text-muted d-block mb-1">Tanggal</small>
                                        <strong class="d-block" id="previewTanggal">-</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="bg-success bg-opacity-10 rounded p-2">
                                            <i class="bx bx-time text-success fs-4"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <small class="text-muted d-block mb-1">Waktu</small>
                                        <strong class="d-block" id="previewWaktu">-</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="bg-warning bg-opacity-10 rounded p-2">
                                            <i class="bx bx-door-open text-warning fs-4"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <small class="text-muted d-block mb-1">Ruangan</small>
                                        <strong class="d-block text-truncate" id="previewRuangan"
                                            title="">-</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Warning Bentrok --}}
                    <div class="alert alert-warning border-0 shadow-sm mt-3 d-none" id="bentrokWarning">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <i class="bx bx-error-circle fs-3 me-2"></i>
                            </div>
                            <div class="flex-grow-1">
                                <strong class="d-block mb-1">Peringatan Bentrok Jadwal!</strong>
                                <p class="mb-0 small" id="bentrokMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveScheduleBtn">
                        <i class="bx bx-send me-1"></i>Simpan & Kirim Undangan
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

            // Form Elements
            const tanggalInput = document.getElementById('tanggal');
            const jamMulaiInput = document.getElementById('jam_mulai');
            const jamSelesaiInput = document.getElementById('jam_selesai');
            const ruanganInput = document.getElementById('ruangan');

            // Preview Elements
            const previewTanggal = document.getElementById('previewTanggal');
            const previewWaktu = document.getElementById('previewWaktu');
            const previewRuangan = document.getElementById('previewRuangan');

            if (!scheduleModal) {
                console.error('❌ Modal #scheduleModal not found!');
                return;
            }

            /**
             * Strip HTML tags from string
             */
            function stripHtml(html) {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                return tempDiv.textContent || tempDiv.innerText || '';
            }

            /**
             * Initialize modal when shown
             */
            scheduleModal.addEventListener('shown.bs.modal', function(event) {
                const button = event.relatedTarget;

                if (button) {
                    // Get data from button
                    const jadwalId = button.getAttribute('data-jadwal-id');
                    const mahasiswaNama = button.getAttribute('data-mahasiswa-nama') || '-';
                    const mahasiswaNim = button.getAttribute('data-mahasiswa-nim') || '-';
                    const mahasiswaJudulRaw = button.getAttribute('data-mahasiswa-judul') || '-';

                    // Strip HTML from judul (karena menggunakan markdown/HTML)
                    const mahasiswaJudul = stripHtml(mahasiswaJudulRaw);

                    // Populate modal data
                    document.getElementById('modalMahasiswaNama').textContent = mahasiswaNama;
                    document.getElementById('modalMahasiswaNim').textContent = mahasiswaNim;
                    document.getElementById('modalMahasiswaJudul').textContent = mahasiswaJudul;

                    // Set form action
                    const formAction = `{{ url('admin/jadwal-seminar-proposal') }}/${jadwalId}`;
                    scheduleForm.setAttribute('action', formAction);
                }

                // Reset preview and warning
                schedulePreview.style.display = 'none';
                bentrokWarning.classList.add('d-none');

                // Auto-trigger preview if all fields are filled (for edit mode)
                updatePreview();

                // Focus on first input
                setTimeout(() => tanggalInput.focus(), 300);
            });

            /**
             * Format tanggal ke Bahasa Indonesia
             */
            function formatTanggalIndonesia(dateString) {
                const date = new Date(dateString);
                const options = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };
                return date.toLocaleDateString('id-ID', options);
            }

            /**
             * Update preview jadwal
             */
            function updatePreview() {
                const tanggal = tanggalInput.value;
                const jamMulai = jamMulaiInput.value;
                const jamSelesai = jamSelesaiInput.value;
                const ruangan = ruanganInput.value;

                if (tanggal && jamMulai && jamSelesai && ruangan) {
                    // Format tanggal
                    const formattedDate = formatTanggalIndonesia(tanggal);

                    // Update preview
                    previewTanggal.textContent = formattedDate;
                    previewWaktu.textContent = `${jamMulai} - ${jamSelesai} WITA`;
                    previewRuangan.textContent = ruangan;
                    previewRuangan.setAttribute('title', ruangan);

                    // Show preview with animation
                    schedulePreview.style.display = 'block';

                    // Check bentrok (optional)
                    checkBentrokRuangan(tanggal, jamMulai, jamSelesai, ruangan);
                } else {
                    // Hide preview if incomplete
                    schedulePreview.style.display = 'none';
                }
            }

            /**
             * Validate jam selesai > jam mulai
             */
            function validateJam() {
                const jamMulai = jamMulaiInput.value;
                const jamSelesai = jamSelesaiInput.value;

                if (jamMulai && jamSelesai) {
                    if (jamSelesai <= jamMulai) {
                        jamSelesaiInput.setCustomValidity('Jam selesai harus lebih besar dari jam mulai');
                        jamSelesaiInput.classList.add('is-invalid');

                        // Show error message
                        const feedback = jamSelesaiInput.parentElement.querySelector('.invalid-feedback');
                        if (feedback) {
                            feedback.textContent = 'Jam selesai harus lebih besar dari jam mulai';
                        }
                    } else {
                        jamSelesaiInput.setCustomValidity('');
                        jamSelesaiInput.classList.remove('is-invalid');
                    }
                }
            }

            /**
             * Check bentrok ruangan (optional - implement AJAX if needed)
             */
            function checkBentrokRuangan(tanggal, jamMulai, jamSelesai, ruangan) {
                // TODO: Implement AJAX check if needed
                // For now, just hide warning
                bentrokWarning.classList.add('d-none');

                // Example AJAX implementation:
                /*
                fetch(`{{ url('admin/jadwal-seminar-proposal/check-bentrok') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        tanggal: tanggal,
                        jam_mulai: jamMulai,
                        jam_selesai: jamSelesai,
                        ruangan: ruangan
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.bentrok) {
                        bentrokWarning.classList.remove('d-none');
                        document.getElementById('bentrokMessage').textContent = data.message;
                    } else {
                        bentrokWarning.classList.add('d-none');
                    }
                })
                .catch(error => console.error('Error checking bentrok:', error));
                */
            }

            // Add event listeners for live preview
            tanggalInput.addEventListener('change', updatePreview);
            jamMulaiInput.addEventListener('change', function() {
                validateJam();
                updatePreview();
            });
            jamSelesaiInput.addEventListener('change', function() {
                validateJam();
                updatePreview();
            });
            ruanganInput.addEventListener('input', updatePreview);

            /**
             * Form submission handler
             */
            scheduleForm.addEventListener('submit', function(e) {
                // Validate jam before submit
                validateJam();

                // Check if form is valid
                if (!scheduleForm.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    scheduleForm.classList.add('was-validated');
                    return false;
                }

                // Disable button to prevent double submit
                saveScheduleBtn.disabled = true;
                saveScheduleBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Menyimpan & Mengirim Undangan...
                `;

                // Show loading state on inputs
                const inputs = scheduleForm.querySelectorAll('input, select, textarea');
                inputs.forEach(input => input.disabled = true);
            });

            /**
             * Reset modal on close
             */
            scheduleModal.addEventListener('hidden.bs.modal', function() {
                // Reset form
                scheduleForm.reset();

                // Reset to default values
                jamMulaiInput.value = '09:00';
                jamSelesaiInput.value = '15:00';
                ruanganInput.value = 'Ruangan Ujian Prodi Teknik Informatika Unima';

                // Hide preview and warning
                schedulePreview.style.display = 'none';
                bentrokWarning.classList.add('d-none');

                // Reset button
                saveScheduleBtn.disabled = false;
                saveScheduleBtn.innerHTML = '<i class="bx bx-send me-1"></i>Simpan & Kirim Undangan';

                // Clear validation
                scheduleForm.classList.remove('was-validated');
                const invalidInputs = scheduleForm.querySelectorAll('.is-invalid');
                invalidInputs.forEach(input => {
                    input.classList.remove('is-invalid');
                    input.setCustomValidity('');
                });

                // Enable all inputs
                const inputs = scheduleForm.querySelectorAll('input, select, textarea');
                inputs.forEach(input => input.disabled = false);
            });
        });
    </script>
@endpush
