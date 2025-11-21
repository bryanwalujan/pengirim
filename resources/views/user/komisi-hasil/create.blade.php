@extends('layouts.user.form')

@section('title', 'Form Pengajuan Persetujuan Komisi Hasil')

@push('styles')
    {{-- Kita perlu sedikit CSS custom untuk memaksa Select2 mengikuti style Tailwind --}}
    <style>
        /* Penyesuaian Select2 agar mirip Input Tailwind */
        .select2-container--bootstrap-5 .select2-selection {
            border-color: #e2e8f0 !important;
            /* border-slate-200 */
            padding: 0.75rem 1rem !important;
            height: auto !important;
            border-radius: 0.75rem !important;
            /* rounded-xl */
            font-size: 1rem;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            color: #334155 !important;
            /* text-slate-700 */
            padding: 0 !important;
        }

        .select2-container--bootstrap-5 .select2-dropdown {
            border-color: #e2e8f0 !important;
            border-radius: 0.75rem !important;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1) !important;
        }

        /* Trix Editor Customization */
        trix-editor {
            border-color: #e2e8f0 !important;
            border-radius: 0.75rem !important;
            padding: 1rem !important;
            background-color: white;
            min-height: 150px;
        }

        trix-editor:focus {
            border-color: #3b82f6 !important;
            /* blue-500 */
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3) !important;
            outline: none;
        }

        trix-toolbar .trix-button--icon {
            border-radius: 0.5rem;
        }
    </style>
@endpush

@section('form-content')
    <div class="max-w-4xl mx-auto pb-10" data-aos="fade-up" data-aos-delay="100">

        {{-- Alert Pengajuan Ulang --}}
        @if (isset($latestHasil) && $latestHasil && $latestHasil->status === 'rejected')
            <div class="mb-6 bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg shadow-sm" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="bi bi-arrow-repeat text-2xl text-amber-500"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-bold text-amber-800">Pengajuan Ulang Diperlukan</h3>
                        <div class="mt-2 text-sm text-amber-700">
                            <p>Pengajuan sebelumnya ditolak. Silakan perbaiki berdasarkan catatan berikut:</p>
                            <div class="mt-2 p-3 bg-white/50 rounded-lg border border-amber-200">
                                <span class="font-semibold">Alasan Penolakan:</span>
                                <span
                                    class="block mt-1 text-red-600 font-medium">{{ $latestHasil->keterangan ?? 'Tidak ada keterangan' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Alert Error Session --}}
        @if (session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg flex items-center shadow-sm">
                <i class="bi bi-exclamation-triangle-fill text-red-500 text-xl mr-3"></i>
                <span class="text-red-700 font-medium">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Main Card --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">

            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-8 text-center text-white relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full bg-white/10 opacity-20"
                    style="background-image: url('data:image/svg+xml,...');"></div> {{-- Optional Pattern --}}
                <h4 class="text-2xl md:text-3xl font-bold tracking-tight relative z-10">Formulir Persetujuan Komisi Hasil
                </h4>
                <p class="mt-2 text-blue-100 text-sm md:text-base relative z-10 opacity-90">Lengkapi data skripsi dan pilih
                    dosen pembimbing Anda</p>
            </div>

            <form action="{{ route('user.komisi-hasil.store') }}" method="POST" id="hasil-form"
                class="p-6 md:p-10 space-y-8">
                @csrf

                <!-- Informasi Mahasiswa Section -->
                <div class="bg-slate-50 p-6 rounded-xl border border-slate-200" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center mb-5 pb-3 border-b border-slate-200">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mr-3">
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
                            <label class="block text-sm font-semibold text-slate-600 mb-2">Nomor Induk Mahasiswa</label>
                            <input type="text" value="{{ Auth::user()->nim }}" readonly
                                class="w-full px-4 py-3 rounded-xl bg-slate-200 border-transparent text-slate-600 font-medium focus:ring-0 cursor-not-allowed">
                        </div>
                    </div>
                </div>

                <!-- Judul Skripsi Section -->
                <div data-aos="fade-up" data-aos-delay="300">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 mr-3">
                            <i class="bi bi-journal-text text-xl"></i>
                        </div>
                        <h5 class="text-lg font-bold text-slate-800">Judul Skripsi</h5>
                    </div>

                    <div class="relative">
                        <label for="judul_skripsi" class="block text-sm font-semibold text-slate-700 mb-2">
                            Judul Lengkap <span class="text-red-500">*</span>
                        </label>

                        <input id="judul_skripsi" type="hidden" name="judul_skripsi" value="{{ old('judul_skripsi') }}">
                        <trix-editor input="judul_skripsi"
                            class="trix-content prose max-w-none @error('judul_skripsi') border-red-500 ring-1 ring-red-500 @enderror"
                            placeholder="Masukkan judul skripsi Anda di sini..."></trix-editor>

                        <p class="mt-2 text-xs text-slate-500 flex items-center">
                            <i class="bi bi-info-circle mr-1"></i> Pastikan judul sesuai dengan yang disetujui pembimbing.
                        </p>
                        @error('judul_skripsi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Dosen Pembimbing Section -->
                <div data-aos="fade-up" data-aos-delay="400">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-10 h-10 rounded-full bg-violet-100 flex items-center justify-center text-violet-600 mr-3">
                            <i class="bi bi-people-fill text-xl"></i>
                        </div>
                        <h5 class="text-lg font-bold text-slate-800">Dosen Pembimbing</h5>
                    </div>

                    @if (isset($dosens) && $dosens->count() > 0)
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6">
                            <h6 class="text-blue-800 font-bold text-sm mb-2 flex items-center">
                                <i class="bi bi-lightbulb-fill mr-2"></i> Panduan Pemilihan
                            </h6>
                            <ul class="list-disc list-inside text-sm text-blue-700 space-y-1 ml-1">
                                <li>Pilih dosen sesuai bidang penelitian.</li>
                                <li><strong>Pembimbing 1 & 2 harus berbeda.</strong></li>
                                <li>Konsultasikan dengan Koordinator Prodi jika ragu.</li>
                            </ul>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Pembimbing 1 -->
                            <div class="group">
                                <label for="dosen_pembimbing1_id" class="block text-sm font-semibold text-slate-700 mb-2">
                                    Dosen Pembimbing 1 <span class="text-red-500">*</span>
                                </label>
                                <select class="form-select select2 w-full" id="dosen_pembimbing1_id"
                                    name="dosen_pembimbing1_id" required>
                                    <option value="">Pilih Dosen Pembimbing 1...</option>
                                    @foreach ($dosens as $dosen)
                                        <option value="{{ $dosen->id }}"
                                            {{ old('dosen_pembimbing1_id') == $dosen->id ? 'selected' : '' }}>
                                            {{ $dosen->name }} - {{ $dosen->jabatan ?? 'Dosen' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('dosen_pembimbing1_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Pembimbing 2 -->
                            <div class="group">
                                <label for="dosen_pembimbing2_id" class="block text-sm font-semibold text-slate-700 mb-2">
                                    Dosen Pembimbing 2 <span class="text-red-500">*</span>
                                </label>
                                <select class="form-select select2 w-full" id="dosen_pembimbing2_id"
                                    name="dosen_pembimbing2_id" required>
                                    <option value="">Pilih Dosen Pembimbing 2...</option>
                                    @foreach ($dosens as $dosen)
                                        <option value="{{ $dosen->id }}"
                                            {{ old('dosen_pembimbing2_id') == $dosen->id ? 'selected' : '' }}>
                                            {{ $dosen->name }} - {{ $dosen->jabatan ?? 'Dosen' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('dosen_pembimbing2_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @else
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center">
                            <i class="bi bi-exclamation-circle-fill text-xl mr-3"></i>
                            <div>
                                <strong class="font-bold">Data Kosong!</strong>
                                <span class="block sm:inline">Tidak ada data dosen tersedia. Hubungi admin.</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Form Actions -->
                <div class="pt-6 border-t border-slate-100" data-aos="fade-up" data-aos-delay="500">
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 flex items-start">
                        <i class="bi bi-shield-exclamation text-amber-600 text-xl mr-3 mt-0.5"></i>
                        <div class="text-sm text-amber-800">
                            <strong>Perhatian:</strong>
                            @if (!isset($latestHasil) || !$latestHasil)
                                Pengajuan hanya dapat dilakukan <strong>sekali</strong>. Pastikan data benar.
                            @else
                                Data tidak dapat diubah setelah dikirim kecuali ditolak oleh admin.
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col-reverse md:flex-row justify-between gap-4">
                        <a href="{{ route('user.komisi-hasil.index') }}"
                            class="px-6 py-3 rounded-full border-2 border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 hover:border-slate-300 hover:text-slate-800 transition-all duration-300 text-center flex items-center justify-center">
                            <i class="bi bi-arrow-left mr-2"></i> Kembali
                        </a>

                        <button type="submit" id="submit-btn"
                            class="px-8 py-3 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-1 hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 flex items-center justify-center">
                            <i class="bi bi-send-check mr-2"></i>
                            {{ isset($latestHasil) && $latestHasil && $latestHasil->status === 'rejected' ? 'Ajukan Ulang' : 'Kirim Pengajuan' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize AOS
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 600,
                    once: true,
                    offset: 50
                });
            }

            // Auto-hide alerts
            setTimeout(() => {
                $('.alert').not('.alert-warning, .alert-info').fadeOut('slow');
            }, 5000);
        });

        // Form Submission Logic
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('hasil-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const p1 = $('#dosen_pembimbing1_id').val();
                    const p2 = $('#dosen_pembimbing2_id').val();

                    if (!p1 || !p2) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Data Belum Lengkap',
                            text: 'Mohon pilih kedua dosen pembimbing.',
                            confirmButtonColor: '#4f46e5'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Konfirmasi Pengajuan',
                        text: "Apakah data yang Anda masukkan sudah benar?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#4f46e5', // Indigo-600
                        cancelButtonColor: '#94a3b8', // Slate-400
                        confirmButtonText: 'Ya, Kirim!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const btn = document.getElementById('submit-btn');
                            btn.disabled = true;
                            btn.innerHTML =
                                '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
                            form.submit();
                        }
                    });
                });
            }
        });
    </script>
@endpush
