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
use Illuminate\Support\Facades\Storage;

class SuratAktifKuliahController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $service = Service::where('slug', 'surat-aktif-kuliah')->firstOrFail();
        $surats = SuratAktifKuliah::with(['status', 'trackings', 'mahasiswa'])
            ->where('mahasiswa_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('user.surat-aktif-kuliah.index', [
            'service' => $service,
            'surats' => $surats,
        ]);
    }

    public function create()
    {
        $this->authorize('create', SuratAktifKuliah::class);
        $service = Service::where('slug', 'surat-aktif-kuliah')->firstOrFail();
        return view('user.surat-aktif-kuliah.create', compact('service'));
    }

    public function store(SuratAktifKuliahRequest $request)
    {
        $this->authorize('create', SuratAktifKuliah::class);
        $validated = $request->validated();

        $filePath = null;
        if ($request->hasFile('file_pendukung')) {
            $filePath = $request->file('file_pendukung')->store('surat-aktif-kuliah/pendukung', 'public');
        }

        $surat = SuratAktifKuliah::create([
            'mahasiswa_id' => Auth::id(),
            'tujuan_pengajuan' => $validated['tujuan_pengajuan'],
            'keterangan_tambahan' => $validated['keterangan_tambahan'],
            'file_pendukung_path' => $filePath,
            'tahun_ajaran' => $validated['tahun_ajaran'],
            'semester' => $validated['semester'],
        ]);

        StatusSurat::create([
            'surat_type' => SuratAktifKuliah::class,
            'surat_id' => $surat->id,
            'status' => 'diajukan',
            'updated_by' => Auth::id(),
        ]);

        TrackingSurat::create([
            'surat_type' => SuratAktifKuliah::class,
            'surat_id' => $surat->id,
            'aksi' => 'diajukan',
            'keterangan' => 'Pengajuan surat aktif kuliah baru',
            'mahasiswa_id' => Auth::id(),
        ]);

        return redirect()->route('user.surat-aktif-kuliah.index')
            ->with('success', 'Surat aktif kuliah berhasil diajukan');
    }

    public function show($id)
    {
        $surat = SuratAktifKuliah::with(['status', 'trackings', 'mahasiswa'])
            ->findOrFail($id);

        $this->authorize('view', $surat);

        return view('user.surat-aktif-kuliah.show', compact('surat'));
    }

    public function download(SuratAktifKuliah $surat)
    {
        $this->authorize('view', $surat);

        if (!$surat->file_surat_path || !Storage::disk('public')->exists($surat->file_surat_path)) {
            abort(404, 'File surat tidak tersedia');
        }

        $filePath = Storage::disk('public')->path($surat->file_surat_path);
        $fileName = basename($surat->file_surat_path);

        return response()->download($filePath, $fileName);
    }
}
