<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Service;
use App\Models\StatusSurat;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use App\Models\TrackingSurat;
use App\Models\SuratAktifKuliah;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\SuratTakenNotification;
use App\Http\Requests\SuratAktifKuliahRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SuratAktifKuliahController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $service = Service::where('slug', 'surat-aktif-kuliah')->firstOrFail();

        $surats = SuratAktifKuliah::with(['status', 'trackings', 'mahasiswa'])
            ->where('mahasiswa_id', Auth::id())
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('nomor_surat', 'like', '%' . $request->search . '%')
                        ->orWhere('tujuan_pengajuan', 'like', '%' . $request->search . '%')
                        ->orWhere('keterangan_tambahan', 'like', '%' . $request->search . '%')
                        ->orWhere('tahun_ajaran', 'like', '%' . $request->search . '%')
                        ->orWhere('semester', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->status, function ($query) use ($request) {
                $query->whereHas('status', function ($q) use ($request) {
                    $q->where('status', $request->status);
                });
            })
            ->when($request->tahun, function ($query) use ($request) {
                $query->where('tahun_ajaran', 'like', '%' . $request->tahun . '%');
            })
            ->when($request->semester, function ($query) use ($request) {
                $query->where('semester', $request->semester);
            })
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
        // Ambil tahun ajaran aktif
        $tahunAjaranAktif = TahunAjaran::where('status_aktif', true)->first();
        return view('user.surat-aktif-kuliah.create', compact('service', 'tahunAjaranAktif'));
    }

    public function store(SuratAktifKuliahRequest $request)
    {
        $this->authorize('create', SuratAktifKuliah::class);
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $surat = SuratAktifKuliah::create([
                'mahasiswa_id' => Auth::id(),
                'tujuan_pengajuan' => $validated['tujuan_pengajuan'],
                'keterangan_tambahan' => $validated['keterangan_tambahan'],
                'tahun_ajaran' => $validated['tahun_ajaran'],
                'semester' => $validated['semester'],
            ]);

            // Simpan multiple files
            if ($request->hasFile('file_pendukung_path')) {
                $surat->attachDokumenPendukung($request->file('file_pendukung_path'));
            }

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

            DB::commit();

            return redirect()->route('user.surat-aktif-kuliah.index')
                ->with('success', 'Surat aktif kuliah berhasil diajukan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengajukan surat: ' . $e->getMessage());
        }
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
            ->where('id', $id)
            ->firstOrFail();

        // Pastikan status saat ini adalah siap_diambil
        if ($surat->status !== 'siap_diambil') {
            return redirect()->back()
                ->with('error', 'Surat belum siap diambil atau sudah diambil sebelumnya');
        }

        DB::beginTransaction();
        try {
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
                'confirmed_at' => now(), // Tambahkan timestamp konfirmasi
            ]);

            // Send notification to all staff
            $staffs = User::role('staff')->get();
            foreach ($staffs as $staff) {
                $staff->notify(new SuratTakenNotification($surat));
            }

            DB::commit();

            return redirect()->route('user.surat-aktif-kuliah.show', $surat->id)
                ->with('success', 'Surat telah dikonfirmasi sebagai sudah diambil. Sekarang Anda bisa mengunduh surat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mengkonfirmasi surat: ' . $e->getMessage());
        }
    }

    public function download(SuratAktifKuliah $surat)
    {
        try {
            // Pastikan hanya pemilik surat yang bisa mengunduh
            if (Auth::id() !== $surat->mahasiswa_id) {
                return redirect()->back()->with('error', 'Anda tidak berhak mengunduh surat ini.');
            }

            // Pastikan status sudah dikonfirmasi (sudah_diambil)
            if ($surat->status !== 'sudah_diambil') {
                return redirect()->back()->with('error', 'Anda harus mengkonfirmasi penerimaan surat terlebih dahulu sebelum mengunduh.');
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

            // Get the confirmation date from tracking
            $tracking = TrackingSurat::where('surat_type', SuratAktifKuliah::class)
                ->where('surat_id', $surat->id)
                ->where('aksi', 'sudah_diambil')
                ->first();

            $downloadDate = $tracking && $tracking->confirmed_at
                ? $tracking->confirmed_at->format('Ymd')
                : now()->format('Ymd');

            $filename = 'Surat_Aktif_Kuliah_' . $surat->mahasiswa->nim . '_' . $downloadDate . '.pdf';

            return response()->download($filePath, $filename);
        } catch (\Exception $e) {
            Log::error('Error saat download PDF untuk surat ID: ' . $surat->id . ' - ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh surat.');
        }
    }
}
