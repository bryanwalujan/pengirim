<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckJabatan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $jabatan
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $jabatan)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk tindakan ini');
        }

        // Cek lebih fleksibel untuk berbagai variasi jabatan
        $jabatanUser = strtolower($user->jabatan);
        $jabatanDicari = strtolower($jabatan);

        // Untuk Pimpinan Jurusan PTIK
        if ($jabatanDicari === 'pimpinan jurusan ptik') {
            if (
                str_contains($jabatanUser, 'pimpinan') ||
                str_contains($jabatanUser, 'ptik') ||
                str_contains($jabatanUser, 'jurusan')
            ) {
                return $next($request);
            }
        }
        // Untuk Koordinator Program Studi
        elseif (str_contains($jabatanUser, $jabatanDicari)) {
            return $next($request);
        }

        return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk tindakan ini');
    }
}