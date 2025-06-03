@extends('layouts.qrcode.app')

@section('title', 'Verifikasi QR Code Surat Pindah')

@push('styles')
    <style>
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            border-radius: 15px 15px 0 0;
            padding: 1.5rem;
            background-color: #6c757d;
            /* Different color for Surat Pindah */
        }

        .section-title {
            font-size: 1.25rem;
            color: #6c757d;
            /* Matching header color */
            border-bottom: 2px solid #6c757d;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .table-bordered th,
        .table-bordered td {
            padding: 12px;
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .table-bordered th {
            background-color: #f8f9fa;
            font-weight: 500;
        }

        .badge-status {
            font-size: 1.1rem;
            padding: 0.75em 1.5em;
            border-radius: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .badge-status:hover {
            transform: scale(1.05);
        }

        .copy-btn {
            font-size: 0.9rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            transition: background-color 0.3s ease;
        }

        .copy-btn:hover {
            background-color: #5a6268;
            /* Darker shade of header color */
        }

        .accordion-button {
            font-size: 1.1rem;
            font-weight: 500;
            color: #343a40;
            background-color: #f8f9fa;
            border: none;
            border-radius: 10px !important;
            transition: background-color 0.3s ease;
        }

        .accordion-button:not(.collapsed) {
            background-color: #e9ecef;
            color: #6c757d;
            /* Matching header color */
        }

        .accordion-button:focus {
            box-shadow: none;
        }

        .accordion-item {
            border: none;
            border-radius: 10px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .alert-icon {
            font-size: 1.25rem;
            margin-right: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="card">
            <div class="card-header text-white">
                <h4 class="mb-0">Verifikasi QR Code - Surat Pindah</h4>
            </div>
            <div class="card-body">
                <!-- Accordion untuk informasi -->
                <div class="accordion" id="verificationAccordion">
                    <!-- Informasi Dokumen -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingDocument">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseDocument" aria-expanded="true" aria-controls="collapseDocument">
                                Informasi Dokumen
                            </button>
                        </h2>
                        <div id="collapseDocument" class="accordion-collapse collapse show"
                            aria-labelledby="headingDocument" data-bs-parent="#verificationAccordion">
                            <div class="accordion-body">
                                @php
                                    $documentLabels = [
                                        'type' => 'Jenis Dokumen',
                                        'number' => 'Nomor Surat',
                                        'date' => 'Tanggal Surat',
                                        'academic_year' => 'Tahun Ajaran',
                                        'semester' => 'Semester',
                                        'university' => 'Universitas Tujuan',
                                        'reason' => 'Alasan Pindah',
                                    ];
                                @endphp
                                <table class="table table-bordered">
                                    @foreach ($verification_data['document'] as $key => $value)
                                        <tr>
                                            <th width="40%">
                                                {{ $documentLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}</th>
                                            <td>{{ is_array($value) ? json_encode($value) : $value ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Mahasiswa -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingStudent">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseStudent" aria-expanded="false" aria-controls="collapseStudent">
                                Informasi Mahasiswa
                            </button>
                        </h2>
                        <div id="collapseStudent" class="accordion-collapse collapse" aria-labelledby="headingStudent"
                            data-bs-parent="#verificationAccordion">
                            <div class="accordion-body">
                                @php
                                    $studentLabels = [
                                        'name' => 'Nama Lengkap',
                                        'nim' => 'NIM',
                                        'study_program' => 'Program Studi',
                                        'current_university' => 'Universitas Asal',
                                    ];
                                @endphp
                                <table class="table table-bordered">
                                    @foreach ($verification_data['student'] as $key => $value)
                                        <tr>
                                            <th width="40%">
                                                {{ $studentLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}</th>
                                            <td>{{ is_array($value) ? json_encode($value) : $value ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Penandatangan -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingSigners">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseSigners" aria-expanded="false" aria-controls="collapseSigners">
                                Informasi Penandatangan
                            </button>
                        </h2>
                        <div id="collapseSigners" class="accordion-collapse collapse" aria-labelledby="headingSigners"
                            data-bs-parent="#verificationAccordion">
                            <div class="accordion-body">
                                @if ($active_signer)
                                    @php
                                        $signerLabels = [
                                            'name' => 'Nama',
                                            'position' => 'Jabatan',
                                            'nip' => 'NIP',
                                            'signature_date' => 'Tanggal Tanda Tangan',
                                            'verification_code' => 'Kode Verifikasi',
                                        ];
                                    @endphp
                                    <table class="table table-bordered">
                                        <tr>
                                            <th colspan="2" class="bg-light">
                                                {{ $active_signer['position'] === 'Koordinator Program Studi' ? 'Penandatangan Koordinator Program Studi' : 'Penandatangan Pimpinan Jurusan PTIK' }}
                                            </th>
                                        </tr>
                                        @foreach ($active_signer as $key => $value)
                                            <tr>
                                                <th width="40%">
                                                    {{ $signerLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}</th>
                                                <td>
                                                    @if ($key === 'verification_code')
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span>{{ $value ?? '-' }}</span>
                                                            <button class="btn btn-sm btn-secondary copy-btn"
                                                                onclick="copyToClipboard('{{ $value }}')">Salin</button>
                                                        </div>
                                                    @else
                                                        {{ is_array($value) ? json_encode($value) : $value ?? '-' }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @else
                                    <div class="alert alert-warning d-flex align-items-center">
                                        <i class="fas fa-exclamation-circle alert-icon"></i>
                                        Dokumen belum ditandatangani atau kode verifikasi tidak valid untuk penandatangan.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Status Verifikasi -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingVerification">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseVerification" aria-expanded="false"
                                aria-controls="collapseVerification">
                                Status Verifikasi
                            </button>
                        </h2>
                        <div id="collapseVerification" class="accordion-collapse collapse"
                            aria-labelledby="headingVerification" data-bs-parent="#verificationAccordion">
                            <div class="accordion-body">
                                @php
                                    $statusClass = [
                                        'diajukan' => 'secondary',
                                        'diproses' => 'warning',
                                        'disetujui_kaprodi' => 'info',
                                        'disetujui' => 'success',
                                        'ditolak' => 'danger',
                                        'siap_diambil' => 'primary',
                                        'sudah_diambil' => 'secondary',
                                    ];
                                    $verificationLabels = [
                                        'status' => 'Status',
                                        'verified_at' => 'Waktu Verifikasi',
                                        'verification_code' => 'Kode Verifikasi',
                                    ];
                                    $statusKey = $verification_data['verification']['status'] ?? 'diproses';
                                    $statusClassValue = $statusClass[$statusKey] ?? 'secondary';
                                @endphp
                                <table class="table table-bordered">
                                    @foreach ($verification_data['verification'] as $key => $value)
                                        <tr>
                                            <th width="40%">
                                                {{ $verificationLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}</th>
                                            <td>
                                                @if ($key === 'verification_code')
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span>{{ $value ?? '-' }}</span>
                                                        <button class="btn btn-sm btn-secondary copy-btn"
                                                            onclick="copyToClipboard('{{ $value }}')">Salin</button>
                                                    </div>
                                                @else
                                                    {{ is_array($value) ? json_encode($value) : $value ?? '-' }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="text-center mt-5">
                    <span class="badge badge-status bg-{{ $statusClassValue }}">
                        STATUS: {{ strtoupper(str_replace('_', ' ', $statusKey)) }}
                    </span>
                    <p class="text-muted small mt-3 mb-0">
                        <i class="fas fa-clock me-1"></i>
                        Diverifikasi pada: {{ $verification_data['verification']['verified_at'] ?? 'Tidak tersedia' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Kode verifikasi berhasil disalin!');
            }).catch(err => {
                console.error('Gagal menyalin kode: ', err);
                alert('Gagal menyalin kode verifikasi.');
            });
        }
    </script>
@endpush
