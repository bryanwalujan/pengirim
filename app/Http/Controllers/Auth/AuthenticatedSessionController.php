<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Redirect berdasarkan role
        $user = User::find(Auth::user()->id);

        // Check if there's an intended URL (for redirecting after login)
        if (session()->has('url.intended')) {
            return redirect()->intended();
        }

        if ($user->hasRole('mahasiswa')) {
            return redirect()->route('user.home.index');
        } elseif ($user->hasRole('staff') || $user->hasRole('dosen')) {
            return redirect()->route('dashboard'); // Admin dashboard
        }

        return redirect('/'); // Fallback

    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
