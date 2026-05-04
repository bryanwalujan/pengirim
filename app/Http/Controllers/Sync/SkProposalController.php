<?php
// app/Http/Controllers/Sync/SkProposalController.php

namespace App\Http\Controllers\Sync;

use App\Http\Controllers\Controller;
use App\Models\JadwalSeminarProposal;
use App\Services\RepodosenSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SkProposalController extends Controller
{
    protected RepodosenSyncService $repodosenSync;

    public function __construct(RepodosenSyncService $repodosenSync)
    {
        $this->repodosenSync = $repodosenSync;
    }

    /**
     * Menampilkan daftar SK Proposal yang siap sync ke Repodosen
     */
    public function index(Request $request)
    {
        $query = JadwalSeminarProposal::with([
            'pendaftaranSeminarProposal.user',
            'pendaftaranSeminarProposal.dosenPembimbing'
        ])
        ->whereNotNull('file_sk_proposal')
        ->whereNull('synced_at')
        ->latest('updated_at');

        // Filter pencarian
        if ($search = $request->query('search')) {
            $query->whereHas('pendaftaranSeminarProposal.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $skProposals = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => JadwalSeminarProposal::whereNotNull('file_sk_proposal')
                ->where('status', 'menunggu_jadwal')
                ->count(),
            'sudah_sync' => JadwalSeminarProposal::whereNotNull('file_sk_proposal')
                ->whereNotNull('synced_at') 
                ->count(),
        ];

        return view('admin.sync.sk-proposal.index', compact('skProposals', 'stats'));
    }

    /**
     * Detail SK Proposal sebelum sync
     */
    public function show(JadwalSeminarProposal $skProposal)
    {
        $skProposal->load([
            'pendaftaranSeminarProposal.user',
            'pendaftaranSeminarProposal.dosenPembimbing',
            'pendaftaranSeminarProposal.proposalPembahas.dosen'
        ]);

        $fileExists = $skProposal->hasSkFile();

        return view('admin.sync.sk-proposal.show', compact('skProposal', 'fileExists'));
    }

    /**
     * Preview SK Proposal
     */
    public function preview(JadwalSeminarProposal $skProposal)
    {
        if (!$skProposal->hasSkFile()) {
            abort(404, 'File SK Proposal tidak ditemukan');
        }

        $filePath = storage_path('app/public/' . $skProposal->file_sk_proposal);
        
        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan di storage');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline'
        ]);
    }

    /**
     * Download SK Proposal
     */
    public function download(JadwalSeminarProposal $skProposal)
    {
        if (!$skProposal->hasSkFile()) {
            abort(404, 'File SK Proposal tidak ditemukan');
        }

        $fileName = 'SK_Proposal_' . $skProposal->pendaftaranSeminarProposal->user->nim . '.pdf';
        $filePath = storage_path('app/public/' . $skProposal->file_sk_proposal);

        return response()->download($filePath, $fileName);
    }

    /**
     * Sync SK Proposal ke Repodosen (per item)
     */
    public function syncToRepodosen(JadwalSeminarProposal $skProposal)
    {
        // ✅ Debug logging
        Log::info('[Sync] Starting sync for SK Proposal', [
            'jadwal_id' => $skProposal->id,
            'file_sk_proposal' => $skProposal->file_sk_proposal,
            'status' => $skProposal->status,
            'has_sk_file' => $skProposal->hasSkFile()
        ]);

        if (!$skProposal->hasSkFile()) {
            return back()->with('error', 'File SK Proposal tidak ditemukan.');
        }

        if ($skProposal->status !== 'menunggu_jadwal') {
            return back()->with('error', 'SK Proposal sudah pernah disync atau status tidak valid.');
        }

        try {
            $result = $this->repodosenSync->syncSkProposal($skProposal);

            Log::info('[Sync] Result from syncSkProposal', [
                'success' => $result['success'],
                'message' => $result['message'] ?? null,
                'sk_proposal_id' => $skProposal->id
            ]);

            if ($result['success']) {
                // Update status setelah sync berhasil
                $skProposal->updateQuietly([
                     'synced_at' => now(),
                ]);

                Log::info('SK Proposal berhasil disync ke Repodosen', [
                    'sk_proposal_id' => $skProposal->id,
                    'mahasiswa' => $skProposal->pendaftaranSeminarProposal->user->name,
                    'nomor_sk' => $skProposal->nomor_sk_proposal,
                    'sync_by' => auth()->user()->name,
                ]);

                return redirect()
                    ->route('admin.sync.sk-proposal.index')
                    ->with('success', '✅ SK Proposal berhasil disinkronkan ke Repodosen.');
            }

            return back()->with('error', '❌ Sync gagal: ' . ($result['message'] ?? 'Unknown error'));

        } catch (\Exception $e) {
            Log::error('Error sync SK Proposal ke Repodosen', [
                'error' => $e->getMessage(),
                'sk_proposal_id' => $skProposal->id,
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat sync: ' . $e->getMessage());
        }
    }

    /**
     * Sync semua SK Proposal yang belum disync
     */
    public function syncAll(Request $request)
    {
        $skProposals = JadwalSeminarProposal::with('pendaftaranSeminarProposal')
            ->whereNotNull('file_sk_proposal')
            ->where('status', 'menunggu_jadwal')
            ->get();

        Log::info('[Sync] Sync all triggered', ['total' => $skProposals->count()]);

        if ($skProposals->isEmpty()) {
            return back()->with('warning', 'Tidak ada SK Proposal yang perlu disync.');
        }

        $berhasil = 0;
        $gagal = 0;
        $errors = [];

        foreach ($skProposals as $skProposal) {
            try {
                Log::info('[Sync] Processing SK Proposal', [
                    'jadwal_id' => $skProposal->id,
                    'file_path' => $skProposal->file_sk_proposal
                ]);

                $result = $this->repodosenSync->syncSkProposal($skProposal);

                if ($result['success']) {
                    $skProposal->updateQuietly(['synced_at' => now()]);
                    $berhasil++;
                    Log::info('[Sync] Success', ['jadwal_id' => $skProposal->id]);
                } else {
                    $gagal++;
                    $errors[] = ($skProposal->pendaftaranSeminarProposal->user->name ?? 'Unknown') . ': ' . ($result['message'] ?? 'Unknown error');
                    Log::error('[Sync] Failed', ['jadwal_id' => $skProposal->id, 'error' => $result['message']]);
                }
            } catch (\Exception $e) {
                $gagal++;
                $errors[] = ($skProposal->pendaftaranSeminarProposal->user->name ?? 'Unknown') . ': ' . $e->getMessage();
                Log::error('[Sync] Exception', ['jadwal_id' => $skProposal->id, 'error' => $e->getMessage()]);
            }
        }

        $message = "Sync selesai. Berhasil: {$berhasil}, Gagal: {$gagal}";
        
        if ($gagal > 0 && !empty($errors)) {
            return back()->with('warning', $message . '<br>Detail: ' . implode('<br>', array_slice($errors, 0, 5)));
        }

        return back()->with('success', $message);
    }
}