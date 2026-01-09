<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Masukkan email UNIMA anda dan kami akan mengirimkan link untuk mengatur ulang password.') }}
    </div>

    <!-- Informasi tambahan -->
    <div class="mb-4 p-3 rounded-md bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 text-sm">
        <strong>Catatan:</strong> Setelah mengirim permintaan, silakan cek juga folder <em>Spam</em> atau <em>Junk</em>
        di email UNIMA anda jika tidak menemukan email reset password di kotak masuk.
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="form-control" placeholder="Email UNIMA anda" type="email" name="email"
                :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-orange w-100 py-2">
                <i class="fas fa-paper-plane me-2"></i>Kirim Link Reset Password
            </button>
        </div>

        <div class="text-center">
            <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100 py-2">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Login
            </a>
        </div>
    </form>

</x-guest-layout>
