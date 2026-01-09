<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KomisiHasil;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdminKomisiHasilController extends Controller
{
    /**
     * Preview PDF dengan data dummy untuk testing template
     */
    public function previewPdf()
    {
        // Data dummy untuk preview
        $komisi = (object) [
            'id' => 999,
            'judul_skripsi' => '<strong>Perbandingan Algoritma Multiple Linear Regression dengan Algoritma Backpropagation dalam Estimasi Produksi Cabai Rawit di Provinsi Sulawesi Utara</strong>',
            'user' => (object) [
                'name' => 'Patrick Rompas',
                'nim' => '20210047',
                'email' => 'patrick.rompas@example.com'
            ],
            'pembimbing1' => (object) [
                'name' => 'SUNDYANTO KUMAJAS, S.T, M.T',
                'nip' => '19870753122010121006',
                'email' => 'sundyanto@unima.ac.id'
            ],
            'pembimbing2' => (object) [
                'name' => 'Dr. JACK OBILIX ELIA PALANDENG, M.Pd., M.Kom',
                'nip' => '198705312015041003',
                'email' => 'jack@unima.ac.id'
            ],
            'penandatanganPembimbing1' => (object) [
                'name' => 'SUNDYANTO KUMAJAS, S.T, M.T',
                'nip' => '19870753122010121006',
            ],
            'penandatanganPembimbing2' => (object) [
                'name' => 'Dr. JACK OBILIX ELIA PALANDENG, M.Pd., M.Kom',
                'nip' => '198705312015041003',
            ],
            'penandatanganKorprodi' => (object) [
                'name' => 'Kristofel Santa, S.ST, M.MT',
                'nip' => '198705312015041003',
                'email' => 'kristofel@unima.ac.id'
            ],
            'tanggal_persetujuan_pembimbing1' => now(),
            'tanggal_persetujuan_pembimbing2' => now(),
            'tanggal_persetujuan_korprodi' => now(),
            'verification_code' => 'KH-' . strtoupper(uniqid()),
            'status' => 'approved'
        ];

        // Generate verification URL
        $verificationUrl = route('document.verify', ['code' => $komisi->verification_code]);

        // Generate QR Codes
        $pembimbing1_qr = 'data:image/png;base64,' . base64_encode(
            QrCode::format('png')
                ->size(200)
                ->margin(1)
                ->errorCorrection('H')
                ->generate($verificationUrl)
        );

        $pembimbing2_qr = 'data:image/png;base64,' . base64_encode(
            QrCode::format('png')
                ->size(200)
                ->margin(1)
                ->errorCorrection('H')
                ->generate($verificationUrl)
        );

        $korprodi_qr = 'data:image/png;base64,' . base64_encode(
            QrCode::format('png')
                ->size(200)
                ->margin(1)
                ->errorCorrection('H')
                ->generate($verificationUrl)
        );

        $verification_code = $komisi->verification_code;
        $show_korprodi_signature = true;

        $pdf = Pdf::loadView('admin.komisi-hasil.pdf', compact(
            'komisi',
            'pembimbing1_qr',
            'pembimbing2_qr',
            'korprodi_qr',
            'verification_code',
            'show_korprodi_signature'
        ))
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', '0.7in')
            ->setOption('margin-bottom', '0.7in')
            ->setOption('margin-left', '0.7in')
            ->setOption('margin-right', '0.7in');

        return $pdf->stream('preview-komisi-hasil.pdf');
    }

    /**
     * Cek apakah user adalah Pembimbing untuk komisi hasil ini
     */
    private function isPembimbingForKomisi(User $user, KomisiHasil $komisi): bool
    {
        if (!$user->hasRole('dosen')) {
            return false;
        }

        return $komisi->dosen_pembimbing1_id == $user->id || $komisi->dosen_pembimbing2_id == $user->id;
    }

    /**
     * Cek apakah user adalah Koordinator Program Studi
     */
    private function isKoordinatorProdi(User $user): bool
    {
        if (!$user->hasRole('dosen')) {
            return false;
        }

        $jabatan = strtolower($user->jabatan ?? '');

        $korprodiKeywords = [
            'koordinator program studi',
            'korprodi',
            'kaprodi',
            'ketua program studi',
        ];

        foreach ($korprodiKeywords as $keyword) {
            if (str_contains($jabatan, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cek apakah user adalah Staff yang bisa override approval
     */
    private function canOverrideApproval(User $user): bool
    {
        return $user->hasRole(['staff']);
    }

    /**
     * Display a listing of komisi hasil
     */
    public function index(Request $request)
    {
        $user = User::find(Auth::id());

        $query = KomisiHasil::with([
            'user',
            'pembimbing1',
            'pembimbing2',
            'penandatanganPembimbing1',
            'penandatanganPembimbing2',
            'penandatanganKorprodi'
        ])->latest();

        // Filter berdasarkan role
        if ($user->hasRole('dosen')) {
            $isKorprodi = $this->isKoordinatorProdi($user);
            $isPembimbingForAny = KomisiHasil::where(function ($q) use ($user) {
                $q->where('dosen_pembimbing1_id', $user->id)
                    ->orWhere('dosen_pembimbing2_id', $user->id);
            })->exists();

            if ($isKorprodi) {
                // Korprodi bisa melihat semua
            } elseif ($isPembimbingForAny) {
                // Pembimbing hanya bisa melihat komisi mahasiswa bimbingannya
                $query->where(function ($q) use ($user) {
                    $q->where('dosen_pembimbing1_id', $user->id)
                        ->orWhere('dosen_pembimbing2_id', $user->id);
                });
            } else {
                // Dosen biasa tidak bisa melihat apa-apa
                $query->whereRaw('1 = 0');
            }
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nim', 'like', '%' . $search . '%');
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $komisiHasils = $query->paginate(15);

        // Statistics
        if ($user->hasRole('dosen')) {
            $isKorprodi = $this->isKoordinatorProdi($user);
            $isPembimbingForAny = KomisiHasil::where(function ($q) use ($user) {
                $q->where('dosen_pembimbing1_id', $user->id)
                    ->orWhere('dosen_pembimbing2_id', $user->id);
            })->exists();

            if ($isKorprodi) {
                $statistics = [
                    'total' => KomisiHasil::count(),
                    'pending' => KomisiHasil::where('status', 'pending')->count(),
                    'approved_pembimbing1' => KomisiHasil::where('status', 'approved_pembimbing1')->count(),
                    'approved_pembimbing2' => KomisiHasil::where('status', 'approved_pembimbing2')->count(),
                    'approved' => KomisiHasil::where('status', 'approved')->count(),
                    'rejected' => KomisiHasil::where('status', 'rejected')->count(),
                ];
            } elseif ($isPembimbingForAny) {
                $baseQuery = KomisiHasil::where(function ($q) use ($user) {
                    $q->where('dosen_pembimbing1_id', $user->id)
                        ->orWhere('dosen_pembimbing2_id', $user->id);
                });
                $statistics = [
                    'total' => (clone $baseQuery)->count(),
                    'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
                    'approved_pembimbing1' => (clone $baseQuery)->where('status', 'approved_pembimbing1')->count(),
                    'approved_pembimbing2' => (clone $baseQuery)->where('status', 'approved_pembimbing2')->count(),
                    'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
                    'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
                ];
            } else {
                $statistics = [
                    'total' => 0,
                    'pending' => 0,
                    'approved_pembimbing1' => 0,
                    'approved_pembimbing2' => 0,
                    'approved' => 0,
                    'rejected' => 0,
                ];
            }
        } else {
            $statistics = [
                'total' => KomisiHasil::count(),
                'pending' => KomisiHasil::where('status', 'pending')->count(),
                'approved_pembimbing1' => KomisiHasil::where('status', 'approved_pembimbing1')->count(),
                'approved_pembimbing2' => KomisiHasil::where('status', 'approved_pembimbing2')->count(),
                'approved' => KomisiHasil::where('status', 'approved')->count(),
                'rejected' => KomisiHasil::where('status', 'rejected')->count(),
            ];
        }

        return view('admin.komisi-hasil.index', compact('komisiHasils', 'statistics'));
    }

    /**
     * Show komisi hasil detail - untuk AJAX modal
     */
    public function show(KomisiHasil $komisiHasil)
    {
        try {
            // Load all required relationships
            $komisiHasil->load([
                'user',
                'pembimbing1',
                'pembimbing2',
                'penandatanganPembimbing1',
                'penandatanganPembimbing2',
                'penandatanganKorprodi'
            ]);

            $user = User::find(Auth::id());

            // Validasi akses untuk dosen
            if ($user->hasRole('dosen')) {
                $isKorprodi = $this->isKoordinatorProdi($user);
                $isPembimbingForThis = $this->isPembimbingForKomisi($user, $komisiHasil);

                if (!$isKorprodi && !$isPembimbingForThis) {
                    if (request()->ajax()) {
                        return response()->json([
                            'error' => 'Anda tidak memiliki akses untuk melihat komisi hasil ini.'
                        ], 403);
                    }
                    abort(403, 'Anda tidak memiliki akses untuk melihat komisi hasil ini.');
                }
            }

            // Staff dan admin bisa melihat semua (skip validation)

            // Log successful access
            Log::info('Komisi hasil detail accessed', [
                'komisi_id' => $komisiHasil->id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_role' => $user->getRoleNames()->first(),
            ]);

            // Jika request AJAX, return modal content
            if (request()->ajax()) {
                return view('admin.komisi-hasil.detail-modal', [
                    'komisi' => $komisiHasil
                ])->render();
            }

            // Jika bukan AJAX (dari notification), redirect ke index dengan auto-open modal
            return redirect()->route('admin.komisi-hasil.index', ['open' => $komisiHasil->id]);

        } catch (\Exception $e) {
            Log::error('Error loading komisi hasil detail', [
                'komisi_id' => $komisiHasil->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan saat memuat data: ' . $e->getMessage());
        }
    }

    /**
     * Approve by Pembimbing 1 - WITH STAFF OVERRIDE (TANPA WAJIB OVERRIDE REASON)
     */
    public function approveByPembimbing1(Request $request, KomisiHasil $komisiHasil)
    {
        $user = User::find(Auth::id());

        Log::info('=== APPROVE BY PEMBIMBING 1 - START ===', [
            'komisi_id' => $komisiHasil->id,
            'komisi_status' => $komisiHasil->status,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->getRoleNames()->first(),
            'user_jabatan' => $user->jabatan,
            'dosen_pembimbing1_id' => $komisiHasil->dosen_pembimbing1_id,
        ]);

        // VALIDASI 1: Check status
        if (!$komisiHasil->canBeApprovedByPembimbing1()) {
            Log::warning('Status tidak bisa diapprove Pembimbing 1', ['status' => $komisiHasil->status]);
            return back()->with('error', 'Komisi hasil ini tidak dapat disetujui pada tahap ini. Status saat ini: ' . $komisiHasil->status);
        }

        // VALIDASI 2: Check permission
        $isPembimbing1 = $user->hasRole('dosen') && $komisiHasil->dosen_pembimbing1_id == $user->id;
        $canOverride = $this->canOverrideApproval($user);

        if (!$isPembimbing1 && !$canOverride) {
            Log::warning('User tidak memiliki izin untuk approve', [
                'user_id' => $user->id,
                'is_pembimbing1' => $isPembimbing1,
                'can_override' => $canOverride,
            ]);
            return back()->with('error', 'Anda tidak memiliki izin untuk menyetujui komisi hasil ini.');
        }

        // VALIDASI 3: Validasi sederhana tanpa wajib override_reason
        $validationRules = [
            'action' => 'required|in:approve,reject',
            'keterangan' => 'required_if:action,reject|string|max:500',
        ];

        $request->validate($validationRules, [
            'keterangan.required_if' => 'Alasan penolakan wajib diisi.',
            'keterangan.max' => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        DB::beginTransaction();
        try {
            if ($request->action === 'approve') {
                // Generate verification code untuk Pembimbing 1
                $verificationCode = 'KH-' . strtoupper(uniqid());

                // Determine penandatangan (Pembimbing 1 asli atau staff override)
                $penandatanganId = $isPembimbing1 ? $user->id : $komisiHasil->dosen_pembimbing1_id;

                // Simpan informasi override jika dilakukan oleh staff (TANPA WAJIB REASON)
                $overrideInfo = null;
                if ($canOverride && !$isPembimbing1) {
                    $overrideInfo = [
                        'override_by' => $user->id,
                        'override_name' => $user->name,
                        'override_role' => $user->getRoleNames()->first(),
                        'override_at' => now()->toDateTimeString(),
                        'approval_type' => 'Pembimbing 1 Override by Staff',
                        'original_pembimbing1_id' => $komisiHasil->dosen_pembimbing1_id,
                        'original_pembimbing1_name' => $komisiHasil->pembimbing1->name,
                    ];
                }

                // Update status dan verification code
                $komisiHasil->update([
                    'status' => 'approved_pembimbing1',
                    'penandatangan_pembimbing1_id' => $penandatanganId,
                    'tanggal_persetujuan_pembimbing1' => now(),
                    'verification_code' => $verificationCode,
                    'keterangan' => $overrideInfo ? json_encode($overrideInfo) : null,
                ]);

                // Generate PDF dengan QR Pembimbing 1
                $filePath = $this->generatePdfWithPembimbing1($komisiHasil);

                // Update file path
                $komisiHasil->update(['file_komisi_pembimbing1' => $filePath]);

                DB::commit();

                Log::info('Komisi hasil approved by Pembimbing 1 - SUCCESS', [
                    'komisi_id' => $komisiHasil->id,
                    'verification_code' => $verificationCode,
                    'pembimbing1_id' => $penandatanganId,
                    'approved_by' => $user->id,
                    'is_override' => $canOverride && !$isPembimbing1,
                    'override_info' => $overrideInfo,
                ]);

                $successMessage = $canOverride && !$isPembimbing1
                    ? 'Komisi hasil berhasil disetujui (Administrative Override oleh Staff). Menunggu persetujuan Pembimbing 2.'
                    : 'Komisi hasil berhasil disetujui. Menunggu persetujuan Pembimbing 2.';

                return redirect()->route('admin.komisi-hasil.index')
                    ->with('success', $successMessage);

            } else {
                // Reject logic
                $komisiHasil->update([
                    'status' => 'rejected',
                    'keterangan' => $request->keterangan,
                ]);

                DB::commit();

                Log::info('Komisi hasil rejected by Pembimbing 1', [
                    'komisi_id' => $komisiHasil->id,
                    'keterangan' => $request->keterangan,
                    'rejected_by' => $user->id,
                ]);

                return redirect()->route('admin.komisi-hasil.index')
                    ->with('success', 'Komisi hasil ditolak.');
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error approving by Pembimbing 1 - FAILED', [
                'komisi_id' => $komisiHasil->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Approve by Pembimbing 2 - WITH STAFF OVERRIDE (TANPA WAJIB OVERRIDE REASON)
     */
    public function approveByPembimbing2(Request $request, KomisiHasil $komisiHasil)
    {
        $user = User::find(Auth::id());

        Log::info('=== APPROVE BY PEMBIMBING 2 - START ===', [
            'komisi_id' => $komisiHasil->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->getRoleNames()->first(),
        ]);

        // VALIDASI 1: Check status
        if (!$komisiHasil->canBeApprovedByPembimbing2()) {
            return back()->with('error', 'Komisi hasil ini tidak dapat disetujui pada tahap ini.');
        }

        // VALIDASI 2: Check permission
        $isPembimbing2 = $user->hasRole('dosen') && $komisiHasil->dosen_pembimbing2_id == $user->id;
        $canOverride = $this->canOverrideApproval($user);

        if (!$isPembimbing2 && !$canOverride) {
            Log::warning('User tidak memiliki izin untuk approve pembimbing2', [
                'user_id' => $user->id,
                'is_pembimbing2' => $isPembimbing2,
                'can_override' => $canOverride,
            ]);
            return back()->with('error', 'Hanya Pembimbing 2 atau Staff yang dapat menyetujui pada tahap ini.');
        }

        // VALIDASI 3: Validasi sederhana tanpa wajib override_reason
        $validationRules = [
            'action' => 'required|in:approve,reject',
            'keterangan' => 'required_if:action,reject|string|max:500',
        ];

        $request->validate($validationRules, [
            'keterangan.required_if' => 'Alasan penolakan wajib diisi.',
            'keterangan.max' => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        DB::beginTransaction();
        try {
            if ($request->action === 'approve') {
                // Generate verification code baru
                $verificationCode = 'KH-' . strtoupper(uniqid());

                // Determine penandatangan
                $penandatanganId = $isPembimbing2 ? $user->id : $komisiHasil->dosen_pembimbing2_id;

                // Simpan informasi override (TANPA WAJIB REASON)
                $overrideInfo = null;
                if ($canOverride && !$isPembimbing2) {
                    $overrideInfo = [
                        'override_by' => $user->id,
                        'override_name' => $user->name,
                        'override_role' => $user->getRoleNames()->first(),
                        'override_at' => now()->toDateTimeString(),
                        'approval_type' => 'Pembimbing 2 Override by Staff',
                        'original_pembimbing2_id' => $komisiHasil->dosen_pembimbing2_id,
                        'original_pembimbing2_name' => $komisiHasil->pembimbing2->name,
                    ];
                }

                // Preserve existing override info from Pembimbing 1
                $existingKeterangan = $komisiHasil->keterangan
                    ? json_decode($komisiHasil->keterangan, true)
                    : [];

                if ($overrideInfo) {
                    if (isset($existingKeterangan['override_by'])) {
                        $existingKeterangan['pembimbing1_override'] = $existingKeterangan;
                    }
                    $existingKeterangan['pembimbing2_override'] = $overrideInfo;
                    $keteranganToSave = json_encode($existingKeterangan);
                } else {
                    $keteranganToSave = $komisiHasil->keterangan;
                }

                // Update status dan verification code
                $updateData = [
                    'status' => 'approved_pembimbing2',
                    'penandatangan_pembimbing2_id' => $penandatanganId,
                    'tanggal_persetujuan_pembimbing2' => now(),
                    'verification_code' => $verificationCode,
                    'keterangan' => $keteranganToSave,
                ];

                $komisiHasil->update($updateData);

                // Generate PDF dengan kedua signature
                $filePath = $this->generatePdfWithPembimbing2($komisiHasil);

                // Update file path
                $komisiHasil->update(['file_komisi_pembimbing2' => $filePath]);

                DB::commit();

                Log::info('Komisi hasil approved by Pembimbing 2 - SUCCESS', [
                    'komisi_id' => $komisiHasil->id,
                    'verification_code' => $verificationCode,
                    'pembimbing2_id' => $penandatanganId,
                    'approved_by' => $user->id,
                    'is_override' => $canOverride && !$isPembimbing2,
                ]);

                $successMessage = $canOverride && !$isPembimbing2
                    ? 'Komisi hasil berhasil disetujui (Administrative Override oleh Staff). Menunggu persetujuan Korprodi.'
                    : 'Komisi hasil berhasil disetujui. Menunggu persetujuan Korprodi.';

                return redirect()->route('admin.komisi-hasil.index')
                    ->with('success', $successMessage);

            } else {
                // Reject logic
                $komisiHasil->update([
                    'status' => 'rejected',
                    'keterangan' => $request->keterangan,
                ]);

                DB::commit();

                Log::info('Komisi hasil rejected by Pembimbing 2', [
                    'komisi_id' => $komisiHasil->id,
                    'keterangan' => $request->keterangan,
                    'rejected_by' => $user->id,
                ]);

                return redirect()->route('admin.komisi-hasil.index')
                    ->with('success', 'Komisi hasil ditolak.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving by Pembimbing 2: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Approve by Korprodi - WITH STAFF OVERRIDE (TANPA WAJIB OVERRIDE REASON)
     */
    public function approveByKorprodi(Request $request, KomisiHasil $komisiHasil)
    {
        $user = User::find(Auth::id());

        Log::info('=== APPROVE BY KORPRODI - START ===', [
            'komisi_id' => $komisiHasil->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
        ]);

        // VALIDASI 1: Check status
        if (!$komisiHasil->canBeApprovedByKorprodi()) {
            return back()->with('error', 'Komisi hasil ini tidak dapat disetujui pada tahap ini.');
        }

        // VALIDASI 2: Check permission
        $isKorprodi = $user->hasRole('dosen') && $this->isKoordinatorProdi($user);
        $canOverride = $this->canOverrideApproval($user);

        if (!$isKorprodi && !$canOverride) {
            Log::warning('User tidak memiliki izin untuk approve korprodi', [
                'user_id' => $user->id,
                'is_korprodi' => $isKorprodi,
                'can_override' => $canOverride,
            ]);
            return back()->with('error', 'Hanya Koordinator Program Studi atau Staff yang dapat menyetujui pada tahap ini.');
        }

        // VALIDASI 3: Validasi sederhana tanpa wajib override_reason
        $validationRules = [
            'action' => 'required|in:approve,reject',
            'keterangan' => 'required_if:action,reject|string|max:500',
        ];

        $request->validate($validationRules, [
            'keterangan.required_if' => 'Alasan penolakan wajib diisi.',
            'keterangan.max' => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        DB::beginTransaction();
        try {
            if ($request->action === 'approve') {
                // Generate verification code baru untuk final
                $verificationCode = 'KH-' . strtoupper(uniqid());

                // Determine penandatangan
                $penandatanganId = $isKorprodi ? $user->id : $this->getDefaultKorprodiId();

                // Simpan informasi override (TANPA WAJIB REASON)
                $overrideInfo = null;
                if ($canOverride && !$isKorprodi) {
                    $overrideInfo = [
                        'override_by' => $user->id,
                        'override_name' => $user->name,
                        'override_role' => $user->getRoleNames()->first(),
                        'override_at' => now()->toDateTimeString(),
                        'approval_type' => 'Korprodi Override by Staff',
                        'default_korprodi_id' => $penandatanganId,
                    ];
                }

                // Preserve existing override info
                $existingKeterangan = $komisiHasil->keterangan
                    ? json_decode($komisiHasil->keterangan, true)
                    : [];

                // Update keterangan jika ada override
                if ($overrideInfo) {
                    $existingKeterangan['korprodi_override'] = $overrideInfo;
                    $updateData['keterangan'] = json_encode($existingKeterangan);
                } else {
                    $updateData['keterangan'] = null;
                }

                // Update status dan verification code
                $updateData = [
                    'status' => 'approved',
                    'penandatangan_korprodi_id' => $penandatanganId,
                    'tanggal_persetujuan_korprodi' => now(),
                    'verification_code' => $verificationCode,
                ];

                // Update keterangan jika ada override
                if ($overrideInfo) {
                    $existingKeterangan['korprodi_override'] = $overrideInfo;
                    $updateData['keterangan'] = json_encode($existingKeterangan);
                } else {
                    $updateData['keterangan'] = null;
                }

                $komisiHasil->update($updateData);

                // Generate final PDF dengan semua signature
                $filePath = $this->generateFinalPdf($komisiHasil);

                // Update file path
                $komisiHasil->update(['file_komisi_hasil' => $filePath]);

                DB::commit();

                Log::info('Komisi hasil approved by Korprodi - SUCCESS', [
                    'komisi_id' => $komisiHasil->id,
                    'verification_code' => $verificationCode,
                    'korprodi_id' => $penandatanganId,
                    'approved_by' => $user->id,
                    'is_override' => $canOverride && !$isKorprodi,
                ]);

                $successMessage = $canOverride && !$isKorprodi
                    ? 'Komisi hasil berhasil disetujui lengkap (Administrative Override oleh Staff). Mahasiswa dapat mengunduh dokumen.'
                    : 'Komisi hasil berhasil disetujui lengkap. Mahasiswa dapat mengunduh dokumen.';

                return redirect()->route('admin.komisi-hasil.index')
                    ->with('success', $successMessage);

            } else {
                // Reject logic
                $komisiHasil->update([
                    'status' => 'rejected',
                    'keterangan' => $request->keterangan,
                ]);

                DB::commit();

                Log::info('Komisi hasil rejected by Korprodi', [
                    'komisi_id' => $komisiHasil->id,
                    'keterangan' => $request->keterangan,
                    'rejected_by' => $user->id,
                ]);

                return redirect()->route('admin.komisi-hasil.index')
                    ->with('success', 'Komisi hasil ditolak.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving by Korprodi: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get default Korprodi ID (ambil yang pertama)
     */
    private function getDefaultKorprodiId(): ?int
    {
        $korprodi = User::role('dosen')
            ->where(function ($query) {
                $query->where('jabatan', 'like', '%koordinator program studi%')
                    ->orWhere('jabatan', 'like', '%korprodi%')
                    ->orWhere('jabatan', 'like', '%kaprodi%')
                    ->orWhere('jabatan', 'like', '%ketua program studi%');
            })
            ->first();

        return $korprodi?->id;
    }

    /**
     * Generate PDF with Pembimbing 1 signature QR
     */
    protected function generatePdfWithPembimbing1(KomisiHasil $komisiHasil)
    {
        $komisiHasil->load(['user', 'pembimbing1', 'pembimbing2', 'penandatanganPembimbing1']);

        $verificationUrl = route('document.verify', ['code' => $komisiHasil->verification_code]);

        $pembimbing1Qr = base64_encode(QrCode::format('png')
            ->size(200)
            ->errorCorrection('H')
            ->generate($verificationUrl));

        $pdf = Pdf::loadView('admin.komisi-hasil.pdf', [
            'komisi' => $komisiHasil,
            'pembimbing1_qr' => 'data:image/png;base64,' . $pembimbing1Qr,
            'pembimbing2_qr' => null,
            'korprodi_qr' => null,
            'show_korprodi_signature' => false,
            'verification_code' => $komisiHasil->verification_code,
        ])->setPaper('a4', 'portrait');

        $nimSanitized = preg_replace('/[^a-zA-Z0-9]/', '', $komisiHasil->user->nim ?? 'unknown');
        $timestamp = now()->format('Ymd_His');
        $hash = substr(md5($komisiHasil->id . '_p1'), 0, 8);
        $filename = sprintf('komisi_hasil_%s_%s_%s_p1.pdf', $nimSanitized, $timestamp, $hash);
        $yearMonth = now()->format('Y/m');

        $path = "komisi_hasil/{$yearMonth}/{$filename}";
        $directory = dirname($path);

        if (!Storage::disk('local')->exists($directory)) {
            Storage::disk('local')->makeDirectory($directory);
        }

        Storage::disk('local')->put($path, $pdf->output());

        Log::info('PDF with Pembimbing 1 signature generated', [
            'komisi_id' => $komisiHasil->id,
            'path' => $path,
            'verification_code' => $komisiHasil->verification_code,
            'verification_url' => $verificationUrl,
        ]);

        return $path;
    }

    /**
     * Generate PDF with Pembimbing 1 & 2 signatures
     */
    protected function generatePdfWithPembimbing2(KomisiHasil $komisiHasil)
    {
        $komisiHasil->load(['user', 'pembimbing1', 'pembimbing2', 'penandatanganPembimbing1', 'penandatanganPembimbing2']);

        $verificationUrl = route('document.verify', ['code' => $komisiHasil->verification_code]);

        $pembimbing1Qr = base64_encode(QrCode::format('png')
            ->size(200)
            ->errorCorrection('H')
            ->generate($verificationUrl));

        $pembimbing2Qr = base64_encode(QrCode::format('png')
            ->size(200)
            ->errorCorrection('H')
            ->generate($verificationUrl));

        $pdf = Pdf::loadView('admin.komisi-hasil.pdf', [
            'komisi' => $komisiHasil,
            'pembimbing1_qr' => 'data:image/png;base64,' . $pembimbing1Qr,
            'pembimbing2_qr' => 'data:image/png;base64,' . $pembimbing2Qr,
            'korprodi_qr' => null,
            'show_korprodi_signature' => false,
            'verification_code' => $komisiHasil->verification_code,
        ])->setPaper('a4', 'portrait');

        $nimSanitized = preg_replace('/[^a-zA-Z0-9]/', '', $komisiHasil->user->nim ?? 'unknown');
        $timestamp = now()->format('Ymd_His');
        $hash = substr(md5($komisiHasil->id . '_p2'), 0, 8);
        $filename = sprintf('komisi_hasil_%s_%s_%s_p2.pdf', $nimSanitized, $timestamp, $hash);
        $yearMonth = now()->format('Y/m');

        $path = "komisi_hasil/{$yearMonth}/{$filename}";
        $directory = dirname($path);

        if (!Storage::disk('local')->exists($directory)) {
            Storage::disk('local')->makeDirectory($directory);
        }

        Storage::disk('local')->put($path, $pdf->output());

        Log::info('PDF with Pembimbing 1 & 2 signatures generated', [
            'komisi_id' => $komisiHasil->id,
            'path' => $path,
            'verification_code' => $komisiHasil->verification_code,
            'verification_url' => $verificationUrl,
        ]);

        return $path;
    }

    /**
     * Generate final PDF with all signatures
     */
    protected function generateFinalPdf(KomisiHasil $komisiHasil)
    {
        $komisiHasil->load(['user', 'pembimbing1', 'pembimbing2', 'penandatanganPembimbing1', 'penandatanganPembimbing2', 'penandatanganKorprodi']);

        $verificationUrl = route('document.verify', ['code' => $komisiHasil->verification_code]);

        $pembimbing1Qr = base64_encode(QrCode::format('png')
            ->size(200)
            ->errorCorrection('H')
            ->generate($verificationUrl));

        $pembimbing2Qr = base64_encode(QrCode::format('png')
            ->size(200)
            ->errorCorrection('H')
            ->generate($verificationUrl));

        $korprodiQr = base64_encode(QrCode::format('png')
            ->size(200)
            ->errorCorrection('H')
            ->generate($verificationUrl));

        $pdf = Pdf::loadView('admin.komisi-hasil.pdf', [
            'komisi' => $komisiHasil,
            'pembimbing1_qr' => 'data:image/png;base64,' . $pembimbing1Qr,
            'pembimbing2_qr' => 'data:image/png;base64,' . $pembimbing2Qr,
            'korprodi_qr' => 'data:image/png;base64,' . $korprodiQr,
            'show_korprodi_signature' => true,
            'verification_code' => $komisiHasil->verification_code,
        ])->setPaper('a4', 'portrait');

        $nimSanitized = preg_replace('/[^a-zA-Z0-9]/', '', $komisiHasil->user->nim ?? 'unknown');
        $timestamp = now()->format('Ymd_His');
        $hash = substr(md5($komisiHasil->id . '_final'), 0, 8);
        $filename = sprintf('komisi_hasil_%s_%s_%s_final.pdf', $nimSanitized, $timestamp, $hash);
        $yearMonth = now()->format('Y/m');

        $path = "komisi_hasil/{$yearMonth}/{$filename}";
        $directory = dirname($path);

        if (!Storage::disk('local')->exists($directory)) {
            Storage::disk('local')->makeDirectory($directory);
        }

        Storage::disk('local')->put($path, $pdf->output());

        Log::info('Final PDF generated', [
            'komisi_id' => $komisiHasil->id,
            'path' => $path,
            'verification_code' => $komisiHasil->verification_code,
            'verification_url' => $verificationUrl,
        ]);

        return $path;
    }

    /**
     * Download PDF
     */
    public function downloadPdf(KomisiHasil $komisiHasil)
    {
        $user = Auth::user();
        $filePath = null;

        if ($komisiHasil->status === 'approved' && $komisiHasil->file_komisi_hasil) {
            $filePath = $komisiHasil->file_komisi_hasil;
        } elseif ($komisiHasil->status === 'approved_pembimbing2' && $komisiHasil->file_komisi_pembimbing2) {
            if ($this->isKoordinatorProdi($user)) {
                $filePath = $komisiHasil->file_komisi_pembimbing2;
            }
        } elseif ($komisiHasil->status === 'approved_pembimbing1' && $komisiHasil->file_komisi_pembimbing1) {
            if ($komisiHasil->dosen_pembimbing2_id == $user->id || $this->isKoordinatorProdi($user)) {
                $filePath = $komisiHasil->file_komisi_pembimbing1;
            }
        }

        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            return back()->with('error', 'File persetujuan belum tersedia atau tidak ditemukan.');
        }

        $fullPath = Storage::disk('local')->path($filePath);
        $filename = 'Persetujuan_Komisi_Hasil_' . $komisiHasil->user->nim . '.pdf';

        return response()->download($fullPath, $filename);
    }

    /**
     * Generate PDF (public method untuk user)
     */
    public function generatePdf(KomisiHasil $komisiHasil)
    {
        // Jika belum punya verification code, generate
        if (empty($komisiHasil->verification_code)) {
            $komisiHasil->verification_code = 'KH-' . strtoupper(uniqid());
            $komisiHasil->save();
        }

        $verificationUrl = route('document.verify', ['code' => $komisiHasil->verification_code]);

        $qrCode = base64_encode(QrCode::format('png')
            ->size(200)
            ->errorCorrection('H')
            ->generate($verificationUrl));

        $qrCodeImage = 'data:image/png;base64,' . $qrCode;
        $showKorprodiSignature = $komisiHasil->status === 'approved';

        $pdf = Pdf::loadView('admin.komisi-hasil.pdf', [
            'komisi' => $komisiHasil,
            'pembimbing1_qr' => $qrCodeImage,
            'pembimbing2_qr' => $qrCodeImage,
            'korprodi_qr' => $showKorprodiSignature ? $qrCodeImage : null,
            'show_korprodi_signature' => $showKorprodiSignature,
            'verification_code' => $komisiHasil->verification_code,
        ])->setPaper('a4', 'portrait');

        $filename = 'persetujuan-komisi-hasil-' . $komisiHasil->user->nim . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Delete komisi hasil
     */
    public function destroy(KomisiHasil $komisiHasil)
    {
        $user = User::find(Auth::id());

        Log::info('=== DELETE KOMISI HASIL - START ===', [
            'komisi_id' => $komisiHasil->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'komisi_status' => $komisiHasil->status,
            'mahasiswa_name' => $komisiHasil->user->name,
            'mahasiswa_nim' => $komisiHasil->user->nim,
        ]);

        // Validasi permission
        if (!$user->hasRole(['admin', 'dosen', 'staff'])) {
            Log::warning('Unauthorized delete attempt', [
                'user_id' => $user->id,
                'user_role' => $user->getRoleNames(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menghapus data ini.',
            ], 403);
        }

        // Validasi untuk dosen
        if ($user->hasRole('dosen')) {
            $isPembimbing = $this->isPembimbingForKomisi($user, $komisiHasil);
            $isKorprodi = $this->isKoordinatorProdi($user);

            // Korprodi bisa menghapus semua
            // Pembimbing hanya bisa menghapus komisi mahasiswa bimbingannya
            if (!$isKorprodi && !$isPembimbing) {
                Log::warning('Dosen tidak memiliki akses untuk menghapus', [
                    'user_id' => $user->id,
                    'is_pembimbing' => $isPembimbing,
                    'is_korprodi' => $isKorprodi,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menghapus komisi hasil ini.',
                ], 403);
            }
        }

        // Check if can be deleted
        if (!$komisiHasil->canBeDeleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Komisi hasil dalam status ini tidak dapat dihapus.',
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Simpan data untuk logging
            $komisiData = [
                'id' => $komisiHasil->id,
                'user_id' => $komisiHasil->user_id,
                'user_name' => $komisiHasil->user->name,
                'user_nim' => $komisiHasil->user->nim,
                'judul_skripsi' => $komisiHasil->judul_skripsi,
                'status' => $komisiHasil->status,
                'verification_code' => $komisiHasil->verification_code,
                'file_komisi_pembimbing1' => $komisiHasil->file_komisi_pembimbing1,
                'file_komisi_pembimbing2' => $komisiHasil->file_komisi_pembimbing2,
                'file_komisi_hasil' => $komisiHasil->file_komisi_hasil,
                'deleted_by' => $user->id,
                'deleted_by_name' => $user->name,
            ];

            // Delete komisi (files akan otomatis dihapus via model boot event)
            $komisiHasil->delete();

            DB::commit();

            Log::info('Komisi hasil deleted successfully', $komisiData);

            return response()->json([
                'success' => true,
                'message' => 'Komisi hasil berhasil dihapus. Mahasiswa dapat mengajukan komisi hasil baru.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete komisi hasil', [
                'komisi_id' => $komisiHasil->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage(),
            ], 500);
        }
    }
}