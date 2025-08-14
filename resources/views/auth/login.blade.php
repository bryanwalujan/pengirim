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
            <input type="password" id="password" name="password"
                class="form-control form-control-lg rounded border-gray-400 focus:border-pink-600 text-sm"
                placeholder="Password" aria-label="Password" required autocomplete="current-password">
        </div>
        <!-- Forgot Password -->
        @if (Route::has('password.request'))
            <div class="text-end">
                <a href="{{ route('password.request') }}" class="text-sm text-orange">
                    {{ __('Lupa password?') }}
                </a>
            </div>
        @endif
        <div class="text-center">
            <button type="submit" class="btn btn-lg btn-orange btn-lg w-100 mt-2 mb-0">Login</button>
        </div>
    </form>
</x-guest-layout>
