<x-guest-layout title="Verifikasi Email" description="Verifikasi alamat email UNIMA Anda">
    <div class="alert alert-info">
        <div class="d-flex align-items-center">
            <i class="fas fa-envelope-circle-check me-3" style="font-size: 1.2rem;"></i>
            <div>
                <h6 class="alert-heading mb-1">Verifikasi Email Diperlukan</h6>
                <p class="mb-0 small">
                    Kami telah mengirimkan link verifikasi ke email UNIMA Anda:
                    <strong>{{ Auth::user()->email }}</strong>.
                    Silakan buka inbox (periksa juga folder spam) untuk melanjutkan proses registrasi.
                </p>
            </div>
        </div>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success small">
            <i class="fas fa-check-circle me-2"></i>
            Link verifikasi baru telah dikirim ke email Anda!
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mt-4">
        <form method="POST" action="{{ route('verification.send') }}" class="mb-0">
            @csrf
            <button type="submit" class="btn btn-primary me-3">
                <i class="fas fa-paper-plane me-1"></i> Kirim Ulang Email Verifikasi
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mb-0">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">
                <i class="fas fa-sign-out-alt me-2"></i> Keluar
            </button>
        </form>
    </div>


    <style>
        .alert {
            border-left: 4px solid;
        }

        .alert-info {
            border-left-color: #17a2b8;
            background-color: rgba(23, 162, 184, 0.1);
        }

        .alert-success {
            border-left-color: #28a745;
            background-color: rgba(40, 167, 69, 0.1);
        }
    </style>
</x-guest-layout>
