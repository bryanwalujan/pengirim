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
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email"
                class="block mt-1 w-full form-control rounded border-gray-400 focus:border-pink-600 text-sm"
                placeholder="Email UNIMA anda" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="text-center">
            <button type="submit"
                class="btn btn-sm btn-primary w-full mt-4 bg-pink-600 hover:bg-pink-700 text-white font-semibold py-2 px-4 rounded-md transition duration-200">
                Kirim Link Reset Password
            </button>
        </div>
    </form>
</x-guest-layout>
