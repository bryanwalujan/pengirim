<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email"
                class="block mt-1 w-full form-control form-control-lg rounded border-gray-400 focus:border-pink-600 text-sm"
                type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-2">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password"
                class="block mt-1 w-full form-control form-control-lg rounded border-gray-400 focus:border-pink-600 text-sm"
                type="password" name="password" required autocomplete="new-password" placeholder="Password lama" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-2">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi password')" />

            <x-text-input id="password_confirmation"
                class="block mt-1 w-full form-control form-control-lg rounded border-gray-400 focus:border-pink-600 text-sm"
                type="password" name="password_confirmation" required autocomplete="new-password"
                placeholder="Password baru" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-sm btn-primary w-100 mt-3 mb-0">Reset Password</button>
        </div>
    </form>
</x-guest-layout>
