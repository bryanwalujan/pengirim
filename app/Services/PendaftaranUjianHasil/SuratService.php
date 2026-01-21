<?php

namespace App\Services\PendaftaranUjianHasil;

use App\Models\PendaftaranUjianHasil;
use App\Models\SuratUsulanSkripsi;
use App\Models\User;
use App\Traits\GeneratesNomorSurat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SuratService
{
    use GeneratesNomorSurat;

    protected string $suratPrefix = 'UN41.2/TI';

    /**
     * Check if surat can be generated
     */
    public function canGenerateSurat(PendaftaranUjianHasil $pendaftaran): array
    {
        if (!$pendaftaran->hasPengujiAssigned()) {
            return [
                'can_generate' => false,
                'message' => 'Penguji belum ditentukan.'
            ];
        }

        if ($pendaftaran->suratUsulanSkripsi) {
            return [
                'can_generate' => false,
                'message' => 'Surat sudah digenerate sebelumnya.'
            ];
        }

        if ($pendaftaran->status !== 'penguji_ditentukan') {
            return [
                'can_generate' => false,
                'message' => 'Status tidak valid untuk generate surat.'
            ];
        }

        return [
            'can_generate' => true,
            'message' => 'Surat dapat digenerate.'
        ];
    }

    /**
     * Generate surat usulan skripsi
     */
    public function generateSurat(PendaftaranUjianHasil $pendaftaran, ?string $customNomorSurat = null): SuratUsulanSkripsi
    {
        DB::beginTransaction();
        try {
            // Generate nomor surat
            $nomorSurat = $this->generateNomorSurat(
                SuratUsulanSkripsi::class,
                $this->suratPrefix,
                $customNomorSurat
            );

            // Generate verification code
            $verificationCode = SuratUsulanSkripsi::generateVerificationCode();

            // Create surat record
            $surat = SuratUsulanSkripsi::create([
                'pendaftaran_ujian_hasil_id' => $pendaftaran->id,
                'nomor_surat' => $nomorSurat,
                'tanggal_surat' => now(),
                'verification_code' => $verificationCode,
                'status' => 'menunggu_ttd_kaprodi',
            ]);

            // Update pendaftaran status
            $pendaftaran->update(['status' => 'menunggu_ttd_kaprodi']);

            DB::commit();

            Log::info('Surat Usulan Skripsi generated', [
                'pendaftaran_id' => $pendaftaran->id,
                'surat_id' => $surat->id,
                'nomor_surat' => $nomorSurat,
            ]);

            return $surat;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error generating surat', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Validate custom nomor surat
     */
    public function validateCustomNomorSurat(string $customNomor): array
    {
        // Check if numeric
        if (!is_numeric($customNomor)) {
            return [
                'valid' => false,
                'message' => 'Nomor surat harus berupa angka.'
            ];
        }

        // Generate full nomor surat to check uniqueness
        $fullNomorSurat = $this->generateNomorSurat(
            SuratUsulanSkripsi::class,
            $this->suratPrefix,
            $customNomor
        );

        // Check if already exists
        if (SuratUsulanSkripsi::where('nomor_surat', $fullNomorSurat)->exists()) {
            return [
                'valid' => false,
                'message' => 'Nomor surat sudah digunakan.'
            ];
        }

        return [
            'valid' => true,
            'message' => 'Nomor surat valid.',
            'full_nomor_surat' => $fullNomorSurat
        ];
    }

    /**
     * Sign surat by Kaprodi
     */
    public function signByKaprodi(SuratUsulanSkripsi $surat, int $kaprodiId, bool $isOverride = false, ?int $overrideBy = null): bool
    {
        DB::beginTransaction();
        try {
            // Generate QR code
            $qrCode = base64_encode(QrCode::format('png')
                ->size(100)
                ->generate($surat->verification_url));

            $updateData = [
                'ttd_kaprodi_by' => $kaprodiId,
                'ttd_kaprodi_at' => now(),
                'qr_code_kaprodi' => $qrCode,
                'status' => 'menunggu_ttd_kajur',
            ];

            if ($isOverride && $overrideBy) {
                $surat->setOverrideInfo('kaprodi', [
                    'original_signer_id' => $kaprodiId,
                    'override_by_id' => $overrideBy,
                    'reason' => 'Staff override'
                ]);
                $updateData['override_info'] = $surat->override_info;
            }

            $surat->update($updateData);

            // Update pendaftaran status
            $surat->pendaftaranUjianHasil->update(['status' => 'menunggu_ttd_kajur']);

            DB::commit();

            Log::info('Surat signed by Kaprodi', [
                'surat_id' => $surat->id,
                'kaprodi_id' => $kaprodiId,
                'is_override' => $isOverride,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error signing by Kaprodi', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Sign surat by Kajur
     */
    public function signByKajur(SuratUsulanSkripsi $surat, int $kajurId, bool $isOverride = false, ?int $overrideBy = null): bool
    {
        DB::beginTransaction();
        try {
            // Generate QR code
            $qrCode = base64_encode(QrCode::format('png')
                ->size(100)
                ->generate($surat->verification_url));

            $updateData = [
                'ttd_kajur_by' => $kajurId,
                'ttd_kajur_at' => now(),
                'qr_code_kajur' => $qrCode,
                'status' => 'selesai',
            ];

            if ($isOverride && $overrideBy) {
                $surat->setOverrideInfo('kajur', [
                    'original_signer_id' => $kajurId,
                    'override_by_id' => $overrideBy,
                    'reason' => 'Staff override'
                ]);
                $updateData['override_info'] = $surat->override_info;
            }

            $surat->update($updateData);

            // Update pendaftaran status
            $surat->pendaftaranUjianHasil->update(['status' => 'selesai']);

            // Generate final PDF
            $this->generatePdf($surat);

            DB::commit();

            Log::info('Surat signed by Kajur', [
                'surat_id' => $surat->id,
                'kajur_id' => $kajurId,
                'is_override' => $isOverride,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error signing by Kajur', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Generate PDF file
     */
    public function generatePdf(SuratUsulanSkripsi $surat): string
    {
        $pendaftaran = $surat->pendaftaranUjianHasil->load([
            'user',
            'dosenPembimbing1',
            'dosenPembimbing2',
            'pengujiUjianHasil.dosen',
        ]);

        $pdf = Pdf::loadView('pdf.surat-usulan-skripsi', [
            'surat' => $surat,
            'pendaftaran' => $pendaftaran,
            'penguji' => $pendaftaran->getPenguji(),
        ]);

        $filename = 'surat-usulan-skripsi/' . $surat->verification_code . '.pdf';
        
        Storage::disk('public')->put($filename, $pdf->output());

        $surat->update(['file_surat' => $filename]);

        return $filename;
    }

    /**
     * Get next nomor surat (for AJAX)
     */
    public function getNextNomorSurat(): string
    {
        return $this->generateNomorSurat(
            SuratUsulanSkripsi::class,
            $this->suratPrefix
        );
    }
}
