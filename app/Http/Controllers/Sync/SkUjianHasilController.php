<?php
// app/Http/Controllers/Sync/SkUjianHasilController.php

namespace App\Http\Controllers\Sync;

use App\Http\Controllers\Controller;
use App\Models\JadwalUjianHasil;
use App\Services\RepodosenSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SkUjianHasilController extends Controller
{
    protected RepodosenSyncService $repodosenSync;

    public function __construct(RepodosenSyncService $repodosenSync)
    {
        $this->repodosenSync = $repodosenSync;
    }

    /**
     * Menampilkan daftar SK Ujian Hasil yang siap sync ke Repodosen
     */
    public function index(Request $request)
    {
        $query = JadwalUjianHasil::with([
            'pendaftaranUjianHasil.user',
            'pendaftaranUjianHasil.dosenPembimbing1',
            'pendaftaranUjianHasil.dosenPembimbing2'
        ])
        ->whereNotNull('file_sk_ujian_hasil')
        ->whereNull('synced_at')
        ->latest('updated_at');

        // Filter pencarian
        if ($search = $request->query('search')) {
            $query->whereHas('pendaftaranUjianHasil.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $skUjianHasilList = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => JadwalUjianHasil::whereNotNull('file_sk_ujian_hasil')
                ->whereNull('synced_at')
                ->count(),
            'sudah_sync' => JadwalUjianHasil::whereNotNull('file_sk_ujian_hasil')
                ->whereNotNull('synced_at')
                ->count(),
        ];

        return view('admin.sync.sk-ujian-hasil.index', compact('skUjianHasilList', 'stats'));
    }

    /**
     * Detail SK Ujian Hasil sebelum sync
     */
    public function show(JadwalUjianHasil $skUjianHasil)
    {
        $skUjianHasil->load([
            'pendaftaranUjianHasil.user',
            'pendaftaranUjianHasil.dosenPembimbing1',
            'pendaftaranUjianHasil.dosenPembimbing2',
            'pendaftaranUjianHasil.pengujiUjianHasil.dosen'
        ]);

        $fileExists = $skUjianHasil->hasSkFile();

        return view('admin.sync.sk-ujian-hasil.show', compact('skUjianHasil', 'fileExists'));
    }

    /**
     * Preview SK Ujian Hasil
     */
    public function preview(JadwalUjianHasil $skUjianHasil)
    {
        if (!$skUjianHasil->hasSkFile()) {
            abort(404, 'File SK Ujian Hasil tidak ditemukan');
        }

        $filePath = storage_path('app/public/' . $skUjianHasil->file_sk_ujian_hasil);
        
        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan di storage');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline'
        ]);
    }

    /**
     * Download SK Ujian Hasil
     */
    public function download(JadwalUjianHasil $skUjianHasil)
    {
        if (!$skUjianHasil->hasSkFile()) {
            abort(404, 'File SK Ujian Hasil tidak ditemukan');
        }

        $fileName = 'SK_UjianHasil_' . $skUjianHasil->pendaftaranUjianHasil->user->nim . '.pdf';
        $filePath = storage_path('app/public/' . $skUjianHasil->file_sk_ujian_hasil);

        return response()->download($filePath, $fileName);
    }

    /**
     * Sync SK Ujian Hasil ke Repodosen (per item)
     */
    public function syncToRepodosen(JadwalUjianHasil $skUjianHasil)
    {
        Log::info('[Sync] Starting sync for SK Ujian Hasil', [
            'jadwal_id' => $skUjianHasil->id,
            'file_sk_ujian_hasil' => $skUjianHasil->file_sk_ujian_hasil,
            'status' => $skUjianHasil->status,
            'synced_at' => $skUjianHasil->synced_at,
            'has_sk_file' => $skUjianHasil->hasSkFile()
        ]);

        if (!$skUjianHasil->hasSkFile()) {
            return back()->with('error', 'File SK Ujian Hasil tidak ditemukan.');
        }

        if (!is_null($skUjianHasil->synced_at)) {
            return back()->with('error', 'SK Ujian Hasil sudah pernah disync.');
        }

        try {
            $result = $this->repodosenSync->syncSkUjianHasil($skUjianHasil);

            Log::info('[Sync] Result from syncSkUjianHasil', [
                'success' => $result['success'],
                'message' => $result['message'] ?? null,
                'sk_ujian_hasil_id' => $skUjianHasil->id
            ]);

            if ($result['success']) {
                $skUjianHasil->updateQuietly([
                    'synced_at' => now(),
                ]);

                Log::info('SK Ujian Hasil berhasil disync ke Repodosen', [
                    'sk_ujian_hasil_id' => $skUjianHasil->id,
                    'mahasiswa' => $skUjianHasil->pendaftaranUjianHasil->user->name,
                    'nomor_sk' => $skUjianHasil->nomor_sk,
                    'sync_by' => auth()->user()->name,
                ]);

                return redirect()
                    ->route('admin.sync.sk-ujian-hasil.index')
                    ->with('success', '✅ SK Ujian Hasil berhasil disinkronkan ke Repodosen.');
            }

            return back()->with('error', '❌ Sync gagal: ' . ($result['message'] ?? 'Unknown error'));

        } catch (\Exception $e) {
            Log::error('Error sync SK Ujian Hasil ke Repodosen', [
                'error' => $e->getMessage(),
                'sk_ujian_hasil_id' => $skUjianHasil->id,
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat sync: ' . $e->getMessage());
        }
    }

    /**
     * Sync semua SK Ujian Hasil yang belum disync
     */
    public function syncAll(Request $request)
    {
        $skUjianHasilList = JadwalUjianHasil::with('pendaftaranUjianHasil')
            ->whereNotNull('file_sk_ujian_hasil')
            ->whereNull('synced_at')
            ->get();

        Log::info('[Sync] Sync all triggered', ['total' => $skUjianHasilList->count()]);

        if ($skUjianHasilList->isEmpty()) {
            return back()->with('warning', 'Tidak ada SK Ujian Hasil yang perlu disync.');
        }

        $berhasil = 0;
        $gagal = 0;
        $errors = [];

        foreach ($skUjianHasilList as $skUjianHasil) {
            try {
                Log::info('[Sync] Processing SK Ujian Hasil', [
                    'jadwal_id' => $skUjianHasil->id,
                    'file_path' => $skUjianHasil->file_sk_ujian_hasil
                ]);

                $result = $this->repodosenSync->syncSkUjianHasil($skUjianHasil);

                if ($result['success']) {
                    $skUjianHasil->updateQuietly(['synced_at' => now()]);
                    $berhasil++;
                    Log::info('[Sync] Success', ['jadwal_id' => $skUjianHasil->id]);
                } else {
                    $gagal++;
                    $errors[] = ($skUjianHasil->pendaftaranUjianHasil->user->name ?? 'Unknown') . ': ' . ($result['message'] ?? 'Unknown error');
                    Log::error('[Sync] Failed', ['jadwal_id' => $skUjianHasil->id, 'error' => $result['message']]);
                }
            } catch (\Exception $e) {
                $gagal++;
                $errors[] = ($skUjianHasil->pendaftaranUjianHasil->user->name ?? 'Unknown') . ': ' . $e->getMessage();
                Log::error('[Sync] Exception', ['jadwal_id' => $skUjianHasil->id, 'error' => $e->getMessage()]);
            }
        }

        $message = "Sync selesai. Berhasil: {$berhasil}, Gagal: {$gagal}";
        
        if ($gagal > 0 && !empty($errors)) {
            return back()->with('warning', $message . '<br>Detail: ' . implode('<br>', array_slice($errors, 0, 5)));
        }

        return back()->with('success', $message);
    }
}