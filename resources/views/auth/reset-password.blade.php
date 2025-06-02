<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Silakan masukkan email dan buat password baru Anda. Pastikan password mudah diingat namun kuat.') }}
    </div>

    <!-- Informasi tambahan -->
    <div class="mb-4 p-3 rounded-md bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 text-sm">
        <strong>Catatan:</strong> Password baru harus minimal 8 karakter dan berisi kombinasi huruf dan angka.
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email UNIMA')" />
            <x-text-input id="email"
                class="block mt-1 w-full form-control form-control-lg rounded border-gray-400 focus:border-pink-600 text-sm"
                type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username"
                placeholder="Masukkan email UNIMA anda" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-2">
            <x-input-label for="password" :value="__('Password Baru')" />
            <x-text-input id="password"
                class="block mt-1 w-full form-control form-control-lg rounded border-gray-400 focus:border-pink-600 text-sm"
                type="password" name="password" required autocomplete="new-password"
                placeholder="Masukkan password baru" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-2">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password Baru')" />
            <x-text-input id="password_confirmation"
                class="block mt-1 w-full form-control form-control-lg rounded border-gray-400 focus:border-pink-600 text-sm"
                type="password" name="password_confirmation" required autocomplete="new-password"
                placeholder="Ulangi password baru" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="text-center">
            <button type="submit"
                class="btn btn-sm btn-primary w-full mt-4 bg-pink-600 hover:bg-pink-700 text-white font-semibold py-2 px-4 rounded-md transition duration-200">
                Reset Password
            </button>
        </div>
    </form>
</x-guest-layout>
