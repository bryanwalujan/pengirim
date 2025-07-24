<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PendaftaranUjianHasil;

class AdminPendaftaranUjianHasilController extends Controller
{
    // Menampilkan daftar semua pendaftaran
    public function index(Request $request)
    {
        $query = PendaftaranUjianHasil::with('user')->latest();

        // Search by nama or nim
        if ($search = $request->query('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        // Filter by angkatan
        if ($angkatan = $request->query('angkatan')) {
            $query->where('angkatan', $angkatan);
        }

        $pendaftaranUjianHasils = $query->paginate(15);
        $uniqueAngkatan = PendaftaranUjianHasil::select('angkatan')->distinct()->pluck('angkatan')->sort();

        return view('admin.pendaftaran-ujian-hasil.index', compact('pendaftaranUjianHasils', 'uniqueAngkatan'));
    }

    // Menampilkan detail satu pendaftaran untuk modal
    public function show(PendaftaranUjianHasil $pendaftaranUjianHasil)
    {
        // Memuat relasi untuk ditampilkan
        $pendaftaranUjianHasil->load(['user', 'dosenPa', 'dosenPembimbing1', 'dosenPembimbing2']);
        return view('admin.pendaftaran-ujian-hasil.detail-modal', compact('pendaftaranUjianHasil'));
    }
}