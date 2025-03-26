<x-guest-layout title="Register" description="Buat akun baru untuk mengakses layanan">
    <!-- Session Status -->
    <x-auth-session-status class="mb-2" :status="session('status')" />

    <form method="POST" action="{{ route('register') }}" class="compact-form">
        @csrf

        <div class="row g-2">
            <!-- Tambahkan hidden input untuk memastikan role -->
            <input type="hidden" name="role" value="mahasiswa">
            <!-- NIM -->
            <div class="col-12">
                <x-input-label for="nim" :value="__('NIM')" class="small-label" />
                <input type="text" id="nim" name="nim" :value="old('nim', '')"
                    class="form-control form-control-sm border-gray-400 rounded" placeholder="Masukkan NIM anda"
                    required autofocus>
                <x-input-error :messages="$errors->get('nim')" class="mt-1 small-error" />
            </div>

            <!-- Nama Lengkap -->
            <div class="col-12">
                <x-input-label for="name" :value="__('Nama Lengkap')" class="small-label" />
                <input type="text" id="name" name="name" :value="old('name', '')"
                    class="form-control form-control-sm border-gray-400 rounded" placeholder="Masukkan nama lengkap"
                    required>
                <x-input-error :messages="$errors->get('name')" class="mt-1 small-error" />
            </div>

            <!-- Email Address -->
            <div class="col-12">
                <x-input-label for="email" :value="__('Email')" class="small-label" />
                <div class="input-group">
                    <input type="text" id="email" name="email" :value="old('email', '')"
                        class="form-control form-control-sm border-gray-400 rounded-start"
                        placeholder="Email unima anda" required>
                    <span
                        class="input-group-text bg-light text-dark border-gray-400 rounded-end small-domain">@unima.ac.id</span>
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1 small-error" />
            </div>

            <!-- Password -->
            <div class="col-12 col-md-6">
                <x-input-label for="password" :value="__('Password')" class="small-label" />
                <input type="password" id="password" name="password"
                    class="form-control form-control-sm rounded border-gray-400" placeholder="Password" required
                    autocomplete="new-password">
                <x-input-error :messages="$errors->get('password')" class="mt-1 small-error" />
            </div>

            <!-- Confirm Password -->
            <div class="col-12 col-md-6">
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" class="small-label" />
                <input type="password" id="password_confirmation" name="password_confirmation"
                    class="form-control form-control-sm rounded border-gray-400" placeholder="Konfirmasi Password"
                    required autocomplete="new-password">
            </div>
        </div>

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-primary btn-sm w-100">Daftar</button>
        </div>

        <script>
            document.querySelector('form').addEventListener('submit', function(e) {
                const emailInput = document.getElementById('email');
                if (emailInput.value && !emailInput.value.includes('@')) {
                    emailInput.value += '@unima.ac.id';
                }
            });
        </script>
    </form>

    <style>
        .compact-form {
            max-height: calc(100vh - 250px);
            overflow-y: auto;
            padding-right: 5px;
        }

        .compact-form::-webkit-scrollbar {
            width: 5px;
        }

        .compact-form::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }

        .small-label {
            font-size: 0.8rem;
            margin-bottom: 0.25rem;
        }

        .small-error {
            font-size: 0.75rem;
        }

        .small-domain {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }

        .form-control-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            height: calc(1.5em + 0.5rem + 2px);
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
</x-guest-layout>
