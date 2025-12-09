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

    <section class="py-8 bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50">
        <div class="container mx-auto px-4 max-w-6xl">

            {{-- Alert Messages --}}
            @if (session('success'))
                <div class="mb-5 animate-slide-down" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                    <div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-lg p-4 shadow-md">
                        <div class="flex items-center">
                            <i class="bx bx-check-circle text-emerald-500 text-2xl mr-3"></i>
                            <p class="text-emerald-700 font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-5 animate-slide-down" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                    <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-md">
                        <div class="flex items-center">
                            <i class="bx bx-error text-red-500 text-2xl mr-3"></i>
                            <p class="text-red-700 font-medium">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (!$pendaftaran)
                {{-- Tidak ada pendaftaran yang selesai --}}
                <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
                    <i class="bx bx-info-circle text-6xl text-gray-400 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Surat Usulan</h3>
                    <p class="text-gray-600 mb-6">{{ $message }}</p>
                    <a href="{{ route('user.pendaftaran-seminar-proposal.index') }}"
                        class="inline-flex items-center px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition">
                        <i class="bx bx-left-arrow-alt mr-2"></i>
                        Lihat Status Pendaftaran
                    </a>
                </div>
            @else
                {{-- Ada pendaftaran yang selesai --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- Card Info Pendaftaran --}}
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="bx bx-file-blank text-orange-500 text-2xl mr-2"></i>
                            Informasi Pendaftaran
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="font-semibold text-green-600">Disetujui Lengkap</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Judul Skripsi:</span>
                                <span
                                    class="font-medium text-gray-800 text-right max-w-xs">{{ $pendaftaran->judul_skripsi }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Pembimbing:</span>
                                <span class="font-medium text-gray-800">{{ $pendaftaran->dosenPembimbing->name }}</span>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t">
                            <a href="{{ route('user.pendaftaran-seminar-proposal.download-surat', $pendaftaran) }}"
                                class="inline-flex items-center text-orange-500 hover:text-orange-600 font-medium">
                                <i class="bx bx-download mr-2"></i>
                                Download Surat Usulan
                            </a>
                        </div>
                    </div>

                    {{-- Card Upload SK --}}
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="bx bx-upload text-orange-500 text-2xl mr-2"></i>
                            Upload SK Proposal
                        </h3>

                        @if ($jadwal && $jadwal->hasSkFile())
                            {{-- SK sudah diupload --}}
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-green-700 font-medium mb-2">
                                            <i class="bx bx-check-circle mr-1"></i>
                                            SK Proposal sudah diupload
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            Status: {!! $jadwal->status_badge !!}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('user.jadwal-seminar-proposal.view-sk', $jadwal) }}" target="_blank"
                                    class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                                    <i class="bx bx-show mr-2"></i>
                                    Lihat SK
                                </a>
                                <a href="{{ route('user.jadwal-seminar-proposal.download-sk', $jadwal) }}"
                                    class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                                    <i class="bx bx-download mr-2"></i>
                                    Download
                                </a>
                            </div>

                            @if ($jadwal->status === 'menunggu_jadwal')
                                <form action="{{ route('user.jadwal-seminar-proposal.delete-sk', $jadwal) }}"
                                    method="POST" class="mt-2"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus SK Proposal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                        <i class="bx bx-trash mr-2"></i>
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

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        File SK Proposal (PDF, Maks 2MB) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="file" name="file_sk" accept=".pdf" required
                                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:border-orange-500">
                                    @error('file_sk')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition font-medium">
                                    <i class="bx bx-upload mr-2"></i>
                                    Upload SK Proposal
                                </button>
                            </form>

                            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-sm text-blue-700">
                                    <i class="bx bx-info-circle mr-1"></i>
                                    <strong>Catatan:</strong> Setelah SK Proposal diupload, admin akan melakukan penjadwalan
                                    seminar proposal Anda.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Card Jadwal (jika sudah dijadwalkan) --}}
                @if ($jadwal && $jadwal->hasJadwal())
                    <div class="mt-6 bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="bx bx-calendar text-orange-500 text-2xl mr-2"></i>
                            Jadwal Seminar Proposal
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-orange-50 rounded-lg p-4">
                                <p class="text-sm text-gray-600 mb-1">Tanggal</p>
                                <p class="font-bold text-gray-800">{{ $jadwal->tanggal_formatted }}</p>
                            </div>
                            <div class="bg-orange-50 rounded-lg p-4">
                                <p class="text-sm text-gray-600 mb-1">Waktu</p>
                                <p class="font-bold text-gray-800">{{ $jadwal->jam_formatted }}</p>
                            </div>
                            <div class="bg-orange-50 rounded-lg p-4">
                                <p class="text-sm text-gray-600 mb-1">Ruangan</p>
                                <p class="font-bold text-gray-800">{{ $jadwal->ruangan }}</p>
                            </div>
                            <div class="bg-orange-50 rounded-lg p-4">
                                <p class="text-sm text-gray-600 mb-1">Status</p>
                                <p class="font-bold">{!! $jadwal->status_badge !!}</p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // File size validation
        document.getElementById('uploadSkForm')?.addEventListener('submit', function(e) {
            const fileInput = document.querySelector('input[name="file_sk"]');
            if (fileInput.files.length > 0) {
                const fileSize = fileInput.files[0].size / 1024 / 1024; // in MB
                if (fileSize > 2) {
                    e.preventDefault();
                    alert('Ukuran file tidak boleh lebih dari 2MB');
                    return false;
                }
            }
        });
    </script>
@endpush
