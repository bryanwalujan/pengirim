<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\StatusSurat;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use App\Models\TrackingSurat;
use App\Models\SuratIjinSurvey;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DokumenPendukung;
use Illuminate\Support\Facades\DB;
use App\Traits\BaseSuratController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Helpers\SuratNotificationHelper;
use App\Services\SuratSubmissionService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Notifications\SuratTakenNotification;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Requests\UpdateSuratIjinSurveyRequest;
use App\Notifications\SuratNeedApprovalNotification;

class AdminSuratIjinSurveyController extends DocumentController
{
    use BaseSuratController;

    /**
     * Get the prefix for generating the letter number.
     *
     * @return string
     */
    protected function getNomorSuratPrefix()
    {
        return 'UN41.2/TI';
    }

    protected function getNextNomorSurat()
    {
        return $this->generateNomorSuratUniversal();
    }

    /**
     * Display a listing of the survey permission letters.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'diajukan');
        $search = $request->input('search');

        // Adjust default status based on user role and position
        if (Auth::check() && User::find(Auth::id())->hasRole('dosen')) {
            $user = Auth::user();
            if (str_contains(strtolower($user->jabatan), 'koordinator program studi')) {
                $status = 'diproses';
            } elseif (str_contains(strtolower($user->jabatan), 'pimpinan jurusan') || str_contains(strtolower($user->jabatan), 'ptik')) {
                $status = 'disetujui_kaprodi';
            }
        }

        $surats = SuratIjinSurvey::with(['mahasiswa', 'status'])
            ->when($status, function ($query) use ($status) {
                $query->whereHas('status', function ($q) use ($status) {
                    $q->where('status', $status);
                });
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nomor_surat', 'like', "%$search%")
                        ->orWhere('judul', 'like', "%$search%")
                        ->orWhere('tempat_survey', 'like', "%$search%")
                        ->orWhereHas('mahasiswa', function ($q) use ($search) {
                            $q->where('name', 'like', "%$search%")
                                ->orWhere('nim', 'like', "%$search%");
                        });
                });
            })
            ->latest()
            ->paginate(15);

        return view('admin.surat-ijin-survey.index', compact('surats', 'status', 'search'));
    }

    /**
     * Display the specified survey permission letter.
     *
     * @param \App\Models\SuratIjinSurvey $surat
     * @return \Illuminate\View\View
     */
    public function show(SuratIjinSurvey $surat)
    {

        // Mark related notifications as read
        if (User::find(Auth::id())->hasRole('dosen')) {
            User::find(Auth::id())->unreadNotifications()
                ->where('type', 'App\Notifications\SuratNeedApprovalNotification')
                ->where('data->surat_id', $surat->id)
                ->update(['read_at' => now()]);
        }

        // Mark related notifications as read (both approval and taken notifications)
        if (User::find(Auth::id())->hasRole('staff')) {
            User::find(Auth::id())->unreadNotifications()
                ->where(function ($query) use ($surat) {
                    $query->where('type', 'App\Notifications\SuratTakenNotification')
                        ->where('data->url', route('admin.surat-aktif-kuliah.show', $surat->id));
                })
                ->orWhere(function ($query) use ($surat) {
                    $query->where('type', 'App\Notifications\SuratNeedApprovalNotification')
                        ->where('data->surat_id', $surat->id);
                })
                ->update(['read_at' => now()]);
        }

        $surat->load([
            'mahasiswa',
            'status',
            'trackings' => fn($query) => $query->latest(),
            'penandatangan',
            'penandatanganKaprodi',
        ]);

        $penandatangans = User::role('dosen')->get();
        $lastNomorSurat = $this->getLastUsedNomorSurat();
        $nextNomorSurat = $this->getNextNomorSurat();

        return view('admin.surat-ijin-survey.show', compact('surat', 'penandatangans', 'lastNomorSurat', 'nextNomorSurat'));
    }

    /**
     * Update the specified survey permission letter's basic details.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SuratIjinSurvey $surat
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, SuratIjinSurvey $surat)
    {
        $validated = $request->validate([
            'nomor_surat' => 'nullable|string|max:50',
            'tanggal_surat' => 'nullable|date',
        ]);

        try {
            $surat->update($validated);
            return redirect()->route('admin.surat-ijin-survey.show', $surat->id)
                ->with('success', 'Surat berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Failed to update surat: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui surat: ' . $e->getMessage());
        }
    }

    /**
     * Update the status of the survey permission letter.
     *
     * @param \App\Http\Requests\UpdateSuratIjinSurveyRequest $request
     * @param \App\Models\SuratIjinSurvey $surat
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(UpdateSuratIjinSurveyRequest $request, SuratIjinSurvey $surat)
    {
        $validated = $request->validated();
        $user = User::find(Auth::id());


        $allowedTransitions = [
            'staff' => [
                'diproses' => ['diajukan'],
                'ditolak' => ['diajukan', 'disetujui_kaprodi', 'disetujui'],
                'siap_diambil' => ['disetujui'],
            ],
            'dosen' => [
                'disetujui_kaprodi' => ['diproses'],
                'disetujui' => ['disetujui_kaprodi'],
                'ditolak' => ['diproses', 'disetujui_kaprodi'],
            ],
        ];

        // Check role permissions
        $role = $user->hasRole('staff') ? 'staff' : ($user->hasRole('dosen') ? 'dosen' : null);
        if (!$role || !array_key_exists($validated['status'], $allowedTransitions[$role] ?? [])) {
            return back()->with('error', 'Anda tidak memiliki izin untuk melakukan aksi ini');
        }

        // Check valid status transition
        if (!in_array($surat->status, $allowedTransitions[$role][$validated['status']] ?? [])) {
            return back()->with('error', 'Transisi status tidak valid');
        }

        DB::beginTransaction();
        try {
            // Process 'diproses' status by staff
            if ($validated['status'] === 'diproses' && $user->hasRole('staff')) {
                // Handle letter number
                if (!empty($validated['nomor_surat'])) {
                    $manualNumber = trim($validated['nomor_surat']);
                    if (preg_match('#^\d{1,4}$#', $manualNumber)) {
                        $proposedNumber = $this->generateNomorSurat($manualNumber);
                    } elseif (!$this->validateNomorSuratFormat($manualNumber, $this->getNomorSuratPrefix())) {
                        return back()->with('error', 'Format nomor surat tidak valid. Contoh: 0001/UN41.2/TI/2025');
                    } else {
                        $proposedNumber = $manualNumber;
                    }

                    if (!$this->validateNomorSuratUnique($proposedNumber)) {
                        DB::rollBack();
                        return back()->with('error', 'Nomor surat sudah digunakan di layanan lain!')->withInput();
                    }

                    $validated['nomor_surat'] = $proposedNumber;
                } else {
                    $validated['nomor_surat'] = $this->generateNomorSurat();
                }

                $surat->update([
                    'nomor_surat' => $validated['nomor_surat'],
                    'tanggal_surat' => now(),
                ]);

                // Generate PDF without QR code
                $filePath = $this->generateSuratFile($surat, false);
                $surat->update(['file_surat_path' => $filePath]);

                // Notify Kaprodi
                $dosenKaprodi = User::role('dosen')
                    ->where('jabatan', 'like', '%Koordinator Program Studi%')
                    ->get();
                foreach ($dosenKaprodi as $dosen) {
                    $dosen->notify(new SuratNeedApprovalNotification($surat));
                }
            }

            // Handle 'siap_diambil' status
            if ($validated['status'] === 'siap_diambil' && $user->hasRole('staff')) {
                $surat->mahasiswa->notify(new SuratNeedApprovalNotification($surat));
            }

            // Handle Kaprodi approval
            if ($validated['status'] === 'disetujui_kaprodi' && $user->hasRole('dosen')) {
                $request->validate([
                    'penandatangan_kaprodi_id' => 'required|exists:users,id',
                    'jabatan_penandatangan_kaprodi' => 'required|string|max:255',
                ]);

                $surat->update([
                    'penandatangan_kaprodi_id' => $request->penandatangan_kaprodi_id,
                    'jabatan_penandatangan_kaprodi' => $request->jabatan_penandatangan_kaprodi,
                    'approved_at' => now(),
                    'approved_by' => $user->id,
                ]);

                // Generate PDF with Kaprodi QR code
                $filePath = $this->generateSuratFile($surat, true, 'kaprodi');
                $surat->update(['file_surat_path' => $filePath]);

                // Notify Pimpinan
                $dosenPimpinan = User::role('dosen')
                    ->where('jabatan', 'like', '%Pimpinan Jurusan PTIK%')
                    ->get();
                foreach ($dosenPimpinan as $dosen) {
                    $dosen->notify(new SuratNeedApprovalNotification($surat));
                }
            }

            // Handle Pimpinan approval
            if ($validated['status'] === 'disetujui' && $user->hasRole('dosen')) {
                $request->validate([
                    'penandatangan_id' => 'required|exists:users,id',
                    'jabatan_penandatangan' => 'required|string|max:255',
                ]);

                $surat->update([
                    'penandatangan_id' => $request->penandatangan_id,
                    'jabatan_penandatangan' => $request->jabatan_penandatangan,
                    'approved_at' => now(),
                    'approved_by' => $user->id,
                ]);

                // Generate final PDF with both QR codes
                $filePath = $this->generateSuratFile($surat, true, 'pimpinan');
                $surat->update(['file_surat_path' => $filePath]);

                $surat->mahasiswa->notify(new SuratNeedApprovalNotification($surat));
            }

            // Update status
            StatusSurat::updateOrCreate(
                ['surat_type' => SuratIjinSurvey::class, 'surat_id' => $surat->id],
                [
                    'status' => $validated['status'],
                    'catatan_admin' => $validated['catatan_admin'] ?? null,
                    'updated_by' => $user->id,
                ]
            );

            TrackingSurat::create([
                'surat_type' => SuratIjinSurvey::class,
                'surat_id' => $surat->id,
                'aksi' => $validated['status'],
                'keterangan' => $validated['catatan_admin'] ?? 'Status diperbarui',
                'mahasiswa_id' => $surat->mahasiswa_id,
            ]);

            DB::commit();

            // Clear cache after status update
            app(SuratSubmissionService::class)->clearCache($surat->mahasiswa_id);

            // Clear notification badge cache
            if (class_exists('\App\Helpers\SuratNotificationHelper')) {
                SuratNotificationHelper::clearSuratCache('surat_ijin_survey');
            }

            return redirect()->route('admin.surat-ijin-survey.show', $surat->id)
                ->with('success', 'Status surat berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update status: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    /**
     * Approve or reject the survey permission letter by a lecturer.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SuratIjinSurvey $surat
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveByDosen(Request $request, SuratIjinSurvey $surat)
    {
        $user = User::find(Auth::id());

        if (!$user->hasRole('dosen')) {
            return back()->with('error', 'Anda tidak memiliki izin untuk menyetujui surat');
        }

        // Validate position
        $isKaprodi = str_contains(strtolower($user->jabatan), 'koordinator program studi');
        $isPimpinan = str_contains(strtolower($user->jabatan), 'pimpinan jurusan') ||
            str_contains(strtolower($user->jabatan), 'ptik');

        if ($surat->status === 'diproses' && !$isKaprodi) {
            return back()->with('error', 'Hanya Koordinator Program Studi yang dapat menyetujui surat pada tahap ini');
        }

        if ($surat->status === 'disetujui_kaprodi' && !$isPimpinan) {
            return back()->with('error', 'Hanya Pimpinan Jurusan PTIK yang dapat menyetujui surat pada tahap ini');
        }

        $allowedTransitions = [
            'disetujui_kaprodi' => ['diproses'],
            'disetujui' => ['disetujui_kaprodi'],
            'ditolak' => ['diproses', 'disetujui_kaprodi'],
        ];

        $status = $request->status;
        if (!in_array($surat->status, $allowedTransitions[$status] ?? [])) {
            Log::error('Invalid status transition', [
                'current_status' => $surat->status,
                'requested_status' => $status,
            ]);
            return back()->with('error', 'Transisi status tidak valid');
        }

        // Validate input
        $rules = [
            'action' => 'required|in:approve,reject',
            'catatan_admin' => 'required|string|max:500',
        ];

        if ($request->action === 'approve') {
            if ($surat->status === 'diproses') {
                $rules['penandatangan_kaprodi_id'] = 'required|exists:users,id';
                $rules['jabatan_penandatangan_kaprodi'] = 'required|string|max:255';
            } elseif ($surat->status === 'disetujui_kaprodi') {
                $rules['penandatangan_id'] = 'required|exists:users,id';
                $rules['jabatan_penandatangan'] = 'required|string|max:255';
            }
        }

        $request->validate($rules);

        DB::beginTransaction();
        try {
            if ($request->action === 'approve') {
                $nomorSurat = $surat->nomor_surat ?: $this->generateNomorSuratUniversal();
                // Modified validation to exclude current surat from uniqueness check
                if (!$this->validateNomorSuratUnique($nomorSurat, $surat->id, get_class($surat))) {
                    DB::rollBack();
                    return back()->with('error', 'Nomor surat sudah digunakan di layanan lain!');
                }
                $qrType = $surat->status === 'diproses' ? 'kaprodi' : 'pimpinan';
                $newStatus = $surat->status === 'diproses' ? 'disetujui_kaprodi' : 'disetujui';

                $updateData = [
                    'nomor_surat' => $nomorSurat,
                    'tanggal_surat' => $surat->tanggal_surat ?? now(),
                    'approved_at' => now(),
                    'approved_by' => $user->id,
                ];

                if ($qrType === 'kaprodi') {
                    $updateData['penandatangan_kaprodi_id'] = $request->penandatangan_kaprodi_id;
                    $updateData['jabatan_penandatangan_kaprodi'] = $request->jabatan_penandatangan_kaprodi;
                } else {
                    $updateData['penandatangan_id'] = $request->penandatangan_id;
                    $updateData['jabatan_penandatangan'] = $request->jabatan_penandatangan;
                }

                $surat->update($updateData);

                // Generate PDF
                $filePath = $this->generateSuratFile($surat, true, $qrType);
                $surat->update(['file_surat_path' => $filePath]);

                // Update status
                StatusSurat::updateOrCreate(
                    [
                        'surat_type' => SuratIjinSurvey::class,
                        'surat_id' => $surat->id,
                    ],
                    [
                        'status' => $newStatus,
                        'catatan_admin' => $request->catatan_admin,
                        'updated_by' => $user->id,
                    ]
                );

                // Create tracking
                TrackingSurat::create([
                    'surat_type' => SuratIjinSurvey::class,
                    'surat_id' => $surat->id,
                    'aksi' => $newStatus,
                    'keterangan' => $request->catatan_admin,
                    'mahasiswa_id' => $surat->mahasiswa_id,
                ]);

                // Notify
                if ($newStatus === 'disetujui_kaprodi') {
                    $dosenPimpinan = User::role('dosen')
                        ->where('jabatan', 'like', '%Pimpinan Jurusan PTIK%')
                        ->get();
                    foreach ($dosenPimpinan as $dosen) {
                        $dosen->notify(new SuratNeedApprovalNotification($surat));
                    }
                } else {
                    $surat->mahasiswa->notify(new SuratNeedApprovalNotification($surat));
                }

                DB::commit();

                // Clear cache after approval
                app(SuratSubmissionService::class)->clearCache($surat->mahasiswa_id);

                // Clear notification badge cache
                if (class_exists('\App\Helpers\SuratNotificationHelper')) {
                    SuratNotificationHelper::clearSuratCache('surat_ijin_survey');
                }

                return redirect()->route('admin.surat-ijin-survey.index')
                    ->with('success', 'Surat berhasil disetujui dan file telah dibuat');
            } else {
                // Handle rejection
                StatusSurat::updateOrCreate(
                    [
                        'surat_type' => SuratIjinSurvey::class,
                        'surat_id' => $surat->id,
                    ],
                    [
                        'status' => 'ditolak',
                        'catatan_admin' => $request->catatan_admin,
                        'updated_by' => $user->id,
                    ]
                );

                TrackingSurat::create([
                    'surat_type' => SuratIjinSurvey::class,
                    'surat_id' => $surat->id,
                    'aksi' => 'ditolak',
                    'keterangan' => $request->catatan_admin,
                    'mahasiswa_id' => $surat->mahasiswa_id,
                ]);

                DB::commit();

                return back()->with('success', 'Surat telah ditolak');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('approveByDosen failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses persetujuan: ' . $e->getMessage());
        }
    }

    /**
     * Generate the letter number for the survey permission letter.
     *
     * @param string|null $customNumber
     * @return string
     */
    protected function generateNomorSurat($customNumber = null)
    {
        return $this->generateNomorSuratUniversal('UN41.2/TI', $customNumber);
    }

    /**
     * Generate the PDF file for the survey permission letter.
     *
     * @param \App\Models\SuratIjinSurvey $surat
     * @param bool $isFinalApproval
     * @param string|null $qrType
     * @return string
     * @throws \Exception
     */
    protected function generateSuratFile(SuratIjinSurvey $surat, $isFinalApproval = false, $qrType = null)
    {
        if (!$surat->nomor_surat || !$surat->tanggal_surat) {
            throw new \Exception('Nomor surat dan tanggal surat wajib diisi');
        }

        if (!$surat->relationLoaded('mahasiswa')) {
            $surat->load('mahasiswa');
        }
        if ($surat->penandatangan_id && !$surat->relationLoaded('penandatangan')) {
            $surat->load('penandatangan');
        }
        if ($surat->penandatangan_kaprodi_id && !$surat->relationLoaded('penandatanganKaprodi')) {
            $surat->load('penandatanganKaprodi');
        }

        // Fetch active academic year
        $activeTahunAjaran = TahunAjaran::where('status_aktif', true)->first();
        if (!$activeTahunAjaran) {
            throw new \Exception('Tidak ada tahun ajaran aktif yang ditemukan');
        }

        // Calculate semester based on active academic year
        $tahunMasuk = 2000 + (int) substr($surat->mahasiswa->nim, 0, 2);
        $tahunAjaranStart = (int) explode('/', $activeTahunAjaran->tahun)[0];
        $semesterOffset = $activeTahunAjaran->semester === 'ganjil' ? 1 : 2;
        $semesterNumber = ($tahunAjaranStart - $tahunMasuk) * 2 + $semesterOffset;
        $semesterNumber = min(max($semesterNumber, 1), 14); // Cap between 1 and 14

        $jabatanPimpinan = $surat->jabatan_penandatangan ?? 'Pimpinan Jurusan PTIK';
        $jabatanKoordinator = $surat->jabatan_penandatangan_kaprodi ?? 'Koordinator Program Studi';

        // Generate QR codes
        $pimpinanQr = null;
        $kaprodiQr = null;
        if ($isFinalApproval) {
            if ($surat->verification_code_kaprodi && in_array($qrType, ['kaprodi', 'pimpinan'])) {
                $kaprodiVerificationUrl = route('document.verify', ['code' => $surat->verification_code_kaprodi]);
                $kaprodiQr = 'data:image/png;base64,' . base64_encode(
                    QrCode::format('png')
                        ->size(120)
                        ->margin(1)
                        ->errorCorrection('H')
                        ->generate($kaprodiVerificationUrl)
                );
            }
            if ($surat->verification_code_pimpinan && $qrType === 'pimpinan') {
                $pimpinanVerificationUrl = route('document.verify', ['code' => $surat->verification_code_pimpinan]);
                $pimpinanQr = 'data:image/png;base64,' . base64_encode(
                    QrCode::format('png')
                        ->size(120)
                        ->margin(1)
                        ->errorCorrection('H')
                        ->generate($pimpinanVerificationUrl)
                );
            }
        }

        try {
            $pdf = Pdf::loadView('admin.surat-ijin-survey.pdf', [
                'surat' => $surat,
                'semester_roman' => $this->getRomanSemester($semesterNumber),
                'show_qr_signature' => $isFinalApproval,
                'pimpinan_qr' => $pimpinanQr,
                'kaprodi_qr' => $kaprodiQr,
                'jabatanPimpinan' => $jabatanPimpinan,
                'jabatanKoordinator' => $jabatanKoordinator,
                'qr_type' => $qrType,
            ]);

            // SECURE FILENAME GENERATION
            // Sanitize NIM - remove any non-alphanumeric characters
            $nimSanitized = preg_replace('/[^a-zA-Z0-9]/', '', $surat->mahasiswa->nim ?? 'unknown');

            // Generate random number for security
            $randomNumber = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

            // Generate timestamp
            $timestamp = now()->format('Ymd_His');

            // Generate unique hash based on surat ID and current time
            $hash = substr(md5($surat->id . '_' . now()->timestamp), 0, 8);

            // Create secure filename with multiple security layers
            $filename = sprintf(
                'surat_ijin_survey_%s_%s_%s_%s.pdf',
                $nimSanitized,
                $timestamp,
                $randomNumber,
                $hash
            );

            // Create secure directory structure
            $yearMonth = now()->format('Y/m');
            $path = "surat-ijin-survey/{$yearMonth}/{$filename}";

            // Ensure the directory exists
            $directory = dirname($path);
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Store the PDF
            Storage::disk('public')->put($path, $pdf->output());

            return $path;
        } catch (\Exception $e) {
            Log::error('Failed to generate PDF: ' . $e->getMessage());
            throw new \Exception('Gagal menghasilkan file PDF: ' . $e->getMessage());
        }
    }

    /**
     * Convert semester number to Roman numeral format.
     *
     * @param int $number
     * @return string
     */
    protected function getRomanSemester($number)
    {
        $map = [
            1 => 'I (Satu)',
            2 => 'II (Dua)',
            3 => 'III (Tiga)',
            4 => 'IV (Empat)',
            5 => 'V (Lima)',
            6 => 'VI (Enam)',
            7 => 'VII (Tujuh)',
            8 => 'VIII (Delapan)',
            9 => 'IX (Sembilan)',
            10 => 'X (Sepuluh)',
            11 => 'XI (Sebelas)',
            12 => 'XII (Dua Belas)',
            13 => 'XIII (Tiga Belas)',
            14 => 'XIV (Empat Belas)',
        ];
        return $map[$number] ?? 'I (Satu)';
    }

    /**
     * Download the PDF file of the survey permission letter.
     *
     * @param \App\Models\SuratIjinSurvey $surat
     * @return \Illuminate\Http\Response
     */
    public function download(SuratIjinSurvey $surat)
    {
        if (!$surat->file_surat_path) {
            return response('File surat belum tersedia.', 404);
        }

        if (!Storage::disk('public')->exists($surat->file_surat_path)) {
            Log::error('File not found: ' . $surat->file_surat_path);
            return response('File surat tidak ditemukan.', 404);
        }

        $filePath = Storage::disk('public')->path($surat->file_surat_path);

        return response()->make(
            response()->download(
                $filePath,
                'Surat_Ijin_Survey_' . $surat->mahasiswa->nim . '.pdf'
            )
        );
    }

    /**
     * Download a supporting document.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function downloadPendukung($id)
    {
        try {
            $dokumen = DokumenPendukung::findOrFail($id);

            if (!Storage::disk('public')->exists($dokumen->path)) {
                Log::error('Supporting document not found: ' . $dokumen->path);
                return response('Dokumen pendukung tidak ditemukan.', 404);
            }

            $filePath = Storage::disk('public')->path($dokumen->path);
            return response()->make(response()->download($filePath, $dokumen->nama_asli));
        } catch (\Exception $e) {
            Log::error('Failed to download supporting document: ' . $e->getMessage());
            return response('Gagal mengunduh dokumen: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(SuratIjinSurvey $surat)
    {
        // Simpan mahasiswa_id sebelum record dihapus
        $mahasiswaId = $surat->mahasiswa_id;
        DB::beginTransaction();
        try {
            // Delete related files
            if ($surat->file_surat_path) {
                Storage::disk('public')->delete($surat->file_surat_path);
            }

            if ($surat->file_pendukung_path) {
                Storage::disk('public')->delete($surat->file_pendukung_path);
            }

            if ($surat->signature_path) {
                Storage::disk('public')->delete($surat->signature_path);
            }

            // Delete dokumen pendukung
            foreach ($surat->dokumenPendukung as $dokumen) {
                Storage::disk('public')->delete($dokumen->path);
                $dokumen->delete();
            }

            // Delete related records
            $surat->status()->delete();
            $surat->trackings()->delete();

            // Finally delete the surat
            $surat->delete();

            DB::commit();

            // Clear cache SETELAH commit berhasil
            app(SuratSubmissionService::class)->clearCacheOnDelete($mahasiswaId);

            // Clear notification badge cache
            if (class_exists('\App\Helpers\SuratNotificationHelper')) {
                SuratNotificationHelper::clearSuratCache('surat_ijin_survey');
            }

            return redirect()->route('admin.surat-ijin-survey.index')
                ->with('success', 'Surat ijin survey berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus surat: ' . $e->getMessage());
        }
    }
}