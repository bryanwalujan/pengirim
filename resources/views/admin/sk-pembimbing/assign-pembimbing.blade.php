{{-- filepath: resources/views/admin/sk-pembimbing/assign-pembimbing.blade.php --}}
@extends('layouts.admin.app')

@section('title', 'Tentukan Pembimbing Skripsi')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.sk-pembimbing.index') }}">SK Pembimbing</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.sk-pembimbing.show', $pengajuan) }}">Detail</a></li>
                <li class="breadcrumb-item active">Tentukan PS</li>
            </ol>
        </nav>

        {{-- Header --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="bx bx-user-plus me-2 text-primary"></i>Tentukan Pembimbing Skripsi
                </h4>
                <p class="text-muted mb-0">Pilih dosen pembimbing untuk mahasiswa ini</p>
            </div>
        </div>

        <div class="row">
            {{-- Form --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bx bx-edit me-2"></i>Form Penentuan Pembimbing</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.sk-pembimbing.store-pembimbing', $pengajuan) }}" method="POST" id="assignForm">
                            @csrf

                            {{-- PS1 --}}
                            <div class="mb-4">
                                <label class="form-label">Pembimbing 1 (PS1) <span class="text-danger">*</span></label>
                                <select name="dosen_pembimbing_1_id" class="form-select select2" required>
                                    <option value="">-- Pilih Dosen --</option>
                                    @foreach($dosenList as $dosen)
                                        <option value="{{ $dosen['id'] }}" 
                                            {{ ($pengajuan->dosen_pembimbing_1_id == $dosen['id'] || $defaultPs1 == $dosen['id']) ? 'selected' : '' }}>
                                            {{ $dosen['name'] }} ({{ $dosen['nip'] }}) - 
                                            PS1: {{ $dosen['jumlah_ps1'] }}, PS2: {{ $dosen['jumlah_ps2'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('dosen_pembimbing_1_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- PS2 --}}
                            <div class="mb-4">
                                <label class="form-label">Pembimbing 2 (PS2) <span class="text-danger">*</span></label>
                                <select name="dosen_pembimbing_2_id" class="form-select select2" required>
                                    <option value="">-- Pilih Dosen --</option>
                                    @foreach($dosenList as $dosen)
                                        <option value="{{ $dosen['id'] }}" 
                                            {{ $pengajuan->dosen_pembimbing_2_id == $dosen['id'] ? 'selected' : '' }}>
                                            {{ $dosen['name'] }} ({{ $dosen['nip'] }}) - 
                                            PS1: {{ $dosen['jumlah_ps1'] }}, PS2: {{ $dosen['jumlah_ps2'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('dosen_pembimbing_2_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Nomor Surat Section --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Nomor Surat <span class="text-danger">*</span></label>

                                {{-- Radio Options --}}
                                <div class="mb-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="nomor_surat_type"
                                            id="nomorSuratAuto" value="auto" checked>
                                        <label class="form-check-label" for="nomorSuratAuto">
                                            <strong>Otomatis</strong>
                                            <span class="text-muted d-block small">Sistem akan generate nomor surat secara otomatis</span>
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
                                <div id="autoNomorPreview" class="p-3 bg-light rounded mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted d-block">Nomor Surat Berikutnya:</small>
                                            <code class="fs-6" id="nextNomorSurat">{{ $nomorSuratInfo['next_nomor'] ?? '-' }}</code>
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
                                        <span class="input-group-text">/{{ $nomorSuratInfo['prefix'] ?? 'UN41.2/TI' }}/{{ $nomorSuratInfo['year'] ?? date('Y') }}</span>
                                        <button type="button" class="btn btn-outline-secondary" id="validateNomorBtn">
                                            <i class="bx bx-check"></i> Validasi
                                        </button>
                                    </div>
                                    <div id="customNomorFeedback" class="small"></div>
                                    <div id="customNomorPreview" class="mt-2 p-2 bg-light rounded" style="display: none;">
                                        <small class="text-muted">Preview: </small>
                                        <code id="customNomorPreviewText"></code>
                                    </div>
                                </div>
                            </div>

                            {{-- Tanggal Surat --}}
                            <div class="mb-4">
                                <label class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_surat" class="form-control" 
                                    value="{{ old('tanggal_surat', $pengajuan->tanggal_surat?->format('Y-m-d') ?? date('Y-m-d')) }}" required>
                                @error('tanggal_surat')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.sk-pembimbing.show', $pengajuan) }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-x me-1"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="bx bx-save me-1"></i>Simpan & Lanjutkan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Info Sidebar --}}
            <div class="col-lg-4">
                {{-- Data Mahasiswa --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bx bx-user me-2"></i>Data Mahasiswa</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Nama:</strong> {{ $pengajuan->mahasiswa->name ?? '-' }}</p>
                        <p class="mb-2"><strong>NIM:</strong> {{ $pengajuan->mahasiswa->nim ?? '-' }}</p>
                        <p class="mb-0"><strong>Judul:</strong> {{ Str::limit($pengajuan->judul_skripsi, 100) }}</p>
                    </div>
                </div>

                {{-- Statistik Dosen --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bx bx-bar-chart me-2"></i>Statistik Bimbingan</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-0">
                            <i class="bx bx-info-circle me-2"></i>
                            <small>Angka di dropdown menunjukkan jumlah mahasiswa yang dibimbing oleh dosen tersebut pada tahun ajaran aktif.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        // Nomor Surat Logic
        const nomorSuratAuto = document.getElementById('nomorSuratAuto');
        const nomorSuratCustom = document.getElementById('nomorSuratCustom');
        const autoNomorPreview = document.getElementById('autoNomorPreview');
        const customNomorSection = document.getElementById('customNomorSection');
        const customNomorInput = document.getElementById('customNomorSurat');
        const validateNomorBtn = document.getElementById('validateNomorBtn');
        const customNomorFeedback = document.getElementById('customNomorFeedback');
        const customNomorPreview = document.getElementById('customNomorPreview');
        const customNomorPreviewText = document.getElementById('customNomorPreviewText');
        const assignForm = document.getElementById('assignForm');
        const submitBtn = document.getElementById('submitBtn');

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

            fetch('{{ route('admin.sk-pembimbing.validate-nomor-surat') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
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

        // Form validation
        assignForm.addEventListener('submit', function(e) {
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
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';
        });
    });
</script>
@endpush
