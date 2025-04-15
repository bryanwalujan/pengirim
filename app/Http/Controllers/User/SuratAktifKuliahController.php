<?php

namespace App\Http\Controllers\User;

use App\Models\Service;
use App\Models\StatusSurat;
use Illuminate\Http\Request;
use App\Models\TrackingSurat;
use App\Models\SuratAktifKuliah;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\SuratAktifKuliahRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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

    public function confirmTaken($id)
{
    $surat = SuratAktifKuliah::with(['status'])
        ->where('mahasiswa_id', Auth::id())
        ->findOrFail($id);

    // Pastikan status saat ini adalah siap_diambil
    if ($surat->status !== 'siap_diambil') {
        return redirect()->back()
            ->with('error', 'Surat belum siap diambil atau sudah diambil sebelumnya');
    }

    // Update status
    StatusSurat::updateOrCreate(
        [
            'surat_type' => SuratAktifKuliah::class,
            'surat_id' => $surat->id,
        ],
        [
            'status' => 'sudah_diambil',
            'updated_by' => Auth::id(),
        ]
    );

    // Tambahkan tracking
    TrackingSurat::create([
        'surat_type' => SuratAktifKuliah::class,
        'surat_id' => $surat->id,
        'aksi' => 'sudah_diambil',
        'keterangan' => 'Surat telah diambil oleh mahasiswa',
        'mahasiswa_id' => Auth::id(),
    ]);

    return redirect()->route('user.surat-aktif-kuliah.show', $surat->id)
        ->with('success', 'Surat telah dikonfirmasi sebagai sudah diambil');
}

    public function download(SuratAktifKuliah $surat)
    {
        try {
            // Pastikan hanya pemilik surat yang bisa mengunduh
            if (Auth::id() !== $surat->mahasiswa_id) {
                return redirect()->back()->with('error', 'Anda tidak berhak mengunduh surat ini.');
            }

            // Pastikan status memungkinkan download
            if (!in_array($surat->status, ['siap_diambil', 'sudah_diambil'])) {
                return redirect()->back()->with('error', 'Surat belum tersedia untuk diunduh.');
            }

            // Pastikan file ada
            if (!$surat->file_surat_path) {
                return redirect()->back()->with('error', 'File surat belum dihasilkan.');
            }

            $filePath = storage_path('app/public/' . $surat->file_surat_path);
            if (!file_exists($filePath)) {
                Log::error('File PDF tidak ditemukan untuk surat ID: ' . $surat->id . ' di path: ' . $filePath);
                return redirect()->back()->with('error', 'File surat tidak ditemukan.');
            }

            return response()->download($filePath, 'surat-aktif-kuliah-' . $surat->id . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error saat download PDF untuk surat ID: ' . $surat->id . ' - ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh surat.');
        }
    }
}
