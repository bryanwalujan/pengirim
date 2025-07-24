@extends('layouts.user.form')

@section('title', 'Form Pendaftaran Ujian Hasil')

@push('styles')
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
            --border-radius: 15px;
            --box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        .form-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2.5rem 1rem;
        }

        .form-card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            background: linear-gradient(145deg, #ffffff, #f9f9f9);
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            text-align: center;
            border-bottom: 4px solid var(--accent-color);
        }

        .form-header h4 {
            font-weight: 700;
            margin: 0;
            font-size: 1.75rem;
            letter-spacing: 0.5px;
        }

        .form-section {
            padding: 1rem 2.5rem 2rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            background: rgba(255, 255, 255, 0.5);
            border-radius: 10px;
        }

        .section-title {
            display: flex;
            align-items: center;
            color: var(--dark-gray);
            font-weight: 700;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
            text-transform: uppercase;
        }

        .section-title i {
            margin-right: 0.75rem;
            color: var(--accent-color);
            font-size: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark-gray);
            font-size: 0.95rem;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            padding: 0.85rem 1.25rem;
            border: 1px solid #e0e0e0;
            transition: var(--transition);
            background: #fff;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.03);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.2);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 0.85rem 2.5rem;
            font-weight: 600;
            border-radius: 50px;
            transition: var(--transition);
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }

        .btn-back {
            background: #fff;
            border: 2px solid #e0e0e0;
            color: var(--dark-gray);
            padding: 0.85rem 2.5rem;
            border-radius: 50px;
            transition: var(--transition);
        }

        .btn-back:hover {
            background: var(--light-gray);
            border-color: var(--accent-color);
            color: var(--dark-gray);
            transform: translateY(-3px);
        }

        .alert-info {
            background-color: #e7f5ff;
            border-color: #d0ebff;
            color: #1864ab;
            border-radius: 10px;
            padding: 1rem 1.5rem;
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 1.5rem 0.5rem;
            }

            .form-section {
                padding: 1rem 1.5rem 1.5rem;
            }

            .form-header {
                padding: 1.5rem;
            }

            .btn-submit,
            .btn-back {
                padding: 0.75rem 1.5rem;
            }
        }
    </style>
@endpush

@section('form-content')
    <div class="form-container" data-aos="fade-up" data-aos-delay="100">
        <div class="form-card">
            <div class="form-header">
                <h4>Formulir Pendaftaran Ujian Hasil</h4>
            </div>

            <form action="{{ route('user.pendaftaran-ujian-hasil.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Informasi Mahasiswa Section -->
                <div class="form-section" data-aos="fade-up" data-aos-delay="200">
                    <h5 class="section-title">
                        <i class="bi bi-person-circle"></i>
                        Informasi Mahasiswa
                    </h5>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama" value="{{ Auth::user()->name }}"
                                    readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nim" class="form-label">Nomor Induk Mahasiswa</label>
                                <input type="text" class="form-control" id="nim" value="{{ Auth::user()->nim }}"
                                    readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Skripsi Section -->
                <div class="form-section" data-aos="fade-up" data-aos-delay="300">
                    <h5 class="section-title">
                        <i class="bi bi-journal-text"></i>
                        Informasi Skripsi
                    </h5>

                    <div class="mb-4">
                        <label for="judul_skripsi" class="form-label">Judul Skripsi</label>
                        <textarea class="form-control @error('judul_skripsi') is-invalid @enderror" id="judul_skripsi" name="judul_skripsi"
                            rows="3" placeholder="Masukkan judul lengkap skripsi Anda" required>{{ old('judul_skripsi') }}</textarea>
                        @error('judul_skripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="ipk" class="form-label">IPK Terakhir</label>
                            <input type="number" class="form-control @error('ipk') is-invalid @enderror" id="ipk"
                                name="ipk" value="{{ old('ipk') }}" placeholder="Contoh: 3.51" step="0.01"
                                min="0" max="4.00" required>
                            @error('ipk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-md-4">
                            <label for="dosen_pa_id" class="form-label">Dosen Pembimbing Akademik</label>
                            <select class="form-select @error('dosen_pa_id') is-invalid @enderror" id="dosen_pa_id"
                                name="dosen_pa_id" required>
                                <option value="" disabled selected>-- Pilih Dosen --</option>
                                @foreach ($dosens as $dosen)
                                    <option value="{{ $dosen->id }}"
                                        {{ old('dosen_pa_id') == $dosen->id ? 'selected' : '' }}>{{ $dosen->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('dosen_pa_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="dosen_pembimbing1_id" class="form-label">Dosen Pembimbing 1</label>
                            <select class="form-select @error('dosen_pembimbing1_id') is-invalid @enderror"
                                id="dosen_pembimbing1_id" name="dosen_pembimbing1_id" required>
                                <option value="" disabled selected>-- Pilih Dosen --</option>
                                @foreach ($dosens as $dosen)
                                    <option value="{{ $dosen->id }}"
                                        {{ old('dosen_pembimbing1_id') == $dosen->id ? 'selected' : '' }}>
                                        {{ $dosen->name }}</option>
                                @endforeach
                            </select>
                            @error('dosen_pembimbing1_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="dosen_pembimbing2_id" class="form-label">Dosen Pembimbing 2</label>
                            <select class="form-select @error('dosen_pembimbing2_id') is-invalid @enderror"
                                id="dosen_pembimbing2_id" name="dosen_pembimbing2_id" required>
                                <option value="" disabled selected>-- Pilih Dosen --</option>
                                @foreach ($dosens as $dosen)
                                    <option value="{{ $dosen->id }}"
                                        {{ old('dosen_pembimbing2_id') == $dosen->id ? 'selected' : '' }}>
                                        {{ $dosen->name }}</option>
                                @endforeach
                            </select>
                            @error('dosen_pembimbing2_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Dokumen Pendukung Section -->
                <div class="form-section" data-aos="fade-up" data-aos-delay="400">
                    <h5 class="section-title">
                        <i class="bi bi-cloud-arrow-up"></i>
                        Upload Dokumen
                    </h5>

                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading fw-bold"><i class="bi bi-info-circle-fill me-2"></i>Persyaratan Dokumen
                        </h6>
                        <p>Silakan upload dokumen-dokumen berikut dalam format PDF, ukuran maksimal 2MB (File Skripsi
                            maksimal 5MB):</p>
                        <ol>
                            <li>Transkrip Nilai</li>
                            <li>File Skripsi (Lengkap)</li>
                            <li>Surat Komisi Hasil</li>
                            <li>Surat Permohonan Ujian Hasil</li>
                        </ol>
                    </div>

                    <div class="mb-3">
                        <label for="transkrip_nilai" class="form-label">1. Transkrip Nilai</label>
                        <input class="form-control @error('transkrip_nilai') is-invalid @enderror" type="file"
                            id="transkrip_nilai" name="transkrip_nilai" accept=".pdf" required>
                        @error('transkrip_nilai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="file_skripsi" class="form-label">2. File Skripsi (Lengkap)</label>
                        <input class="form-control @error('file_skripsi') is-invalid @enderror" type="file"
                            id="file_skripsi" name="file_skripsi" accept=".pdf" required>
                        @error('file_skripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="komisi_hasil" class="form-label">3. Surat Komisi Hasil</label>
                        <input class="form-control @error('komisi_hasil') is-invalid @enderror" type="file"
                            id="komisi_hasil" name="komisi_hasil" accept=".pdf" required>
                        @error('komisi_hasil')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="surat_permohonan_hasil" class="form-label">4. Surat Permohonan Ujian Hasil</label>
                        <input class="form-control @error('surat_permohonan_hasil') is-invalid @enderror" type="file"
                            id="surat_permohonan_hasil" name="surat_permohonan_hasil" accept=".pdf" required>
                        @error('surat_permohonan_hasil')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-section pt-0" data-aos="fade-up" data-aos-delay="500">
                    <div class="d-flex justify-content-between gap-3">
                        <a href="{{ route('user.pendaftaran-ujian-hasil.index') }}" class="btn btn-back">
                            <i class="bi bi-arrow-left me-2"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-submit text-white">
                            <i class="bi bi-send-check me-2"></i> Ajukan Pendaftaran
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        AOS.init({
            duration: 400,
            once: true,
            offset: 100
        });
    </script>
@endpush
