<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeminjamanLaboratorium;
use Illuminate\Http\Request;

class AdminPeminjamanLaboratoriumController extends Controller
{
    /**
     * Menampilkan daftar semua peminjaman laboratorium dengan fitur search, filter, dan paginasi.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Query dasar dengan relasi ke user
        $query = PeminjamanLaboratorium::with('user')->latest();

        // Logika untuk pencarian berdasarkan nama atau nim/nip
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        // Logika untuk filter status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Ambil data dengan paginasi (misal: 10 data per halaman)
        $peminjaman = $query->paginate(15)->withQueryString();

        return view('admin.peminjaman-laboratorium.index', compact('peminjaman'));
    }

}