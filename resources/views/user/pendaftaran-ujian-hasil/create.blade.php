@extends('layouts.user.form')

@section('title', 'Form Pendaftaran Ujian Hasil')

@push('styles')
    <style>
        textarea[readonly] {
            resize: none;
            cursor: not-allowed;
            background-color: #f1f5f9 !important;
            opacity: 0.8;
        }

        input[type="file"]::-webkit-file-upload-button {
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        input[type="file"]::-webkit-file-upload-button:hover {
            background: linear-gradient(135deg, #ea580c, #c2410c);
            transform: scale(1.02);
        }

        .readonly-field-wrapper {
            position: relative;
        }

        .readonly-field-wrapper::before {
            content: '\f023';
            font-family: 'bootstrap-icons';
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 1rem;
            pointer-events: none;
            z-index: 10;
        }
    </style>
@endpush

@section('form-content')
    <div class="max-w-4xl mx-auto pb-10" data-aos="fade-up" data-aos-delay="100">

        {{-- ========== NOT ELIGIBLE WARNING ========== --}}
        @if (!$eligible)
            <div class="mb-6 bg-amber-50 border-l-4 border-amber-500 p-5 rounded-r-xl shadow-sm" role="alert"
                data-aos="fade-down" data-aos-delay="150">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="bi bi-exclamation-triangle-fill text-3xl text-amber-500"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-base font-bold text-amber-800 mb-2">⚠️ Belum Dapat Mendaftar</h3>
                        <p class="text-sm text-amber-700">{{ $message }}</p>
                        <div class="mt-4">
                            <a href="{{ route('user.komisi-hasil.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg transition-all">
                                <i class="bi bi-arrow-left me-2"></i>
                                Lihat Status Komisi Hasil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- ========== ALERT KOMISI HASIL STATUS ========== --}}
            @if (isset($komisiHasil) && $komisiHasil)
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-5 rounded-r-xl shadow-sm" role="alert"
                    data-aos="fade-down" data-aos-delay="150">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="bi bi-check-circle-fill text-3xl text-emerald-500"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-base font-bold text-emerald-800 mb-2">✅ Komisi Hasil Telah Disetujui</h3>
                            <div class="text-sm text-emerald-700 space-y-2">
                                <div class="bg-white/60 p-3 rounded-lg border border-emerald-200">
                                    <p class="font-semibold text-emerald-900">Judul Skripsi:</p>
                                    <p class="mt-1 text-slate-700">{{ $komisiHasil->judul_skripsi }}</p>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div class="bg-white/60 p-3 rounded-lg border border-emerald-200">
                                        <p class="font-semibold text-emerald-900">Pembimbing 1:</p>
                                        <p class="mt-1 text-slate-700">{{ $komisiHasil->pembimbing1->name ?? '-' }}</p>
                                    </div>
                                    <div class="bg-white/60 p-3 rounded-lg border border-emerald-200">
                                        <p class="font-semibold text-emerald-900">Pembimbing 2:</p>
                                        <p class="mt-1 text-slate-700">{{ $komisiHasil->pembimbing2->name ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="mt-3 pt-3 border-t border-emerald-300 flex items-center">
                                    <i class="bi bi-lock-fill text-emerald-600 mr-2"></i>
                                    <span class="text-xs text-emerald-700 font-medium">
                                        Data Judul dan Pembimbing akan otomatis terisi dari Komisi Hasil yang sudah
                                        disetujui
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Alert Error Session --}}
            @if (session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg flex items-center shadow-sm"
                    data-aos="fade-down">
                    <i class="bi bi-exclamation-triangle-fill text-red-500 text-xl mr-3"></i>
                    <span class="text-red-700 font-medium">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Main Card --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">

                {{-- Header --}}
                <div
                    class="bg-gradient-to-r from-orange-500 to-orange-600 p-8 text-center text-white relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-full bg-white/10 opacity-20"></div>
                    <h4 class="text-2xl md:text-3xl font-bold tracking-tight relative z-10">
                        Formulir Pendaftaran Ujian Hasil
                    </h4>
                    <p class="mt-2 text-orange-50 text-sm md:text-base relative z-10 opacity-90">
                        Lengkapi data dan upload dokumen persyaratan di bawah ini
                    </p>
                </div>

                <form action="{{ route('user.pendaftaran-ujian-hasil.store') }}" method="POST"
                    enctype="multipart/form-data" id="ujian-hasil-form" class="p-6 md:p-10 space-y-8">
                    @csrf

                    {{-- ========== INFORMASI MAHASISWA SECTION ========== --}}
                    <div class="bg-slate-50 p-6 rounded-xl border border-slate-200" data-aos="fade-up"
                        data-aos-delay="200">
                        <div class="flex items-center mb-5 pb-3 border-b border-orange-200">
                            <div
                                class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 mr-3">
                                <i class="bi bi-person-circle text-xl"></i>
                            </div>
                            <h5 class="text-lg font-bold text-slate-800">Informasi Mahasiswa</h5>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-600 mb-2">Nama Lengkap</label>
                                <input type="text" value="{{ Auth::user()->name }}" readonly
                                    class="w-full px-4 py-3 rounded-xl bg-slate-200 border-transparent text-slate-600 font-medium focus:ring-0 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-600 mb-2">Nomor Induk
                                    Mahasiswa</label>
                                <input type="text" value="{{ Auth::user()->nim }}" readonly
                                    class="w-full px-4 py-3 rounded-xl bg-slate-200 border-transparent text-slate-600 font-medium focus:ring-0 cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    {{-- ========== INFORMASI SKRIPSI SECTION (READONLY) ========== --}}
                    <div data-aos="fade-up" data-aos-delay="300">
                        <div class="flex items-center mb-5">
                            <div
                                class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 mr-3">
                                <i class="bi bi-journal-text text-xl"></i>
                            </div>
                            <h5 class="text-lg font-bold text-slate-800">Informasi Skripsi</h5>
                            <span class="ml-auto text-xs bg-orange-100 text-orange-700 px-3 py-1 rounded-full font-semibold">
                                <i class="bi bi-lock-fill mr-1"></i>Data dari Komisi Hasil
                            </span>
                        </div>

                        {{-- Judul Skripsi (READONLY) --}}
                        <div class="mb-6">
                            <label for="judul_skripsi_display" class="block text-sm font-semibold text-slate-700 mb-2">
                                Judul Skripsi
                                <i class="bi bi-lock-fill text-slate-400 ml-1"></i>
                            </label>
                            <div id="judul_skripsi_display"
                                class="w-full px-4 py-3 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 font-medium min-h-[100px] cursor-not-allowed">
                                {!! $komisiHasil->judul_skripsi ?? '-' !!}
                            </div>
                            <p class="mt-2 text-xs text-slate-500 flex items-center">
                                <i class="bi bi-info-circle mr-1"></i>
                                Judul otomatis terisi dari Komisi Hasil yang sudah disetujui (tidak dapat diubah)
                            </p>
                        </div>

                        {{-- Dosen Pembimbing (READONLY) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="readonly-field-wrapper">
                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    Dosen Pembimbing 1
                                    <i class="bi bi-lock-fill text-slate-400 ml-1"></i>
                                </label>
                                <input type="text" value="{{ $komisiHasil->pembimbing1->name ?? 'Tidak ada data' }}"
                                    readonly
                                    class="w-full px-4 py-3 pr-12 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 font-medium focus:ring-0 cursor-not-allowed">
                            </div>
                            <div class="readonly-field-wrapper">
                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    Dosen Pembimbing 2
                                    <i class="bi bi-lock-fill text-slate-400 ml-1"></i>
                                </label>
                                <input type="text" value="{{ $komisiHasil->pembimbing2->name ?? 'Tidak ada data' }}"
                                    readonly
                                    class="w-full px-4 py-3 pr-12 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 font-medium focus:ring-0 cursor-not-allowed">
                            </div>
                        </div>

                        {{-- IPK (EDITABLE) --}}
                        <div>
                            <label for="ipk" class="block text-sm font-semibold text-slate-700 mb-2">
                                IPK Terakhir <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all @error('ipk') border-red-500 ring-1 ring-red-500 @enderror"
                                id="ipk" name="ipk" value="{{ old('ipk') }}" placeholder="Contoh: 3.51"
                                step="0.01" min="0" max="4.00" required>
                            @error('ipk')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- ========== UPLOAD DOKUMEN SECTION ========== --}}
                    <div data-aos="fade-up" data-aos-delay="400">
                        <div class="flex items-center mb-5">
                            <div
                                class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 mr-3">
                                <i class="bi bi-cloud-arrow-up text-xl"></i>
                            </div>
                            <h5 class="text-lg font-bold text-slate-800">Upload Dokumen</h5>
                        </div>

                            {{-- Info Box --}}
                        <div class="bg-orange-50 border border-orange-100 rounded-xl p-4 mb-6">
                            <h6 class="text-orange-800 font-bold text-sm mb-2 flex items-center">
                                <i class="bi bi-info-circle-fill mr-2"></i> Persyaratan Dokumen
                            </h6>
                            <ol class="list-decimal list-inside text-sm text-orange-700 space-y-1 ml-1">
                                <li>Transkrip Nilai (PDF, maks. 2MB)</li>
                                <li>File Skripsi Lengkap (PDF, maks. 10MB)</li>
                                <li>Surat Permohonan Ujian Hasil (PDF, maks. 2MB)</li>
                                <li>Slip Pembayaran UKT (PDF/JPG/PNG, maks. 2MB)</li>
                                <li>Nomor &amp; File SK Pembimbing Skripsi (PDF, maks. 2MB)</li>
                            </ol>
                        </div>

                        <div class="space-y-5">
                            {{-- File Transkrip Nilai --}}
                            <div class="relative">
                                <label for="file_transkrip_nilai" class="block text-sm font-semibold text-slate-700 mb-2">
                                    1. Transkrip Nilai <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-orange-50 file:to-amber-50 file:text-orange-700 hover:file:bg-gradient-to-r hover:file:from-orange-100 hover:file:to-amber-100 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all @error('file_transkrip_nilai') border-red-500 ring-1 ring-red-500 @enderror"
                                    type="file" id="file_transkrip_nilai" name="file_transkrip_nilai" accept=".pdf"
                                    required>
                                <p class="mt-1 text-xs text-slate-500">Format: PDF | Ukuran maksimal: 2MB</p>
                                @error('file_transkrip_nilai')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- File Skripsi --}}
                            <div class="relative">
                                <label for="file_skripsi" class="block text-sm font-semibold text-slate-700 mb-2">
                                    2. File Skripsi (Lengkap) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-orange-50 file:to-amber-50 file:text-orange-700 hover:file:bg-gradient-to-r hover:file:from-orange-100 hover:file:to-amber-100 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all @error('file_skripsi') border-red-500 ring-1 ring-red-500 @enderror"
                                    type="file" id="file_skripsi" name="file_skripsi" accept=".pdf" required>
                                <p class="mt-1 text-xs text-slate-500">Format: PDF | Ukuran maksimal: 10MB</p>
                                @error('file_skripsi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- File Surat Permohonan --}}
                            <div class="relative">
                                <label for="file_surat_permohonan" class="block text-sm font-semibold text-slate-700 mb-2">
                                    3. Surat Permohonan Ujian Hasil <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-orange-50 file:to-amber-50 file:text-orange-700 hover:file:bg-gradient-to-r hover:file:from-orange-100 hover:file:to-amber-100 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all @error('file_surat_permohonan') border-red-500 ring-1 ring-red-500 @enderror"
                                    type="file" id="file_surat_permohonan" name="file_surat_permohonan" accept=".pdf"
                                    required>
                                <p class="mt-1 text-xs text-slate-500">Format: PDF | Ukuran maksimal: 2MB</p>
                                @error('file_surat_permohonan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- File Slip UKT --}}
                            <div class="relative">
                                <label for="file_slip_ukt" class="block text-sm font-semibold text-slate-700 mb-2">
                                    4. Slip Pembayaran UKT <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-orange-50 file:to-amber-50 file:text-orange-700 hover:file:bg-gradient-to-r hover:file:from-orange-100 hover:file:to-amber-100 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all @error('file_slip_ukt') border-red-500 ring-1 ring-red-500 @enderror"
                                    type="file" id="file_slip_ukt" name="file_slip_ukt" accept=".pdf,.jpg,.jpeg,.png"
                                    required>
                                <p class="mt-1 text-xs text-slate-500">Format: PDF, JPG, JPEG, PNG | Ukuran maksimal: 2MB
                                </p>
                                @error('file_slip_ukt')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Nomor SK Pembimbing --}}
                            <div class="relative">
                                <label for="nomor_sk_pembimbing" class="block text-sm font-semibold text-slate-700 mb-2">
                                    5. Nomor SK Pembimbing Skripsi <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="nomor_sk_pembimbing" name="nomor_sk_pembimbing"
                                    value="{{ old('nomor_sk_pembimbing') }}"
                                    maxlength="100" placeholder="Contoh: 3714/UN41.2/PS/2025" required
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all @error('nomor_sk_pembimbing') border-red-500 ring-1 ring-red-500 @enderror">
                                @error('nomor_sk_pembimbing')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-slate-500">
                                    <i class="bi bi-info-circle mr-1"></i>
                                    Masukkan nomor SK sesuai dengan yang tertera pada dokumen
                                </p>
                            </div>

                            {{-- File SK Pembimbing --}}
                            <div class="relative">
                                <label for="file_sk_pembimbing" class="block text-sm font-semibold text-slate-700 mb-2">
                                    6. File SK Pembimbing Skripsi <span class="text-red-500">*</span>
                                </label>
                                <input
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-orange-50 file:to-amber-50 file:text-orange-700 hover:file:bg-gradient-to-r hover:file:from-orange-100 hover:file:to-amber-100 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all @error('file_sk_pembimbing') border-red-500 ring-1 ring-red-500 @enderror"
                                    type="file" id="file_sk_pembimbing" name="file_sk_pembimbing" accept=".pdf"
                                    required>
                                <p class="mt-1 text-xs text-slate-500">Format: PDF | Ukuran maksimal: 2MB</p>
                                @error('file_sk_pembimbing')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- ========== FORM ACTIONS ========== --}}
                    <div class="pt-6 border-t border-slate-100">
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 flex items-start">
                            <i class="bi bi-shield-exclamation text-amber-600 text-xl mr-3 mt-0.5"></i>
                            <div class="text-sm text-amber-800">
                                <strong>Perhatian:</strong> Pastikan semua data dan dokumen sudah benar sebelum mengirim.
                                Setelah pengajuan dibuat, Anda tidak dapat mengubahnya.
                            </div>
                        </div>

                        <div class="flex flex-col-reverse md:flex-row justify-between gap-4">
                            <a href="{{ route('user.pendaftaran-ujian-hasil.index') }}"
                                class="px-6 py-3 rounded-full border-2 border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800 transition-all duration-300 text-center flex items-center justify-center">
                                <i class="bi bi-arrow-left mr-2"></i> Kembali
                            </a>

                            <button type="submit" id="submit-btn"
                                class="px-8 py-3 rounded-full bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 hover:-translate-y-1 hover:from-orange-600 hover:to-orange-700 transition-all duration-300 flex items-center justify-center">
                                <i class="bi bi-send-check mr-2"></i> Ajukan Pendaftaran
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize AOS
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 400,
                    once: true,
                    offset: 50
                });
            }

            // Auto-hide alerts
            setTimeout(() => {
                $('[role="alert"]').not('.bg-amber-50').fadeOut('slow');
            }, 8000);
        });

        // ========== FILE UPLOAD VALIDATION & PREVIEW ==========
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function(e) {
                const fileName = e.target.files[0]?.name;
                const fileSize = e.target.files[0]?.size;
                const maxSize = this.id === 'file_skripsi' ? 10 * 1024 * 1024 : 2 * 1024 * 1024;

                if (fileName) {
                    if (fileSize > maxSize) {
                        const maxSizeMB = maxSize / (1024 * 1024);
                        Swal.fire({
                            icon: 'error',
                            title: 'File Terlalu Besar',
                            text: `Ukuran file maksimal ${maxSizeMB}MB`,
                            confirmButtonColor: '#f97316'
                        });
                        this.value = '';
                        this.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                        return;
                    }

                    this.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
                    this.classList.add('border-emerald-500', 'ring-1', 'ring-emerald-200');

                    console.log(`✅ File selected: ${fileName} (${(fileSize / 1024).toFixed(2)} KB)`);
                }
            });
        });

        // ========== FORM SUBMISSION WITH SWEETALERT CONFIRMATION ==========
        document.getElementById('ujian-hasil-form')?.addEventListener('submit', function(e) {
            e.preventDefault();

            const requiredFiles = [
                'file_transkrip_nilai',
                'file_skripsi',
                'file_surat_permohonan',
                'file_slip_ukt',
                'file_sk_pembimbing'
            ];

            // Check nomor SK pembimbing
            const nomorSkInput = document.getElementById('nomor_sk_pembimbing');
            if (nomorSkInput && !nomorSkInput.value.trim()) {
                nomorSkInput.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                Swal.fire({
                    icon: 'warning',
                    title: 'Nomor SK Belum Diisi',
                    text: 'Harap isi Nomor SK Pembimbing Skripsi terlebih dahulu.',
                    confirmButtonColor: '#f97316',
                    confirmButtonText: 'OK, Saya Mengerti'
                });
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }

            let allFilesUploaded = true;
            let missingFiles = [];

            requiredFiles.forEach(fieldName => {
                const fileInput = document.getElementById(fieldName);
                if (!fileInput.files || fileInput.files.length === 0) {
                    allFilesUploaded = false;
                    fileInput.classList.add('border-red-500', 'ring-1', 'ring-red-500');

                    const label = fileInput.previousElementSibling;
                    if (label && label.tagName === 'LABEL') {
                        missingFiles.push(label.textContent.replace('*', '').trim());
                    }
                }
            });

            if (!allFilesUploaded) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Dokumen Belum Lengkap',
                    html: `
                        <div class="text-left">
                            <p class="mb-3 text-slate-700">Harap lengkapi dokumen berikut:</p>
                            <ul class="list-disc list-inside space-y-1 text-slate-600">
                                ${missingFiles.map(file => `<li class="text-sm">${file}</li>`).join('')}
                            </ul>
                        </div>
                    `,
                    confirmButtonColor: '#f97316',
                    confirmButtonText: 'OK, Saya Mengerti'
                });
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                return;
            }

            Swal.fire({
                title: '<strong>Konfirmasi Pengajuan</strong>',
                html: `
                    <div class="text-left">
                        <p class="mb-3 text-slate-700">Apakah Anda yakin data dan dokumen yang diupload sudah benar?</p>
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
                            <div class="flex items-start">
                                <i class="bi bi-exclamation-triangle text-amber-600 text-lg mr-2 mt-0.5"></i>
                                <small class="text-amber-800">
                                    <strong>Perhatian:</strong> Setelah pengajuan dibuat, Anda tidak dapat mengubahnya.
                                </small>
                            </div>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="bi bi-send me-1"></i>Ya, Kirim Sekarang',
                cancelButtonText: '<i class="bi bi-x me-1"></i>Batal',
                confirmButtonColor: '#f97316',
                cancelButtonColor: '#94a3b8',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'px-6 py-2.5 rounded-full font-semibold',
                    cancelButton: 'px-6 py-2.5 rounded-full font-semibold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const btn = document.getElementById('submit-btn');
                    btn.disabled = true;
                    btn.innerHTML =
                        '<span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>Mengirim...';
                    this.submit();
                }
            });
        });
    </script>
@endpush
