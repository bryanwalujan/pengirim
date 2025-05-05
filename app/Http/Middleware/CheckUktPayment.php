<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use App\Models\PembayaranUkt;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUktPayment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user adalah mahasiswa
        if (User::find(Auth::id())->hasRole('mahasiswa')) {
            $tahunAktif = TahunAjaran::where('status_aktif', true)->first();

            // Jika tidak ada tahun aktif, block akses
            if (!$tahunAktif) {
                return redirect()->route('user.payment.alert')
                    ->with('error', 'Tidak ada tahun ajaran aktif. Silakan hubungi administrasi.');
            }

            // Cek status pembayaran
            $hasPaid = PembayaranUkt::where('mahasiswa_id', User::find(Auth::id()))
                ->where('tahun_ajaran_id', $tahunAktif->id)
                ->where('status', 'paid')
                ->exists();

            // Jika belum bayar, redirect ke halaman alert
            if (!$hasPaid) {
                return redirect()->route('user.payment.alert')
                    ->with('error', 'Anda harus melunasi UKT terlebih dahulu untuk mengakses layanan');
            }
        }

        return $next($request);
    }
}