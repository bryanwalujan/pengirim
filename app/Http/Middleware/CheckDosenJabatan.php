<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckDosenJabatan
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::find(Auth::id());

        // Allow staff to pass
        if ($user->hasRole('staff')) {
            return $next($request);
        }

        // Check if dosen has approval authority
        if ($user->isDosen() && !$user->isDosenWithApprovalAuthority()) {
            abort(403, 'Akses ditolak. Hanya Koordinator Program Studi dan Pimpinan Jurusan PTIK yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}