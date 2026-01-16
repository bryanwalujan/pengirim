@extends('layouts.user.app')

@section('title', 'Edit Surat Usulan Penerbitan SK Pembimbing Skripsi')

@push('styles')
    <style>
         .upload-area {
            border: 2px dashed #e2e8f0;
            transition: all 0.3s ease;
        }

        .upload-area:hover,
        .upload-area.dragover {
            border-color: #f59e0b;
            background-color: #fffbeb;
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
            <h1 data-aos="fade-up" class="text-2xl md:text-3xl">Edit Pengajuan</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan</a></li>
                    <li><a href="{{ route('user.sk-pembimbing.index') }}">Usulan SK Pembimbing</a></li>
                    <li class="current">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-8 bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50 min-h-screen">
        <div class="container mx-auto px-4 max-w-6xl">

            <!-- Rejection Alert -->
            @if($pengajuan->isDitolak())
                <div class="mb-6 animate-slide-down" data-aos="fade-up">
                    <div class="bg-red-50 border-l-4 border-red-500 rounded-xl p-5 shadow-lg">
                        <div class="flex items-start">
                             <div class="flex-shrink-0">
                                <i class="bx bx-error-circle text-red-500 text-3xl"></i>
                            </div>
                            <div class="ml-4 flex-1">
                                <h4 class="font-bold text-red-800 text-lg mb-1">Perlu Perbaikan</h4>
                                <div class="text-red-700 bg-red-100/50 p-3 rounded-lg border border-red-200">
                                    <p class="font-semibold text-xs uppercase tracking-wide opacity-70 mb-1">Alasan Penolakan/Revisi:</p>
                                    <p class="italic">"{{ $pengajuan->alasan_ditolak }}"</p>
                                </div>
                                <p class="text-sm text-red-600 mt-2">Silakan perbaiki data atau dokumen sesuai catatan di atas, lalu kirim ulang pengajuan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('user.sk-pembimbing.update', $pengajuan) }}" method="POST" enctype="multipart/form-data" id="formEdit">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- LEFT COLUMN -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Data Skripsi -->
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100" data-aos="fade-up">
                            <div class="bg-gradient-to-r from-yellow-500 to-amber-500 px-6 py-4">
                                <h3 class="text-lg font-bold text-white flex items-center">
                                    <i class="bx bx-book mr-2"></i> Data Skripsi
                                </h3>
                            </div>
                            <div class="p-6">
                                <div class="mb-4">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Judul Skripsi <span class="text-red-500">*</span></label>
                                    <textarea name="judul_skripsi" id="judul_skripsi" rows="3" 
                                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 transition-all resize-none"
                                        required>{{ old('judul_skripsi', $pengajuan->judul_skripsi) }}</textarea>
                                    @error('judul_skripsi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Upload Dokumen -->
                         <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100" data-aos="fade-up" data-aos-delay="100">
                             <div class="bg-gradient-to-r from-yellow-500 to-amber-500 px-6 py-4">
                                <h3 class="text-lg font-bold text-white flex items-center">
                                    <i class="bx bx-folder-open mr-2"></i> Perbarui Dokumen
                                </h3>
                            </div>
                            <div class="p-6 space-y-6">
                                
                                <!-- Surat Permohonan -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Surat Permohonan (Opsional)</label>
                                    
                                    <!-- Current File -->
                                    <div class="mb-3 flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        <i class='bx bxs-file-pdf text-red-500 text-2xl mr-3'></i>
                                        <div class="flex-1 overflow-hidden">
                                            <p class="text-sm font-medium text-gray-900">File Saat Ini</p>
                                            <a href="{{ route('user.sk-pembimbing.view-document', [$pengajuan, 'permohonan']) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat Dokumen</a>
                                        </div>
                                    </div>

                                    <div class="upload-area rounded-xl p-4 text-center cursor-pointer bg-gray-50" data-target="file_surat_permohonan">
                                        <div class="flex flex-col items-center">
                                            <i class="bx bx-cloud-upload text-3xl text-amber-400 mb-1"></i>
                                            <p class="text-sm font-medium text-gray-700">Ganti file...</p>
                                            <p class="text-xs text-gray-500">Biarkan kosong jika tidak diubah</p>
                                        </div>
                                    </div>
                                    <input type="file" name="file_surat_permohonan" id="file_surat_permohonan" class="hidden" accept=".pdf,.jpg,.jpeg,.png">
                                    <div id="preview-permohonan" class="mt-2"></div>
                                    @error('file_surat_permohonan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Slip UKT -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Slip UKT (Opsional)</label>
                                    
                                     <!-- Current File -->
                                    <div class="mb-3 flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        <i class='bx bxs-file-image text-blue-500 text-2xl mr-3'></i>
                                        <div class="flex-1 overflow-hidden">
                                            <p class="text-sm font-medium text-gray-900">File Saat Ini</p>
                                            <a href="{{ route('user.sk-pembimbing.view-document', [$pengajuan, 'ukt']) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat Dokumen</a>
                                        </div>
                                    </div>

                                    <div class="upload-area rounded-xl p-4 text-center cursor-pointer bg-gray-50" data-target="file_slip_ukt">
                                        <div class="flex flex-col items-center">
                                            <i class="bx bx-cloud-upload text-3xl text-amber-400 mb-1"></i>
                                            <p class="text-sm font-medium text-gray-700">Ganti file...</p>
                                            <p class="text-xs text-gray-500">Biarkan kosong jika tidak diubah</p>
                                        </div>
                                    </div>
                                    <input type="file" name="file_slip_ukt" id="file_slip_ukt" class="hidden" accept=".pdf,.jpg,.jpeg,.png">
                                    <div id="preview-ukt" class="mt-2"></div>
                                    @error('file_slip_ukt') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Proposal Revisi -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Proposal Revisi (Opsional)</label>
                                    
                                     <!-- Current File -->
                                    <div class="mb-3 flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                                        <i class='bx bxs-book-content text-emerald-500 text-2xl mr-3'></i>
                                        <div class="flex-1 overflow-hidden">
                                            <p class="text-sm font-medium text-gray-900">File Saat Ini</p>
                                            <a href="{{ route('user.sk-pembimbing.view-document', [$pengajuan, 'proposal']) }}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat Dokumen</a>
                                        </div>
                                    </div>

                                    <div class="upload-area rounded-xl p-4 text-center cursor-pointer bg-gray-50" data-target="file_proposal_revisi">
                                        <div class="flex flex-col items-center">
                                            <i class="bx bx-cloud-upload text-3xl text-amber-400 mb-1"></i>
                                            <p class="text-sm font-medium text-gray-700">Ganti file...</p>
                                            <p class="text-xs text-gray-500">Biarkan kosong jika tidak diubah</p>
                                        </div>
                                    </div>
                                    <input type="file" name="file_proposal_revisi" id="file_proposal_revisi" class="hidden" accept=".pdf">
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
                            <button type="submit" id="btnSubmit" class="w-2/3 px-6 py-3 bg-gradient-to-r from-yellow-500 to-amber-600 text-white font-bold rounded-xl shadow-lg hover:from-yellow-600 hover:to-amber-700 hover:shadow-xl transition-all hover:-translate-y-0.5">
                                <i class="bx bx-save mr-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: Info -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 sticky top-24">
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                                <h3 class="text-lg font-bold text-gray-800">
                                    <i class="bx bx-info-circle mr-2 text-amber-500"></i> Informasi
                                </h3>
                            </div>
                            <div class="p-6">
                                <p class="text-sm text-gray-600 mb-4">
                                    Anda sedang menyunting Surat Usulan Penerbitan SK Pembimbing Skripsi.
                                </p>
                                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                                    <h5 class="text-xs font-bold text-blue-800 uppercase mb-2">Catatan</h5>
                                    <ul class="text-xs text-blue-700 space-y-2 list-disc list-inside">
                                        <li>File lama akan tetap digunakan jika Anda tidak mengupload file baru.</li>
                                        <li>Pastikan data yang diinputkan sudah benar sebelum menyimpan.</li>
                                    </ul>
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
        document.addEventListener('DOMContentLoaded', function() {
            // File upload handling (Reuse logic)
             const uploadAreas = document.querySelectorAll('.upload-area');

            uploadAreas.forEach(area => {
                const targetId = area.dataset.target;
                const input = document.getElementById(targetId);
                const previewId = targetId.replace('file_surat_permohonan', 'preview-permohonan')
                                          .replace('file_slip_ukt', 'preview-ukt')
                                          .replace('file_proposal_revisi', 'preview-proposal');
                const preview = document.getElementById(previewId);

                area.addEventListener('click', () => input.click());

                // Drag and drop events
                area.addEventListener('dragover', (e) => { e.preventDefault(); area.classList.add('dragover'); });
                area.addEventListener('dragleave', () => { area.classList.remove('dragover'); });
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

                const maxSize = input.id === 'file_proposal_revisi' ? 10 * 1024 * 1024 : 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('File terlalu besar!');
                    input.value = '';
                    return;
                }

                area.classList.add('has-file');
                
                // Preview for Edit (shows that file is selected for replacement)
                 const fileSize = (file.size / 1024).toFixed(1) + ' KB';
                 preview.innerHTML = `
                    <div class="flex items-center p-2 bg-amber-50 rounded border border-amber-200 mt-2">
                        <i class='bx bx-check-circle text-amber-500 text-xl mr-2'></i>
                        <div class="flex-1 overflow-hidden">
                            <p class="text-xs font-bold text-gray-900">Akan diganti dengan:</p>
                            <p class="text-xs text-gray-700 truncate">${file.name}</p>
                        </div>
                        <button type="button" class="text-gray-400 hover:text-red-500" onclick="removeFile('${input.id}')">
                            <i class='bx bx-x text-lg'></i>
                        </button>
                    </div>
                `;
            }

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

            document.getElementById('formEdit').addEventListener('submit', function() {
                const btn = document.getElementById('btnSubmit');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span> Menyimpan...';
            });
        });
    </script>
@endpush
