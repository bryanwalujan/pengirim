{{-- filepath: /c:/laragon/www/eservice-app/resources/views/auth/reset-password.blade.php --}}
<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Silakan masukkan email dan buat password baru Anda. Pastikan password mudah diingat namun kuat.') }}
    </div>

    <!-- Enhanced Informasi tambahan -->
    <div class="mb-4 p-3 rounded-md border-l-4" style="background-color: #fff8f3; border-left-color: #ff8c00;">
        <div class="d-flex align-items-start">
            <i class="bi bi-info-circle text-warning me-2 mt-1"></i>
            <div>
                <strong class="text-warning">Persyaratan Password Baru:</strong>
                <ul class="mt-2 mb-0 small" style="color: #d68910;">
                    <li>Minimal <strong>8 karakter</strong></li>
                    <li>Kombinasi huruf, angka, dan simbol</li>
                    <li>Mudah diingat tapi sulit ditebak</li>
                </ul>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="mb-3">
            <x-input-label for="email" :value="__('Email UNIMA')" />
            <div class="input-group">
                <input type="text" id="email" name="email" value="{{ old('email', $request->email) }}"
                    class="form-control form-control-lg border-gray-400 text-sm rounded-start"
                    placeholder="Email UNIMA anda" required autofocus autocomplete="username">
                <span
                    class="input-group-text bg-light bg-gradient-orange text-white text-bold border-gray-400 rounded-end">
                    @unima.ac.id
                </span>
            </div>
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
            <button type="submit" class="btn btn-lg btn-orange w-100 mt-3 mb-3">
                <i class="bi bi-shield-lock me-2"></i>Reset Password
            </button>
        </div>

        <!-- Link kembali ke login -->
        <div class="text-center">
            <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100">
                <i class="bi bi-arrow-left me-2"></i>Kembali ke Login
            </a>
        </div>
    </form>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');

            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });

        document.getElementById('togglePasswordConfirm').addEventListener('click', function() {
            const passwordConfirm = document.getElementById('password_confirmation');
            const icon = document.getElementById('toggleIconConfirm');

            if (passwordConfirm.type === 'password') {
                passwordConfirm.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                passwordConfirm.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });

        // Auto-complete email domain
        document.querySelector('form').addEventListener('submit', function(e) {
            const emailInput = document.getElementById('email');
            if (emailInput.value && !emailInput.value.includes('@')) {
                emailInput.value += '@unima.ac.id';
            }
        });
    </script>
</x-guest-layout>
