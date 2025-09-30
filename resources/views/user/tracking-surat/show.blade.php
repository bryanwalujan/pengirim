@extends('layouts.user.app')

@section('title', 'Detail Tracking Surat - E-Service')

@push('styles')
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

        /* Algorithm Comparison Enhancements */
        .algorithm-step {
            transition: all 0.3s ease;
        }

        .algorithm-step:hover {
            transform: translateX(5px);
            background-color: rgba(0, 0, 0, 0.02);
        }

        .performance-grid {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .metric-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        /* Responsive improvements */
        @media (max-width: 640px) {
            .algorithm-step {
                flex-direction: column;
                align-items: start;
            }

            .performance-grid>div {
                text-align: left;
            }
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


                <!-- Performance Comparison Analysis -->
                <div class="card p-6 mb-6" data-aos="fade-up" data-aos-delay="400">
                    <h2 class="text-2xl font-semibold mb-6 flex items-center">
                        <i class="bi bi-graph-up mr-2 text-green-600"></i>
                        Analisis Performa Algoritma Pencarian
                    </h2>

                    <!-- Real-time Performance Stats -->
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-6 mb-6 border border-indigo-200">
                        <div class="text-center mb-4">
                            <h3 class="text-lg font-bold text-indigo-800 mb-2">Hasil Pencarian Real-time</h3>
                            <p class="text-sm text-indigo-600">Tracking Code: <span
                                    class="font-mono font-bold">{{ $surat->tracking_code }}</span></p>
                            <p class="text-xs text-indigo-500 mt-1">Dataset:
                                {{ number_format($performanceData['data_size']) }} records</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Linear Search Visualization -->
                            <div class="bg-white rounded-xl p-6 border-l-4 border-red-500 shadow-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="bg-red-100 p-2 rounded-lg mr-3">
                                            <i class="bi bi-arrow-right text-red-600 text-lg"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-red-800">Linear Search</h4>
                                            <p class="text-xs text-red-600">Sequential O(n)</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-red-700">
                                            {{ $performanceData['linear_search']['iterations'] }}
                                        </div>
                                        <div class="text-xs text-red-500">iterasi</div>
                                    </div>
                                </div>


                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-red-700">Waktu:</span>
                                        <span class="font-mono font-semibold text-red-800">
                                            {{ number_format($performanceData['linear_search']['time'], 4) }} ms
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-red-700">Worst Case:</span>
                                        <span class="font-mono font-semibold text-red-800">
                                            {{ number_format($performanceData['data_size']) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Binary Search Visualization -->
                            <div class="bg-white rounded-xl p-6 border-l-4 border-green-500 shadow-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="bg-green-100 p-2 rounded-lg mr-3">
                                            <i class="bi bi-lightning text-green-600 text-lg"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-green-800">Binary Search</h4>
                                            <p class="text-xs text-green-600">Logarithmic O(log n)</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-green-700">
                                            {{ $performanceData['binary_search']['iterations'] }}
                                        </div>
                                        <div class="text-xs text-green-500">iterasi</div>
                                    </div>
                                </div>


                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-green-700">Waktu:</span>
                                        <span class="font-mono font-semibold text-green-800">
                                            {{ number_format($performanceData['binary_search']['time'], 4) }} ms
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-green-700">Worst Case:</span>
                                        <span class="font-mono font-semibold text-green-800">
                                            {{ $performanceData['binary_search']['worst_case'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Metrics Dashboard -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <!-- Improvement Card -->
                        <div
                            class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-6 border border-blue-200 text-center">
                            <div class="bg-blue-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="bi bi-speedometer2 text-blue-600 text-xl"></i>
                            </div>
                            <div class="text-3xl font-bold text-blue-700 mb-1">
                                {{ $performanceData['performance']['improvement_percentage'] }}%
                            </div>
                            <div class="text-sm text-blue-600 font-medium">Peningkatan Kecepatan</div>
                            <div class="text-xs text-blue-500 mt-1">vs Linear Search</div>
                        </div>

                        <!-- Speedup Factor Card -->
                        <div
                            class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-6 border border-emerald-200 text-center">
                            <div
                                class="bg-emerald-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="bi bi-rocket-takeoff text-emerald-600 text-xl"></i>
                            </div>
                            <div class="text-3xl font-bold text-emerald-700 mb-1">
                                {{ $performanceData['performance']['speedup_factor'] }}x
                            </div>
                            <div class="text-sm text-emerald-600 font-medium">Faktor Percepatan</div>
                            <div class="text-xs text-emerald-500 mt-1">Lebih Cepat</div>
                        </div>

                        <!-- Efficiency Card -->
                        <div
                            class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-200 text-center">
                            <div
                                class="bg-purple-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="bi bi-award text-purple-600 text-xl"></i>
                            </div>
                            <div class="text-3xl font-bold text-purple-700 mb-1">
                                {{ $performanceData['performance']['efficiency_gained'] }}
                            </div>
                            <div class="text-sm text-purple-600 font-medium">Iterasi Dihemat</div>
                            <div class="text-xs text-purple-500 mt-1">Operasi</div>
                        </div>
                    </div>

                    <!-- Scalability Projection -->
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                            <i class="bi bi-graph-up-arrow mr-2 text-orange-600"></i>
                            Proyeksi Skalabilitas
                        </h3>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-300">
                                        <th class="text-left py-2 px-3 font-semibold text-gray-700">Ukuran Data</th>
                                        <th class="text-center py-2 px-3 font-semibold text-red-700">Linear Search</th>
                                        <th class="text-center py-2 px-3 font-semibold text-green-700">Binary Search</th>
                                        <th class="text-center py-2 px-3 font-semibold text-blue-700">Improvement</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <tr class="border-b border-gray-200 hover:bg-gray-100 transition-colors">
                                        <td class="py-2 px-3 font-medium text-gray-800">Current
                                            ({{ number_format($performanceData['data_size']) }})</td>
                                        <td class="py-2 px-3 text-red-600">
                                            {{ $performanceData['linear_search']['iterations'] }} iterasi</td>
                                        <td class="py-2 px-3 text-green-600">
                                            {{ $performanceData['binary_search']['iterations'] }} iterasi</td>
                                        <td class="py-2 px-3">
                                            <span
                                                class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold">
                                                {{ $performanceData['performance']['improvement_percentage'] }}%
                                            </span>
                                        </td>
                                    </tr>
                                    <tr class="border-b border-gray-200 hover:bg-gray-100 transition-colors">
                                        <td class="py-2 px-3 font-medium text-gray-800">1,000</td>
                                        <td class="py-2 px-3 text-red-600">~1,000 iterasi</td>
                                        <td class="py-2 px-3 text-green-600">~10 iterasi</td>
                                        <td class="py-2 px-3">
                                            <span
                                                class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">99%</span>
                                        </td>
                                    </tr>
                                    <tr class="border-b border-gray-200 hover:bg-gray-100 transition-colors">
                                        <td class="py-2 px-3 font-medium text-gray-800">10,000</td>
                                        <td class="py-2 px-3 text-red-600">~10,000 iterasi</td>
                                        <td class="py-2 px-3 text-green-600">~14 iterasi</td>
                                        <td class="py-2 px-3">
                                            <span
                                                class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">99.9%</span>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-100 transition-colors">
                                        <td class="py-2 px-3 font-medium text-gray-800">100,000</td>
                                        <td class="py-2 px-3 text-red-600">~100,000 iterasi</td>
                                        <td class="py-2 px-3 text-green-600">~17 iterasi</td>
                                        <td class="py-2 px-3">
                                            <span
                                                class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">99.98%</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Algorithm Step-by-Step Visualization -->
                <div class="card p-6 mb-6" data-aos="fade-up" data-aos-delay="500">
                    <h2 class="text-2xl font-semibold mb-6 flex items-center">
                        <i class="bi bi-diagram-3 mr-2 text-purple-600"></i>
                        Visualisasi Proses Algoritma
                    </h2>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Linear Search Animation -->
                        <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-lg p-6 border border-red-200">
                            <h3 class="font-bold text-red-800 mb-4 flex items-center">
                                <i class="bi bi-play-circle mr-2"></i>
                                Linear Search Process
                            </h3>

                            <!-- Algorithm Steps -->
                            <div class="space-y-3 mb-4">
                                <div
                                    class="flex items-center p-3 bg-white rounded-lg border border-red-200 algorithm-step">
                                    <div
                                        class="bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3">
                                        1</div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-red-800">Start dari index 0</p>
                                        <p class="text-xs text-red-600">Mulai pencarian dari elemen pertama</p>
                                    </div>
                                </div>
                                <div
                                    class="flex items-center p-3 bg-white rounded-lg border border-red-200 algorithm-step">
                                    <div
                                        class="bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3">
                                        2</div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-red-800">Compare setiap elemen</p>
                                        <p class="text-xs text-red-600">Bandingkan dengan target secara berurutan</p>
                                    </div>
                                </div>
                                <div
                                    class="flex items-center p-3 bg-white rounded-lg border border-red-200 algorithm-step">
                                    <div
                                        class="bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3">
                                        3</div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-red-800">Found atau End</p>
                                        <p class="text-xs text-red-600">Berhenti jika ditemukan atau habis</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Complexity Info -->
                            <div class="bg-red-100 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-bold text-red-800">Time Complexity</span>
                                    <span class="bg-red-200 text-red-800 px-2 py-1 rounded text-xs font-mono">O(n)</span>
                                </div>
                                <p class="text-xs text-red-700">Waktu eksekusi bertambah linear seiring ukuran data</p>
                                <div class="mt-2 text-xs text-red-600">
                                    <strong>Actual Performance:</strong>
                                    {{ $performanceData['linear_search']['iterations'] }} dari
                                    {{ number_format($performanceData['data_size']) }} elements
                                </div>
                            </div>
                        </div>

                        <!-- Binary Search Animation -->
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-6 border border-green-200">
                            <h3 class="font-bold text-green-800 mb-4 flex items-center">
                                <i class="bi bi-lightning-charge mr-2"></i>
                                Binary Search Process
                            </h3>

                            <!-- Algorithm Steps -->
                            <div class="space-y-3 mb-4">
                                <div
                                    class="flex items-center p-3 bg-white rounded-lg border border-green-200 algorithm-step">
                                    <div
                                        class="bg-green-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3">
                                        1</div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-green-800">Find middle element</p>
                                        <p class="text-xs text-green-600">Cari elemen tengah dari array terurut</p>
                                    </div>
                                </div>
                                <div
                                    class="flex items-center p-3 bg-white rounded-lg border border-green-200 algorithm-step">
                                    <div
                                        class="bg-green-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3">
                                        2</div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-green-800">Compare & divide</p>
                                        <p class="text-xs text-green-600">Bandingkan dan bagi array menjadi dua</p>
                                    </div>
                                </div>
                                <div
                                    class="flex items-center p-3 bg-white rounded-lg border border-green-200 algorithm-step">
                                    <div
                                        class="bg-green-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3">
                                        3</div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-green-800">Repeat on half</p>
                                        <p class="text-xs text-green-600">Ulangi pada setengah yang relevan</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Complexity Info -->
                            <div class="bg-green-100 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-bold text-green-800">Time Complexity</span>
                                    <span class="bg-green-200 text-green-800 px-2 py-1 rounded text-xs font-mono">O(log
                                        n)</span>
                                </div>
                                <p class="text-xs text-green-700">Waktu eksekusi bertambah logaritmik seiring ukuran data
                                </p>
                                <div class="mt-2 text-xs text-green-600">
                                    <strong>Actual Performance:</strong>
                                    {{ $performanceData['binary_search']['iterations'] }} dari
                                    {{ number_format($performanceData['data_size']) }} elements
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Interactive Complexity Chart -->
                    <div
                        class="bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 rounded-xl p-6 border border-indigo-200">
                        <h4 class="font-bold text-gray-800 mb-4 text-center flex items-center justify-center">
                            <i class="bi bi-bar-chart mr-2 text-indigo-600"></i>
                            Perbandingan Kompleksitas pada Berbagai Dataset
                        </h4>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @php
                                $datasets = [
                                    ['size' => 100, 'linear' => 100, 'binary' => 7],
                                    ['size' => 1000, 'linear' => 1000, 'binary' => 10],
                                    ['size' => 10000, 'linear' => 10000, 'binary' => 14],
                                    ['size' => 100000, 'linear' => 100000, 'binary' => 17],
                                ];
                            @endphp

                            @foreach ($datasets as $data)
                                <div
                                    class="text-center bg-white rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow">
                                    <div class="text-lg font-bold text-gray-800 mb-2">{{ number_format($data['size']) }}
                                    </div>
                                    <div class="text-xs text-gray-500 mb-3">records</div>

                                    <!-- Visual bars -->
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-red-600">Linear</span>
                                            <span
                                                class="font-mono text-red-700">{{ number_format($data['linear']) }}</span>
                                        </div>
                                        <div class="w-full bg-red-100 rounded-full h-1.5">
                                            <div class="bg-red-500 h-1.5 rounded-full" style="width: 100%"></div>
                                        </div>

                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-green-600">Binary</span>
                                            <span class="font-mono text-green-700">{{ $data['binary'] }}</span>
                                        </div>
                                        <div class="w-full bg-green-100 rounded-full h-1.5">
                                            <div class="bg-green-500 h-1.5 rounded-full"
                                                style="width: {{ ($data['binary'] / $data['linear']) * 100 }}%"></div>
                                        </div>
                                    </div>

                                    <div class="mt-2 text-xs text-blue-600 font-semibold">
                                        {{ number_format((1 - $data['binary'] / $data['linear']) * 100, 1) }}% faster
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

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
