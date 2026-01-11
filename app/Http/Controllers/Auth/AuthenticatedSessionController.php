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
            return redirect()->route('admin.dashboard.index'); // Admin dashboard
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

    /**
     * Quick login for development (local environment only)
     */
    public function quickLogin(string $role, Request $request): RedirectResponse
    {
        // Double check environment (middleware should already handle this)
        if (config('app.env') !== 'local') {
            abort(404);
        }

        // Get user ID from request
        $userId = $request->input('user_id');
        
        if (!$userId) {
            return redirect('/login')->with('error', 'Silakan pilih user terlebih dahulu.');
        }

        $user = User::find($userId);

        if (!$user || !$user->hasRole($role)) {
            return redirect('/login')->with('error', "User tidak ditemukan atau bukan {$role}.");
        }

        // Login the user
        Auth::login($user);
        $request->session()->regenerate();

        // Redirect based on role
        if ($user->hasRole('mahasiswa')) {
            return redirect()->route('user.home.index')->with('success', "Quick login sebagai {$user->name} berhasil!");
        } elseif ($user->hasRole('staff') || $user->hasRole('dosen')) {
            return redirect()->route('admin.dashboard.index')->with('success', "Quick login sebagai {$user->name} berhasil!");
        }

        return redirect('/');
    }

    /**
     * Get users by role for quick login (local environment only)
     */
    public function getUsersByRole(string $role)
    {
        // Double check environment
        if (config('app.env') !== 'local') {
            abort(404);
        }

        // Validate role
        if (!in_array($role, ['staff', 'dosen', 'mahasiswa'])) {
            abort(404);
        }

        // Get users with the specified role
        $users = User::role($role)
            ->select('id', 'name', 'email', 'nim', 'nip')
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'identifier' => $user->nim ?? $user->nip ?? $user->email,
                ];
            });

        return response()->json($users);
    }
}
