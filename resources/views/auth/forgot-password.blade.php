<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __(' Masukkan email UNIMA anda dan kami akan mengirimkan link untuk mengatur ulang password.') }}
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
                placeholder="Email unima anda" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-sm btn-primary w-100 mt-4 mb-0">Email Password Reset Link</button>
        </div>
    </form>
</x-guest-layout>
