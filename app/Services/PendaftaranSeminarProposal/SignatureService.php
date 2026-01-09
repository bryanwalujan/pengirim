<?php

namespace App\Services\PendaftaranSeminarProposal;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\SuratUsulanProposal;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\PendaftaranSeminarProposal;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SignatureService
{
    public function __construct(
        protected SuratUsulanService $suratService
    ) {
    }

    /**
     * Sign as Kaprodi dengan QR Code
     * 
     * @param PendaftaranSeminarProposal $pendaftaran
     * @param User $currentUser User yang sedang login
     * @param int|null $defaultKaprodiId Default Kaprodi ID jika staff override
     * @return bool
     */
    public function signAsKaprodi(
        PendaftaranSeminarProposal $pendaftaran,
        User $currentUser,
        ?int $defaultKaprodiId = null
    ): bool {
        DB::beginTransaction();
        try {
            $surat = $pendaftaran->suratUsulan;

            if (!$surat) {
                throw new \Exception('Surat usulan belum dibuat');
            }

            if (!$surat->canBeSignedByKaprodi()) {
                throw new \Exception('Surat tidak dapat ditandatangani oleh Kaprodi saat ini');
            }

            $isKaprodi = $currentUser->isKoordinatorProdi();
            $canOverride = $currentUser->hasRole('staff');

            Log::info('SignatureService - signAsKaprodi', [
                'current_user_id' => $currentUser->id,
                'current_user_name' => $currentUser->name,
                'is_kaprodi' => $isKaprodi,
                'can_override' => $canOverride,
                'default_kaprodi_id' => $defaultKaprodiId,
            ]);

            // Determine penandatangan
            if ($isKaprodi) {
                // Kaprodi langsung TTD
                $penandatanganId = $currentUser->id;
                $penandatangan = $currentUser;
                $isOverride = false;
            } elseif ($canOverride) {
                // Staff override - gunakan default Kaprodi atau cari otomatis
                $penandatanganId = $defaultKaprodiId ?? $this->getDefaultKaprodiId();

                if (!$penandatanganId) {
                    throw new \Exception('Default Kaprodi tidak ditemukan di sistem. Silakan tambahkan dosen dengan jabatan Koordinator Program Studi.');
                }

                $penandatangan = User::find($penandatanganId);
                if (!$penandatangan) {
                    throw new \Exception('Penandatangan Kaprodi tidak ditemukan');
                }

                $isOverride = true;

                // Simpan informasi override
                $overrideInfo = $surat->override_info ?? [];
                $overrideInfo['kaprodi'] = [
                    'override_by_id' => $currentUser->id,
                    'override_by_name' => $currentUser->name,
                    'override_by_role' => 'staff',
                    'original_kaprodi_id' => $penandatanganId,
                    'original_kaprodi_name' => $penandatangan->name,
                    'override_at' => now()->toDateTimeString(),
                    'reason' => 'Staff override untuk persetujuan Kaprodi',
                ];
                $surat->override_info = $overrideInfo;

                Log::info('Staff override for Kaprodi', [
                    'staff_id' => $currentUser->id,
                    'staff_name' => $currentUser->name,
                    'kaprodi_id' => $penandatanganId,
                    'kaprodi_name' => $penandatangan->name,
                ]);
            } else {
                throw new \Exception('Anda tidak memiliki izin untuk menandatangani surat ini sebagai Kaprodi');
            }

            // Generate QR Code untuk Kaprodi
            $verificationUrl = $surat->verification_url;
            $qrCodeKaprodi = base64_encode(
                QrCode::format('png')
                    ->size(200)
                    ->margin(1)
                    ->errorCorrection('H')
                    ->generate($verificationUrl)
            );

            // Update surat dengan TTD Kaprodi
            $surat->update([
                'ttd_kaprodi_by' => $penandatanganId,
                'ttd_kaprodi_at' => now(),
                'qr_code_kaprodi' => $qrCodeKaprodi,
                'status' => 'menunggu_ttd_kajur',
                'override_info' => $surat->override_info,
            ]);

            // Regenerate PDF dengan QR Kaprodi
            $this->regeneratePdfWithSignature($surat);

            // Update status pendaftaran
            $pendaftaran->update([
                'status' => 'menunggu_ttd_kajur',
            ]);

            DB::commit();

            Log::info('Surat usulan signed by Kaprodi - SUCCESS', [
                'surat_id' => $surat->id,
                'pendaftaran_id' => $pendaftaran->id,
                'signed_by' => $penandatanganId,
                'signed_by_name' => $penandatangan->name,
                'is_override' => $isOverride,
                'action_by' => $currentUser->id,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error signing as Kaprodi: ' . $e->getMessage(), [
                'pendaftaran_id' => $pendaftaran->id,
                'user_id' => $currentUser->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Sign as Kajur dengan QR Code
     * 
     * @param PendaftaranSeminarProposal $pendaftaran
     * @param User $currentUser User yang sedang login
     * @param int|null $defaultKajurId Default Kajur ID jika staff override
     * @return bool
     */
    public function signAsKajur(
        PendaftaranSeminarProposal $pendaftaran,
        User $currentUser,
        ?int $defaultKajurId = null
    ): bool {
        DB::beginTransaction();
        try {
            $surat = $pendaftaran->suratUsulan;

            if (!$surat) {
                throw new \Exception('Surat usulan belum dibuat');
            }

            if (!$surat->canBeSignedByKajur()) {
                throw new \Exception('Surat tidak dapat ditandatangani oleh Kajur saat ini. Pastikan Kaprodi sudah TTD.');
            }

            $isKajur = $currentUser->isKetuaJurusan();
            $canOverride = $currentUser->hasRole('staff');

            Log::info('SignatureService - signAsKajur', [
                'current_user_id' => $currentUser->id,
                'current_user_name' => $currentUser->name,
                'current_user_jabatan' => $currentUser->jabatan,
                'is_kajur' => $isKajur,
                'can_override' => $canOverride,
                'default_kajur_id' => $defaultKajurId,
            ]);

            // Determine penandatangan
            if ($isKajur) {
                // Kajur langsung TTD
                $penandatanganId = $currentUser->id;
                $penandatangan = $currentUser;
                $isOverride = false;
            } elseif ($canOverride) {
                // Staff override - gunakan default Kajur atau cari otomatis
                $penandatanganId = $defaultKajurId ?? $this->getDefaultKajurId();

                if (!$penandatanganId) {
                    throw new \Exception('Default Kajur tidak ditemukan di sistem. Silakan tambahkan dosen dengan jabatan Ketua Jurusan.');
                }

                $penandatangan = User::find($penandatanganId);
                if (!$penandatangan) {
                    throw new \Exception('Penandatangan Kajur tidak ditemukan');
                }

                $isOverride = true;

                // Simpan informasi override
                $overrideInfo = $surat->override_info ?? [];
                $overrideInfo['kajur'] = [
                    'override_by_id' => $currentUser->id,
                    'override_by_name' => $currentUser->name,
                    'override_by_role' => 'staff',
                    'original_kajur_id' => $penandatanganId,
                    'original_kajur_name' => $penandatangan->name,
                    'override_at' => now()->toDateTimeString(),
                    'reason' => 'Staff override untuk persetujuan Kajur',
                ];
                $surat->override_info = $overrideInfo;

                Log::info('Staff override for Kajur', [
                    'staff_id' => $currentUser->id,
                    'staff_name' => $currentUser->name,
                    'kajur_id' => $penandatanganId,
                    'kajur_name' => $penandatangan->name,
                ]);
            } else {
                throw new \Exception('Anda tidak memiliki izin untuk menandatangani surat ini sebagai Kajur');
            }

            // Generate QR Code untuk Kajur
            $verificationUrl = $surat->verification_url;
            $qrCodeKajur = base64_encode(
                QrCode::format('png')
                    ->size(200)
                    ->margin(1)
                    ->errorCorrection('H')
                    ->generate($verificationUrl)
            );

            // Update surat dengan TTD Kajur
            $surat->update([
                'ttd_kajur_by' => $penandatanganId,
                'ttd_kajur_at' => now(),
                'qr_code_kajur' => $qrCodeKajur,
                'status' => 'selesai',
                'override_info' => $surat->override_info,
            ]);

            // Regenerate PDF dengan kedua QR
            $this->regeneratePdfWithSignature($surat);

            // Update status pendaftaran
            $pendaftaran->update([
                'status' => 'selesai',
            ]);

            DB::commit();

            Log::info('Surat usulan signed by Kajur - COMPLETED', [
                'surat_id' => $surat->id,
                'pendaftaran_id' => $pendaftaran->id,
                'signed_by' => $penandatanganId,
                'signed_by_name' => $penandatangan->name,
                'is_override' => $isOverride,
                'action_by' => $currentUser->id,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error signing as Kajur: ' . $e->getMessage(), [
                'pendaftaran_id' => $pendaftaran->id,
                'user_id' => $currentUser->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Get default Kaprodi ID
     */
    public function getDefaultKaprodiId(): ?int
    {
        $kaprodi = User::whereHas('roles', function ($q) {
            $q->where('name', 'dosen');
        })
            ->where(function ($query) {
                $query->whereRaw('LOWER(jabatan) LIKE ?', ['%koordinator%'])
                    ->orWhereRaw('LOWER(jabatan) LIKE ?', ['%kaprodi%'])
                    ->orWhereRaw('LOWER(jabatan) LIKE ?', ['%korprodi%']);
            })
            ->first();

        if ($kaprodi) {
            Log::info('Default Kaprodi found', [
                'kaprodi_id' => $kaprodi->id,
                'kaprodi_name' => $kaprodi->name,
                'jabatan' => $kaprodi->jabatan,
            ]);
        } else {
            Log::warning('No default Kaprodi found in system');
        }

        return $kaprodi?->id;
    }

    /**
     * Get default Kajur ID
     */
    public function getDefaultKajurId(): ?int
    {
        $kajur = User::whereHas('roles', function ($q) {
            $q->where('name', 'dosen');
        })
            ->where(function ($query) {
                $query->whereRaw('LOWER(jabatan) LIKE ?', ['%ketua jurusan%'])
                    ->orWhereRaw('LOWER(jabatan) LIKE ?', ['%kajur%'])
                    ->orWhereRaw('LOWER(jabatan) LIKE ?', ['%pimpinan jurusan%'])
                    ->orWhereRaw('LOWER(jabatan) LIKE ?', ['%kepala jurusan%']);
            })
            ->first();

        if ($kajur) {
            Log::info('Default Kajur found', [
                'kajur_id' => $kajur->id,
                'kajur_name' => $kajur->name,
                'jabatan' => $kajur->jabatan,
            ]);
        } else {
            Log::warning('No default Kajur found in system');
        }

        return $kajur?->id;
    }

    /**
     * Regenerate PDF dengan signature QR codes
     */
    protected function regeneratePdfWithSignature(SuratUsulanProposal $surat): void
    {
        $surat->load([
            'pendaftaranSeminarProposal.user',
            'pendaftaranSeminarProposal.komisiProposal',
            'pendaftaranSeminarProposal.dosenPembimbing',
            'pendaftaranSeminarProposal.proposalPembahas.dosen',
            'ttdKaprodiBy',
            'ttdKajurBy',
        ]);

        $pendaftaran = $surat->pendaftaranSeminarProposal;

        // Prepare QR codes (data URL format)
        $qrKaprodi = $surat->qr_code_kaprodi
            ? 'data:image/png;base64,' . $surat->qr_code_kaprodi
            : null;

        $qrKajur = $surat->qr_code_kajur
            ? 'data:image/png;base64,' . $surat->qr_code_kajur
            : null;

        // Generate PDF
        $pdf = Pdf::loadView('admin.pendaftaran-seminar-proposal.surat-usulan-pdf', [
            'surat' => $surat,
            'pendaftaran' => $pendaftaran,
            'mahasiswa' => $pendaftaran->user,
            'komisi' => $pendaftaran->komisiProposal,
            'pembimbing' => $pendaftaran->dosenPembimbing,
            'pembahas' => $pendaftaran->proposalPembahas,
            'qr_kaprodi' => $qrKaprodi,
            'qr_kajur' => $qrKajur,
            'verification_code' => $surat->verification_code,
            'show_kaprodi_signature' => $surat->isKaprodiSigned(),
            'show_kajur_signature' => $surat->isKajurSigned(),
        ])->setPaper('a4', 'portrait');

        // Save PDF
        $oldFilePath = $surat->file_surat;

        $nimSanitized = preg_replace('/[^a-zA-Z0-9]/', '', $pendaftaran->user->nim ?? 'unknown');
        $timestamp = now()->format('Ymd_His');
        $statusSuffix = $surat->isFullySigned() ? 'final' : ($surat->isKaprodiSigned() ? 'kaprodi' : 'draft');
        $filename = sprintf('surat_usulan_%s_%s_%s.pdf', $nimSanitized, $timestamp, $statusSuffix);

        $yearMonth = now()->format('Y/m');
        $path = "surat_usulan_proposal/{$yearMonth}/{$filename}";
        $directory = dirname($path);

        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        Storage::disk('public')->put($path, $pdf->output());

        // Update file path
        $surat->update(['file_surat' => $path]);

        // Delete old file
        if ($oldFilePath && $oldFilePath !== $path && Storage::disk('public')->exists($oldFilePath)) {
            Storage::disk('public')->delete($oldFilePath);
        }

        Log::info('PDF regenerated with signature', [
            'surat_id' => $surat->id,
            'new_path' => $path,
            'old_path' => $oldFilePath,
            'status' => $statusSuffix,
        ]);
    }
}