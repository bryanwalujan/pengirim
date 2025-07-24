@extends('layouts.user.app')

@section('title', 'Detail Tracking Surat - E-Service')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #e0e7ff;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
        }

        .card {
            border-radius: 1.25rem;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.12);
        }

        .status-tracker {
            position: relative;
            padding: 2rem 0;
            margin: 2rem 0;
        }

        .status-line {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 4px;
            background: #e2e8f0;
            z-index: 1;
            border-radius: 2px;
        }

        .status-progress {
            position: absolute;
            top: 50%;
            left: 0;
            height: 4px;
            background: var(--primary);
            z-index: 2;
            border-radius: 2px;
            transition: width 1.5s ease-out;
            width: {{ $progressPercentage }}%;
            /* Menggunakan data dari controller */
        }

        .status-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            z-index: 3;
        }

        .status-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: calc(100% / 6);
            /* 6 steps */
            position: relative;
        }

        .step-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 3px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            color: #64748b;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .step-icon.active {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            transform: scale(1.1);
        }

        .step-icon.completed {
            background: var(--success);
            border-color: var(--success);
            color: white;
        }

        .step-label {
            margin-top: 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: #64748b;
            text-align: center;
            max-width: 100px;
            transition: all 0.3s ease;
        }

        .step-label.active {
            color: var(--primary);
            font-weight: 600;
        }

        .step-label.completed {
            color: var(--success);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .status-badge i {
            margin-right: 0.3rem;
        }

        .status-diajukan {
            background: #fef3c7;
            color: #92400e;
        }

        .status-diproses {
            background: #bfdbfe;
            color: #1e40af;
        }

        .status-disetujui-kaprodi {
            background: #c7d2fe;
            color: #3730a3;
        }

        .status-disetujui {
            background: #bbf7d0;
            color: #166534;
        }

        .status-ditolak {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-siap-diambil {
            background: #a7f3d0;
            color: #065f46;
        }

        .status-sudah-diambil {
            background: #d1fae5;
            color: #047857;
        }

        .timeline-item {
            position: relative;
            padding-left: 2rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .timeline-item:hover {
            transform: translateX(5px);
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--primary);
            border: 3px solid white;
            z-index: 2;
        }

        .timeline-item:after {
            content: '';
            position: absolute;
            left: 7px;
            top: 16px;
            bottom: -1.5rem;
            width: 2px;
            background: #e2e8f0;
        }

        .timeline-item:last-child:after {
            display: none;
        }

        .timeline-content {
            background: white;
            border-radius: 0.75rem;
            padding: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
        }

        .timeline-date {
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 0.25rem;
        }

        .timeline-action {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .timeline-details {
            color: #475569;
            font-size: 0.875rem;
        }

        .copy-btn {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .copy-btn:hover {
            transform: scale(1.1);
            color: var(--primary);
        }

        .tooltip {
            position: relative;
            display: inline-block;
        }

        .tooltip .tooltip-text {
            visibility: hidden;
            width: 120px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 0.75rem;
        }

        .tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        .action-btn {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .info-card {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            border-left: 4px solid var(--primary);
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            text-align: center;
            color: #64748b;
        }

        .empty-state-icon {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .status-steps {
                flex-wrap: wrap;
                justify-content: flex-start;
                gap: 1rem;
            }

            .status-step {
                flex: 0 0 calc(50% - 1rem);
                margin-bottom: 1rem;
            }

            .status-line,
            .status-progress {
                display: none;
            }

            .step-arrow {
                display: none;
                /* Sembunyikan panah di mobile */
            }
        }
    </style>
@endpush

@section('main')
    <div class="page-title light-background">
        <div class="container">
            <h1 data-aos="fade-up" class="text-3xl font-bold text-gray-800">Detail Tracking Surat</h1>
            <nav class="breadcrumbs" data-aos="fade-up" data-aos-delay="100">
                <ol class="flex space-x-2 text-gray-600">
                    <li><a href="{{ route('user.home.index') }}">Beranda</a>
                    </li>
                    <li><a href="{{ route('user.tracking-surat.index') }}">Tracking Surat</a></li>
                    <li>Detail</li>
                </ol>
            </nav>
        </div>
    </div>

    <section id="tracking" class="section-space py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                @if ($surat->statusSurat->status === 'unknown')
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 p-4 mb-6 rounded-lg flex items-start"
                        role="alert" data-aos="fade-up">
                        <i class="bi bi-exclamation-circle-fill mr-3 text-xl"></i>
                        <div>
                            <h3 class="font-semibold">Status Tidak Diketahui</h3>
                            <p class="text-sm">Status surat belum tersedia. Silakan hubungi admin untuk informasi lebih
                                lanjut.</p>
                        </div>
                    </div>
                @endif

                <!-- Status Tracker Visualization -->
                <div class="card p-6 mb-6" data-aos="fade-up">
                    <h2 class="text-2xl font-semibold mb-6 flex items-center">
                        <i class="bi bi-activity mr-2 text-blue-600"></i>
                        Proses Surat
                    </h2>

                    <div class="status-tracker">
                        <div class="status-line"></div>
                        <div class="status-progress" style="width: {{ $progressPercentage }}%"></div>
                        <div class="status-steps">
                            @foreach ($statusSteps as $step)
                                <div class="status-step">
                                    <div
                                        class="step-icon {{ $step['status_class'] === 'active' ? 'active' : ($step['status_class'] === 'completed' ? 'completed' : '') }}">
                                        <i class="{{ $statusIcons[$step['status']] }}"></i>
                                    </div>
                                    <span
                                        class="step-label {{ $step['status_class'] === 'active' ? 'active' : ($step['status_class'] === 'completed' ? 'completed' : '') }}">{{ $step['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Document Information Card -->
                <div class="grid grid-cols-1 gap-6 mb-6">
                    <!-- Main Info Card -->
                    <div class="card p-6 lg:col-span-2" data-aos="fade-up">
                        <h2 class="text-2xl font-semibold mb-4 flex items-center">
                            <i class="bi bi-file-earmark-text mr-2 text-blue-600"></i>
                            Informasi Surat
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="info-item">
                                <p class="text-sm text-gray-500">Jenis Surat</p>
                                <p class="font-medium">
                                    {{ $surat->getQrCodeDataAttribute()['document_type'] ?? str_replace('Surat', '', class_basename($surat)) }}
                                </p>
                            </div>

                            <div class="info-item">
                                <p class="text-sm text-gray-500">Kode Tracking</p>
                                <div class="flex items-center">
                                    <p class="font-medium mr-2">{{ $surat->tracking_code ?? '-' }}</p>
                                    <button class="copy-btn tooltip"
                                        onclick="copyTrackingCode('{{ $surat->tracking_code }}')">
                                        <i class="bi bi-clipboard text-blue-600"></i>
                                        <span class="tooltip-text">Salin Kode</span>
                                    </button>
                                </div>
                            </div>

                            <div class="info-item">
                                <p class="text-sm text-gray-500">Nomor Surat</p>
                                <p class="font-medium">{{ $surat->nomor_surat ?? 'Belum ditentukan' }}</p>
                            </div>

                            <div class="info-item">
                                <p class="text-sm text-gray-500">Status</p>
                                <p>
                                    <span
                                        class="status-badge status-{{ str_replace('_', '-', $surat->statusSurat->status ?? 'unknown') }}">
                                        <i class="{{ $statusIcons[$surat->statusSurat->status ?? 'unknown'] }}"></i>
                                        {{ str_replace('_', ' ', $surat->statusSurat->status ?? 'unknown') }}
                                    </span>
                                </p>
                            </div>

                            <div class="info-item">
                                <p class="text-sm text-gray-500">Nama Mahasiswa</p>
                                <p class="font-medium">{{ $surat->mahasiswa->name ?? '-' }}</p>
                            </div>

                            <div class="info-item">
                                <p class="text-sm text-gray-500">NIM</p>
                                <p class="font-medium">{{ $surat->mahasiswa->nim ?? '-' }}</p>
                            </div>

                            <div class="info-item md:col-span-2">
                                <p class="text-sm text-gray-500">Catatan</p>
                                <p class="font-medium">{{ $surat->statusSurat->catatan_admin ?? 'Tidak ada catatan' }}</p>
                            </div>

                            <div class="info-item md:col-span-2">
                                <p class="text-sm text-gray-500">Diperbarui Oleh</p>
                                <p class="font-medium">{{ optional($surat->statusSurat->updatedBy)->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Timeline Section -->
                <div class="card p-6 mb-6" data-aos="fade-up">
                    <h2 class="text-2xl font-semibold mb-6 flex items-center">
                        <i class="bi bi-clock-history mr-2 text-blue-600"></i>
                        Riwayat Tracking
                    </h2>

                    @if ($surat->trackings->count() > 0)
                        <div class="timeline">
                            @foreach ($surat->trackings as $tracking)
                                <div class="timeline-item">
                                    <div class="timeline-content">
                                        <div class="timeline-date">
                                            <i class="bi bi-calendar-event mr-1"></i>
                                            {{ $tracking->created_at->format('d M Y, H:i') }}
                                        </div>
                                        <div class="timeline-action">
                                            <i class="bi bi-activity mr-1"></i>
                                            {{ str_replace('_', ' ', $tracking->aksi) }}
                                        </div>
                                        <div class="timeline-details">
                                            {{ $tracking->keterangan ?? 'Tidak ada keterangan tambahan' }}
                                        </div>
                                        @if ($tracking->mahasiswa)
                                            <div class="mt-2 text-xs text-gray-500">
                                                <i class="bi bi-person mr-1"></i>
                                                Oleh: {{ $tracking->mahasiswa->name }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="bi bi-clock"></i>
                            </div>
                            <h3 class="font-medium text-gray-600">Belum ada riwayat tracking</h3>
                            <p class="text-sm text-gray-500 mt-1">Riwayat proses surat akan muncul di sini</p>
                        </div>
                    @endif
                </div>

                <!-- Performance Info (for tech-savvy users) -->
                <details class="card p-4 mb-6 overflow-hidden" data-aos="fade-up" data-aos-delay="300">
                    <summary class="cursor-pointer font-semibold text-gray-700 flex items-center">
                        <i class="bi bi-speedometer2 mr-2 text-blue-600"></i>
                        Informasi Teknis Pencarian
                        <span class="ml-auto transform transition-transform duration-300">›</span>
                    </summary>
                    <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-blue-800 font-medium">Jumlah Iterasi</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $iterationCount ?? '0' }}</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <p class="text-sm text-purple-800 font-medium">Waktu Eksekusi</p>
                            <p class="text-2xl font-bold text-purple-600">{{ number_format($executionTime ?? 0, 2) }} ms
                            </p>
                        </div>
                        <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-800 font-medium">Efisiensi Algoritma</p>
                            <p class="text-gray-600">Binary Search (O(log n)) memberikan performa optimal untuk pencarian
                                dalam kumpulan data yang besar.</p>
                        </div>
                    </div>
                </details>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-between gap-4" data-aos="fade-up">
                    <a href="{{ route('user.tracking-surat.index') }}"
                        class="action-btn bg-white text-blue-600 px-6 py-3 rounded-lg border border-blue-200 hover:border-blue-300 flex items-center justify-center transition-colors duration-300">
                        <i class="bi bi-arrow-left mr-2"></i>
                        Kembali ke Daftar
                    </a>

                    @if ($surat->file_surat_path && ($surat->statusSurat->status ?? 'diajukan') === 'sudah_diambil')
                        <a href="{{ route('user.surat-aktif-kuliah.download', $surat->id) }}"
                            class="action-btn bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 flex items-center justify-center transition-colors duration-300">
                            <i class="bi bi-download mr-2"></i>
                            Unduh Surat
                        </a>
                    @endif

                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('library/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script>
        function copyTrackingCode(code) {
            navigator.clipboard.writeText(code).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Kode Disalin!',
                    text: 'Kode tracking telah disalin ke clipboard.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
            }).catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyalin',
                    text: 'Tidak dapat menyalin kode tracking: ' + err,
                });
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            AOS.init({
                duration: 400,
                once: true,
                easing: 'ease-out-quad'
            });

            // Animate progress bar
            const progressBar = document.querySelector('.status-progress');
            if (progressBar) {
                setTimeout(() => {
                    progressBar.style.transition = 'width 1.5s ease-out';
                }, 500);
            }

            // Details element animation
            const details = document.querySelector('details');
            if (details) {
                details.addEventListener('toggle', function() {
                    const arrow = this.querySelector('summary span');
                    if (this.open) {
                        arrow.style.transform = 'rotate(90deg)';
                    } else {
                        arrow.style.transform = 'rotate(0deg)';
                    }
                });
            }
        });
    </script>
@endpush
