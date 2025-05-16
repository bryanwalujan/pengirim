@extends('layouts.admin.app')

@section('content')
    <div class="container py-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Verifikasi Dokumen Resmi</h4>
            </div>

            <div class="card-body">
                <div class="alert alert-success">
                    <h5><i class="fas fa-check-circle"></i> Dokumen Terverifikasi</h5>
                    <p class="mb-0">Dokumen ini telah ditandatangani secara resmi oleh Universitas</p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h5>Informasi Dokumen</h5>
                        <p><strong>Jenis:</strong> {{ str_replace('_', ' ', Str::title(Str::snake($qrData['type']))) }}</p>
                        <p><strong>Nomor:</strong> {{ $qrData['number'] ?? '-' }}</p>
                        <p><strong>Tanggal:</strong> {{ $qrData['date'] ? $qrData['date']->format('d/m/Y') : '-' }}</p>
                    </div>

                    <div class="col-md-6">
                        <h5>Penandatangan</h5>
                        <p><strong>Nama:</strong> {{ $qrData['signer'] }}</p>
                        <p><strong>Jabatan:</strong> {{ $qrData['position'] }}</p>
                    </div>
                </div>

                <hr>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <h5>Pemohon</h5>
                        <p><strong>Nama:</strong> {{ $qrData['student'] }}</p>
                        <p><strong>NIM/NIP:</strong> {{ $qrData['student_id'] }}</p>
                    </div>

                    <div class="col-md-6">
                        <h5>Verifikasi</h5>
                        <p><strong>Tanggal Tanda Tangan:</strong> {{ $qrData['verification_date']->format('d/m/Y H:i') }}
                        </p>
                        <p><strong>Email Verifikasi:</strong> {{ $qrData['contacts']['email'] }}</p>
                        <p><strong>Telepon Verifikasi:</strong> {{ $qrData['contacts']['phone'] }}</p>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <img src="{{ $document->qr_code_url }}" alt="QR Code Dokumen" style="width: 150px;">
                    <p class="text-muted mt-2">Scan QR code untuk verifikasi keaslian dokumen</p>
                </div>
            </div>
        </div>
    </div>
@endsection
