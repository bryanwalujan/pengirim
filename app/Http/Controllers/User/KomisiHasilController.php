<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\KomisiHasil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class KomisiHasilController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $komisiHasils = KomisiHasil::where('user_id', $userId)
            ->with(['pembimbing1', 'pembimbing2', 'penandatanganKorprodi'])
            ->latest()
            ->get();

        // Check status pengajuan
        $canCreateStatus = KomisiHasil::canCreateNewHasil($userId);
        $latestHasil = KomisiHasil::getLatestHasil($userId);

        return view('user.komisi-hasil.index', compact(
            'komisiHasils',
            'canCreateStatus',
            'latestHasil'
        ));
    }

    public function create()
    {
        $userId = Auth::id();

        // Validasi apakah bisa membuat komisi hasil baru
        $canCreateStatus = KomisiHasil::canCreateNewHasil($userId);

        if (!$canCreateStatus['can_create']) {
            Log::warning('User mencoba membuat komisi hasil padahal tidak bisa', [
                'user_id' => $userId,
                'reason' => $canCreateStatus['reason'],
                'existing_hasil_id' => $canCreateStatus['hasil']->id ?? null,
                'existing_status' => $canCreateStatus['hasil']->status ?? null,
            ]);

            return redirect()
                ->route('user.komisi-hasil.index')
                ->with('error', $canCreateStatus['reason']);
        }

        // Ambil dosen yang bisa jadi pembimbing
        $dosens = User::role('dosen')
            ->orderByRaw("
                CASE 
                    WHEN LOWER(jabatan) LIKE '%koordinator program studi%' THEN 1
                    WHEN LOWER(jabatan) LIKE '%pimpinan jurusan%' THEN 2
                    ELSE 3
                END
            ")
            ->orderBy('name')
            ->get();

        // Ambil komisi hasil terakhir jika ada (untuk informasi)
        $latestHasil = $canCreateStatus['hasil'];

        return view('user.komisi-hasil.create', compact('dosens', 'latestHasil'));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $lockKey = "komisi_hasil_creation_{$userId}";

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
            // STEP 2: Validasi apakah user boleh membuat komisi hasil (dalam lock)
            $canCreateStatus = KomisiHasil::canCreateNewHasil($userId);

            if (!$canCreateStatus['can_create']) {
                Log::warning('Blocked komisi hasil creation attempt', [
                    'user_id' => $userId,
                    'reason' => $canCreateStatus['reason'],
                    'existing_hasil_id' => $canCreateStatus['hasil']->id ?? null,
                    'existing_status' => $canCreateStatus['hasil']->status ?? null,
                    'ip' => $request->ip(),
                ]);

                return redirect()
                    ->route('user.komisi-hasil.index')
                    ->with('error', $canCreateStatus['reason']);
            }

            // STEP 3: Validasi input
            $validated = $request->validate([
                'judul_skripsi' => 'required|string|max:500',
                'dosen_pembimbing1_id' => 'required|exists:users,id',
                'dosen_pembimbing2_id' => 'required|exists:users,id|different:dosen_pembimbing1_id',
            ], [
                'judul_skripsi.required' => 'Judul skripsi harus diisi.',
                'judul_skripsi.max' => 'Judul skripsi maksimal 500 karakter.',
                'dosen_pembimbing1_id.required' => 'Pembimbing 1 harus dipilih.',
                'dosen_pembimbing1_id.exists' => 'Pembimbing 1 yang dipilih tidak valid.',
                'dosen_pembimbing2_id.required' => 'Pembimbing 2 harus dipilih.',
                'dosen_pembimbing2_id.exists' => 'Pembimbing 2 yang dipilih tidak valid.',
                'dosen_pembimbing2_id.different' => 'Pembimbing 2 harus berbeda dengan Pembimbing 1.',
            ]);

            // STEP 4: Validasi dosen yang dipilih
            $pembimbing1 = User::lockForUpdate()->find($request->dosen_pembimbing1_id);
            $pembimbing2 = User::lockForUpdate()->find($request->dosen_pembimbing2_id);

            if (!$pembimbing1 || !$pembimbing1->hasRole('dosen')) {
                Log::error('Invalid pembimbing 1 selected', [
                    'dosen_id' => $request->dosen_pembimbing1_id,
                    'user_id' => $userId,
                    'ip' => $request->ip(),
                ]);

                return back()
                    ->with('error', 'Pembimbing 1 yang dipilih tidak valid.')
                    ->withInput();
            }

            if (!$pembimbing2 || !$pembimbing2->hasRole('dosen')) {
                Log::error('Invalid pembimbing 2 selected', [
                    'dosen_id' => $request->dosen_pembimbing2_id,
                    'user_id' => $userId,
                    'ip' => $request->ip(),
                ]);

                return back()
                    ->with('error', 'Pembimbing 2 yang dipilih tidak valid.')
                    ->withInput();
            }

            // STEP 5: Start Database Transaction
            DB::beginTransaction();

            try {
                // DOUBLE CHECK: Cek lagi apakah ada komisi hasil yang sedang diproses
                $existingHasil = KomisiHasil::where('user_id', $userId)
                    ->whereIn('status', ['pending', 'approved_pembimbing1', 'approved_pembimbing2'])
                    ->lockForUpdate()
                    ->first();

                if ($existingHasil) {
                    DB::rollBack();

                    Log::warning('Duplicate komisi hasil detected in transaction', [
                        'user_id' => $userId,
                        'existing_hasil_id' => $existingHasil->id,
                        'existing_status' => $existingHasil->status,
                    ]);

                    return redirect()
                        ->route('user.komisi-hasil.index')
                        ->with('error', 'Anda masih memiliki pengajuan komisi hasil yang sedang diproses.');
                }

                // STEP 6: Create komisi hasil
                $hasil = KomisiHasil::create([
                    'user_id' => $userId,
                    'judul_skripsi' => $validated['judul_skripsi'],
                    'dosen_pembimbing1_id' => $validated['dosen_pembimbing1_id'],
                    'dosen_pembimbing2_id' => $validated['dosen_pembimbing2_id'],
                    'status' => 'pending',
                ]);

                // STEP 7: Commit transaction
                DB::commit();

                // STEP 8: Log success
                Log::info('New komisi hasil created successfully', [
                    'hasil_id' => $hasil->id,
                    'user_id' => $userId,
                    'user_name' => Auth::user()->name,
                    'user_nim' => Auth::user()->nim,
                    'pembimbing1_id' => $pembimbing1->id,
                    'pembimbing1_name' => $pembimbing1->name,
                    'pembimbing2_id' => $pembimbing2->id,
                    'pembimbing2_name' => $pembimbing2->name,
                    'judul_skripsi' => $hasil->judul_skripsi,
                    'timestamp' => now(),
                    'ip' => $request->ip(),
                ]);

                // STEP 9: Success response
                return redirect()
                    ->route('user.komisi-hasil.index')
                    ->with('success', 'Pengajuan Komisi Hasil berhasil dibuat. Menunggu persetujuan dari ' . $pembimbing1->name);

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

            Log::error('Error creating komisi hasil', [
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

    public function show(KomisiHasil $komisiHasil)
    {
        // Validasi bahwa komisi hasil milik user yang login
        if ($komisiHasil->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke komisi hasil ini.');
        }

        $komisiHasil->load([
            'pembimbing1',
            'pembimbing2',
            'penandatanganKorprodi'
        ]);

        return view('user.komisi-hasil.show', compact('komisiHasil'));
    }

    public function downloadPdf(KomisiHasil $komisiHasil)
    {
        // Validasi bahwa komisi hasil milik user yang login
        if ($komisiHasil->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        // Hanya bisa download jika sudah approved
        if ($komisiHasil->status !== 'approved') {
            return back()->with('error', 'Dokumen hanya dapat diunduh setelah disetujui lengkap.');
        }

        // Gunakan disk 'local'
        if (!$komisiHasil->file_komisi || !Storage::disk('local')->exists($komisiHasil->file_komisi)) {
            return back()->with('error', 'File dokumen tidak ditemukan.');
        }

        $fullPath = Storage::disk('local')->path($komisiHasil->file_komisi);
        $filename = 'Komisi_Hasil_' . Auth::user()->nim . '_' . now()->format('Ymd') . '.pdf';

        return response()->download($fullPath, $filename);
    }
}