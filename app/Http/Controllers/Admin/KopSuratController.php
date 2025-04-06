<?php

namespace App\Http\Controllers\Admin;

use App\Models\KopSurat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KopSuratController extends Controller
{
    public function index()
    {
        $kopSurat = KopSurat::firstOrNew(); // Ambil atau buat instance baru
        return view('admin.kop-surat.index', compact('kopSurat'));
    }

    public function edit()
    {
        $kopSurat = KopSurat::firstOrNew([], [
            'kementerian' => 'KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI',
            'universitas' => 'UNIVERSITAS NEGERI MANADO',
            'fakultas' => 'FAKULTAS TEKNIK',
            'prodi' => 'PROGRAM STUDI S1 TEKNIK INFORMATIKA',
            'alamat' => 'Kampus UNIMA Tondano 95618',
            'kontak' => 'Telp.(0431)7233580 Website : ti.unima.ac.id, Email : teknikinformatika@unima.ac.id'
        ]);

        return view('admin.kop-surat.edit', compact('kopSurat'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:png|max:2048',
            'universitas' => 'required|string',
            'fakultas' => 'required|string',
            'prodi' => 'required|string',
            'alamat' => 'required|string',
            'kontak' => 'required|string',
        ]);

        $data = $request->except('logo');
        $kopSurat = KopSurat::firstOrNew();

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('public/kop-surat');
            $data['logo'] = str_replace('public/', '', $path);
        }

        $kopSurat->fill($data)->save();

        return redirect()->route('admin.kop-surat.index')->with('success', 'Kop surat berhasil diperbarui!');
    }
}
