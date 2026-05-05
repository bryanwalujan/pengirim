<?php
// app/Http/Controllers/Sync/SkPembimbingController.php

namespace App\Http\Controllers\Sync;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSkPembimbing;
use App\Services\RepodosenSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SkPembimbingController extends Controller
{
    protected RepodosenSyncService $repodosenSync;

    public function __construct(RepodosenSyncService $repodosenSync)
    {
        $this->repodosenSync = $repodosenSync;
    }

    /**
     * Menampilkan daftar SK Pembimbing yang siap sync ke Repodosen
     */
    public function index(Request $request)
    {
        $query = PengajuanSkPembimbing::with([
            'mahasiswa',
            'dosenPembimbing1',
            'dosenPembimbing2'
        ])
        ->whereNotNull('file_surat_sk')
        ->whereNull('synced_at')
        ->where('status', PengajuanSkPembimbing::STATUS_SELESAI)
        ->latest('updated_at');

        // Filter pencarian
        if ($search = $request->query('search')) {
            $query->whereHas('mahasiswa', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $skPembimbingList = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => PengajuanSkPembimbing::whereNotNull('file_surat_sk')
                ->whereNull('synced_at')
                ->where('status', PengajuanSkPembimbing::STATUS_SELESAI)
                ->count(),
            'sudah_sync' => PengajuanSkPembimbing::whereNotNull('file_surat_sk')
                ->whereNotNull('synced_at')
                ->count(),
        ];

        return view('admin.sync.sk-pembimbing.index', compact('skPembimbingList', 'stats'));
    }

    /**
     * Detail SK Pembimbing sebelum sync
     */
    public function show(PengajuanSkPembimbing $skPembimbing)
    {
        $skPembimbing->load([
            'mahasiswa',
            'dosenPembimbing1',
            'dosenPembimbing2'
        ]);

        $fileExists = $skPembimbing->file_surat_sk && Storage::disk('local')->exists($skPembimbing->file_surat_sk);

        return view('admin.sync.sk-pembimbing.show', compact('skPembimbing', 'fileExists'));
    }

    /**
     * Preview SK Pembimbing
     */
    public function preview(PengajuanSkPembimbing $skPembimbing)
    {
        if (!$skPembimbing->file_surat_sk || !Storage::disk('local')->exists($skPembimbing->file_surat_sk)) {
            abort(404, 'File SK Pembimbing tidak ditemukan');
        }

        $filePath = Storage::disk('local')->path($skPembimbing->file_surat_sk);
        
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline'
        ]);
    }

    /**
     * Download SK Pembimbing
     */
    public function download(PengajuanSkPembimbing $skPembimbing)
    {
        if (!$skPembimbing->file_surat_sk || !Storage::disk('local')->exists($skPembimbing->file_surat_sk)) {
            abort(404, 'File SK Pembimbing tidak ditemukan');
        }

        $fileName = 'SK_Pembimbing_' . $skPembimbing->mahasiswa->nim . '.pdf';
        $filePath = Storage::disk('local')->path($skPembimbing->file_surat_sk);

        return response()->download($filePath, $fileName);
    }

    /**
     * Sync SK Pembimbing ke Repodosen (per item)
     */
    public function syncToRepodosen(PengajuanSkPembimbing $skPembimbing)
    {
        Log::info('[Sync] Starting sync for SK Pembimbing', [
            'pengajuan_id' => $skPembimbing->id,
            'file_surat_sk' => $skPembimbing->file_surat_sk,
            'status' => $skPembimbing->status,
            'synced_at' => $skPembimbing->synced_at,
        ]);

        if (!$skPembimbing->file_surat_sk || !Storage::disk('local')->exists($skPembimbing->file_surat_sk)) {
            return back()->with('error', 'File SK Pembimbing tidak ditemukan.');
        }

        if (!is_null($skPembimbing->synced_at)) {
            return back()->with('error', 'SK Pembimbing sudah pernah disync.');
        }

        if ($skPembimbing->status !== PengajuanSkPembimbing::STATUS_SELESAI) {
            return back()->with('error', 'SK Pembimbing belum selesai diproses.');
        }

        try {
            $result = $this->repodosenSync->syncSkPembimbing($skPembimbing);

            Log::info('[Sync] Result from syncSkPembimbing', [
                'success' => $result['success'],
                'message' => $result['message'] ?? null,
                'pengajuan_id' => $skPembimbing->id
            ]);

            if ($result['success']) {
                $skPembimbing->updateQuietly([
                    'synced_at' => now(),
                ]);

                Log::info('SK Pembimbing berhasil disync ke Repodosen', [
                    'pengajuan_id' => $skPembimbing->id,
                    'mahasiswa' => $skPembimbing->mahasiswa->name,
                    'sync_by' => auth()->user()->name,
                ]);

                return redirect()
                    ->route('admin.sync.sk-pembimbing.index')
                    ->with('success', '✅ SK Pembimbing berhasil disinkronkan ke Repodosen.');
            }

            return back()->with('error', '❌ Sync gagal: ' . ($result['message'] ?? 'Unknown error'));

        } catch (\Exception $e) {
            Log::error('Error sync SK Pembimbing ke Repodosen', [
                'error' => $e->getMessage(),
                'pengajuan_id' => $skPembimbing->id,
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat sync: ' . $e->getMessage());
        }
    }

    /**
     * Sync semua SK Pembimbing yang belum disync
     */
    public function syncAll(Request $request)
    {
        $skPembimbingList = PengajuanSkPembimbing::with('mahasiswa')
            ->whereNotNull('file_surat_sk')
            ->whereNull('synced_at')
            ->where('status', PengajuanSkPembimbing::STATUS_SELESAI)
            ->get();

        Log::info('[Sync] Sync all triggered', ['total' => $skPembimbingList->count()]);

        if ($skPembimbingList->isEmpty()) {
            return back()->with('warning', 'Tidak ada SK Pembimbing yang perlu disync.');
        }

        $berhasil = 0;
        $gagal = 0;
        $errors = [];

        foreach ($skPembimbingList as $skPembimbing) {
            try {
                $result = $this->repodosenSync->syncSkPembimbing($skPembimbing);

                if ($result['success']) {
                    $skPembimbing->updateQuietly(['synced_at' => now()]);
                    $berhasil++;
                } else {
                    $gagal++;
                    $errors[] = ($skPembimbing->mahasiswa->name ?? 'Unknown') . ': ' . ($result['message'] ?? 'Unknown error');
                }
            } catch (\Exception $e) {
                $gagal++;
                $errors[] = ($skPembimbing->mahasiswa->name ?? 'Unknown') . ': ' . $e->getMessage();
            }
        }

        $message = "Sync selesai. Berhasil: {$berhasil}, Gagal: {$gagal}";
        
        if ($gagal > 0 && !empty($errors)) {
            return back()->with('warning', $message . '<br>Detail: ' . implode('<br>', array_slice($errors, 0, 5)));
        }

        return back()->with('success', $message);
    }
}