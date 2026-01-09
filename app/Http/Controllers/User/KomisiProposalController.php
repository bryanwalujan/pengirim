<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\KomisiProposal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class KomisiProposalController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $komisiProposals = KomisiProposal::where('user_id', $userId)
            ->with(['pembimbing', 'penandatanganPA', 'penandatanganKorprodi'])
            ->latest()
            ->get();

        // Check status pengajuan
        $canCreateStatus = KomisiProposal::canCreateNewProposal($userId);
        $latestProposal = KomisiProposal::getLatestProposal($userId);

        return view('user.komisi-proposal.index', compact(
            'komisiProposals',
            'canCreateStatus',
            'latestProposal'
        ));
    }

    public function create()
    {
        $userId = Auth::id();

        // Validasi apakah bisa membuat proposal baru
        $canCreateStatus = KomisiProposal::canCreateNewProposal($userId);

        if (!$canCreateStatus['can_create']) {
            Log::warning('User mencoba membuat proposal padahal tidak bisa', [
                'user_id' => $userId,
                'reason' => $canCreateStatus['reason'],
                'existing_proposal_id' => $canCreateStatus['proposal']->id ?? null,
                'existing_status' => $canCreateStatus['proposal']->status ?? null,
            ]);

            return redirect()
                ->route('user.komisi-proposal.index')
                ->with('error', $canCreateStatus['reason']);
        }

        // Ambil dosen yang bisa jadi PA
        $dosens = User::role('dosen')
            ->orderByRaw("
                CASE 
                    WHEN LOWER(jabatan) LIKE '%pembimbing akademik%' THEN 1
                    WHEN LOWER(jabatan) LIKE '%koordinator program studi%' THEN 2
                    WHEN LOWER(jabatan) LIKE '%pimpinan jurusan%' THEN 3
                    ELSE 4
                END
            ")
            ->orderBy('name')
            ->get();

        // Ambil proposal terakhir jika ada (untuk informasi)
        $latestProposal = $canCreateStatus['proposal'];

        return view('user.komisi-proposal.create', compact('dosens', 'latestProposal'));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $lockKey = "komisi_proposal_creation_{$userId}";

        // STEP 1: Prevent duplicate submission dengan Cache Lock
        $lock = Cache::lock($lockKey, 10); // Lock selama 10 detik

        if (!$lock->get()) {
            Log::warning('Duplicate submission attempt blocked by cache lock', [
                'user_id' => $userId,
                'ip' => $request->ip(),
                'timestamp' => now(),
            ]);

            return back()
                ->with('warning', 'Mohon tunggu, pengajuan Anda sedang diproses.')
                ->withInput();
        }

        try {
            // STEP 2: Validasi apakah user boleh membuat proposal (dalam lock)
            $canCreateStatus = KomisiProposal::canCreateNewProposal($userId);

            if (!$canCreateStatus['can_create']) {
                Log::warning('Blocked proposal creation attempt', [
                    'user_id' => $userId,
                    'reason' => $canCreateStatus['reason'],
                    'existing_proposal_id' => $canCreateStatus['proposal']->id ?? null,
                    'existing_status' => $canCreateStatus['proposal']->status ?? null,
                    'ip' => $request->ip(),
                ]);

                return redirect()
                    ->route('user.komisi-proposal.index')
                    ->with('error', $canCreateStatus['reason']);
            }

            // STEP 3: Validasi input
            $validated = $request->validate([
                'judul_skripsi' => 'required|string|max:500',
                'dosen_pembimbing_id' => 'required|exists:users,id',
            ], [
                'judul_skripsi.required' => 'Judul skripsi harus diisi.',
                'judul_skripsi.max' => 'Judul skripsi maksimal 500 karakter.',
                'dosen_pembimbing_id.required' => 'Pembimbing Akademik harus dipilih.',
                'dosen_pembimbing_id.exists' => 'Pembimbing Akademik yang dipilih tidak valid.',
            ]);

            // ✅ VALIDASI TAMBAHAN: Cegah judul ALL CAPS
            $judul = trim($validated['judul_skripsi']);
            if (strlen($judul) >= 10) {
                preg_match_all('/[A-Z]/', $judul, $uppercaseMatches);
                preg_match_all('/[a-z]/', $judul, $lowercaseMatches);
                
                $uppercaseCount = count($uppercaseMatches[0]);
                $lowercaseCount = count($lowercaseMatches[0]);
                $totalLetters = $uppercaseCount + $lowercaseCount;
                
                // Jika lebih dari 80% huruf adalah kapital, tolak
                if ($totalLetters > 0 && ($uppercaseCount / $totalLetters) > 0.8) {
                    return back()
                        ->withInput()
                        ->withErrors(['judul_skripsi' => 'Judul skripsi tidak boleh ditulis dengan huruf kapital semua (ALL CAPS). Gunakan huruf kapital hanya di awal kata yang sesuai.']);
                }
            }

            // STEP 4: Validasi dosen yang dipilih
            $dosen = User::lockForUpdate()->find($request->dosen_pembimbing_id);

            if (!$dosen || !$dosen->hasRole('dosen')) {
                Log::error('Invalid dosen selected', [
                    'dosen_id' => $request->dosen_pembimbing_id,
                    'user_id' => $userId,
                    'ip' => $request->ip(),
                ]);

                return back()
                    ->with('error', 'Dosen yang dipilih tidak valid.')
                    ->withInput();
            }

            // STEP 5: Start Database Transaction
            DB::beginTransaction();

            try {
                // DOUBLE CHECK: Cek lagi apakah ada proposal pending/approved_by_pa
                $existingProposal = KomisiProposal::where('user_id', $userId)
                    ->whereIn('status', ['pending', 'approved_by_pa'])
                    ->lockForUpdate()
                    ->first();

                if ($existingProposal) {
                    DB::rollBack();

                    Log::warning('Duplicate proposal detected in transaction', [
                        'user_id' => $userId,
                        'existing_proposal_id' => $existingProposal->id,
                        'existing_status' => $existingProposal->status,
                    ]);

                    return redirect()
                        ->route('user.komisi-proposal.index')
                        ->with('error', 'Anda masih memiliki pengajuan yang sedang diproses.');
                }

                // STEP 6: Create proposal
                $proposal = KomisiProposal::create([
                    'user_id' => $userId,
                    'judul_skripsi' => $validated['judul_skripsi'],
                    'dosen_pembimbing_id' => $validated['dosen_pembimbing_id'],
                    'status' => 'pending',
                ]);

                // STEP 7: Commit transaction
                DB::commit();

                // STEP 8: Log success
                Log::info('New komisi proposal created successfully', [
                    'proposal_id' => $proposal->id,
                    'user_id' => $userId,
                    'user_name' => Auth::user()->name,
                    'user_nim' => Auth::user()->nim,
                    'dosen_id' => $dosen->id,
                    'dosen_name' => $dosen->name,
                    'judul_skripsi' => $proposal->judul_skripsi,
                    'timestamp' => now(),
                    'ip' => $request->ip(),
                ]);

                // STEP 9: Success response
                return redirect()
                    ->route('user.komisi-proposal.index')
                    ->with('success', 'Pengajuan Komisi Proposal berhasil dibuat. Menunggu persetujuan dari ' . $dosen->name);

            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollBack();

                // Handle specific database errors
                if ($e->getCode() === '23000') { // Integrity constraint violation
                    Log::error('Database constraint violation', [
                        'user_id' => $userId,
                        'error_code' => $e->getCode(),
                        'error_message' => $e->getMessage(),
                    ]);

                    return back()
                        ->with('error', 'Terjadi kesalahan integritas data. Silakan coba lagi.')
                        ->withInput();
                }

                throw $e; // Re-throw untuk ditangani di catch luar
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors akan ditangani otomatis oleh Laravel
            throw $e;

        } catch (\Exception $e) {
            // Rollback jika masih dalam transaction
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            Log::error('Error creating komisi proposal', [
                'user_id' => $userId,
                'error_type' => get_class($e),
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token']),
                'ip' => $request->ip(),
                'timestamp' => now(),
            ]);

            return back()
                ->with('error', 'Terjadi kesalahan saat membuat pengajuan. Silakan coba lagi.')
                ->withInput();

        } finally {
            // STEP 10: Release lock
            optional($lock)->release();
        }
    }

    public function show(KomisiProposal $komisiProposal)
    {
        // Validasi bahwa proposal milik user yang login
        if ($komisiProposal->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke proposal ini.');
        }

        $komisiProposal->load([
            'pembimbing',
            'penandatanganPA',
            'penandatanganKorprodi'
        ]);

        return view('user.komisi-proposal.show', compact('komisiProposal'));
    }

    public function downloadPdf(KomisiProposal $komisiProposal)
    {
        // Validasi bahwa proposal milik user yang login
        if ($komisiProposal->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        // Hanya bisa download jika sudah approved
        if ($komisiProposal->status !== 'approved') {
            return back()->with('error', 'Dokumen hanya dapat diunduh setelah disetujui lengkap.');
        }

        // UBAH: Gunakan disk 'local'
        if (!$komisiProposal->file_komisi || !Storage::disk('local')->exists($komisiProposal->file_komisi)) {
            return back()->with('error', 'File dokumen tidak ditemukan.');
        }

        // UBAH: Gunakan disk 'local' untuk get path
        $fullPath = Storage::disk('local')->path($komisiProposal->file_komisi);
        $filename = 'Komisi_Proposal_' . Auth::user()->nim . '_' . now()->format('Ymd') . '.pdf';

        return response()->download($fullPath, $filename);
    }
}