@extends('layouts.admin.app')

@section('title', 'Tambah Pembayaran UKT')

@push('styles')
    <style>
        .form-label.required:after {
            content: " *";
            color: #ff3e1d;
            font-weight: bold;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            border-color: #d9dee3;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: 12px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .info-card {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            border-left: 4px solid #667eea;
            border-radius: 8px;
        }

        .status-preview {
            transition: all 0.3s ease;
            padding: 15px;
            border-radius: 8px;
            background: #f8f9fa;
        }

        .status-preview.bayar {
            background: #d4edda;
            border: 2px solid #28a745;
        }

        .status-preview.belum_bayar {
            background: #fff3cd;
            border: 2px solid #ffc107;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5568d3 0%, #63408b 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard.index') }}">
                        Dashboard
                        <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>

                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.pembayaran-ukt.index') }}">Pembayaran UKT</a>
                    <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>

                </li>
                <li class="breadcrumb-item active">Tambah Baru</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="bx bx-plus-circle text-primary"></i> Tambah Pembayaran UKT
                </h4>
                <p class="text-muted mb-0 small">Tambahkan data pembayaran UKT mahasiswa baru</p>
            </div>
            <a href="{{ route('admin.pembayaran-ukt.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>

        <!-- Info Card -->
        @if ($mahasiswa->count() == 0)
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bx bx-info-circle me-2" style="font-size: 24px;"></i>
                    <div>
                        <h6 class="mb-1">Tidak Ada Mahasiswa Tersedia</h6>
                        <small>Semua mahasiswa sudah memiliki data pembayaran untuk tahun ajaran aktif saat ini.</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @else
            <div class="info-card p-3 mb-4">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar">
                            <span class="avatar-initial rounded-circle bg-label-primary">
                                <i class="bx bx-info-circle bx-md"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-2">Informasi Penting</h6>
                        <ul class="mb-0 ps-3 small">
                            <li>Pastikan data mahasiswa dan tahun ajaran sudah benar</li>
                            <li>Status pembayaran dapat diubah sewaktu-waktu</li>
                            <li>Mahasiswa yang sudah memiliki data pada tahun ajaran tertentu tidak akan muncul di daftar
                            </li>
                            <li class="text-primary fw-semibold">Total mahasiswa tersedia: {{ $mahasiswa->count() }} orang
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Card -->
        <div class="card shadow-sm">
            <div class="card-header text-white border-0">
                <h5 class="card-title mb-0 text-white">
                    <i class="bx bx-edit"></i> Form Pembayaran UKT
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.pembayaran-ukt.store') }}" method="POST" id="createForm">
                    @csrf

                    <div class="row">
                        <!-- Mahasiswa Selection -->
                        <div class="col-md-6 mb-4">
                            <label for="mahasiswa_id" class="form-label required">Mahasiswa</label>
                            <select class="form-select select2 @error('mahasiswa_id') is-invalid @enderror"
                                id="mahasiswa_id" name="mahasiswa_id" required
                                {{ $mahasiswa->count() == 0 ? 'disabled' : '' }}>
                                <option value="">-- Pilih Mahasiswa --</option>
                                @foreach ($mahasiswa as $mhs)
                                    <option value="{{ $mhs->id }}"
                                        {{ old('mahasiswa_id') == $mhs->id ? 'selected' : '' }}
                                        data-nim="{{ $mhs->nim }}" data-name="{{ $mhs->name }}">
                                        {{ $mhs->nim }} - {{ $mhs->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mahasiswa_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i> Pilih mahasiswa yang akan ditambahkan data pembayarannya
                            </small>

                            <!-- Selected Mahasiswa Preview -->
                            <div id="mahasiswaPreview" class="mt-3" style="display: none;">
                                <div class="alert alert-info mb-0">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-primary"
                                                id="previewInitial"></span>
                                        </div>
                                        <div>
                                            <div class="fw-semibold" id="previewName"></div>
                                            <small class="text-muted" id="previewNim"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tahun Ajaran Selection -->
                        <div class="col-md-6 mb-4">
                            <label for="tahun_ajaran_id" class="form-label required">Tahun Ajaran</label>
                            <select class="form-select @error('tahun_ajaran_id') is-invalid @enderror" id="tahun_ajaran_id"
                                name="tahun_ajaran_id" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                @foreach ($tahunAjaranList as $tahun)
                                    <option value="{{ $tahun->id }}"
                                        {{ old('tahun_ajaran_id') == $tahun->id || (!old('tahun_ajaran_id') && $tahunAjaranAktif && $tahunAjaranAktif->id == $tahun->id) ? 'selected' : '' }}
                                        data-tahun="{{ $tahun->tahun }}" data-semester="{{ $tahun->semester }}">
                                        {{ $tahun->tahun }} - {{ ucfirst($tahun->semester) }}
                                        @if ($tahun->status_aktif)
                                            <span class="badge badge-sm bg-success">Aktif</span>
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('tahun_ajaran_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="bx bx-calendar"></i>
                                @if ($tahunAjaranAktif)
                                    Tahun ajaran aktif: {{ $tahunAjaranAktif->tahun }} -
                                    {{ ucfirst($tahunAjaranAktif->semester) }}
                                @else
                                    Pilih tahun ajaran untuk data pembayaran
                                @endif
                            </small>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Status Selection -->
                        <div class="col-md-6 mb-4">
                            <label for="status" class="form-label required">Status Pembayaran</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status"
                                required>
                                <option value="">-- Pilih Status --</option>
                                <option value="bayar" {{ old('status') == 'bayar' ? 'selected' : '' }}>
                                    ✓ Sudah Bayar (Lunas)
                                </option>
                                <option value="belum_bayar" {{ old('status') == 'belum_bayar' ? 'selected' : '' }}>
                                    ⏳ Belum Bayar
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i> Status dapat diubah sewaktu-waktu
                            </small>
                        </div>

                        <!-- Status Preview -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Preview Status</label>
                            <div id="statusPreview" class="status-preview">
                                <div class="d-flex align-items-center justify-content-center" style="min-height: 60px;">
                                    <div class="text-center">
                                        <i class="bx bx-info-circle text-muted" style="font-size: 24px;"></i>
                                        <p class="text-muted mb-0 mt-2 small">Pilih status untuk melihat preview</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <hr class="my-4">

                    <!-- Action Buttons -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('admin.pembayaran-ukt.index') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-x"></i> Batal
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-outline-warning me-2" id="resetBtn">
                                        <i class="bx bx-reset"></i> Reset Form
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="submitBtn"
                                        {{ $mahasiswa->count() == 0 ? 'disabled' : '' }}>
                                        <i class="bx bx-save"></i> Simpan Data
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Card -->
        <div class="card mt-4 border-0 shadow-sm">
            <div class="card-body">
                <h6 class="mb-3">
                    <i class="bx bx-help-circle text-primary"></i> Bantuan
                </h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Cara Menambah Data:</strong>
                        <ol class="small mb-0 mt-2">
                            <li>Pilih mahasiswa dari dropdown</li>
                            <li>Pilih tahun ajaran (default: tahun aktif)</li>
                            <li>Pilih status pembayaran</li>
                            <li>Klik tombol "Simpan Data"</li>
                        </ol>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Catatan Penting:</strong>
                        <ul class="small mb-0 mt-2">
                            <li>Data duplikat akan ditolak sistem</li>
                            <li>Mahasiswa hanya bisa memiliki 1 data per tahun ajaran</li>
                            <li>Status dapat diubah kapan saja dari halaman daftar</li>
                            <li>Gunakan fitur import untuk data massal</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Select2 JS -->

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'default',
                width: '100%',
                placeholder: '-- Pilih Mahasiswa --',
                allowClear: true,
                language: {
                    noResults: function() {
                        return "Mahasiswa tidak ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });

            // Mahasiswa selection change
            $('#mahasiswa_id').on('change', function() {
                const selectedOption = $(this).find(':selected');
                const nim = selectedOption.data('nim');
                const name = selectedOption.data('name');

                if (nim && name) {
                    const initials = getInitials(name);
                    $('#previewInitial').text(initials);
                    $('#previewName').text(name);
                    $('#previewNim').text('NIM: ' + nim);
                    $('#mahasiswaPreview').slideDown();
                } else {
                    $('#mahasiswaPreview').slideUp();
                }
            });

            // Status selection change with preview
            $('#status').on('change', function() {
                const status = $(this).val();
                const preview = $('#statusPreview');

                preview.removeClass('bayar belum_bayar');

                if (status === 'bayar') {
                    preview.addClass('bayar');
                    preview.html(`
                <div class="text-center">
                    <i class="bx bx-check-circle text-success" style="font-size: 32px;"></i>
                    <h6 class="mt-2 mb-1 text-success fw-bold">Sudah Bayar (Lunas)</h6>
                    <small class="text-muted">Mahasiswa telah menyelesaikan pembayaran UKT</small>
                </div>
            `);
                } else if (status === 'belum_bayar') {
                    preview.addClass('belum_bayar');
                    preview.html(`
                <div class="text-center">
                    <i class="bx bx-time-five text-warning" style="font-size: 32px;"></i>
                    <h6 class="mt-2 mb-1 text-warning fw-bold">Belum Bayar</h6>
                    <small class="text-muted">Mahasiswa belum menyelesaikan pembayaran UKT</small>
                </div>
            `);
                } else {
                    preview.html(`
                <div class="d-flex align-items-center justify-content-center" style="min-height: 60px;">
                    <div class="text-center">
                        <i class="bx bx-info-circle text-muted" style="font-size: 24px;"></i>
                        <p class="text-muted mb-0 mt-2 small">Pilih status untuk melihat preview</p>
                    </div>
                </div>
            `);
                }
            });

            // Trigger status change if old value exists
            if ($('#status').val()) {
                $('#status').trigger('change');
            }

            // Trigger mahasiswa change if old value exists
            if ($('#mahasiswa_id').val()) {
                $('#mahasiswa_id').trigger('change');
            }

            // Form submission with loading state
            $('#createForm').on('submit', function(e) {
                const submitBtn = $('#submitBtn');
                submitBtn.prop('disabled', true);
                submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');
            });

            // Reset button
            $('#resetBtn').on('click', function() {
                setTimeout(function() {
                    $('#mahasiswaPreview').slideUp();
                    $('#statusPreview').html(`
                <div class="d-flex align-items-center justify-content-center" style="min-height: 60px;">
                    <div class="text-center">
                        <i class="bx bx-info-circle text-muted" style="font-size: 24px;"></i>
                        <p class="text-muted mb-0 mt-2 small">Pilih status untuk melihat preview</p>
                    </div>
                </div>
            `).removeClass('bayar belum_bayar');
                    $('.select2').val(null).trigger('change');
                }, 100);
            });

            // Helper function to get initials
            function getInitials(name) {
                const parts = name.trim().split(' ');
                if (parts.length >= 2) {
                    return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
                }
                return name.substring(0, 2).toUpperCase();
            }

            // Form validation
            $('#createForm').on('submit', function(e) {
                let isValid = true;
                const requiredFields = ['mahasiswa_id', 'tahun_ajaran_id', 'status'];

                requiredFields.forEach(function(field) {
                    const input = $(`[name="${field}"]`);
                    if (!input.val()) {
                        isValid = false;
                        input.addClass('is-invalid');

                        if (!input.next('.invalid-feedback').length) {
                            input.after(
                                '<div class="invalid-feedback">Field ini wajib diisi</div>');
                        }
                    } else {
                        input.removeClass('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Form Tidak Lengkap',
                        text: 'Mohon lengkapi semua field yang wajib diisi',
                        confirmButtonText: 'OK'
                    });
                    $('#submitBtn').prop('disabled', false);
                    $('#submitBtn').html('<i class="bx bx-save"></i> Simpan Data');
                }
            });
        });
    </script>
@endpush
