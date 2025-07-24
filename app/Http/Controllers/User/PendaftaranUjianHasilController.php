<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranUjianHasil;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PendaftaranUjianHasilController extends Controller
{
    public function index()
    {
        $registrations = PendaftaranUjianHasil::where('user_id', Auth::id())->latest()->paginate(10);
        return view('user.pendaftaran-ujian-hasil.index', compact('registrations'));
    }

    public function create()
    {
        $dosens = User::whereHas('roles', fn($q) => $q->where('name', 'dosen'))->get();
        return view('user.pendaftaran-ujian-hasil.create', compact('dosens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ipk' => 'required|numeric|between:0,4.00',
            'judul_skripsi' => 'required|string|max:255',
            'dosen_pa_id' => 'required|exists:users,id',
            'dosen_pembimbing1_id' => 'required|exists:users,id',
            'dosen_pembimbing2_id' => 'required|exists:users,id',
            'transkrip_nilai' => 'required|file|mimes:pdf|max:2048',
            'file_skripsi' => 'required|file|mimes:pdf|max:5120', // 5MB
            'komisi_hasil' => 'required|file|mimes:pdf|max:2048',
            'surat_permohonan_hasil' => 'required|file|mimes:pdf|max:2048',
        ]);

        $user = Auth::user();
        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['nim'] = $user->nim;
        $data['nama'] = $user->name;
        // Tentukan angkatan berdasarkan 2 digit pertama NIM
        $data['angkatan'] = '20' . substr($user->nim, 0, 2);

        // Handle file uploads
        $path = 'pendaftaran-ujian-hasil';
        $data['transkrip_nilai'] = $request->file('transkrip_nilai')->store($path . '/transkrip-nilai', 'public');
        $data['file_skripsi'] = $request->file('file_skripsi')->store($path . '/skripsi', 'public');
        $data['komisi_hasil'] = $request->file('komisi_hasil')->store($path . '/komisi-hasil', 'public');
        $data['surat_permohonan_hasil'] = $request->file('surat_permohonan_hasil')->store($path . '/permohonan-hasil', 'public');

        PendaftaranUjianHasil::create($data);

        return redirect()->route('user.pendaftaran-ujian-hasil.index')->with('success', 'Pendaftaran ujian hasil berhasil disimpan.');
    }

    public function show(PendaftaranUjianHasil $pendaftaran_ujian_hasil)
    {
        if ($pendaftaran_ujian_hasil->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        return view('user.pendaftaran-ujian-hasil.show', compact('pendaftaran_ujian_hasil'));
    }
}