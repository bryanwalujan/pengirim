<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\PeminjamanLaboratorium;

class PeminjamanLaboratoriumController extends Controller
{
    public function index()
    {
        // Mengambil riwayat peminjaman milik user yang login
        $peminjaman = PeminjamanLaboratorium::where('user_id', Auth::id())->latest()->paginate(10)->withQueryString();

        // Mengambil data peminjaman yang sedang aktif (milik siapa saja)
        $peminjamanAktif = PeminjamanLaboratorium::with('user')->where('status', 'diajukan')->first();

        return view('user.peminjaman-laboratorium.index', compact('peminjaman', 'peminjamanAktif'));
    }

    public function store(Request $request)
    {
        // 1. Cek apakah ada peminjaman lain yang masih berstatus 'diajukan'
        $activeLoan = PeminjamanLaboratorium::where('status', 'diajukan')->first();

        if ($activeLoan) {
            // 2. Jika ada, kembalikan pengguna dengan pesan error
            return back()->with('error', 'Gagal mengajukan peminjaman. Laboratorium sedang digunakan oleh pengguna lain.');
        }

        // 3. Jika tidak ada, lanjutkan proses penyimpanan
        $request->validate([
            'tanggal_peminjaman' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'keperluan' => 'required|string',
        ]);

        PeminjamanLaboratorium::create([
            'user_id' => Auth::id(),
            // 'nama_laboratorium' tidak perlu lagi
            'tanggal_peminjaman' => $request->tanggal_peminjaman,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keperluan' => $request->keperluan,
            'status' => 'diajukan', // Status langsung 'diajukan'
        ]);

        return redirect()->route('user.peminjaman-laboratorium.index')->with('success', 'Peminjaman laboratorium berhasil diajukan.');
    }

    /**
     * Mahasiswa mengonfirmasi bahwa peminjaman telah selesai.
     */
    public function update($id)
    {
        $peminjaman = PeminjamanLaboratorium::findOrFail($id);

        // Pastikan hanya pemilik peminjaman yang bisa mengonfirmasi
        if ($peminjaman->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk aksi ini.');
        }

        $peminjaman->status = 'selesai';
        $peminjaman->save();

        return back()->with('success', 'Peminjaman laboratorium telah berhasil diselesaikan.');
    }
}
