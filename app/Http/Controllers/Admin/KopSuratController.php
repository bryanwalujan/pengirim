<?php

namespace App\Http\Controllers\Admin;

use App\Models\KopSurat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

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
            'kementerian' => 'required|string',
            'universitas' => 'required|string',
            'fakultas' => 'required|string',
            'prodi' => 'required|string',
            'alamat' => 'required|string',
            'kontak' => 'required|string',
            'logo' => 'nullable|image|mimes:png|max:2048',
        ]);

        $data = $request->except('logo');
        $kopSurat = KopSurat::firstOrNew();

        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($kopSurat->logo && Storage::disk('public')->exists($kopSurat->logo)) {
                Storage::disk('public')->delete($kopSurat->logo);
            }

            // Simpan logo baru
            $path = $request->file('logo')->store('kop-surat', 'public');
            $data['logo'] = $path;
        }

        $kopSurat->fill($data)->save();

        return redirect()->route('admin.kop-surat.index')->with('success', 'Kop surat berhasil diperbarui!');
    }


}
