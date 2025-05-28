<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\StatusSurat;
use Illuminate\Http\Request;
use App\Models\TrackingSurat;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DokumenPendukung;
use App\Models\SuratIjinSurvey;
use Illuminate\Support\Facades\DB;
use App\Traits\BaseSuratController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Notifications\SuratTakenNotification;
use App\Http\Requests\UpdateSuratIjinSurveyRequest;
use App\Notifications\SuratNeedApprovalNotification;

class AdminSuratIjinSurveyController extends DocumentController
{
    use BaseSuratController;

    protected function getNomorSuratPrefix()
    {
        return 'UN41.2/TI';
    }

    public function index(Request $request)
    {
        $status = $request->input('status', 'diajukan');
        $search = $request->input('search');

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

    public function show(SuratIjinSurvey $surat)
    {
        $surat->load([
            'mahasiswa',
            'status',
            'trackings' => fn($query) => $query->latest(),
            'penandatangan',
            'penandatanganKaprodi',
        ]);

        $penandatangans = User::role('dosen')->get();

        return view('admin.surat-ijin-survey.show', compact('surat', 'penandatangans'));
    }

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

        if ($user->hasRole('staff') && !array_key_exists($validated['status'], $allowedTransitions['staff'])) {
            return back()->with('error', 'Anda tidak memiliki izin untuk melakukan aksi ini');
        }

        if (!in_array($surat->status, $allowedTransitions['staff'][$validated['status']] ?? [])) {
            return back()->with('error', 'Transisi status tidak valid');
        }

        DB::beginTransaction();
        try {
            if ($validated['status'] === 'diproses' && $user->hasRole('staff')) {
                if (!empty($validated['nomor_surat'])) {
                    $manualNumber = trim($validated['nomor_surat']);

                    if (preg_match('#^\d{1,4}$#', $manualNumber)) {
                        $proposedNumber = $this->generateNomorSurat($manualNumber);
                    } elseif (!$this->validateNomorSuratFormat($manualNumber, $this->getNomorSuratPrefix())) {
                        return back()->with('error', 'Format nomor surat tidak valid. Contoh: 0001/UN41.2/TI/2024');
                    } else {
                        $proposedNumber = $manualNumber;
                    }

                    $existingSurat = SuratIjinSurvey::where('nomor_surat', $proposedNumber)
                        ->where('id', '!=', $surat->id)
                        ->first();

                    if ($existingSurat) {
                        DB::rollBack();
                        return back()->with('error', 'Nomor surat sudah digunakan!')->withInput();
                    }

                    $validated['nomor_surat'] = $proposedNumber;
                } else {
                    $validated['nomor_surat'] = $this->generateNomorSurat();
                }

                $surat->update([
                    'nomor_surat' => $validated['nomor_surat'],
                    'tanggal_surat' => now(),
                ]);

                $filePath = $this->generateSuratFile($surat, false);
                $surat->update(['file_surat_path' => $filePath]);

                $dosenKaprodi = User::role('dosen')
                    ->where('jabatan', 'like', '%Koordinator Program Studi%')
                    ->get();
                foreach ($dosenKaprodi as $dosen) {
                    $dosen->notify(new SuratNeedApprovalNotification($surat));
                }
            }

            if ($validated['status'] === 'siap_diambil' && $user->hasRole('staff')) {
                $surat->mahasiswa->notify(new SuratNeedApprovalNotification($surat));
            }

            if ($validated['status'] === 'disetujui' && $user->hasRole('dosen')) {
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

                $filePath = $this->generateSuratFile($surat, true, 'kaprodi');
                $surat->update(['file_surat_path' => $filePath]);

                $dosenPimpinan = User::role('dosen')
                    ->where('jabatan', 'like', '%Pimpinan Jurusan PTIK%')
                    ->get();
                foreach ($dosenPimpinan as $dosen) {
                    $dosen->notify(new SuratNeedApprovalNotification($surat));
                }
            }

            StatusSurat::updateOrCreate(
                ['surat_type' => get_class($surat), 'surat_id' => $surat->id],
                [
                    'status' => $validated['status'],
                    'catatan_admin' => $validated['catatan_admin'],
                    'updated_by' => $user->id,
                ]
            );

            TrackingSurat::create([
                'surat_type' => get_class($surat),
                'surat_id' => $surat->id,
                'aksi' => $validated['status'],
                'keterangan' => $validated['catatan_admin'],
                'mahasiswa_id' => $surat->mahasiswa_id,
            ]);

            DB::commit();

            return redirect()->route('admin.surat-ijin-survey.show', $surat->id)
                ->with('success', 'Status surat berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    protected function generateSuratFile(SuratIjinSurvey $surat, $isFinalApproval = false, $qrType = null)
    {
        if (!$surat->nomor_surat || !$surat->tanggal_surat) {
            throw new \Exception('Nomor surat dan tanggal surat wajib diisi');
        }

        if (!$surat->relationLoaded('mahasiswa')) {
            $surat->load('mahasiswa');
        }
        if (!$surat->relationLoaded('penandatangan') && $surat->penandatangan_id) {
            $surat->load('penandatangan');
        }
        if (!$surat->relationLoaded('penandatanganKaprodi') && $surat->penandatangan_kaprodi_id) {
            $surat->load('penandatanganKaprodi');
        }

        $jabatanPimpinan = $surat->jabatan_penandatangan ?? 'Pimpinan Jurusan PTIK';
        $jabatanKoordinator = $surat->jabatan_penandatangan_kaprodi ?? 'Koordinator Program Studi';

        $pimpinanQr = null;
        $kaprodiQr = null;
        if ($isFinalApproval) {
            if ($surat->verification_code_kaprodi && ($qrType === 'kaprodi' || $qrType === 'pimpinan')) {
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

        $pdf = Pdf::loadView('admin.surat-ijin-survey.pdf', [
            'surat' => $surat,
            'show_qr_signature' => $isFinalApproval,
            'pimpinan_qr' => $pimpinanQr,
            'kaprodi_qr' => $kaprodiQr,
            'jabatanPimpinan' => $jabatanPimpinan,
            'jabatanKoordinator' => $jabatanKoordinator,
            'qr_type' => $qrType,
        ]);

        $filename = 'surat_ijin_survey_' . $surat->mahasiswa->nim . '_' . now()->format('YmdHis') . '.pdf';
        $path = 'surat-ijin-survey/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    public function download(SuratIjinSurvey $surat)
    {
        if (!$surat->file_surat_path) {
            return back()->with('error', 'File surat belum tersedia.');
        }

        if (!Storage::disk('public')->exists($surat->file_surat_path)) {
            return back()->with('error', 'File surat tidak ditemukan.');
        }

        $filePath = Storage::disk('public')->path($surat->file_surat_path);

        return response()->download(
            $filePath,
            'Surat_Ijin_Survey_' . $surat->mahasiswa->nim . '.pdf'
        );
    }
}