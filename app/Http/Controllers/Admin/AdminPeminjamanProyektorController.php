<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\PeminjamanProyektor;
use App\Http\Controllers\Controller;

class AdminPeminjamanProyektorController extends Controller
{
    /**
     * Display a listing of all borrowing records.
     */
    public function index(Request $request)
    {
        $query = PeminjamanProyektor::with('user')->latest();

        // Filter berdasarkan status
        if ($request->has('status') && in_array($request->status, ['dipinjam', 'dikembalikan'])) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan kode proyektor
        if ($request->has('proyektor') && $request->proyektor) {
            $query->where('proyektor_code', 'like', '%' . $request->proyektor . '%');
        }

        // Search by name or NIM
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nim', 'like', '%' . $search . '%');
            });
        }

        $peminjaman = $query->paginate(20)->withQueryString();

        // Statistik untuk dashboard
        $stats = [
            'total' => PeminjamanProyektor::count(),
            'sedang_dipinjam' => PeminjamanProyektor::where('status', 'dipinjam')->count(),
            'dikembalikan' => PeminjamanProyektor::where('status', 'dikembalikan')->count(),
        ];

        return view('admin.peminjaman-proyektor.index', compact('peminjaman', 'stats'));
    }

    /**
     * Show detail peminjaman
     */
    public function show(PeminjamanProyektor $peminjamanProyektor)
    {
        $peminjamanProyektor->load('user');
        return view('admin.peminjaman-proyektor.show', compact('peminjamanProyektor'));
    }

    /**
     * Update status peminjaman (untuk proses pengembalian)
     */
    public function updateStatus(Request $request, PeminjamanProyektor $peminjamanProyektor)
    {
        $validated = $request->validate([
            'status' => 'required|in:dipinjam,dikembalikan',
            'keterangan' => 'nullable|string|max:500',
        ]);

        // Jika status dikembalikan, set tanggal kembali
        if ($validated['status'] === 'dikembalikan') {
            $peminjamanProyektor->update([
                'status' => $validated['status'],
                'tanggal_kembali' => now(),
                'keterangan' => $validated['keterangan'] ?? null,
            ]);
        } else {
            $peminjamanProyektor->update($validated);
        }

        return redirect()->route('admin.peminjaman-proyektor.index')
            ->with('success', 'Status peminjaman berhasil diperbarui.');
    }

    /**
     * Delete peminjaman record
     */
    public function destroy(PeminjamanProyektor $peminjamanProyektor)
    {
        $peminjamanProyektor->delete();

        return redirect()->route('admin.peminjaman-proyektor.index')
            ->with('success', 'Data peminjaman berhasil dihapus.');
    }
}