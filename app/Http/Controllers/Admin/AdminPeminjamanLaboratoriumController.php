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
                    ->orWhere('nim_nip', 'like', "%{$search}%");
            });
        }

        // Logika untuk filter status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Ambil data dengan paginasi (misal: 10 data per halaman)
        $peminjaman = $query->paginate(10)->withQueryString();

        return view('admin.peminjaman-laboratorium.index', compact('peminjaman'));
    }

    /**
     * Memperbarui status peminjaman menjadi 'selesai'.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $peminjaman = PeminjamanLaboratorium::findOrFail($id);

        $peminjaman->status = 'selesai';
        $peminjaman->save();

        return redirect()->route('admin.peminjaman-laboratorium.index')
            ->with('success', 'Peminjaman laboratorium telah ditandai sebagai selesai.');
    }
}