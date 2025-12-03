{{-- filepath: /c:/laragon/www/eservice-app/resources/views/admin/pendaftaran-seminar-proposal/modals/generate-surat.blade.php --}}
<div class="modal fade" id="generateSuratModal" tabindex="-1" aria-labelledby="generateSuratModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateSuratModalLabel">
                    <i class="bx bx-file me-2"></i>Generate Surat Usulan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.pendaftaran-seminar-proposal.generate-surat', $pendaftaran) }}" method="POST"
                id="generateSuratForm">
                @csrf
                <div class="modal-body">
                    {{-- Info Section --}}
                    <div class="alert alert-info mb-4">
                        <div class="d-flex">
                            <i class="bx bx-info-circle fs-4 me-2"></i>
                            <div>
                                <strong>Informasi</strong>
                                <p class="mb-0 small">Surat usulan akan digenerate dengan nomor surat yang ditentukan di
                                    bawah ini.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Nomor Surat Section --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Nomor Surat</label>

                        {{-- Radio Options --}}
                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="nomor_surat_type"
                                    id="nomorSuratAuto" value="auto" checked>
                                <label class="form-check-label" for="nomorSuratAuto">
                                    <strong>Otomatis</strong>
                                    <span class="text-muted d-block small">Sistem akan generate nomor surat secara
                                        otomatis</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="nomor_surat_type"
                                    id="nomorSuratCustom" value="custom">
                                <label class="form-check-label" for="nomorSuratCustom">
                                    <strong>Custom</strong>
                                    <span class="text-muted d-block small">Tentukan nomor surat sendiri</span>
                                </label>
                            </div>
                        </div>

                        {{-- Auto Nomor Preview --}}
                        <div id="autoNomorPreview" class="p-3 bg-lighter rounded mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block">Nomor Surat Berikutnya:</small>
                                    <code class="fs-6"
                                        id="nextNomorSurat">{{ $nomorSuratInfo['next_nomor'] ?? '-' }}</code>
                                </div>
                               
                            </div>
                            @if (isset($nomorSuratInfo['last_nomor']) && $nomorSuratInfo['last_nomor'])
                                <div class="mt-2 pt-2 border-top">
                                    <small class="text-muted">
                                        <i class="bx bx-history me-1"></i>
                                        Terakhir: <code>{{ $nomorSuratInfo['last_nomor'] }}</code>
                                    </small>
                                </div>
                            @endif
                        </div>

                        {{-- Custom Nomor Input --}}
                        <div id="customNomorSection" style="display: none;">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="customNomorSurat"
                                    name="custom_nomor_surat" placeholder="Masukkan nomor (1-4 digit)" maxlength="4"
                                    pattern="\d{1,4}">
                                <span
                                    class="input-group-text">/{{ $nomorSuratInfo['prefix'] ?? 'UN41.2/TI' }}/{{ date('Y') }}</span>
                                <button type="button" class="btn btn-outline-secondary" id="validateNomorBtn">
                                    <i class="bx bx-check"></i> Validasi
                                </button>
                            </div>
                            <div id="customNomorFeedback" class="small"></div>
                            <div id="customNomorPreview" class="mt-2 p-2 bg-lighter rounded" style="display: none;">
                                <small class="text-muted">Preview: </small>
                                <code id="customNomorPreviewText"></code>
                            </div>
                        </div>
                    </div>

                    {{-- Summary --}}
                    <div class="border rounded p-3 bg-light">
                        <h6 class="mb-3"><i class="bx bx-list-check me-1"></i> Ringkasan</h6>
                        <div class="row g-2 small">
                            <div class="col-5 text-muted">Mahasiswa:</div>
                            <div class="col-7 fw-medium">{{ $pendaftaran->user->name }}</div>

                            <div class="col-5 text-muted">NIM:</div>
                            <div class="col-7 fw-medium">{{ $pendaftaran->user->nim }}</div>

                            <div class="col-5 text-muted">Judul:</div>
                            <div class="col-7 fw-medium text-truncate" title="{{ $pendaftaran->judul_skripsi }}">
                                {{ Str::limit(strip_tags($pendaftaran->judul_skripsi, 50)) }}
                            </div>

                            <div class="col-5 text-muted">Pembahas:</div>
                            <div class="col-7 fw-medium">
                                {{ $pendaftaran->proposalPembahas->count() }} Dosen
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-success" id="generateSuratBtn">
                        <i class="bx bx-file me-1"></i> Generate Surat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for modal to be fully loaded
            const generateSuratModal = document.getElementById('generateSuratModal');

            if (!generateSuratModal) {
                console.error('❌ Modal #generateSuratModal not found!');
                return;
            }

            // Initialize when modal is shown
            generateSuratModal.addEventListener('shown.bs.modal', function() {
                console.log('✅ Modal loaded');
                initializeModal();
            });

            function initializeModal() {
                const nomorSuratAuto = document.getElementById('nomorSuratAuto');
                const nomorSuratCustom = document.getElementById('nomorSuratCustom');
                const autoNomorPreview = document.getElementById('autoNomorPreview');
                const customNomorSection = document.getElementById('customNomorSection');
                const customNomorInput = document.getElementById('customNomorSurat');
                const validateNomorBtn = document.getElementById('validateNomorBtn');
                const customNomorFeedback = document.getElementById('customNomorFeedback');
                const customNomorPreview = document.getElementById('customNomorPreview');
                const customNomorPreviewText = document.getElementById('customNomorPreviewText');
                const generateSuratBtn = document.getElementById('generateSuratBtn');
                const generateSuratForm = document.getElementById('generateSuratForm');

                let isCustomValid = false;


                // Toggle between auto and custom
                function toggleNomorType() {
                    if (nomorSuratCustom.checked) {
                        autoNomorPreview.style.display = 'none';
                        customNomorSection.style.display = 'block';
                        customNomorInput.required = true;
                    } else {
                        autoNomorPreview.style.display = 'block';
                        customNomorSection.style.display = 'none';
                        customNomorInput.required = false;
                        isCustomValid = false;
                        resetCustomValidation();
                    }
                }

                nomorSuratAuto.addEventListener('change', toggleNomorType);
                nomorSuratCustom.addEventListener('change', toggleNomorType);


                // Validate custom nomor
                function validateCustomNomor() {
                    const customNumber = customNomorInput.value.trim();

                    if (!customNumber) {
                        showFeedback('warning', 'Masukkan nomor surat terlebih dahulu.');
                        isCustomValid = false;
                        return;
                    }

                    if (!/^\d{1,4}$/.test(customNumber)) {
                        showFeedback('danger', 'Format tidak valid. Masukkan 1-4 digit angka.');
                        isCustomValid = false;
                        return;
                    }

                    validateNomorBtn.disabled = true;
                    validateNomorBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                    fetch('{{ route('admin.pendaftaran-seminar-proposal.validate-nomor-surat') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                custom_number: customNumber
                            }),
                            credentials: 'same-origin'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.valid) {
                                showFeedback('success', data.message);
                                customNomorPreview.style.display = 'block';
                                customNomorPreviewText.textContent = data.nomor_surat;
                                isCustomValid = true;
                            } else {
                                showFeedback('danger', data.message);
                                customNomorPreview.style.display = 'none';
                                isCustomValid = false;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showFeedback('danger', 'Terjadi kesalahan saat validasi.');
                            isCustomValid = false;
                        })
                        .finally(() => {
                            validateNomorBtn.disabled = false;
                            validateNomorBtn.innerHTML = '<i class="bx bx-check"></i> Validasi';
                        });
                }

                validateNomorBtn.addEventListener('click', validateCustomNomor);

                customNomorInput.addEventListener('input', function() {
                    isCustomValid = false;
                    customNomorPreview.style.display = 'none';
                    resetCustomValidation();
                });

                customNomorInput.addEventListener('keypress', function(e) {
                    if (!/\d/.test(e.key)) {
                        e.preventDefault();
                    }
                });

                function showFeedback(type, message) {
                    const colors = {
                        success: 'text-success',
                        danger: 'text-danger',
                        warning: 'text-warning'
                    };
                    const icons = {
                        success: 'bx-check-circle',
                        danger: 'bx-error-circle',
                        warning: 'bx-info-circle'
                    };
                    customNomorFeedback.className = `small ${colors[type]}`;
                    customNomorFeedback.innerHTML = `<i class="bx ${icons[type]} me-1"></i>${message}`;
                }

                function resetCustomValidation() {
                    customNomorFeedback.innerHTML = '';
                    customNomorPreview.style.display = 'none';
                }

                function showNotification(type, message) {
                    const notification = document.createElement('div');
                    notification.className =
                        `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
                    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                    notification.innerHTML = `
                        <i class="bx ${type === 'success' ? 'bx-check-circle' : 'bx-error-circle'} me-2"></i>
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.body.appendChild(notification);
                    setTimeout(() => notification.remove(), 3000);
                }

                // Form validation
                generateSuratForm.addEventListener('submit', function(e) {
                    if (nomorSuratCustom.checked && !isCustomValid) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Validasi Diperlukan',
                            text: 'Silakan validasi nomor surat custom terlebih dahulu.',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }
                    generateSuratBtn.disabled = true;
                    generateSuratBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-1"></span> Generating...';
                });
            }

            // Reset modal on close
            generateSuratModal.addEventListener('hidden.bs.modal', function() {
                const nomorSuratAuto = document.getElementById('nomorSuratAuto');
                const generateSuratBtn = document.getElementById('generateSuratBtn');

                if (nomorSuratAuto) nomorSuratAuto.checked = true;
                if (generateSuratBtn) {
                    generateSuratBtn.disabled = false;
                    generateSuratBtn.innerHTML = '<i class="bx bx-file me-1"></i> Generate Surat';
                }
            });
        });
    </script>
@endpush
