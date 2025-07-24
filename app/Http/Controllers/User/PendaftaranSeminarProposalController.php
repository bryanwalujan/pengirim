<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\PendaftaranSeminarProposal;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class PendaftaranSeminarProposalController extends Controller
{
    public function create()
    {
        // Ambil daftar dosen untuk ditampilkan di form
        $dosen = User::whereHas('roles', fn($query) => $query->where('name', 'dosen'))->get();
        return view('user.pendaftaran-seminar-proposal.create', compact('dosen'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_skripsi' => 'required|string|max:255',
            'ipk' => 'required|numeric|between:0,4.00',
            'dosen_pembimbing_id' => 'required|exists:users,id',
            'file_transkrip_nilai' => 'required|file|mimes:pdf|max:2048',
            'file_proposal_penelitian' => 'required|file|mimes:pdf|max:5120',
            'file_surat_permohonan' => 'required|file|mimes:pdf|max:2048',
        ]);

        // Dapatkan user yang login
        $user = Auth::user();

        // Tentukan angkatan berdasarkan 2 digit pertama NIM
        $angkatan = '20' . substr($user->nim, 0, 2);

        // Proses upload file ke public storage
        $pathTranskrip = $request->file('file_transkrip_nilai')->store('sempro/transkrip', 'public');
        $pathProposal = $request->file('file_proposal_penelitian')->store('sempro/proposal', 'public');
        $pathPermohonan = $request->file('file_surat_permohonan')->store('sempro/permohonan', 'public');

        // Simpan semua data ke database
        PendaftaranSeminarProposal::create([
            'user_id' => $user->id,
            'angkatan' => $angkatan,
            'judul_skripsi' => $request->judul_skripsi,
            'ipk' => $request->ipk,
            'dosen_pembimbing_id' => $request->dosen_pembimbing_id,
            'file_transkrip_nilai' => $pathTranskrip,
            'file_proposal_penelitian' => $pathProposal,
            'file_surat_permohonan' => $pathPermohonan,
        ]);

        return redirect()->route('user.pendaftaran-seminar-proposal.index')->with('success', 'Pendaftaran seminar proposal berhasil diajukan.');
    }

    public function index()
    {
        $pendaftaran = PendaftaranSeminarProposal::where('user_id', Auth::id())->latest()->get();
        return view('user.pendaftaran-seminar-proposal.index', compact('pendaftaran'));
    }
}