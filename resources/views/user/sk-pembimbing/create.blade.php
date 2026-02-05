@extends('layouts.user.app')

@section('title', 'Ajukan Surat Usulan Penerbitan SK Pembimbing Skripsi')

@push('styles')
    <style>
        .upload-area {
            border: 2px dashed #e2e8f0;
            transition: all 0.3s ease;
        }

        .upload-area:hover,
        .upload-area.dragover {
            border-color: #f97316;
            background-color: #fff7ed;
        }

        .upload-area.has-file {
            border-color: #22c55e;
            background-color: #f0fdf4;
        }
    </style>
@endpush

@section('main')
    <!-- Page Title -->
    <div class="page-title light-background">
        <div class="container">
            <h1 data-aos="fade-up" class="text-2xl md:text-3xl">Ajukan SK Baru</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan</a></li>
                    <li><a href="{{ route('user.sk-pembimbing.index') }}">Usulan SK Pembimbing</a></li>
                    <li class="current">Buat Pengajuan</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-8 bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50 min-h-screen">
        <div class="container mx-auto px-4 max-w-6xl">

            @if(session('error'))
                <div class="mb-6 animate-slide-down">
                    <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-semibold text-red-800">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('user.sk-pembimbing.store') }}" method="POST" enctype="multipart/form-data" id="formPengajuan">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- LEFT COLUMN -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <!-- Step 1: Pilih Berita Acara -->
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100" data-aos="fade-up">
                            <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-6 py-4">
                                <h3 class="text-lg font-bold text-white flex items-center">
                                    <span class="flex items-center justify-center w-6 h-6 bg-white/20 rounded-full text-sm mr-3">1</span>
                                    Pilih Hasil Seminar Proposal
                                </h3>
                            </div>
                            <div class="p-6">
                                @if($beritaAcaras->count() > 0)
                                    <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                                        <div class="flex">
                                            <i class="bx bx-info-circle text-blue-500 text-xl mr-2"></i>
                                            <div class="text-sm text-blue-700">
                                                <p class="font-semibold mb-1">Untuk mahasiswa yang sudah melakukan seminar proposal di e-service:</p>
                                                <p>Pilih salah satu hasil seminar proposal di bawah ini.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 gap-4 mb-4">
                                        @foreach($beritaAcaras as $ba)
                                            @php
                                                $jadwal = $ba->jadwalSeminarProposal;
                                                $pendaftaran = $jadwal?->pendaftaranSeminarProposal;
                                                $checked = old('berita_acara_id') == $ba->id ? 'checked' : '';
                                            @endphp
                                            <label class="relative flex cursor-pointer rounded-xl border p-4 shadow-sm focus:outline-none transition-all hover:border-orange-500 hover:ring-1 hover:ring-orange-500 {{ $checked ? 'border-orange-500 ring-1 ring-orange-500 bg-orange-50' : 'border-gray-200' }}">
                                                <input type="radio" name="berita_acara_id" value="{{ $ba->id }}" class="sr-only" {{ $checked }} onchange="updateSelection(this, {{ $ba->id }})">
                                                <span class="flex flex-1">
                                                    <span class="flex flex-col">
                                                        <span class="block text-sm font-medium text-gray-900 mb-1">
                                                            {{ Str::limit($pendaftaran?->judul_skripsi ?? 'Judul tidak tersedia', 100) }}
                                                        </span>
                                                        <span class="flex items-center text-xs text-gray-500 space-x-4">
                                                            <span class="flex items-center">
                                                                <i class="bx bx-calendar mr-1"></i> {{ $jadwal?->tanggal_ujian?->format('d M Y') ?? '-' }}
                                                            </span>
                                                            <span class="flex items-center">
                                                                <i class="bx bx-check-circle mr-1"></i> {{ $ba->keputusan ?? '-' }}
                                                            </span>
                                                        </span>
                                                    </span>
                                                </span>
                                                <span class="flex items-center ml-4 {{ $checked ? 'text-orange-600' : 'text-gray-300' }}" id="check-icon-{{ $ba->id }}">
                                                    <i class="bx bxs-check-circle text-2xl"></i>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>

                                    <div class="relative">
                                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                            <div class="w-full border-t border-gray-300"></div>
                                        </div>
                                        <div class="relative flex justify-center text-sm">
                                            <span class="px-2 bg-white text-gray-500">ATAU</span>
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-4">
                                    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded">
                                        <div class="flex">
                                            <i class="bx bx-info-circle text-amber-600 text-xl mr-2"></i>
                                            <div class="text-sm text-amber-800">
                                                <p class="font-semibold mb-1">Untuk mahasiswa yang sudah melakukan seminar proposal di luar e-service:</p>
                                                <p>Anda dapat langsung melanjutkan pengajuan tanpa memilih berita acara di atas. Staff akan memverifikasi kelengkapan dokumen Anda.</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($beritaAcaras->count() > 0)
                                        <label class="relative flex cursor-pointer rounded-xl border p-4 shadow-sm mt-4 transition-all hover:border-orange-500 hover:ring-1 hover:ring-orange-500 {{ old('berita_acara_id') === null || old('berita_acara_id') === '' ? 'border-orange-500 ring-1 ring-orange-500 bg-orange-50' : 'border-gray-200' }}">
                                            <input type="radio" name="berita_acara_id" value="" class="sr-only" {{ old('berita_acara_id') === null || old('berita_acara_id') === '' ? 'checked' : '' }} onchange="clearSelection(this)">
                                            <span class="flex flex-1">
                                                <span class="flex flex-col">
                                                    <span class="block text-sm font-medium text-gray-900 mb-1">
                                                        <i class="bx bx-file-blank mr-1"></i> Lanjutkan tanpa berita acara
                                                    </span>
                                                    <span class="text-xs text-gray-500">
                                                        Saya sudah melakukan seminar proposal di luar sistem e-service
                                                    </span>
                                                </span>
                                            </span>
                                            <span class="flex items-center ml-4 {{ old('berita_acara_id') === null || old('berita_acara_id') === '' ? 'text-orange-600' : 'text-gray-300' }}" id="check-icon-manual">
                                                <i class="bx bxs-check-circle text-2xl"></i>
                                            </span>
                                        </label>
                                    @endif
                                </div>
                                @error('berita_acara_id') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Step 2: Judul Skripsi -->
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100" data-aos="fade-up" data-aos-delay="100">
                            <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-6 py-4">
                                <h3 class="text-lg font-bold text-white flex items-center">
                                    <span class="flex items-center justify-center w-6 h-6 bg-white/20 rounded-full text-sm mr-3">2</span>
                                    Data Skripsi
                                </h3>
                            </div>
                            <div class="p-6">
                                <div class="mb-4">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Judul Skripsi <span class="text-red-500">*</span></label>
                                    <textarea name="judul_skripsi" id="judul_skripsi" rows="3" 
                                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 transition-all resize-none @error('judul_skripsi') border-red-500 @enderror"
                                        placeholder="Judul skripsi dapat diperbaiki sesuai revisi..." required>{{ old('judul_skripsi', $beritaAcaras->first()?->jadwalSeminarProposal?->pendaftaranSeminarProposal?->judul_skripsi ?? '') }}</textarea>
                                    <p id="caps-warning" class="text-xs text-red-500 mt-2 font-semibold" style="display: none;">
                                        <i class="bx bx-error-circle mr-1"></i> Peringatan: Judul tidak boleh ditulis dengan huruf kapital semua (ALL CAPS).
                                    </p>
                                    <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                                        <i class="bx bx-info-circle mr-1"></i> Judul dapat disesuaikan jika terdapat perbaikan dari seminar proposal. 
                                        <span class="text-red-500 font-medium">Gunakan huruf kapital secara proporsional, bukan ALL CAPS.</span>
                                    </p>
                                    @error('judul_skripsi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Upload Dokumen -->
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100" data-aos="fade-up" data-aos-delay="200">
                             <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-6 py-4">
                                <h3 class="text-lg font-bold text-white flex items-center">
                                    <span class="flex items-center justify-center w-6 h-6 bg-white/20 rounded-full text-sm mr-3">3</span>
                                    Upload Dokumen
                                </h3>
                            </div>
                            <div class="p-6 space-y-6">
                                
                                <!-- Surat Permohonan -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Surat Permohonan (Tulis Tangan) <span class="text-red-500">*</span></label>
                                    <div class="upload-area rounded-xl p-6 text-center cursor-pointer bg-gray-50" data-target="file_surat_permohonan">
                                        <div class="flex flex-col items-center">
                                            <i class="bx bx-cloud-upload text-4xl text-orange-400 mb-2"></i>
                                            <p class="text-sm font-medium text-gray-700">Klik atau drag file ke sini</p>
                                            <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (Max. 2MB)</p>
                                        </div>
                                    </div>
                                    <input type="file" name="file_surat_permohonan" id="file_surat_permohonan" class="hidden" accept=".pdf,.jpg,.jpeg,.png" required>
                                    <div id="preview-permohonan" class="mt-2"></div>
                                    @error('file_surat_permohonan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Slip UKT -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Slip UKT Terakhir <span class="text-red-500">*</span></label>
                                    <div class="upload-area rounded-xl p-6 text-center cursor-pointer bg-gray-50" data-target="file_slip_ukt">
                                        <div class="flex flex-col items-center">
                                            <i class="bx bx-receipt text-4xl text-blue-400 mb-2"></i>
                                            <p class="text-sm font-medium text-gray-700">Klik atau drag file ke sini</p>
                                            <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (Max. 2MB)</p>
                                        </div>
                                    </div>
                                    <input type="file" name="file_slip_ukt" id="file_slip_ukt" class="hidden" accept=".pdf,.jpg,.jpeg,.png" required>
                                    <div id="preview-ukt" class="mt-2"></div>
                                    @error('file_slip_ukt') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Proposal Revisi -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Proposal Revisi <span class="text-red-500">*</span></label>
                                    <div class="upload-area rounded-xl p-6 text-center cursor-pointer bg-gray-50" data-target="file_proposal_revisi">
                                        <div class="flex flex-col items-center">
                                            <i class="bx bx-book-content text-4xl text-emerald-400 mb-2"></i>
                                            <p class="text-sm font-medium text-gray-700">Klik atau drag file ke sini</p>
                                            <p class="text-xs text-gray-500 mt-1">PDF Only (Max. 10MB)</p>
                                        </div>
                                    </div>
                                    <input type="file" name="file_proposal_revisi" id="file_proposal_revisi" class="hidden" accept=".pdf" required>
                                    <div id="preview-proposal" class="mt-2"></div>
                                    @error('file_proposal_revisi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-4 pt-4">
                            <a href="{{ route('user.sk-pembimbing.index') }}" class="w-1/3 px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl text-center hover:bg-gray-200 transition-all">
                                Batal
                            </a>
                            <button type="submit" id="btnSubmit" class="w-2/3 px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-600 text-white font-bold rounded-xl shadow-lg hover:from-orange-600 hover:to-amber-700 hover:shadow-xl transition-all hover:-translate-y-0.5">
                                <i class="bx bx-send mr-2"></i> Kirim Pengajuan
                            </button>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: User and Info -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 sticky top-24">
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                                <h3 class="text-lg font-bold text-gray-800">
                                    <i class="bx bx-user mr-2 text-orange-500"></i> Data Pemohon
                                </h3>
                            </div>
                            <div class="p-6">
                                <div class="text-center mb-6">
                                    <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="bx bx-user text-4xl text-orange-500"></i>
                                    </div>
                                    <h4 class="font-bold text-gray-900">{{ Auth::user()->name }}</h4>
                                    <p class="text-sm text-gray-500">{{ Auth::user()->nim }}</p>
                                </div>
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between py-2 border-b border-gray-50">
                                        <span class="text-gray-500">Program Studi</span>
                                        <span class="font-semibold text-gray-700">Teknik Informatika</span>
                                    </div>
                                    <div class="flex justify-between py-2 border-b border-gray-50">
                                        <span class="text-gray-500">Email</span>
                                        <span class="font-semibold text-gray-700">{{ Auth::user()->email }}</span>
                                    </div>
                                </div>
                            
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Update selection UI for radio buttons
        function updateSelection(radio, id) {
            // Reset stylings
            document.querySelectorAll('label').forEach(lbl => {
                if (lbl.querySelector('input[type="radio"]')) {
                    lbl.classList.remove('border-orange-500', 'ring-1', 'ring-orange-500', 'bg-orange-50');
                    lbl.classList.add('border-gray-200');
                }
            });
            document.querySelectorAll('[id^="check-icon-"]').forEach(icon => {
                icon.classList.remove('text-orange-600');
                icon.classList.add('text-gray-300');
            });

            // Apply active style
            const label = radio.parentElement;
            label.classList.remove('border-gray-200');
            label.classList.add('border-orange-500', 'ring-1', 'ring-orange-500', 'bg-orange-50');
            
            const icon = document.getElementById('check-icon-' + id);
            if(icon) {
                icon.classList.remove('text-gray-300');
                icon.classList.add('text-orange-600');
            }

            // Auto fill judul
            const beritaAcaraData = @json($beritaAcaras->mapWithKeys(function($ba) {
                return [$ba->id => $ba->jadwalSeminarProposal?->pendaftaranSeminarProposal?->judul_skripsi ?? ''];
            }));
            
            if (beritaAcaraData[id] && (!judulInput.value.trim() || judulInput.value.trim() === '')) {
                judulInput.value = beritaAcaraData[id];
                // Trigger input event to check for caps on newly filled title
                judulInput.dispatchEvent(new Event('input'));
            }
        }

        // Clear selection for manual submission (without berita acara)
        function clearSelection(radio) {
            // Reset all selections
            document.querySelectorAll('label').forEach(lbl => {
                if (lbl.querySelector('input[type="radio"]')) {
                    lbl.classList.remove('border-orange-500', 'ring-1', 'ring-orange-500', 'bg-orange-50');
                    lbl.classList.add('border-gray-200');
                }
            });
            document.querySelectorAll('[id^="check-icon-"]').forEach(icon => {
                icon.classList.remove('text-orange-600');
                icon.classList.add('text-gray-300');
            });

            // Apply active style to manual option
            const label = radio.parentElement;
            label.classList.remove('border-gray-200');
            label.classList.add('border-orange-500', 'ring-1', 'ring-orange-500', 'bg-orange-50');
            
            const icon = document.getElementById('check-icon-manual');
            if(icon) {
                icon.classList.remove('text-gray-300');
                icon.classList.add('text-orange-600');
            }

            // Clear judul input (user will manually enter)
            if (!judulInput.value.trim()) {
                judulInput.value = '';
            }
        }

        // Logic validasi ALL CAPS (Sama seperti komisi-proposal)
        const judulInput = document.getElementById('judul_skripsi');
        const capsWarning = document.getElementById('caps-warning');

        if (judulInput) {
            judulInput.addEventListener('input', function() {
                const value = this.value.trim();
                
                // Cek apakah semua huruf adalah kapital (minimal 10 karakter untuk validasi)
                if (value.length >= 10) {
                    const uppercaseCount = (value.match(/[A-Z]/g) || []).length;
                    const lowercaseCount = (value.match(/[a-z]/g) || []).length;
                    const totalLetters = uppercaseCount + lowercaseCount;
                    
                    // Jika lebih dari 80% huruf adalah kapital, tampilkan warning
                    if (totalLetters > 0 && (uppercaseCount / totalLetters) > 0.8) {
                        capsWarning.style.display = 'block';
                        this.classList.add('border-red-500', 'ring-red-500');
                        this.classList.remove('focus:border-orange-500', 'focus:ring-orange-500');
                    } else {
                        capsWarning.style.display = 'none';
                        this.classList.remove('border-red-500', 'ring-red-500');
                        this.classList.add('focus:border-orange-500', 'focus:ring-orange-500');
                    }
                } else {
                    capsWarning.style.display = 'none';
                    this.classList.remove('border-red-500', 'ring-red-500');
                    this.classList.add('focus:border-orange-500', 'focus:ring-orange-500');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // File upload handling
            const uploadAreas = document.querySelectorAll('.upload-area');

            uploadAreas.forEach(area => {
                const targetId = area.dataset.target;
                const input = document.getElementById(targetId);
                const previewId = targetId.replace('file_surat_permohonan', 'preview-permohonan')
                                          .replace('file_slip_ukt', 'preview-ukt')
                                          .replace('file_proposal_revisi', 'preview-proposal');
                const preview = document.getElementById(previewId);

                area.addEventListener('click', () => input.click());

                area.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    area.classList.add('dragover');
                });

                area.addEventListener('dragleave', () => {
                    area.classList.remove('dragover');
                });

                area.addEventListener('drop', (e) => {
                    e.preventDefault();
                    area.classList.remove('dragover');
                    if (e.dataTransfer.files.length) {
                        input.files = e.dataTransfer.files;
                        handleFileSelect(input, area, preview);
                    }
                });

                input.addEventListener('change', () => {
                    handleFileSelect(input, area, preview);
                });
            });

            function handleFileSelect(input, area, preview) {
                const file = input.files[0];
                if (!file) return;

                // Validate
                const maxSize = input.id === 'file_proposal_revisi' ? 10 * 1024 * 1024 : 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('File terlalu besar!');
                    input.value = '';
                    return;
                }

                area.classList.add('has-file');
                
                // Show preview
                const fileSize = (file.size / 1024).toFixed(1) + ' KB';
                const isPdf = file.type === 'application/pdf' || file.name.endsWith('.pdf');
                
                preview.innerHTML = `
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <i class='bx ${isPdf ? 'bxs-file-pdf text-red-500' : 'bxs-image text-blue-500'} text-2xl mr-3'></i>
                        <div class="flex-1 overflow-hidden">
                            <p class="text-sm font-medium text-gray-900 truncate">${file.name}</p>
                            <p class="text-xs text-gray-500">${fileSize}</p>
                        </div>
                        <button type="button" class="text-gray-400 hover:text-red-500" onclick="removeFile('${input.id}')">
                            <i class='bx bx-x text-xl'></i>
                        </button>
                    </div>
                `;
            }

            // Remove file global function
            window.removeFile = function(inputId) {
                const input = document.getElementById(inputId);
                const area = document.querySelector(`[data-target="${inputId}"]`);
                const previewId = inputId.replace('file_surat_permohonan', 'preview-permohonan')
                                          .replace('file_slip_ukt', 'preview-ukt')
                                          .replace('file_proposal_revisi', 'preview-proposal');
                
                input.value = '';
                area.classList.remove('has-file');
                document.getElementById(previewId).innerHTML = '';
            };
            
            // Prevent double submit and validate ALL CAPS
            document.getElementById('formPengajuan').addEventListener('submit', function(e) {
                // Final validation for ALL CAPS before submit
                const judulValue = judulInput.value.trim();
                if (judulValue.length >= 10) {
                    const uppercaseCount = (judulValue.match(/[A-Z]/g) || []).length;
                    const lowercaseCount = (judulValue.match(/[a-z]/g) || []).length;
                    const totalLetters = uppercaseCount + lowercaseCount;
                    
                    if (totalLetters > 0 && (uppercaseCount / totalLetters) > 0.8) {
                        e.preventDefault();
                        
                        // Using standard SweetAlert2 if available, otherwise fallback to alert
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Judul Tidak Valid',
                                text: 'Judul skripsi tidak boleh ditulis dengan huruf kapital semua (ALL CAPS). Gunakan huruf kapital hanya sesuai aturan penulisan.',
                                icon: 'error',
                                confirmButtonColor: '#f97316'
                            });
                        } else {
                            alert('Judul skripsi tidak boleh menggunakan huruf kapital semua (ALL CAPS).');
                        }
                        
                        judulInput.focus();
                        return false;
                    }
                }

                const btn = document.getElementById('btnSubmit');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2 animate-spin"></span> Mengirim...';
                
                return true;
            });
        });
    </script>
@endpush
