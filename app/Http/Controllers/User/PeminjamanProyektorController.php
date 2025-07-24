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
        $peminjaman = PeminjamanProyektor::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        // Cek apakah ada proyektor yang sedang dipinjam oleh user
        $isCurrentlyBorrowing = PeminjamanProyektor::where('user_id', Auth::id())
            ->where('status', 'dipinjam')
            ->exists();

        return view('user.peminjaman-proyektor.index', compact('peminjaman', 'isCurrentlyBorrowing'));
    }

    /**
     * Store a new borrowing record.
     */
    public function store(Request $request)
    {
        // Mencegah user meminjam lebih dari satu kali jika belum dikembalikan
        $isCurrentlyBorrowing = PeminjamanProyektor::where('user_id', Auth::id())
            ->where('status', 'dipinjam')
            ->exists();

        if ($isCurrentlyBorrowing) {
            return redirect()->route('user.peminjaman-proyektor.index')
                ->with('error', 'Anda sudah meminjam proyektor dan belum mengembalikannya.');
        }

        PeminjamanProyektor::create([
            'user_id' => Auth::id(),
            'tanggal_pinjam' => now(),
        ]);

        return redirect()->route('user.peminjaman-proyektor.index')
            ->with('success', 'Peminjaman proyektor berhasil. Silahkan ambil proyektor di ruang administrasi.');
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

        $peminjamanProyektor->update([
            'status' => 'dikembalikan',
            'tanggal_kembali' => now(),
        ]);

        return redirect()->route('user.peminjaman-proyektor.index')
            ->with('success', 'Anda telah berhasil mengkonfirmasi pengembalian proyektor.');
    }
}
