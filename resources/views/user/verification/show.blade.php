@extends('layouts.qrcode.app')

@section('title', 'Verifikasi QR Code Signature')

@push('styles')
    <style>
        .table-bordered th,
        .table-bordered td {
            padding: 10px;
            vertical-align: middle;
        }

        .badge {
            font-size: 1rem;
            padding: 0.5em 1em;
        }

        .alert-icon {
            font-size: 1.25rem;
            margin-right: 10px;
        }

        .section-title {
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }
    </style>
@endpush

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Verifikasi QR Code Signature - Surat Aktif Kuliah</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Informasi Dokumen -->
                <div class="col-md-6 mb-4">
                    <h5 class="text-primary section-title">Informasi Dokumen</h5>
                    <table class="table table-bordered">
                        @foreach ($verification_data['document'] as $key => $value)
                            <tr>
                                <th width="40%">{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                <td>{{ is_array($value) ? json_encode($value) : $value ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </table>

                    <!-- Informasi Mahasiswa -->
                    <h5 class="text-primary section-title mt-4">Informasi Mahasiswa</h5>
                    <table class="table table-bordered">
                        @foreach ($verification_data['student'] as $key => $value)
                            <tr>
                                <th width="40%">{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                <td>{{ is_array($value) ? json_encode($value) : $value ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                <!-- Informasi Penandatangan -->
                <div class="col-md-6 mb-4">
                    <h5 class="text-primary section-title">Informasi Penandatangan</h5>
                    @if (!empty($verification_data['signers']['kaprodi']) || !empty($verification_data['signers']['pimpinan']))
                        <table class="table table-bordered">
                            @if (!empty($verification_data['signers']['kaprodi']))
                                <tr>
                                    <th colspan="2" class="bg-light">Penandatangan Kaprodi</th>
                                </tr>
                                @foreach ($verification_data['signers']['kaprodi'] as $key => $value)
                                    <tr>
                                        <th width="40%">{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                        <td>{{ is_array($value) ? json_encode($value) : $value ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            @if (!empty($verification_data['signers']['pimpinan']))
                                <tr>
                                    <th colspan="2" class="bg-light">Penandatangan Pimpinan</th>
                                </tr>
                                @foreach ($verification_data['signers']['pimpinan'] as $key => $value)
                                    <tr>
                                        <th width="40%">{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                        <td>{{ is_array($value) ? json_encode($value) : $value ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </table>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-circle alert-icon"></i>
                            Dokumen belum ditandatangani
                        </div>
                    @endif

                    <!-- Status Verifikasi -->
                    <h5 class="text-primary section-title mt-4">Status Verifikasi</h5>
                    @php
                        $statusClass =
                            [
                                'diajukan' => 'secondary',
                                'diproses' => 'warning',
                                'disetujui_kaprodi' => 'info',
                                'disetujui_pimpinan' => 'info',
                                'disetujui' => 'success',
                                'ditolak' => 'danger',
                                'siap_diambil' => 'primary',
                                'sudah_diambil' => 'secondary',
                            ][$verification_data['verification']['status'] ?? 'diproses'] ?? 'secondary';
                    @endphp
                    <table class="table table-bordered">
                        @foreach ($verification_data['verification'] as $key => $value)
                            <tr>
                                <th width="40%">{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                <td>{{ is_array($value) ? json_encode($value) : $value ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>

            <!-- Status Badge -->
            <div class="text-center mt-4">
                <span class="badge bg-{{ $statusClass }} p-2 fs-6">
                    STATUS: {{ strtoupper(str_replace('_', ' ', $verification_data['verification']['status'])) }}
                </span>
                <p class="text-muted small mt-2 mb-0">
                    <i class="fas fa-clock me-1"></i>
                    Diverifikasi pada: {{ $verification_data['verification']['verified_at'] }}
                </p>
            </div>
        </div>
    </div>
@endsection
