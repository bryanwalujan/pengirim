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

        // Search by name or NIM
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nim', 'like', '%' . $search . '%');
            });
        }

        $peminjaman = $query->paginate(10);
        return view('admin.peminjaman-proyektor.index', compact('peminjaman'));
    }

    /**
     * Mark a projector as returned.
     */
}
