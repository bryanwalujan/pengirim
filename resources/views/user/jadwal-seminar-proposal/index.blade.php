{{-- filepath: resources/views/user/jadwal-seminar-proposal/index.blade.php --}}
@extends('layouts.user.app')

@section('title', 'Jadwal Seminar Proposal')

@section('main')
    <div class="page-title light-background">
        <div class="container">
            <h1 data-aos="fade-up">Jadwal Seminar Proposal</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan</a></li>
                    <li class="current">Jadwal Seminar Proposal</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-12 bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50 min-h-screen">
        <div class="container mx-auto px-4 max-w-7xl">

            {{-- Alert Messages --}}
            @if (session('success'))
                <div class="mb-6" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                    <div
                        class="bg-gradient-to-r from-emerald-50 to-green-50 border-l-4 border-emerald-500 rounded-xl p-5 shadow-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="bx bx-check-circle text-emerald-500 text-3xl"></i>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-emerald-800 font-semibold text-lg">Berhasil!</p>
                                <p class="text-emerald-700 mt-1">{{ session('success') }}</p>
                            </div>
                            <button @click="show = false"
                                class="flex-shrink-0 ml-4 text-emerald-400 hover:text-emerald-600">
                                <i class="bx bx-x text-2xl"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                    <div class="bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 rounded-xl p-5 shadow-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="bx bx-error-circle text-red-500 text-3xl"></i>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-red-800 font-semibold text-lg">Perhatian!</p>
                                <p class="text-red-700 mt-1">{{ session('error') }}</p>
                            </div>
                            <button @click="show = false" class="flex-shrink-0 ml-4 text-red-400 hover:text-red-600">
                                <i class="bx bx-x text-2xl"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if (!$pendaftaran)
                {{-- Tidak ada pendaftaran yang selesai --}}
                <div class="bg-white rounded-3xl shadow-xl p-12 text-center border border-gray-100">
                    <div class="max-w-md mx-auto">
                        <div
                            class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-orange-100 to-amber-100 rounded-full mb-6">
                            <i class="bx bx-info-circle text-orange-500 text-6xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-3">Belum Ada Surat Usulan</h3>
                        <p class="text-gray-600 text-lg mb-8 leading-relaxed">{{ $message }}</p>
                        <a href="{{ route('user.pendaftaran-seminar-proposal.index') }}"
                            class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-semibold rounded-xl hover:from-orange-600 hover:to-amber-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="bx bx-left-arrow-alt text-xl mr-2"></i>
                            Lihat Status Pendaftaran
                        </a>
                    </div>
                </div>
            @else
                {{-- Ada pendaftaran yang selesai --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

                    {{-- Card Info Pendaftaran --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-orange-500 to-amber-500 p-6">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <i class="bx bx-file-blank text-3xl mr-3"></i>
                                Informasi Pendaftaran
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex items-start justify-between pb-4 border-b border-gray-100">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-orange-500 rounded-full mr-3"></div>
                                        <span class="text-gray-600 font-medium">Status</span>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 font-semibold rounded-lg text-sm">
                                        <i class="bx bx-check-circle mr-1"></i>
                                        Disetujui Lengkap
                                    </span>
                                </div>
                                <div class="flex items-start justify-between pb-4 border-b border-gray-100">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-orange-500 rounded-full mr-3"></div>
                                        <span class="text-gray-600 font-medium">Judul Skripsi</span>
                                    </div>
                                    <span
                                        class="font-medium text-gray-800 text-right max-w-xs leading-relaxed">{!! $pendaftaran->judul_skripsi !!}</span>
                                </div>
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-orange-500 rounded-full mr-3"></div>
                                        <span class="text-gray-600 font-medium">Pembimbing</span>
                                    </div>
                                    <span class="font-medium text-gray-800">{{ $pendaftaran->dosenPembimbing->name }}</span>
                                </div>
                            </div>

                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <a href="{{ route('user.pendaftaran-seminar-proposal.download-surat', $pendaftaran) }}"
                                    class="inline-flex items-center text-orange-600 hover:text-orange-700 font-semibold transition-colors group">
                                    <i class="bx bx-download text-xl mr-2 group-hover:animate-bounce"></i>
                                    Download Surat Usulan
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Card Upload SK --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-500 p-6">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <i class="bx bx-upload text-3xl mr-3"></i>
                                Upload SK Proposal
                            </h3>
                        </div>
                        <div class="p-6">
                            @if ($jadwal && $jadwal->hasSkFile())
                                {{-- SK sudah diupload --}}
                                <div
                                    class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl p-5 mb-5">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <i class="bx bx-check-circle text-green-500 text-3xl"></i>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <p class="text-green-800 font-bold text-lg mb-2">
                                                SK Proposal Sudah Diupload
                                            </p>

                                            @if ($jadwal->nomor_sk_proposal)
                                                <div class="mb-3 pb-3 border-b border-green-200">
                                                    <span class="text-sm text-gray-600 font-medium">Nomor SK:</span>
                                                    <p class="text-base text-gray-800 font-semibold mt-1">{{ $jadwal->nomor_sk_proposal }}</p>
                                                </div>
                                            @endif

                                            <div class="flex items-center gap-2">
                                                <span class="text-sm text-gray-700 font-medium">Status:</span>
                                                @if ($jadwal->status === 'menunggu_sk')
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 bg-amber-100 text-amber-700 font-semibold rounded-lg text-sm">
                                                        <i class="bx bx-upload mr-1"></i>
                                                        Menunggu Upload SK
                                                    </span>
                                                @elseif ($jadwal->status === 'menunggu_jadwal')
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 font-semibold rounded-lg text-sm">
                                                        <i class="bx bx-calendar-check mr-1"></i>
                                                        Menunggu Penjadwalan
                                                    </span>
                                                @elseif ($jadwal->status === 'dijadwalkan')
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-700 font-semibold rounded-lg text-sm">
                                                        <i class="bx bx-calendar mr-1"></i>
                                                        Sudah Dijadwalkan
                                                    </span>
                                                @elseif ($jadwal->status === 'selesai')
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 font-semibold rounded-lg text-sm">
                                                        <i class="bx bx-check-circle mr-1"></i>
                                                        Selesai
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 font-semibold rounded-lg text-sm">
                                                        Unknown
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3 mb-3">
                                    <a href="{{ route('user.jadwal-seminar-proposal.view-sk', $jadwal) }}" target="_blank"
                                        class="inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-md hover:shadow-lg">
                                        <i class="bx bx-show text-xl mr-2"></i>
                                        Lihat SK
                                    </a>
                                    <a href="{{ route('user.jadwal-seminar-proposal.download-sk', $jadwal) }}"
                                        class="inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all duration-300 shadow-md hover:shadow-lg">
                                        <i class="bx bx-download text-xl mr-2"></i>
                                        Download
                                    </a>
                                </div>


                                @php
                                    // ✅ PERBAIKAN: Allow delete if status is 'menunggu_jadwal' OR 'dijadwalkan' without berita acara
                                    $canDelete = $jadwal->status === 'menunggu_jadwal' || 
                                                 ($jadwal->status === 'dijadwalkan' && !$jadwal->hasBeritaAcara());
                                @endphp

                                @if ($canDelete)
                                    <form action="{{ route('user.jadwal-seminar-proposal.delete-sk', $jadwal) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-red-500 to-rose-600 text-white font-semibold rounded-xl hover:from-red-600 hover:to-rose-700 transition-all duration-300 shadow-md hover:shadow-lg">
                                            <i class="bx bx-trash text-xl mr-2"></i>
                                            Hapus SK & Upload Ulang
                                        </button>
                                    </form>
                                @endif
                            @else
                                {{-- Form upload SK --}}
                                <form action="{{ route('user.jadwal-seminar-proposal.upload-sk') }}" method="POST"
                                    enctype="multipart/form-data" id="uploadSkForm">
                                    @csrf
                                    <input type="hidden" name="pendaftaran_id" value="{{ $pendaftaran->id }}">

                                    <div class="mb-5">
                                        <label class="block text-sm font-bold text-gray-700 mb-3">
                                            Nomor SK Proposal <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="nomor_sk_proposal" value="{{ old('nomor_sk_proposal') }}" required
                                            maxlength="100" placeholder="Contoh: 3714/UN41.2/PS/2025"
                                            class="block w-full px-4 py-3 text-gray-900 border-2 border-gray-300 rounded-xl focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all duration-300">
                                        @error('nomor_sk_proposal')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <i class="bx bx-error-circle mr-1"></i>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                        <p class="mt-2 text-xs text-gray-500">
                                            <i class="bx bx-info-circle mr-1"></i>
                                            Masukkan nomor SK sesuai dengan yang tertera pada dokumen
                                        </p>
                                    </div>

                                    <div class="mb-6">
                                        <label class="block text-sm font-bold text-gray-700 mb-3">
                                            File SK Proposal (PDF, Maks 2MB) <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="file" name="file_sk" accept=".pdf" required
                                                class="block w-full text-sm text-gray-900 border-2 border-gray-300 rounded-xl cursor-pointer bg-gray-50 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all duration-300 file:mr-4 file:py-3 file:px-4 file:rounded-l-xl file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                                        </div>
                                        @error('file_sk')
                                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                                <i class="bx bx-error-circle mr-1"></i>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold rounded-xl hover:from-orange-600 hover:to-amber-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                        <i class="bx bx-upload text-xl mr-2"></i>
                                        Upload SK Proposal
                                    </button>
                                </form>

                                <div
                                    class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-lg">
                                    <div class="flex items-start">
                                        <i class="bx bx-info-circle text-blue-500 text-2xl mr-3 flex-shrink-0"></i>
                                        <div>
                                            <p class="text-sm text-blue-800 font-semibold mb-1">Catatan Penting</p>
                                            <p class="text-sm text-blue-700 leading-relaxed">
                                                Setelah SK Proposal diupload, admin akan melakukan penjadwalan seminar
                                                proposal Anda.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Card Jadwal (jika sudah dijadwalkan) --}}
                @if ($jadwal && $jadwal->hasJadwal())
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-500 to-indigo-600 p-6">
                            <h3 class="text-xl font-bold text-white flex items-center">
                                <i class="bx bx-calendar text-3xl mr-3"></i>
                                Jadwal Seminar Proposal Anda
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div
                                    class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl p-5 border border-orange-100 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-center mb-3">
                                        <div
                                            class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                                            <i class="bx bx-calendar text-white text-xl"></i>
                                        </div>
                                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Tanggal</p>
                                    </div>
                                    <p class="font-bold text-gray-900 text-lg">{{ $jadwal->tanggal_formatted }}</p>
                                </div>
                                <div
                                    class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-100 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-center mb-3">
                                        <div
                                            class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                            <i class="bx bx-time text-white text-xl"></i>
                                        </div>
                                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Waktu</p>
                                    </div>
                                    <p class="font-bold text-gray-900 text-lg">{{ $jadwal->jam_formatted }}</p>
                                </div>
                                <div
                                    class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-5 border border-green-100 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-center mb-3">
                                        <div
                                            class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                            <i class="bx bx-door-open text-white text-xl"></i>
                                        </div>
                                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Ruangan</p>
                                    </div>
                                    <p class="font-bold text-gray-900 text-lg">{{ $jadwal->ruangan }}</p>
                                </div>
                                <div
                                    class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl p-5 border border-purple-100 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-center mb-3">
                                        <div
                                            class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                                            <i class="bx bx-info-circle text-white text-xl"></i>
                                        </div>
                                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</p>
                                    </div>
                                    <div class="font-bold text-sm">
                                        @if ($jadwal->status === 'menunggu_sk')
                                            <span
                                                class="inline-flex items-center px-3 py-1 bg-amber-100 text-amber-700 font-semibold rounded-lg">
                                                <i class="bx bx-upload mr-1"></i>
                                                Menunggu Upload SK
                                            </span>
                                        @elseif ($jadwal->status === 'menunggu_jadwal')
                                            <span
                                                class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 font-semibold rounded-lg">
                                                <i class="bx bx-calendar-check mr-1"></i>
                                                Menunggu Penjadwalan
                                            </span>
                                        @elseif ($jadwal->status === 'dijadwalkan')
                                            <span
                                                class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-700 font-semibold rounded-lg">
                                                <i class="bx bx-calendar mr-1"></i>
                                                Sudah Dijadwalkan
                                            </span>
                                        @elseif ($jadwal->status === 'selesai')
                                            <span
                                                class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 font-semibold rounded-lg">
                                                <i class="bx bx-check-circle mr-1"></i>
                                                Selesai
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 font-semibold rounded-lg">
                                                Unknown
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </section>
@endsection

{{-- filepath: resources/views/user/jadwal-seminar-proposal/index.blade.php --}}
@push('scripts')
    <script>
        // ========== WAIT FOR DOM AND SWAL TO BE READY ==========
        document.addEventListener('DOMContentLoaded', function() {
            // Check if SweetAlert2 is loaded
            if (typeof Swal === 'undefined') {
                console.error('SweetAlert2 is not loaded!');
                return;
            }

            // ========== SWEET ALERT 2 CONFIGURATION ==========
            const UploadToast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            // ========== FILE UPLOAD VALIDATION ==========
            const uploadForm = document.getElementById('uploadSkForm');
            const fileInput = document.querySelector('input[name="file_sk"]');
            const submitButton = uploadForm?.querySelector('button[type="submit"]');

            let isSubmitting = false;

            if (uploadForm) {
                // Real-time file validation
                fileInput?.addEventListener('change', function(e) {
                    const file = e.target.files[0];

                    if (!file) return;

                    // Validate file type
                    if (file.type !== 'application/pdf') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Format File Salah',
                            text: 'File harus berformat PDF',
                            confirmButtonColor: '#f59e0b',
                            confirmButtonText: 'OK'
                        });
                        e.target.value = '';
                        return;
                    }

                    // Validate file size (max 2MB)
                    const fileSize = file.size / 1024 / 1024; // in MB
                    if (fileSize > 2) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Ukuran File Terlalu Besar',
                            html: `
                                <p class="text-gray-600 mb-2">Ukuran file: <strong>${fileSize.toFixed(2)} MB</strong></p>
                                <p class="text-gray-600">Maksimal: <strong>2 MB</strong></p>
                            `,
                            confirmButtonColor: '#f59e0b',
                            confirmButtonText: 'OK'
                        });
                        e.target.value = '';
                        return;
                    }

                    // Show success preview
                    UploadToast.fire({
                        icon: 'success',
                        title: 'File Valid',
                        text: `${file.name} (${fileSize.toFixed(2)} MB)`
                    });
                });

                // Form submission validation (SEPERTI DI KOMISI PROPOSAL)
                uploadForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Prevent duplicate submission
                    if (isSubmitting) {
                        UploadToast.fire({
                            icon: 'warning',
                            title: 'Sedang Proses Upload',
                            text: 'Mohon tunggu sebentar...'
                        });
                        return false;
                    }

                    const file = fileInput.files[0];

                    // Validate file exists
                    if (!file) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'File Belum Dipilih',
                            text: 'Silakan pilih file SK Proposal terlebih dahulu',
                            confirmButtonColor: '#f59e0b',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }

                    // Validate file type
                    if (file.type !== 'application/pdf') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Format File Salah',
                            text: 'File harus berformat PDF',
                            confirmButtonColor: '#f59e0b',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }

                    // Validate file size
                    const fileSize = file.size / 1024 / 1024;
                    if (fileSize > 2) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Ukuran File Terlalu Besar',
                            html: `
                                <p class="text-gray-600 mb-2">Ukuran file: <strong>${fileSize.toFixed(2)} MB</strong></p>
                                <p class="text-gray-600">Maksimal: <strong>2 MB</strong></p>
                            `,
                            confirmButtonColor: '#f59e0b',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }

                    // Confirmation dialog
                    Swal.fire({
                        title: '<strong>Konfirmasi Upload</strong>',
                        html: `
                            <div class="text-start">
                                <p class="mb-3">Apakah Anda yakin ingin mengupload file SK Proposal?</p>
                                <div class="bg-blue-50 rounded-lg p-3 border border-blue-200 mb-2">
                                    <p class="text-sm text-gray-600 mb-1"><strong>Nama File:</strong></p>
                                    <p class="text-sm text-blue-700 break-all">${file.name}</p>
                                </div>
                                <div class="bg-blue-50 rounded-lg p-3 border border-blue-200 mb-3">
                                    <p class="text-sm text-gray-600 mb-1"><strong>Ukuran:</strong></p>
                                    <p class="text-sm text-blue-700">${fileSize.toFixed(2)} MB</p>
                                </div>
                                <div class="alert alert-warning mb-0">
                                    <small>
                                        <i class="bx bx-info-circle me-1"></i>
                                        <strong>Perhatian:</strong> Pastikan file yang diupload adalah SK Proposal yang benar
                                    </small>
                                </div>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#f59e0b',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: '<i class="bx bx-upload me-1"></i> Ya, Upload',
                        cancelButtonText: '<i class="bx bx-x me-1"></i> Batal',
                        customClass: {
                            confirmButton: 'btn btn-primary rounded-xl px-4 py-2',
                            cancelButton: 'btn btn-secondary rounded-xl px-4 py-2 me-2'
                        },
                        buttonsStyling: false,
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Set submitting flag
                            isSubmitting = true;

                            // Disable submit button (JANGAN disable file input!)
                            if (submitButton) {
                                submitButton.disabled = true;
                                submitButton.innerHTML = `
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    Uploading...
                                `;
                            }

                            // Show loading
                            Swal.fire({
                                title: 'Uploading SK Proposal',
                                html: `
                                    <div class="text-center">
                                        <div class="spinner-border text-primary mb-3" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="text-gray-600">Mohon tunggu, file sedang diupload...</p>
                                        <p class="text-sm text-gray-500 mt-2">Jangan tutup atau refresh halaman ini</p>
                                    </div>
                                `,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // Submit form immediately (tanpa setTimeout)
                            this.submit();
                        }
                    });
                });
            }

            // ========== DELETE SK CONFIRMATION ==========
            const deleteForm = document.querySelector('form[action*="delete-sk"]');

            if (deleteForm) {
                deleteForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: '<strong>Konfirmasi Hapus</strong>',
                        html: `
                            <div class="text-start">
                                <p class="mb-3">Apakah Anda yakin ingin menghapus SK Proposal?</p>
                                <div class="alert alert-danger mb-0">
                                    <small>
                                        <i class="bx bx-error-circle me-1"></i>
                                        <strong>Perhatian:</strong> Setelah dihapus, Anda harus mengupload ulang file SK Proposal yang baru.
                                    </small>
                                </div>
                            </div>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: '<i class="bx bx-trash me-1"></i> Ya, Hapus',
                        cancelButtonText: '<i class="bx bx-x me-1"></i> Batal',
                        customClass: {
                            confirmButton: 'btn btn-danger rounded-xl px-4 py-2',
                            cancelButton: 'btn btn-secondary rounded-xl px-4 py-2 me-2'
                        },
                        buttonsStyling: false,
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show deleting message
                            Swal.fire({
                                title: 'Menghapus SK Proposal',
                                html: '<div class="spinner-border text-danger" role="status"></div>',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            this.submit();
                        }
                    });

                    return false;
                });
            }

            // ========== PREVENT MULTIPLE FORM SUBMISSIONS ==========
            window.addEventListener('pageshow', function() {
                isSubmitting = false;
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = `
                        <i class="bx bx-upload text-xl mr-2"></i>
                        Upload SK Proposal
                    `;
                }
            });

            // ========== FILE INPUT DRAG & DROP ENHANCEMENT ==========
            if (fileInput) {
                const fileInputParent = fileInput.parentElement;

                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    fileInputParent.addEventListener(eventName, preventDefaults, false);
                });

                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                ['dragenter', 'dragover'].forEach(eventName => {
                    fileInputParent.addEventListener(eventName, () => {
                        fileInputParent.classList.add('border-orange-500', 'bg-orange-50');
                    }, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    fileInputParent.addEventListener(eventName, () => {
                        fileInputParent.classList.remove('border-orange-500', 'bg-orange-50');
                    }, false);
                });

                fileInputParent.addEventListener('drop', function(e) {
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        fileInput.files = files;
                        const event = new Event('change', {
                            bubbles: true
                        });
                        fileInput.dispatchEvent(event);
                    }
                }, false);
            }
        }); // END DOMContentLoaded
    </script>
@endpush
