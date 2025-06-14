@extends('layouts.user.form')

@section('title', 'Form Pengajuan Persetujuan Hasil')

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

        .form-header p {
            margin: 0.5rem 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
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

        .form-floating>label {
            padding: 0.85rem 1.25rem;
            color: #6c757d;
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

        .dosen-card {
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: var(--transition);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.03);
        }

        .dosen-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-color: var(--accent-color);
        }

        .dosen-name {
            font-weight: 600;
            color: var(--dark-gray);
            margin-bottom: 0.5rem;
        }

        .dosen-info {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .dosen-info i {
            margin-right: 0.5rem;
            width: 16px;
            text-align: center;
        }

        .select2-container--default .select2-selection--single {
            height: 48px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            padding: 0.5rem 1rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            color: #495057;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px;
        }

        .select2-container--default .select2-results__option--highlighted {
            background-color: var(--accent-color);
        }

        .select2-container--default .select2-results__option--selected {
            background-color: var(--primary-color);
        }

        .select2-dropdown {
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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
                width: 100%;
            }

            .action-buttons {
                flex-direction: column;
                gap: 0.75rem;
            }
        }
    </style>
@endpush

@section('form-content')
    <div class="form-container" data-aos="fade-up" data-aos-delay="100">
        <div class="form-card">
            <div class="form-header">
                <h4>Formulir Persetujuan Komisi Pembimbing</h4>
                <p>Silakan isi judul skripsi dan pilih dosen pembimbing Anda dari daftar yang tersedia</p>
            </div>

            <form action="{{ route('user.komisi-hasil.store') }}" method="POST" id="hasil-form">
                @csrf

                <!-- Informasi Mahasiswa Section -->
                <div class="form-section" data-aos="fade-up" data-aos-delay="200">
                    <h5 class="section-title">
                        <i class="bi bi-person-circle"></i>
                        Informasi Mahasiswa
                    </h5>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="nama" value="{{ Auth::user()->name }}"
                                    readonly>
                                <label for="nama">Nama Lengkap</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="nim" value="{{ Auth::user()->nim }}"
                                    readonly>
                                <label for="nim">Nomor Induk Mahasiswa</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Judul Skripsi Section -->
                <div class="form-section" data-aos="fade-up" data-aos-delay="300">
                    <h5 class="section-title">
                        <i class="bi bi-journal-text"></i>
                        Judul Skripsi
                    </h5>

                    <div class="mb-4">
                        <label for="judul_skripsi" class="form-label">Judul Skripsi <span
                                class="text-danger">*</span></label>
                        <!-- Hidden input untuk menyimpan data -->
                        <input id="judul_skripsi" type="hidden" name="judul_skripsi" value="{{ old('judul_skripsi') }}">
                        <!-- Trix Editor yang akan menampilkan konten -->
                        <trix-editor input="judul_skripsi" class="form-control @error('judul_skripsi') is-invalid @enderror"
                            placeholder="Masukkan judul lengkap skripsi Anda"></trix-editor>
                        {{-- <textarea class="form-control @error('judul_skripsi') is-invalid @enderror" id="judul_skripsi" name="judul_skripsi"
                            rows="4" required placeholder="Masukkan judul lengkap skripsi Anda">{{ old('judul_skripsi') }}</textarea> --}}
                        <div class="form-text mt-2">Pastikan judul skripsi sudah disetujui oleh calon pembimbing</div>
                        @error('judul_skripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Dosen Pembimbing Section -->
                <div class="form-section" data-aos="fade-up" data-aos-delay="400">
                    <h5 class="section-title">
                        <i class="bi bi-people-fill"></i>
                        Dosen Pembimbing
                    </h5>

                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading fw-bold"><i class="bi bi-info-circle-fill me-2"></i>Panduan Pemilihan Dosen
                        </h6>
                        <ul class="mb-0">
                            <li>Pastikan sudah berdiskusi dengan calon pembimbing sebelum mengajukan</li>
                            <li>Pilih dosen yang sesuai dengan bidang penelitian skripsi Anda</li>
                            <li>Jika belum menentukan pembimbing, konsultasikan terlebih dahulu dengan koordinator program
                                studi</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <label for="dosen_pembimbing1_id" class="form-label">Dosen Pembimbing I <span
                                class="text-danger">*</span></label>
                        <select class="form-select @error('dosen_pembimbing1_id') is-invalid @enderror select2"
                            id="dosen_pembimbing1_id" name="dosen_pembimbing1_id" required>
                            <option selected disabled value="">Pilih Dosen Pembimbing ...</option>
                            @foreach ($dosens as $dosen)
                                <option value="{{ $dosen->id }}"
                                    {{ old('dosen_pembimbing1_id') == $dosen->id ? 'selected' : '' }}>{{ $dosen->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('dosen_pembimbing1_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="dosen_pembimbing2_id" class="form-label">Dosen Pembimbing II <span
                                class="text-danger">*</span></label>
                        <select class="form-select @error('dosen_pembimbing2_id') is-invalid @enderror select2"
                            id="dosen_pembimbing2_id" name="dosen_pembimbing2_id" required>
                            <option selected disabled value="">Pilih Dosen Pembimbing ...</option>
                            @foreach ($dosens as $dosen)
                                <option value="{{ $dosen->id }}"
                                    {{ old('dosen_pembimbing2_id') == $dosen->id ? 'selected' : '' }}>{{ $dosen->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('dosen_pembimbing2_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="dosen-list">
                        <h6 class="mb-3 fw-semibold">Daftar Dosen Pembimbing Tersedia:</h6>
                        <div class="row">
                            @foreach ($dosens as $dosen)
                                <div class="col-md-6">
                                    <div class="dosen-card">
                                        <h6 class="dosen-name">{{ $dosen->name }}</h6>
                                        <div class="dosen-info">
                                            <div><i class="bi bi-envelope"></i> {{ $dosen->email }}</div>
                                            <div><i class="bi bi-bookmark"></i> Bidang: {{ $dosen->jabatan ?? 'Umum' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-section pt-0" data-aos="fade-up" data-aos-delay="500">
                    <div class="d-flex justify-content-between gap-3">
                        <a href="{{ route('user.komisi-hasil.index') }}" class="btn btn-back">
                            <i class="bi bi-arrow-left me-2"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-submit text-white" id="submit-btn">
                            <i class="bi bi-send-check me-2"></i> Ajukan Persetujuan
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

        // Initialize Select2
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Pilih dosen pembimbing",
                allowClear: true,
                width: '100%'
            });

            // Prevent selecting same dosen for both pembimbing
            $('#dosen_pembimbing1_id, #dosen_pembimbing2_id').on('change', function() {
                const pembimbing1 = $('#dosen_pembimbing1_id').val();
                const pembimbing2 = $('#dosen_pembimbing2_id').val();

                if (pembimbing1 && pembimbing2 && pembimbing1 === pembimbing2) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Pembimbing Tidak Valid',
                        text: 'Dosen pembimbing 1 dan 2 tidak boleh sama',
                        confirmButtonColor: '#4361ee'
                    });
                    $(this).val('').trigger('change');
                }
            });
        });

        // Form submission handler with SweetAlert
        document.getElementById('proposal-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Mengirim...';

            // Simulate form submission
            this.submit();
        });
    </script>
@endpush
