<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\KomisiProposal;
use App\Models\User; // Import model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KomisiProposalController extends Controller
{
    public function index()
    {
        // Menampilkan daftar pengajuan milik user yang sedang login
        $komisiProposals = KomisiProposal::where('user_id', Auth::id())->latest()->get();
        return view('user.komisi-proposal.index', compact('komisiProposals'));
    }

    public function create()
    {
        // Ambil semua user dengan role 'dosen'
        $dosens = User::role('dosen')->get();
        return view('user.komisi-proposal.create', compact('dosens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_skripsi' => 'required|string|max:255',
            // Validasi bahwa ID yang dikirim ada di tabel users
            'dosen_pembimbing_id' => 'required|exists:users,id',
        ]);

        KomisiProposal::create([
            'user_id' => Auth::id(),
            'judul_skripsi' => $request->judul_skripsi,
            'dosen_pembimbing_id' => $request->dosen_pembimbing_id,
        ]);

        return redirect()->route('user.komisi-proposal.index')->with('success', 'Pengajuan Komisi Proposal berhasil dibuat.');
    }
}