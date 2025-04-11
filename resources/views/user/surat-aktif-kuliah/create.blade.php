@extends('layouts.user.app')

@section('title', 'Pengajuan Surat Aktif Kuliah')

@push('style')
    <!-- CSS Libraries -->
@endpush

@section('main')
    <!-- Page Title -->
    <div class="page-title light-background">
        <div class="container">
            <h1>Pengajuan Surat Aktif Kuliah</h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.services.index') }}">Layanan</a></li>
                    <li class="current">Pengajuan Surat Aktif Kuliah</li>
                </ol>
            </nav>
        </div>
    </div><!-- End Page Title -->

    <!-- Form Section -->
    <section id="form-surat" class="form-surat section">
        <div class="container" data-aos="fade-up">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-body">
                            <h4 class="card-title text-center mb-4">Formulir Pengajuan Surat Aktif Kuliah</h4>

                            <form action="{{ route('user.surat-aktif-kuliah.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf

                                <!-- Informasi Mahasiswa -->
                                <div class="mb-4">
                                    <h5 class="section-title">Informasi Mahasiswa</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nama Lengkap</label>
                                            <input type="text" class="form-control" value="{{ Auth::user()->name }}"
                                                readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">NIM</label>
                                            <input type="text" class="form-control" value="{{ Auth::user()->nim }}"
                                                readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Program Studi</label>
                                            <input type="text" class="form-control" value="{{ Auth::user()->prodi }}"
                                                readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Semester</label>
                                            <input type="text" class="form-control" value="{{ Auth::user()->semester }}"
                                                readonly>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informasi Akademik -->
                                <div class="mb-4">
                                    <h5 class="section-title">Informasi Akademik</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="tahun_ajaran" class="form-label">Tahun Ajaran <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select select2 @error('tahun_ajaran') is-invalid @enderror"
                                                id="tahun_ajaran" name="tahun_ajaran" required>
                                                <option value="">Pilih Tahun Ajaran</option>
                                                @foreach (range(date('Y') - 3, date('Y') + 1) as $year)
                                                    <option value="{{ $year }}/{{ $year + 1 }}"
                                                        {{ old('tahun_ajaran') == $year . '/' . ($year + 1) ? 'selected' : '' }}>
                                                        {{ $year }}/{{ $year + 1 }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('tahun_ajaran')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="semester" class="form-label">Semester <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('semester') is-invalid @enderror"
                                                id="semester" name="semester" required>
                                                <option value="">Pilih Semester</option>
                                                <option value="ganjil" {{ old('semester') == 'ganjil' ? 'selected' : '' }}>
                                                    Ganjil</option>
                                                <option value="genap" {{ old('semester') == 'genap' ? 'selected' : '' }}>
                                                    Genap</option>
                                            </select>
                                            @error('semester')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Tujuan Pengajuan -->
                                <div class="mb-4">
                                    <h5 class="section-title">Tujuan Pengajuan</h5>
                                    <div class="mb-3">
                                        <label for="tujuan_pengajuan" class="form-label">Tujuan Pengajuan Surat <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control @error('tujuan_pengajuan') is-invalid @enderror" id="tujuan_pengajuan"
                                            name="tujuan_pengajuan" rows="3" required>{{ old('tujuan_pengajuan') }}</textarea>
                                        <small class="text-muted">Contoh: Untuk keperluan beasiswa, pengajuan KIP-Kuliah,
                                            dll.</small>
                                        @error('tujuan_pengajuan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Keterangan Tambahan -->
                                <div class="mb-4">
                                    <h5 class="section-title">Keterangan Tambahan</h5>
                                    <div class="mb-3">
                                        <label for="keterangan_tambahan" class="form-label">Keterangan Tambahan</label>
                                        <textarea class="form-control @error('keterangan_tambahan') is-invalid @enderror" id="keterangan_tambahan"
                                            name="keterangan_tambahan" rows="3">{{ old('keterangan_tambahan') }}</textarea>
                                        <small class="text-muted">Isi jika ada keterangan khusus yang perlu
                                            disampaikan</small>
                                        @error('keterangan_tambahan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- File Pendukung -->
                                <div class="mb-4">
                                    <h5 class="section-title">Dokumen Pendukung</h5>
                                    <div class="mb-3">
                                        <label for="file_pendukung" class="form-label">Upload Dokumen Pendukung</label>
                                        <input type="file"
                                            class="form-control @error('file_pendukung') is-invalid @enderror"
                                            id="file_pendukung" name="file_pendukung">
                                        <small class="text-muted">Format: PDF/JPG/PNG (Maks. 2MB)</small>
                                        @error('file_pendukung')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                    <a href="{{ route('user.services.index') }}" class="btn btn-secondary me-md-2">
                                        <i class="bi bi-arrow-left"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send"></i> Ajukan Surat
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- End Form Section -->
@endsection

@push('scripts')
    <!-- JS Libraries -->

    <script>
        $(document).ready(function() {
            // Initialize select2
            $('.select2').select2({
                placeholder: 'Pilih Tahun Ajaran',
                allowClear: true
            });

            // Form validation
            $('form').submit(function() {
                $(this).find(':submit').prop('disabled', true);
                $(this).find(':submit').html('<i class="bi bi-hourglass"></i> Mengirim...');
            });
        });
    </script>
@endpush
