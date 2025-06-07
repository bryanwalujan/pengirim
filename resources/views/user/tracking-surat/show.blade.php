@extends('layouts.user.app')

@section('title', 'Detail Tracking Surat - E-Service')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .card {
            border-radius: 1.25rem;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
            background: linear-gradient(145deg, #ffffff, #f8fafc);
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 0.75rem;
            overflow: hidden;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #e2e8f0;
            padding: 1rem;
            text-align: left;
        }

        th {
            background: #4361ee;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
        }

        tr:nth-child(even) {
            background: #f9fafb;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
        }

        .status-diajukan {
            background: #fefcbf;
            color: #b45309;
        }

        .status-diproses {
            background: #bfdbfe;
            color: #1e40af;
        }

        .status-disetujui_kaprodi {
            background: #c3dafe;
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

        .status-siap_diambil {
            background: #d1fae5;
            color: #065f46;
        }

        .status-sudah_diambil {
            background: #e2e8f0;
            color: #4b5563;
        }

        .tracking-code {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .copy-btn {
            transition: background 0.3s ease;
        }

        .copy-btn:hover {
            background: #e0e7ff;
        }

        .copied-tooltip {
            position: absolute;
            top: -2rem;
            background: #4361ee;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .copied-tooltip.show {
            opacity: 1;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
        }

        @media (max-width: 640px) {
            .grid-cols-1 md\:grid-cols-2 {
                grid-template-columns: 1fr;
            }

            th,
            td {
                font-size: 0.75rem;
                padding: 0.75rem;
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
                    <li><a href="{{ route('user.home.index') }}" class="hover:text-blue-600">Beranda</a></li>
                    <li><a href="{{ route('user.tracking-surat.index') }}" class="hover:text-blue-600">Tracking Surat</a>
                    </li>
                    <li class="current">Detail</li>
                </ol>
            </nav>
        </div>
    </div>

    <section id="tracking-detail" class="section-space py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                @if ($surat->status && $surat->status === 'unknown')
                    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 rounded" role="alert">
                        <p>Status surat belum tersedia. Silakan hubungi admin.</p>
                    </div>
                @endif


                <div class="card p-6" data-aos="fade-up">
                    <h2 class="text-2xl font-semibold mb-4">Informasi Surat</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <p>
                            <strong>Jenis Surat:</strong>
                            {{ $surat->getQrCodeDataAttribute()['document_type'] ?? str_replace('Surat', '', class_basename($surat)) }}
                        </p>
                        <p class="tracking-code">
                            <strong>Kode Tracking:</strong>
                            <span>{{ $surat->tracking_code ?? '-' }}</span>
                            <button class="copy-btn p-1 rounded-full"
                                onclick="copyTrackingCode('{{ $surat->tracking_code }}')" title="Salin kode tracking"
                                aria-label="Salin kode tracking">
                                <i class="bi bi-clipboard text-blue-600"></i>
                            </button>
                            <span class="copied-tooltip">Disalin!</span>
                        </p>
                        <p><strong>Nomor Surat:</strong> {{ $surat->nomor_surat ?? 'Belum ditentukan' }}</p>
                        <p><strong>Nama Mahasiswa:</strong> {{ $surat->mahasiswa->name ?? '-' }}</p>
                        <p><strong>NIM:</strong> {{ $surat->mahasiswa->nim ?? '-' }}</p>
                        <p>
                            <strong>Status:</strong>
                            <span
                                class="status-badge status-{{ str_replace('_', '-', $surat->status->status ?? 'unknown') }}">
                                {{ str_replace('_', ' ', $surat->status->status ?? 'Unknown') }}
                            </span>
                        </p>
                        <p><strong>Catatan Admin:</strong> {{ $surat->status->catatan_admin ?? '-' }}</p>
                        <p><strong>Diperbarui Oleh:</strong> {{ $surat->status->updatedBy->name ?? '-' }}
                    </div>
                </div>

                <div class="card p-6 mt-6" data-aos="fade-up" data-aos-delay="100">
                    <h2 class="text-2xl font-semibold mb-4">Riwayat Tracking</h2>
                    <div class="table-container">
                        <table class="w-full" role="grid">
                            <thead>
                                <tr>
                                    <th scope="col">Tanggal</th>
                                    <th scope="col">Aksi</th>
                                    <th scope="col">Keterangan</th>
                                    <th scope="col">Mahasiswa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($surat->trackings as $tracking)
                                    <tr>
                                        <td>{{ $tracking->created_at->format('d-m-Y H:i') }}</td>
                                        <td>{{ str_replace('_', ' ', $tracking->aksi) }}</td>
                                        <td>{{ $tracking->keterangan ?? '-' }}</td>
                                        <td>{{ $tracking->mahasiswa->name ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="empty-state">
                                            <p>Belum ada riwayat tracking untuk surat ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6 flex justify-between" data-aos="fade-up" data-aos-delay="200">
                    <a href="{{ route('user.tracking-surat.index') }}"
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors duration-300">
                        Kembali ke Tracking
                    </a>
                    @if ($surat->file_surat_path && ($surat->statusSurat->status ?? 'diajukan') === 'sudah_diambil')
                        <a href="{{ route('user.surat-aktif-kuliah.download', $surat->id) }}"
                            class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors duration-300">
                            <i class="bi bi-download mr-2"></i> Unduh Surat
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
                const tooltip = document.querySelector('.tracking-code .copied-tooltip');
                tooltip.classList.add('show');
                setTimeout(() => tooltip.classList.remove('show'), 1000);
                Swal.fire({
                    icon: 'success',
                    title: 'Disalin!',
                    text: 'Kode tracking telah disalin ke clipboard.',
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            AOS.init({
                duration: 400,
                once: true
            });
        });
    </script>
@endpush
