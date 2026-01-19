<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use App\Models\PembayaranUkt;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUktPayment
{
    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Handle an incoming request to check UKT payment status for mahasiswa.
     *
     * This middleware ensures that the authenticated user has paid the UKT for 
     * the active academic year before accessing certain services. If there is no 

/*******  7e712d56-b2d9-4193-873f-be8d2d9d878d  *******/
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Jika user adalah mahasiswa
        if ($user && $user->hasRole('mahasiswa')) {
            $tahunAktif = TahunAjaran::aktif()->first();

            if (! $tahunAktif) {
                return redirect()->route('user.payment.alert')
                    ->with('error', 'Tidak ada tahun ajaran aktif. Silakan hubungi administrasi.');
            }

            $hasPaid = PembayaranUkt::where('mahasiswa_id', $user->id)
                ->where('tahun_ajaran_id', $tahunAktif->id)
                ->where('status', 'bayar')
                ->exists();

            if (! $hasPaid) {
                return redirect()->route('user.payment.alert')
                    ->with('error', 'Anda harus melunasi UKT terlebih dahulu untuk mengakses layanan');
            }
        }

        return $next($request);
    }
}