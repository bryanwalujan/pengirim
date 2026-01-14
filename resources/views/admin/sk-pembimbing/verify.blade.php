{{-- filepath: resources/views/admin/sk-pembimbing/verify.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi SK Pembimbing - E-Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .verify-card {
            max-width: 500px;
            width: 100%;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .verify-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 16px 16px 0 0;
        }
        .verify-header.invalid {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        .verify-icon {
            font-size: 4rem;
            margin-bottom: 10px;
        }
        .verify-body {
            padding: 30px;
            background: white;
            border-radius: 0 0 16px 16px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #6c757d;
            font-weight: 500;
        }
        .info-value {
            font-weight: 600;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="verify-card">
        @if($pengajuan->isSelesai())
            <div class="verify-header">
                <i class="bx bx-check-circle verify-icon"></i>
                <h4 class="mb-1">Dokumen Valid</h4>
                <p class="mb-0 opacity-75">SK Pembimbing ini sah dan terverifikasi</p>
            </div>
        @else
            <div class="verify-header invalid">
                <i class="bx bx-x-circle verify-icon"></i>
                <h4 class="mb-1">Dokumen Belum Selesai</h4>
                <p class="mb-0 opacity-75">Status: {{ $pengajuan->status_label }}</p>
            </div>
        @endif

        <div class="verify-body">
            <h6 class="text-muted mb-3">
                <i class="bx bx-info-circle me-1"></i>INFORMASI DOKUMEN
            </h6>

            <div class="info-row">
                <span class="info-label">Kode Verifikasi</span>
                <span class="info-value"><code>{{ $pengajuan->verification_code }}</code></span>
            </div>

            <div class="info-row">
                <span class="info-label">Nama Mahasiswa</span>
                <span class="info-value">{{ $pengajuan->mahasiswa->name ?? '-' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">NIM</span>
                <span class="info-value">{{ $pengajuan->mahasiswa->nim ?? '-' }}</span>
            </div>

            @if($pengajuan->nomor_surat)
            <div class="info-row">
                <span class="info-label">No. Surat</span>
                <span class="info-value">{{ $pengajuan->nomor_surat }}</span>
            </div>
            @endif

            @if($pengajuan->tanggal_surat)
            <div class="info-row">
                <span class="info-label">Tanggal Surat</span>
                <span class="info-value">{{ $pengajuan->tanggal_surat->format('d F Y') }}</span>
            </div>
            @endif

            <div class="info-row">
                <span class="info-label">Pembimbing 1</span>
                <span class="info-value">{{ $pengajuan->dosenPembimbing1->name ?? '-' }}</span>
            </div>

            @if($pengajuan->dosenPembimbing2)
            <div class="info-row">
                <span class="info-label">Pembimbing 2</span>
                <span class="info-value">{{ $pengajuan->dosenPembimbing2->name }}</span>
            </div>
            @endif

            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value">
                    @if($pengajuan->isSelesai())
                        <span class="badge bg-success">Terbit & Sah</span>
                    @else
                        <span class="badge bg-warning">{{ $pengajuan->status_label }}</span>
                    @endif
                </span>
            </div>

            <div class="text-center mt-4">
                <small class="text-muted">
                    Dokumen diverifikasi pada {{ now()->format('d M Y H:i') }} WIB
                </small>
            </div>

            <div class="text-center mt-3">
                <a href="{{ route('user.home.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bx bx-home me-1"></i>Ke Beranda
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
