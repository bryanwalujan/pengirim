{{-- Digital Verification Card --}}
<div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
    <div class="card-body p-0">
        <div class="bg-warning p-3 px-4 d-flex align-items-center gap-3 shadow-sm">
            <i class="bx bx-shield-quarter text-white fs-3"></i>
            <div class="text-white fw-bold">Autentikasi Digital</div>
        </div>
        <div class="p-4 text-center">
            <small class="text-muted text-uppercase fw-bold mb-2 d-block">Kode Verifikasi</small>
            <div class="bg-light rounded p-3 mb-3 border">
                <code class="fs-4 text-dark fw-bold font-mono tracking-widest">{{ $beritaAcara->verification_code }}</code>
            </div>
            @if ($beritaAcara->verification_url)
                <a href="{{ $beritaAcara->verification_url }}" target="_blank" class="btn btn-dark w-100 shadow-sm">
                    <i class="bx bx-qr-scan me-2"></i>Validasi Berkas
                </a>
            @endif
            <div class="mt-3 text-muted x-small">
                Gunakan kode ini untuk mengecek keaslian dokumen melalui portal publik E-Services UNIMA.
            </div>
        </div>
    </div>
</div>
