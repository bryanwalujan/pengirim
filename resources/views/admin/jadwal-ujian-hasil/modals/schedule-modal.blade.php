{{-- filepath: resources/views/admin/jadwal-ujian-hasil/modals/schedule-modal.blade.php --}}
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning bg-gradient p-3">
                <h5 class="modal-title text-white" id="scheduleModalLabel">
                    <i class="bx bx-calendar-event me-2"></i>Penjadwalan Ujian Hasil
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
                    <div class="alert alert-warning border-0 shadow-sm mb-4">
                        <div class="d-flex align-items-start">
                            <i class="bx bx-info-circle fs-3 me-2"></i>
                            <div class="flex-grow-1">
                                <strong>Informasi Penting</strong>
                                <p class="mb-1 small mt-1">
                                    • Setelah jadwal disimpan, sistem akan otomatis mengirimkan undangan ke Dosen
                                    Penguji via email.
                                </p>
                                <p class="mb-0 small">
                                    • Batch scheduling diperbolehkan: <strong>Multiple mahasiswa boleh ujian di hari,
                                        waktu, dan ruangan yang sama.</strong>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Mahasiswa Info --}}
                    <div class="card border-warning border-opacity-25 shadow-sm mb-4">
                        <div class="card-header bg-warning bg-opacity-10 border-0 p-2">
                            <h6 class="mb-0 text-warning fw-semibold">
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
                                    <label for="tanggal_ujian" class="form-label fw-semibold">
                                        <i class="bx bx-calendar text-warning me-1"></i>Tanggal Ujian
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control form-control-lg" id="tanggal_ujian"
                                        name="tanggal_ujian" min="{{ date('Y-m-d') }}" required>
                                </div>

                                {{-- Jam Mulai --}}
                                <div class="col-md-6">
                                    <label for="waktu_mulai" class="form-label fw-semibold">
                                        <i class="bx bx-time text-success me-1"></i>Jam Mulai
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="time" class="form-control form-control-lg" id="waktu_mulai"
                                        name="waktu_mulai" value="09:00" required>
                                </div>

                                {{-- Jam Selesai --}}
                                <div class="col-md-6">
                                    <label for="waktu_selesai" class="form-label fw-semibold">
                                        <i class="bx bx-time-five text-danger me-1"></i>Jam Selesai
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="time" class="form-control form-control-lg" id="waktu_selesai"
                                        name="waktu_selesai" value="15:00" required>
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
                                        name="ruangan" value="Gedung C Ruangan Ujian Prodi TI (Luring)" maxlength="100"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- BATCH INFO (Real-time) --}}
                    <div id="batchInfo" class="mt-4" style="display:none;">
                        <div class="alert alert-warning border-0 shadow-sm">
                            <div class="d-flex align-items-start">
                                <i class="bx bx-info-circle fs-4 me-2"></i>
                                <div class="flex-grow-1">
                                    <h6 class="mb-2 fw-semibold">
                                        <i class="bx bx-group me-1"></i>Info Batch Scheduling
                                    </h6>
                                    <div id="batchInfoContent">
                                        <div class="spinner-border spinner-border-sm text-warning me-2"
                                            role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <small>Memuat data...</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Preview --}}
                    <div id="schedulePreview" class="mt-4 p-4 bg-light border border-2 border-warning rounded-3"
                        style="display:none;">
                        <h6 class="mb-3 text-warning fw-semibold">
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
                    <button type="submit" class="btn btn-warning" id="saveScheduleBtn">
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
            const tanggalInput = document.getElementById('tanggal_ujian');
            const jamMulaiInput = document.getElementById('waktu_mulai');
            const jamSelesaiInput = document.getElementById('waktu_selesai');
            const ruanganInput = document.getElementById('ruangan');
            const batchInfo = document.getElementById('batchInfo');
            const batchInfoContent = document.getElementById('batchInfoContent');

            // GET BATCH INFO (Real-time AJAX)
            let batchInfoTimeout;

            function getBatchInfo() {
                const tanggal_ujian = tanggalInput.value;
                const jamMulai = jamMulaiInput.value;
                const jamSelesai = jamSelesaiInput.value;
                const ruangan = ruanganInput.value;

                if (!tanggal_ujian) {
                    batchInfo.style.display = 'none';
                    return;
                }

                // Show loading
                batchInfo.style.display = 'block';
                batchInfoContent.innerHTML = `
                    <div class="spinner-border spinner-border-sm text-warning me-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <small>Memuat data batch...</small>
                `;

                // Debounce AJAX call
                clearTimeout(batchInfoTimeout);
                batchInfoTimeout = setTimeout(() => {
                    fetch(`{{ route('admin.jadwal-ujian-hasil.get-batch-info') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                tanggal_ujian: tanggal_ujian,
                                waktu_mulai: jamMulai || null,
                                waktu_selesai: jamSelesai || null,
                                ruangan: ruangan || null
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            // HANDLE ERROR RESPONSE
                            if (!data.success && data.error) {
                                batchInfoContent.innerHTML = `
                                    <p class="mb-0 small text-danger">
                                        <i class="bx bx-error-circle me-1"></i>
                                        Error: ${data.error}
                                    </p>
                                `;
                                return;
                            }

                            // HANDLE EMPTY DATA
                            if (!data.scheduled_count_total || data.scheduled_count_total === 0) {
                                batchInfoContent.innerHTML = `
                                    <p class="mb-0 small">
                                        <i class="bx bx-calendar-check me-1"></i>
                                        Belum ada jadwal pada <strong>${data.tanggal_formatted || 'tanggal yang dipilih'}</strong>
                                    </p>
                                `;
                                return;
                            }

                            // BUILD HTML WITH PROPER DATA
                            let html = `<ul class="mb-0 small">`;
                            html +=
                                `<li><strong>${data.scheduled_count_total} mahasiswa</strong> terjadwal pada <strong>${data.tanggal_formatted}</strong></li>`;

                            if (jamMulai && jamSelesai && data.scheduled_count_same_time > 0) {
                                html +=
                                    `<li><strong>${data.scheduled_count_same_time} mahasiswa</strong> pada jam <strong>${jamMulai} - ${jamSelesai}</strong></li>`;
                            }

                            html += `</ul>`;

                            // SHOW GROUPED SCHEDULES
                            if (data.schedules_grouped && data.schedules_grouped.length > 0) {
                                html += `<hr class="my-2">`;
                                html +=
                                    `<small class="text-muted d-block mb-2"><strong>Detail Jadwal Hari Ini:</strong></small>`;
                                html += `<div class="small">`;

                                data.schedules_grouped.forEach(group => {
                                    html += `<div class="mb-2 p-2 bg-light rounded">`;
                                    html += `<div class="fw-semibold text-warning mb-1">`;
                                    html += `<i class="bx bx-time-five me-1"></i>${group.slot}`;
                                    html +=
                                        ` <span class="badge bg-warning">${group.count} mahasiswa</span>`;
                                    html += `</div>`;
                                    html += `<div class="ms-3">`;

                                    group.mahasiswa.forEach((mhs, idx) => {
                                        html +=
                                            `<small>${idx + 1}. ${mhs.nama} (${mhs.nim})`;
                                        if (mhs.ruangan) {
                                            html +=
                                                ` - <span class="text-muted">${mhs.ruangan}</span>`;
                                        }
                                        html += `</small><br>`;
                                    });

                                    html += `</div>`;
                                    html += `</div>`;
                                });

                                html += `</div>`;
                            }

                            batchInfoContent.innerHTML = html;
                        })
                        .catch(error => {
                            console.error('Error getting batch info:', error);
                            batchInfoContent.innerHTML = `
                                <p class="mb-0 small text-danger">
                                    <i class="bx bx-error-circle me-1"></i>
                                    Gagal memuat data batch: ${error.message}
                                </p>
                            `;
                        });
                }, 500);
            }

            // Attach event listeners
            [tanggalInput, jamMulaiInput, jamSelesaiInput].forEach(input => {
                input?.addEventListener('change', getBatchInfo);
                input?.addEventListener('blur', getBatchInfo);
            });

            // Auto-refresh CSRF token
            scheduleModal?.addEventListener('show.bs.modal', function() {
                fetch('{{ route('admin.jadwal-ujian-hasil.index') }}', {
                    method: 'HEAD',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(() => {
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

                const jadwalId = button.getAttribute('data-jadwal-id');
                const mahasiswaNama = button.getAttribute('data-mahasiswa-nama') || '-';
                const mahasiswaNim = button.getAttribute('data-mahasiswa-nim') || '-';
                const mahasiswaJudul = stripHtml(button.getAttribute('data-mahasiswa-judul') || '-');

                document.getElementById('modalMahasiswaNama').textContent = mahasiswaNama;
                document.getElementById('modalMahasiswaNim').textContent = mahasiswaNim;
                document.getElementById('modalMahasiswaJudul').textContent = mahasiswaJudul;

                // Set form action
                const formAction = `{{ url('admin/jadwal-ujian-hasil') }}/${jadwalId}/store`;
                scheduleForm.setAttribute('action', formAction);

                document.getElementById('jadwalIdInput').value = jadwalId;

                // Load existing data
                const tanggal_ujian = button.getAttribute('data-tanggal_ujian');
                const jamMulai = button.getAttribute('data-jam-mulai');
                const jamSelesai = button.getAttribute('data-jam-selesai');
                const ruangan = button.getAttribute('data-ruangan');

                if (tanggal_ujian) document.getElementById('tanggal_ujian').value = tanggal_ujian;
                if (jamMulai) document.getElementById('waktu_mulai').value = jamMulai;
                if (jamSelesai) document.getElementById('waktu_selesai').value = jamSelesai;
                if (ruangan) document.getElementById('ruangan').value = ruangan;

                updatePreview();
                getBatchInfo();
            });

            // Form submit
            scheduleForm?.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!validateJam()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validasi Gagal',
                        text: 'Jam selesai harus lebih besar dari jam mulai!',
                        confirmButtonColor: '#ff9f43',
                        customClass: {
                            confirmButton: 'btn btn-warning'
                        },
                        buttonsStyling: false
                    });
                    return false;
                }

                const csrfToken = this.querySelector('input[name="_token"]')?.value;
                if (!csrfToken) {
                    csrfErrorAlert.classList.remove('d-none');
                    setTimeout(() => location.reload(), 3000);
                    return false;
                }

                const submitBtn = document.getElementById('saveScheduleBtn');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

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
                            csrfErrorAlert.classList.remove('d-none');
                            setTimeout(() => location.reload(), 3000);
                            throw new Error('CSRF token mismatch');
                        }

                        if (!response.ok) {
                            return response.text().then(text => {
                                throw new Error(text || 'Network response was not ok');
                            });
                        }

                        // Redirect to index with success
                        window.location.href =
                            '{{ route('admin.jadwal-ujian-hasil.index', ['status' => 'dijadwalkan']) }}';
                    })
                    .catch(error => {
                        console.error('Error:', error);

                        if (!csrfErrorAlert.classList.contains('d-none')) {
                            return;
                        }

                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;

                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan!',
                            html: `
                    <div class="text-start">
                        <p class="mb-2">Gagal menyimpan jadwal ujian hasil.</p>
                        <div class="alert alert-danger border-0 mb-0">
                            <i class="bx bx-error-circle me-1"></i>
                            <small>${error.message || 'Silakan coba lagi atau hubungi administrator.'}</small>
                        </div>
                    </div>
                `,
                            confirmButtonColor: '#d33',
                            confirmButtonText: '<i class="bx bx-x me-1"></i> Tutup',
                            customClass: {
                                confirmButton: 'btn btn-danger'
                            },
                            buttonsStyling: false
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
                const jamMulai = document.getElementById('waktu_mulai').value;
                const jamSelesai = document.getElementById('waktu_selesai').value;
                const errorDiv = document.getElementById('jamSelesaiError');

                if (jamSelesai <= jamMulai) {
                    errorDiv.style.display = 'block';
                    document.getElementById('waktu_selesai').classList.add('is-invalid');
                    return false;
                }

                errorDiv.style.display = 'none';
                document.getElementById('waktu_selesai').classList.remove('is-invalid');
                return true;
            }

            function updatePreview() {
                const tanggal_ujian = document.getElementById('tanggal_ujian').value;
                const jamMulai = document.getElementById('waktu_mulai').value;
                const jamSelesai = document.getElementById('waktu_selesai').value;
                const ruangan = document.getElementById('ruangan').value;
                const preview = document.getElementById('schedulePreview');

                if (tanggal_ujian && jamMulai && jamSelesai && ruangan) {
                    const date = new Date(tanggal_ujian);
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

            ['tanggal_ujian', 'waktu_mulai', 'waktu_selesai', 'ruangan'].forEach(id => {
                document.getElementById(id)?.addEventListener('change', updatePreview);
            });

            document.getElementById('waktu_selesai')?.addEventListener('change', validateJam);
        });
    </script>
@endpush
