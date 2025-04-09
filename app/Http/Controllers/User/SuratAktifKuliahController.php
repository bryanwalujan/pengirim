<?php

namespace App\Http\Controllers\User;

use App\Models\Service;
use App\Models\StatusSurat;
use Illuminate\Http\Request;
use App\Models\TrackingSurat;
use App\Models\SuratAktifKuliah;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SuratAktifKuliahRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SuratAktifKuliahController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $surats = SuratAktifKuliah::with(['status', 'trackings'])
            ->where('mahasiswa_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('user.surat-aktif-kuliah.index', compact('surats'));
    }

    public function create()
    {
        $service = Service::where('slug', 'surat-aktif-kuliah')->firstOrFail();
        return view('user.surat-aktif-kuliah.create', compact('service'));
    }

    public function store(SuratAktifKuliahRequest $request)
    {
        $validated = $request->validated();

        // Upload file pendukung jika ada
        $filePath = null;
        if ($request->hasFile('file_pendukung')) {
            $filePath = $request->file('file_pendukung')->store('surat-aktif-kuliah/pendukung');
        }

        $surat = SuratAktifKuliah::create([
            'mahasiswa_id' => Auth::id(),
            'tujuan_pengajuan' => $validated['tujuan_pengajuan'],
            'keterangan_tambahan' => $validated['keterangan_tambahan'],
            'file_pendukung_path' => $filePath,
            'tahun_ajaran' => $validated['tahun_ajaran'],
            'semester' => $validated['semester'],
        ]);

        // Buat status awal
        StatusSurat::create([
            'surat_type' => SuratAktifKuliah::class,
            'surat_id' => $surat->id,
            'status' => 'diajukan',
            'updated_by' => Auth::id(),
        ]);

        // Buat tracking
        TrackingSurat::create([
            'surat_type' => SuratAktifKuliah::class,
            'surat_id' => $surat->id,
            'aksi' => 'diajukan',
            'keterangan' => 'Pengajuan surat aktif kuliah baru',
            'mahasiswa_id' => Auth::id(),
        ]);

        return redirect()->route('user.surat-aktif-kuliah.show', $surat->id)
            ->with('success', 'Surat aktif kuliah berhasil diajukan');
    }

    public function show(SuratAktifKuliah $surat)
    {
        $this->authorize('view', $surat);

        $surat->load([
            'status',
            'trackings' => function ($query) {
                $query->latest();
            }
        ]);

        return view('user.surat-aktif-kuliah.show', compact('surat'));
    }

    public function download(SuratAktifKuliah $surat)
    {
        $this->authorize('view', $surat);

        if (!$surat->file_surat_path) {
            abort(404);
        }

        return response()->download(storage_path('app/' . $surat->file_surat_path));
    }
}
