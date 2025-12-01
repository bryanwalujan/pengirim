{{-- filepath: /c:/laragon/www/eservice-app/resources/views/admin/pendaftaran-seminar-proposal/verify-surat.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Surat Usulan Proposal</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
</head>

<body>
    <div class="container-xxl container-p-y">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="text-white mb-0">
                            <i class="bx bx-check-shield me-2"></i>
                            Verifikasi Surat Usulan Seminar Proposal
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <i class="bx bx-check-circle me-2"></i>
                            <strong>Dokumen Valid!</strong> Surat ini telah diverifikasi oleh sistem.
                        </div>

                        <h5 class="mb-3">Informasi Surat</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Nomor Surat</strong></td>
                                <td>: {{ $surat->nomor_surat }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Surat</strong></td>
                                <td>: {{ $surat->tanggal_surat->locale('id')->isoFormat('D MMMM Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kode Verifikasi</strong></td>
                                <td>: <code>{{ $surat->verification_code }}</code></td>
                            </tr>
                        </table>

                        <hr class="my-4">

                        <h5 class="mb-3">Informasi Mahasiswa</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Nama</strong></td>
                                <td>: {{ $surat->pendaftaranSeminarProposal->user->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>NIM</strong></td>
                                <td>: {{ $surat->pendaftaranSeminarProposal->user->nim }}</td>
                            </tr>
                            <tr>
                                <td><strong>Judul Penelitian</strong></td>
                                <td>: {{ $surat->pendaftaranSeminarProposal->judul_skripsi }}</td>
                            </tr>
                        </table>

                        <hr class="my-4">

                        <h5 class="mb-3">Status Tanda Tangan</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div
                                    class="card {{ $surat->isKaprodiSigned() ? 'border-success' : 'border-secondary' }}">
                                    <div class="card-body text-center">
                                        <i class="bx {{ $surat->isKaprodiSigned() ? 'bx-check-circle text-success' : 'bx-time text-secondary' }}"
                                            style="font-size: 3rem;"></i>
                                        <h6 class="mt-2">Koordinator Prodi</h6>
                                        @if ($surat->isKaprodiSigned())
                                            <p class="mb-0 small">{{ $surat->ttdKaprodiBy->name }}</p>
                                            <p class="mb-0 small text-muted">
                                                {{ $surat->ttd_kaprodi_at->locale('id')->isoFormat('D MMM Y, HH:mm') }}
                                            </p>
                                        @else
                                            <p class="mb-0 small text-muted">Belum ditandatangani</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div
                                    class="card {{ $surat->isKajurSigned() ? 'border-success' : 'border-secondary' }}">
                                    <div class="card-body text-center">
                                        <i class="bx {{ $surat->isKajurSigned() ? 'bx-check-circle text-success' : 'bx-time text-secondary' }}"
                                            style="font-size: 3rem;"></i>
                                        <h6 class="mt-2">Ketua Jurusan</h6>
                                        @if ($surat->isKajurSigned())
                                            <p class="mb-0 small">{{ $surat->ttdKajurBy->name }}</p>
                                            <p class="mb-0 small text-muted">
                                                {{ $surat->ttd_kajur_at->locale('id')->isoFormat('D MMM Y, HH:mm') }}
                                            </p>
                                        @else
                                            <p class="mb-0 small text-muted">Belum ditandatangani</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('admin.pendaftaran-seminar-proposal.download-surat', $surat->pendaftaranSeminarProposal) }}"
                                class="btn btn-primary" target="_blank">
                                <i class="bx bx-download me-1"></i> Download Surat
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
