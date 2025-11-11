{{-- filepath: /c:/laragon/www/eservice-app/resources/views/auth/login.blade.php --}}
<x-guest-layout title="Login">
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @section('title', 'Login')

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <!-- Email Address -->
        <div class="mb-3">
            <x-input-label for="email" :value="__('Email')" />
            <div class="input-group">
                <input type="text" id="email" name="email" :value="old('email', '')"
                    class="form-control form-control-lg border-gray-400 text-sm rounded-start"
                    placeholder="Email unima anda" aria-label="Email" required autofocus autocomplete="username">
                <span
                    class="input-group-text bg-light bg-gradient-orange text-white text-bold border-gray-400 rounded-end"
                    id="domain">@unima.ac.id</span>
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <script>
            // Pastikan nilai yang dikirim hanya bagian nama pengguna
            document.querySelector('form').addEventListener('submit', function(e) {
                const emailInput = document.getElementById('email');
                if (emailInput.value && !emailInput.value.includes('@')) {
                    emailInput.value += '@unima.ac.id';
                }
            });
        </script>

        <!-- Password -->
        <div class="mb-1">
            <x-input-label for="password" :value="__('Password')" />
            <div class="password-wrapper">
                <input type="password" id="password" name="password"
                    class="form-control form-control-lg rounded border-gray-400 text-sm" placeholder="Password"
                    aria-label="Password" required autocomplete="current-password">
                <button type="button" class="password-toggle" id="togglePassword" tabindex="-1"
                    aria-label="Toggle password visibility">
                    <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Forgot Password -->
        @if (Route::has('password.request'))
            <div class="text-end">
                <a href="{{ route('password.request') }}" class="text-sm text-orange">
                    {{ __('Lupa password?') }}
                </a>
            </div>
        @endif

        <!-- Login Button -->
        <div class="text-center">
            <button type="submit" class="btn btn-lg btn-orange btn-lg w-100 mt-2 mb-3">Login</button>
        </div>

        <!-- Link kembali ke beranda untuk mobile -->
        <div class="text-center d-md-none">
            <a href="{{ route('user.home.index') }}" class="btn btn-outline-secondary w-100">
                <i class="fas fa-home me-2"></i>
                Kembali ke Beranda
            </a>
        </div>
    </form>

    <!-- Informasi tambahan -->
    <div class="text-center mt-3">
        <p class="text-muted small">
            Belum memiliki akses?
            <a href="{{ route('user.home.index') }}" class="text-orange text-decoration-none">
                Kunjungi beranda untuk informasi lebih lanjut
            </a>
        </p>
    </div>

    {{-- Script untuk Toggle Password --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const togglePassword = document.getElementById('togglePassword');
                const passwordInput = document.getElementById('password');
                const toggleIcon = document.getElementById('togglePasswordIcon');

                if (togglePassword && passwordInput && toggleIcon) {
                    togglePassword.addEventListener('click', function(e) {
                        e.preventDefault();

                        // Toggle password visibility
                        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordInput.setAttribute('type', type);

                        // Toggle icon and color
                        if (type === 'text') {
                            toggleIcon.classList.remove('bi-eye-slash');
                            toggleIcon.classList.add('bi-eye');
                            this.classList.add('active');
                        } else {
                            toggleIcon.classList.remove('bi-eye');
                            toggleIcon.classList.add('bi-eye-slash');
                            this.classList.remove('active');
                        }
                    });

                    // Prevent form submission when clicking toggle
                    togglePassword.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                    });
                }
            });
        </script>
    @endpush
</x-guest-layout>
