{{-- filepath: resources/views/admin/jadwal-seminar-proposal/modals/schedule-modal.blade.php --}}
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary bg-gradient p-3">
                <h5 class="modal-title text-white" id="scheduleModalLabel">
                    <i class="bx bx-calendar-event me-2"></i>Penjadwalan Seminar Proposal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="scheduleForm" method="POST" action="">
                @csrf
                <input type="hidden" name="jadwal_id" id="jadwalIdInput">

                <div class="modal-body">
                    {{-- Alert untuk CSRF Error --}}
                    <div class="alert alert-danger d-none" id="csrfErrorAlert">
                        <i class="bx bx-error-circle me-2"></i>
                        <strong>Session Expired!</strong> Halaman akan di-refresh dalam 3 detik...
                    </div>

                    {{-- Info Section --}}
                    <div class="alert alert-info border-0 shadow-sm mb-4">
                        <div class="d-flex align-items-start">
                            <i class="bx bx-info-circle fs-3 me-2"></i>
                            <div class="flex-grow-1">
                                <strong>Informasi Penting</strong>
                                <p class="mb-0 small mt-1">Setelah jadwal disimpan, sistem akan otomatis mengirimkan
                                    undangan ke Dosen Pembimbing dan Dosen Pembahas via email.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Mahasiswa Info --}}
                    <div class="card border-primary border-opacity-25 shadow-sm mb-4">
                        <div class="card-header bg-primary bg-opacity-10 border-0 p-2">
                            <h6 class="mb-0 text-primary fw-semibold">
                                <i class="bx bx-user me-2"></i>Data Mahasiswa
                            </h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="text-muted" width="30%"><i class="bx bx-user-circle me-1"></i>Nama
                                    </td>
                                    <td width="5%">:</td>
                                    <td class="fw-semibold" id="modalMahasiswaNama">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted"><i class="bx bx-id-card me-1"></i>NIM</td>
                                    <td>:</td>
                                    <td class="fw-semibold" id="modalMahasiswaNim">-</td>
                                </tr>
                                <tr>
                                    <td class="text-muted align-top"><i class="bx bx-book-content me-1"></i>Judul</td>
                                    <td class="align-top">:</td>
                                    <td class="fw-medium" id="modalMahasiswaJudul">-</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Form Inputs --}}
                    <div class="card border shadow-sm">
                        <div class="card-header bg-light border-0 p-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="bx bx-calendar-plus me-2"></i>Detail Penjadwalan
                            </h6>
                        </div>
                        <div class="card-body mt-2">
                            <div class="row g-3">
                                {{-- Tanggal --}}
                                <div class="col-12">
                                    <label for="tanggal" class="form-label fw-semibold">
                                        <i class="bx bx-calendar text-primary me-1"></i>Tanggal Ujian
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control form-control-lg" id="tanggal"
                                        name="tanggal" min="{{ date('Y-m-d') }}" required>
                                </div>

                                {{-- Jam Mulai --}}
                                <div class="col-md-6">
                                    <label for="jam_mulai" class="form-label fw-semibold">
                                        <i class="bx bx-time text-success me-1"></i>Jam Mulai
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="time" class="form-control form-control-lg" id="jam_mulai"
                                        name="jam_mulai" value="09:00" required>
                                </div>

                                {{-- Jam Selesai --}}
                                <div class="col-md-6">
                                    <label for="jam_selesai" class="form-label fw-semibold">
                                        <i class="bx bx-time-five text-danger me-1"></i>Jam Selesai
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="time" class="form-control form-control-lg" id="jam_selesai"
                                        name="jam_selesai" value="15:00" required>
                                    <div class="invalid-feedback" id="jamSelesaiError" style="display:none;">
                                        Jam selesai harus lebih besar dari jam mulai
                                    </div>
                                </div>

                                {{-- Ruangan --}}
                                <div class="col-12">
                                    <label for="ruangan" class="form-label fw-semibold">
                                        <i class="bx bx-door-open text-warning me-1"></i>Ruangan
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form-control-lg" id="ruangan"
                                        name="ruangan" value="Ruangan Ujian Prodi Teknik Informatika Unima"
                                        maxlength="100" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Preview --}}
                    <div id="schedulePreview" class="mt-4 p-4 bg-light border border-2 border-primary rounded-3"
                        style="display:none;">
                        <h6 class="mb-3 text-primary fw-semibold">
                            <i class="bx bx-show me-2"></i>Preview Jadwal
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Tanggal</small>
                                <strong id="previewTanggal">-</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Waktu</small>
                                <strong id="previewWaktu">-</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Ruangan</small>
                                <strong id="previewRuangan">-</strong>
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
            const csrfErrorAlert = document.getElementById('csrfErrorAlert');

            // ✅ SOLUSI 1: Auto-refresh CSRF token setiap kali modal dibuka
            scheduleModal?.addEventListener('show.bs.modal', function() {
                fetch('{{ route('admin.jadwal-seminar-proposal.index') }}', {
                    method: 'HEAD',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(() => {
                    // Refresh CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    const csrfInput = scheduleForm.querySelector('input[name="_token"]');
                    if (csrfToken && csrfInput) {
                        csrfInput.value = csrfToken;
                    }
                }).catch(error => {
                    console.error('Failed to refresh CSRF token:', error);
                });
            });

            scheduleModal?.addEventListener('shown.bs.modal', function(event) {
                const button = event.relatedTarget;
                if (!button) return;

                // Get data attributes
                const jadwalId = button.getAttribute('data-jadwal-id');
                const mahasiswaNama = button.getAttribute('data-mahasiswa-nama') || '-';
                const mahasiswaNim = button.getAttribute('data-mahasiswa-nim') || '-';
                const mahasiswaJudul = stripHtml(button.getAttribute('data-mahasiswa-judul') || '-');

                // Populate modal
                document.getElementById('modalMahasiswaNama').textContent = mahasiswaNama;
                document.getElementById('modalMahasiswaNim').textContent = mahasiswaNim;
                document.getElementById('modalMahasiswaJudul').textContent = mahasiswaJudul;

                // ✅ SOLUSI 2: Set form action dengan route name helper
                const formAction = `{{ url('admin/jadwal-seminar-proposal') }}/${jadwalId}`;
                scheduleForm.setAttribute('action', formAction);
                document.getElementById('jadwalIdInput').value = jadwalId;

                console.log('✅ Form action:', formAction);
                console.log('✅ CSRF token:', document.querySelector('input[name="_token"]')?.value);

                // Load existing data (for edit)
                const tanggal = button.getAttribute('data-tanggal');
                const jamMulai = button.getAttribute('data-jam-mulai');
                const jamSelesai = button.getAttribute('data-jam-selesai');
                const ruangan = button.getAttribute('data-ruangan');

                if (tanggal) document.getElementById('tanggal').value = tanggal;
                if (jamMulai) document.getElementById('jam_mulai').value = jamMulai;
                if (jamSelesai) document.getElementById('jam_selesai').value = jamSelesai;
                if (ruangan) document.getElementById('ruangan').value = ruangan;

                updatePreview();
            });

            // ✅ SOLUSI 3: Form submit dengan error handling untuk CSRF
            scheduleForm?.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validate
                if (!validateJam()) {
                    return false;
                }

                // Get CSRF token
                const csrfToken = this.querySelector('input[name="_token"]')?.value;
                if (!csrfToken) {
                    csrfErrorAlert.classList.remove('d-none');
                    setTimeout(() => location.reload(), 3000);
                    return false;
                }

                // Disable button
                const submitBtn = document.getElementById('saveScheduleBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

                // Submit via AJAX to catch CSRF errors
                const formData = new FormData(this);

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (response.status === 419) {
                            // CSRF token mismatch
                            csrfErrorAlert.classList.remove('d-none');
                            setTimeout(() => location.reload(), 3000);
                            throw new Error('CSRF token mismatch');
                        }

                        if (!response.ok) {
                            return response.text().then(text => {
                                throw new Error(text || 'Network response was not ok');
                            });
                        }

                        // Success - redirect
                        window.location.href = response.url ||
                            '{{ route('admin.jadwal-seminar-proposal.index', ['status' => 'dijadwalkan']) }}';
                    })
                    .catch(error => {
                        console.error('Error:', error);

                        // Re-enable button if not CSRF error
                        if (!csrfErrorAlert.classList.contains('d-none')) {
                            return;
                        }

                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="bx bx-send me-1"></i>Simpan & Kirim Undangan';

                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: error.message || 'Gagal menyimpan jadwal. Silakan coba lagi.',
                            confirmButtonColor: '#d33'
                        });
                    });
            });

            // Helper functions
            function stripHtml(html) {
                const div = document.createElement('div');
                div.innerHTML = html;
                return div.textContent || div.innerText || '';
            }

            function validateJam() {
                const jamMulai = document.getElementById('jam_mulai').value;
                const jamSelesai = document.getElementById('jam_selesai').value;
                const errorDiv = document.getElementById('jamSelesaiError');

                if (jamSelesai <= jamMulai) {
                    errorDiv.style.display = 'block';
                    document.getElementById('jam_selesai').classList.add('is-invalid');
                    return false;
                }

                errorDiv.style.display = 'none';
                document.getElementById('jam_selesai').classList.remove('is-invalid');
                return true;
            }

            function updatePreview() {
                const tanggal = document.getElementById('tanggal').value;
                const jamMulai = document.getElementById('jam_mulai').value;
                const jamSelesai = document.getElementById('jam_selesai').value;
                const ruangan = document.getElementById('ruangan').value;
                const preview = document.getElementById('schedulePreview');

                if (tanggal && jamMulai && jamSelesai && ruangan) {
                    const date = new Date(tanggal);
                    const options = {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    };

                    document.getElementById('previewTanggal').textContent = date.toLocaleDateString('id-ID',
                        options);
                    document.getElementById('previewWaktu').textContent = `${jamMulai} - ${jamSelesai} WITA`;
                    document.getElementById('previewRuangan').textContent = ruangan;

                    preview.style.display = 'block';
                } else {
                    preview.style.display = 'none';
                }
            }

            // Live preview
            ['tanggal', 'jam_mulai', 'jam_selesai', 'ruangan'].forEach(id => {
                document.getElementById(id)?.addEventListener('change', updatePreview);
            });

            document.getElementById('jam_selesai')?.addEventListener('change', validateJam);
        });
    </script>
@endpush
