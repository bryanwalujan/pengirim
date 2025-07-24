<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\KomisiHasil;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KomisiHasilController extends Controller
{
    public function index()
    {
        $komisiHasils = KomisiHasil::where('user_id', Auth::id())->latest()->get();
        return view('user.komisi-hasil.index', compact('komisiHasils'));
    }

    public function create()
    {
        $dosens = User::role('dosen')->get();
        return view('user.komisi-hasil.create', compact('dosens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_skripsi' => 'required|string|max:255',
            'dosen_pembimbing1_id' => 'required|exists:users,id',
            'dosen_pembimbing2_id' => 'required|exists:users,id|different:dosen_pembimbing1_id',
        ]);

        KomisiHasil::create([
            'user_id' => Auth::id(),
            'judul_skripsi' => $request->judul_skripsi,
            'dosen_pembimbing1_id' => $request->dosen_pembimbing1_id,
            'dosen_pembimbing2_id' => $request->dosen_pembimbing2_id,
            'status' => 'pending'
        ]);

        return redirect()->route('user.komisi-hasil.index')
            ->with('success', 'Pengajuan Komisi Hasil berhasil dibuat.');
    }

    public function show(KomisiHasil $komisiHasil)
    {
        return view('user.komisi-hasil.show', compact('komisiHasil'));
    }
}