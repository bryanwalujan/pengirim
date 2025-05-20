<?php

namespace App\Http\Controllers\Admin;

use App\Models\KopSurat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class KopSuratController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('manage kopsurat'); // Pastikan user memiliki izin untuk mengelola kop surat
        $kopSurat = KopSurat::firstOrNew(); // Ambil atau buat instance baru
        return view('admin.kop-surat.index', compact('kopSurat'));
    }
    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Halaman edit kop surat.
     *
     * @return \Illuminate\View\View
     */
    /*******  3116446e-48e0-4f32-8733-ab2d945a6098  *******/

    public function edit()
    {
        $kopSurat = KopSurat::firstOrNew([], [
            'kementerian' => 'KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI',
            'universitas' => 'UNIVERSITAS NEGERI MANADO',
            'fakultas' => 'FAKULTAS TEKNIK',
            'prodi' => 'PROGRAM STUDI S1 TEKNIK INFORMATIKA',
            'alamat' => 'Alamat : Kampus UNIMA Tondano 95618, Telp.(0431)7233580',
            'kontak' => 'Website : https://ti.unima.ac.id, Email : teknikinformatika@unima.ac.id'
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
