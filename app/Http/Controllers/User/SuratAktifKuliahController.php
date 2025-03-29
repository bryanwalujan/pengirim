<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Models\SuratAktifKuliah;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SuratAktifKuliahController extends Controller
{
    public function index()
    {
        // Menampilkan form pengajuan
        return view('user.surat.aktif-kuliah.create');
    }
    public function create()
    {
        return view('user.surat.aktif-kuliah.create');
    }
    public function store(Request $request)
    {
        // Validasi form
        $request->validate([
            'tahun_akademik' => 'required',
            'semester' => 'required',
            'dokumen' => 'required|file|mimes:pdf|max:2048'
        ]);

        // Simpan ke database
        $surat = SuratAktifKuliah::create([
            'user_id' => Auth::user()->id,
            'dokumen_path' => $request->file('dokumen')->store('dokumen-surat'),
            'status' => 'pending'
        ]);

        return redirect()->route('surat.aktif-kuliah.show', $surat->id);
    }
}
