<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Models\PeminjamanProyektor;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PeminjamanProyektorController extends Controller
{
    /**
     * Display a listing of the user's borrowing history.
     */
    public function index()
    {
        $peminjaman = PeminjamanProyektor::byUser(Auth::id())
            ->latest()
            ->paginate(10);

        // Cek apakah ada proyektor yang sedang dipinjam oleh user
        $isCurrentlyBorrowing = PeminjamanProyektor::byUser(Auth::id())
            ->status('dipinjam')
            ->exists();

        // Get available proyektor list
        $availableProyektor = PeminjamanProyektor::getAvailableProyektorList();

        return view('user.peminjaman-proyektor.index', compact(
            'peminjaman',
            'isCurrentlyBorrowing',
            'availableProyektor'
        ));
    }

    /**
     * Store a new borrowing record.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'proyektor_code' => [
                'required',
                'string',
                'in:' . implode(',', config('proyektor.list', [])),
            ],
            'keperluan' => 'required|string|max:255',
        ], [
            'proyektor_code.required' => 'Proyektor harus dipilih',
            'proyektor_code.in' => 'Proyektor yang dipilih tidak valid',
            'keperluan.required' => 'Keperluan peminjaman harus diisi',
        ]);

        // Mencegah user meminjam lebih dari satu kali jika belum dikembalikan
        if (PeminjamanProyektor::byUser(Auth::id())->status('dipinjam')->exists()) {
            return redirect()->route('user.peminjaman-proyektor.index')
                ->with('error', 'Anda sudah meminjam proyektor dan belum mengembalikannya.');
        }

        // Check if proyektor is available
        if (!PeminjamanProyektor::isProyektorAvailable($validated['proyektor_code'])) {
            return redirect()->route('user.peminjaman-proyektor.index')
                ->with('error', 'Proyektor yang dipilih sedang dipinjam oleh mahasiswa lain.');
        }

        PeminjamanProyektor::create([
            'user_id' => Auth::id(),
            'proyektor_code' => strtoupper($validated['proyektor_code']),
            'keperluan' => $validated['keperluan'],
            'tanggal_pinjam' => now(),
            'status' => 'dipinjam',
        ]);

        return redirect()->route('user.peminjaman-proyektor.index')
            ->with('success', 'Peminjaman proyektor berhasil. Silakan ambil proyektor di ruang administrasi.');
    }

    /**
     * Mark a projector as returned by the user.
     */
    public function kembalikan(PeminjamanProyektor $peminjamanProyektor)
    {
        // Keamanan: Pastikan user hanya bisa mengembalikan peminjamannya sendiri
        if ($peminjamanProyektor->user_id !== Auth::id()) {
            abort(403, 'AKSI TIDAK DIIZINKAN');
        }

        if ($peminjamanProyektor->status !== 'dipinjam') {
            return redirect()->route('user.peminjaman-proyektor.index')
                ->with('error', 'Proyektor sudah dikembalikan.');
        }

        $peminjamanProyektor->update([
            'status' => 'dikembalikan',
            'tanggal_kembali' => now(),
        ]);

        return redirect()->route('user.peminjaman-proyektor.index')
            ->with('success', 'Anda telah berhasil mengkonfirmasi pengembalian proyektor.');
    }
}