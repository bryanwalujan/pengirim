<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // $request->merge(['email' => $request->email . '@unima.ac.id']);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim' => 'required|string|max:20|unique:users',
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'nim' => $request->nim,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role mahasiswa
        $mahasiswaRole = Role::where('name', 'mahasiswa')->first();
        $user->assignRole($mahasiswaRole);

        event(new Registered($user));

        Auth::login($user);

        // return redirect(route('dashboard', absolute: false));
        return redirect()->route('verification.notice');
    }
}
