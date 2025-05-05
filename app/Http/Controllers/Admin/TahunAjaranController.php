<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use App\Models\PembayaranUkt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TahunAjaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tahunAjaran = TahunAjaran::orderBy('tahun', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        return view('admin.tahun-ajaran.index', compact('tahunAjaran'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.tahun-ajaran.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|numeric|min:2000|max:2100',
            'semester' => 'required|in:ganjil,genap',
            'status_aktif' => 'sometimes|boolean'
        ]);

        DB::transaction(function () use ($request) {
            // Jika mengaktifkan tahun ajaran baru, nonaktifkan yang lain
            if ($request->status_aktif) {
                TahunAjaran::where('status_aktif', true)->update(['status_aktif' => false]);
            }

            TahunAjaran::create([
                'tahun' => $request->tahun,
                'semester' => $request->semester,
                'status_aktif' => $request->status_aktif ?? false
            ]);
        });

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan');
    }

    /**
     * Activate the specified academic year.
     */
    public function activate(TahunAjaran $tahunAjaran)
    {
        DB::transaction(function () use ($tahunAjaran) {
            // Nonaktifkan semua tahun ajaran
            TahunAjaran::where('status_aktif', true)->update(['status_aktif' => false]);

            // Aktifkan tahun ajaran yang dipilih
            $tahunAjaran->update(['status_aktif' => true]);

            // Reset semua status pembayaran mahasiswa untuk tahun ajaran ini
            PembayaranUkt::where('tahun_ajaran_id', $tahunAjaran->id)
                ->update(['status' => 'unpaid']);
        });

        return back()->with('success', 'Tahun ajaran berhasil diaktifkan dan status pembayaran direset');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TahunAjaran $tahunAjaran)
    {
        return view('admin.tahun-ajaran.edit', compact('tahunAjaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $request->validate([
            'tahun' => 'required|numeric|min:2000|max:2100',
            'semester' => 'required|in:ganjil,genap'
        ]);

        $tahunAjaran->update([
            'tahun' => $request->tahun,
            'semester' => $request->semester
        ]);

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TahunAjaran $tahunAjaran)
    {
        // Pastikan tahun ajaran yang aktif tidak bisa dihapus
        if ($tahunAjaran->status_aktif) {
            return back()->with('error', 'Tidak dapat menghapus tahun ajaran yang sedang aktif');
        }

        $tahunAjaran->delete();

        return back()->with('success', 'Tahun ajaran berhasil dihapus');
    }
}