@extends('layouts.user.app')

@section('title', 'Detail Surat Aktif Kuliah')

@push('styles')
    <style>
        .breadcrumbs ol {
            margin-bottom: 0;
            padding-left: 0;
            list-style: none;
            display: flex;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .card {
            border-radius: 1.5rem;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            background: #fff;
        }

        .card-header {
            border-bottom: none;
            background: linear-gradient(135deg, #4361ee, #3f37c9);
            border-top-left-radius: 1.5rem;
            border-top-right-radius: 1.5rem;
            padding: 2rem;
        }

        .card-body {
            padding: 2.5rem;
        }

        .section-title {
            color: #343a40;
            font-weight: 700;
            margin-bottom: 1.5rem;
            font-size: 1.25rem;
            text-transform: uppercase;
            border-bottom: 2px solid #4361ee;
            padding-bottom: 0.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #343a40;
            font-size: 0.95rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.85rem 1.25rem;
            border: 1px solid #e0e0e0;
            background: #f8f9fa;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.03);
        }

        .alert {
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .alert-heading {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .btn-primary,
        .btn-secondary {
            border-radius: 50px;
            padding: 0.75rem 2rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4361ee, #3f37c9);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #3f37c9, #4361ee);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
            transform: translateY(-3px);
        }

        .btn-secondary {
            background: #fff;
            border: 2px solid #e0e0e0;
            color: #343a40;
        }

        .btn-secondary:hover {
            background: #f8f9fa;
            border-color: #4361ee;
            transform: translateY(-3px);
        }

        .file-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .file-link:hover {
            background: #e9ecef;
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .status-alert {
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .status-alert.alert-success {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
            border-left: 5px solid #10b981;
        }

        .status-alert.alert-danger {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
            border-left: 5px solid #ef4444;
        }

        .status-alert.alert-warning {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
            border-left: 5px solid #f59e0b;
        }

        .status-alert.alert-info {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #1e40af;
            border-left: 5px solid #3b82f6;
        }

        .status-icon {
            font-size: 2rem;
            margin-right: 1rem;
        }

        .status-content h4 {
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .action-buttons-enhanced {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .btn-enhanced {
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-enhanced:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-confirm {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            color: white;
        }

        .btn-download {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border: none;
            color: white;
        }

        .rejection-reason {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-top: 1rem;
        }
    </style>
@endpush

@section('main')
    <div class="page-title light-background">
        <div class="container">
            <h1 data-aos="fade-up">Detail Surat Aktif Kuliah</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol>
                    <li><a href="{{ route('user.home.index') }}">Beranda</a></li>
                    <li><a href="{{ route('user.surat-aktif-kuliah.index') }}">Surat Aktif Kuliah</a></li>
                    <li class="current">Detail Surat</li>
                </ol>
            </nav>
        </div>
    </div>

    <section id="services" class="detail-surat section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Status Alert -->
                    @if ($surat->status === 'siap_diambil')
                        <div class="status-alert alert-success d-flex align-items-center" data-aos="fade-up">
                            <i class="bi bi-check-circle-fill status-icon"></i>
                            <div class="status-content">
                                <h4>🎉 Surat Sudah Siap Diambil!</h4>
                                <p class="mb-0">Selamat! Surat aktif kuliah Anda telah selesai diproses dan siap untuk
                                    diambil. Silakan konfirmasi pengambilan untuk dapat mengunduh surat.</p>
                                <div class="action-buttons-enhanced">
                                    <form action="{{ route('user.surat-aktif-kuliah.confirm-taken', $surat->id) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-enhanced btn-confirm">
                                            <i class="bi bi-check-circle me-2"></i> Konfirmasi Sudah Diambil
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @elseif($surat->status === 'sudah_diambil')
                        <div class="status-alert alert-success d-flex align-items-center" data-aos="fade-up">
                            <i class="bi bi-download status-icon"></i>
                            <div class="status-content">
                                <h4>✅ Surat Telah Dikonfirmasi</h4>
                                <p class="mb-0">Anda telah mengonfirmasi pengambilan surat. Sekarang Anda dapat mengunduh
                                    surat kapan saja.</p>
                                @if ($surat->file_surat_path)
                                    <div class="action-buttons-enhanced">
                                        <a href="{{ route('user.surat-aktif-kuliah.download', $surat->id) }}"
                                            class="btn btn-enhanced btn-download">
                                            <i class="bi bi-download me-2"></i> Unduh Surat
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @elseif($surat->status === 'ditolak')
                        <div class="status-alert alert-danger d-flex align-items-center" data-aos="fade-up">
                            <i class="bi bi-x-circle-fill status-icon"></i>
                            <div class="status-content">
                                <h4>❌ Pengajuan Ditolak</h4>
                                <p class="mb-0">Maaf, pengajuan surat aktif kuliah Anda ditolak. Silakan periksa alasan
                                    penolakan di bawah ini dan lakukan perbaikan jika diperlukan.</p>
                                @if ($surat->statusSurat && $surat->statusSurat->catatan_admin)
                                    <div class="rejection-reason">
                                        <strong>Alasan Penolakan:</strong><br>
                                        {{ $surat->statusSurat->catatan_admin }}
                                    </div>
                                @endif
                                <div class="action-buttons-enhanced">
                                    <a href="{{ route('user.surat-aktif-kuliah.create') }}"
                                        class="btn btn-enhanced btn-primary">
                                        <i class="bi bi-plus-circle me-2"></i> Ajukan Ulang
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="card" data-aos="fade-up">
                        <div class="card-header text-white">
                            <h4 class="mb-0 fw-bold text-center text-white">Detail Pengajuan Surat Aktif Kuliah</h4>
                        </div>
                        <div class="card-body">
                            <!-- Informasi Surat -->
                            <div class="mb-5" data-aos="fade-up" data-aos-delay="100">
                                <h5 class="section-title">Informasi Surat</h5>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Kode Tracking</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control"
                                                value="{{ $surat->tracking_code ?? '-' }}" readonly>
                                            <button class="btn btn-outline-primary"
                                                onclick="copyTrackingCode('{{ $surat->tracking_code }}')">
                                                <i class="bi bi-clipboard"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nomor Surat</label>
                                        <input type="text" class="form-control"
                                            value="{{ $surat->nomor_surat ?? 'Belum ditentukan' }}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status Surat</label>
                                        <div class="d-flex align-items-center">
                                            <span class="{{ $surat->status_badge_class }}">
                                                {{ $surat->status_display }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Pengajuan</label>
                                        <input type="text" class="form-control"
                                            value="{{ $surat->created_at->format('d F Y, H:i') }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Mahasiswa -->
                            <div class="mb-5" data-aos="fade-up" data-aos-delay="100">
                                <h5 class="section-title">Informasi Mahasiswa</h5>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" value="{{ $surat->mahasiswa->name }}"
                                            readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">NIM</label>
                                        <input type="text" class="form-control"
                                            value="{{ $surat->mahasiswa->nim ?? '-' }}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Program Studi</label>
                                        <input type="text" class="form-control"
                                            value="{{ $surat->mahasiswa->prodi ?? 'S1 Teknik Informatika' }}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Semester</label>
                                        <input type="text" class="form-control" value="{{ $surat->semester ?? '-' }}"
                                            readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Pengajuan -->
                            <div class="mb-5" data-aos="fade-up" data-aos-delay="200">
                                <h5 class="section-title">Detail Pengajuan</h5>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Tahun Ajaran</label>
                                        <input type="text" class="form-control"
                                            value="{{ $surat->tahun_ajaran ?? '-' }}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Semester</label>
                                        <input type="text" class="form-control"
                                            value="{{ ucfirst($surat->semester ?? '-') }}" readonly>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Tujuan Pengajuan</label>
                                        <textarea class="form-control" rows="4" readonly>{{ $surat->tujuan_pengajuan ?? '-' }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Keterangan Tambahan</label>
                                        <textarea class="form-control" rows="3" readonly>{{ $surat->keterangan_tambahan ?? '-' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Dokumen Pendukung -->
                            @if ($surat->dokumenPendukung->count() > 0)
                                <div class="mb-5" data-aos="fade-up" data-aos-delay="300">
                                    <h5 class="section-title">Dokumen Pendukung</h5>
                                    @foreach ($surat->dokumenPendukung as $dokumen)
                                        <a href="{{ Storage::url($dokumen->path) }}" target="_blank"
                                            class="file-link text-decoration-none d-block mb-2">
                                            <i class="bi bi-file-earmark-text fs-4 text-primary"></i>
                                            {{ $dokumen->nama_asli }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="d-flex justify-content-between mt-5" data-aos="fade-up" data-aos-delay="500">
                                <a href="{{ route('user.surat-aktif-kuliah.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i> Kembali ke Daftar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        AOS.init({
            duration: 400,
            once: true
        });

        function copyTrackingCode(code) {
            navigator.clipboard.writeText(code);
            Swal.fire({
                icon: 'success',
                title: 'Disalin!',
                text: 'Kode tracking telah disalin ke clipboard.',
                showConfirmButton: false,
                timer: 1500
            });
        }

        // Confirm taken action
        $(document).on('submit', 'form[action*="confirm-taken"]', function(e) {
            e.preventDefault();
            var form = this;

            Swal.fire({
                title: 'Konfirmasi Pengambilan Surat',
                html: `
                    <p>Dengan mengkonfirmasi, Anda menyatakan bahwa:</p>
                    <ul style="text-align: left; display: inline-block;">
                        <li>Anda telah menerima surat aktif kuliah</li>
                        <li>Surat dalam kondisi baik dan benar</li>
                        <li>Anda dapat mengunduh surat setelah konfirmasi</li>
                    </ul>
                    <p><strong>Apakah Anda yakin ingin melanjutkan?</strong></p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-check-circle me-2"></i>Ya, Konfirmasi',
                cancelButtonText: 'Batal',
                width: '500px'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang mengkonfirmasi pengambilan surat',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    form.submit();
                }
            });
        });
    </script>
@endpush
