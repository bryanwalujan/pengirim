<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\SuratAktifKuliah;
use App\Http\Controllers\Controller;

class DosenSuratAktifKuliahController extends Controller
{
    public function index()
    {
        $surats = SuratAktifKuliah::where('status', 'diajukan')
            ->with(['mahasiswa', 'status'])
            ->latest()
            ->paginate(15);

        return view('dosen.surat-aktif-kuliah.index', compact('surats'));
    }

    public function show(SuratAktifKuliah $surat)
    {
        $surat->load(['mahasiswa', 'status', 'trackings']);
        return view('dosen.surat-aktif-kuliah.show', compact('surat'));
    }


    public function approve(Request $request, SuratAktifKuliah $surat)
    {
        // Panggil method dari AdminController
        return app(AdminSuratAktifKuliahController::class)->approveByDosen($request, $surat);
    }
}
