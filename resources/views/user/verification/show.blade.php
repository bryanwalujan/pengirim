@extends('layouts.admin.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Verifikasi Dokumen Resmi</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-primary">Informasi Dokumen</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Jenis Dokumen</th>
                                <td>{{ $verification_data['document']['type'] }}</td>
                            </tr>
                            <tr>
                                <th>Nomor Surat</th>
                                <td>{{ $verification_data['document']['number'] }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Surat</th>
                                <td>{{ $verification_data['document']['date'] }}</td>
                            </tr>
                        </table>

                        <h5 class="text-primary mt-4">Informasi Mahasiswa</h5>
                        <table class="table table-bordered">
                            @foreach ($verification_data['student'] as $key => $value)
                                <tr>
                                    <th width="40%">{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                    <td>{{ $value }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h5 class="text-primary">Informasi Penandatangan</h5>
                        @if ($verification_data['signer'])
                            <table class="table table-bordered">
                                @foreach ($verification_data['signer'] as $key => $value)
                                    <tr>
                                        <th width="40%">{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                        <td>{{ $value }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        @else
                            <div class="alert alert-warning">Dokumen belum ditandatangani</div>
                        @endif

                        <h5 class="text-primary mt-4">Status Verifikasi</h5>
                        <table class="table table-bordered">
                            @foreach ($verification_data['verification'] as $key => $value)
                                <tr>
                                    <th width="40%">{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                    <td>{{ $value }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>

                @php
                    $statusClass =
                        [
                            'disetujui' => 'success',
                            'diproses' => 'warning',
                            'ditolak' => 'danger',
                        ][$verification_data['verification']['status'] ?? 'diproses'] ?? 'secondary';
                @endphp

                <div class="text-center mt-3">
                    <span class="badge bg-{{ $statusClass }} p-2 fs-6">
                        STATUS: {{ strtoupper($verification_data['verification']['status']) }}
                    </span>
                    <p class="text-muted small mt-2 mb-0">
                        <i class="fas fa-clock me-1"></i>
                        Diverifikasi pada: {{ $verification_data['verification']['verified_at'] }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
