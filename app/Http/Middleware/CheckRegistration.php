<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRegistration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika ada parameter role dan bukan mahasiswa, tolak
        if ($request->has('role') && $request->role !== 'mahasiswa') {
            abort(403, 'Registrasi hanya untuk mahasiswa');
        }

        return $next($request);
    }
}
