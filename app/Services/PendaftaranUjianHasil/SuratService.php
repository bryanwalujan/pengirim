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

    /**
     * Get prefix for nomor surat usulan skripsi
     */
    protected function getNomorSuratPrefix(): string
    {
        return 'UN41.2/TI';
    }

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
            // Generate atau gunakan nomor surat custom
            if ($customNomorSurat) {
                // Validate custom number format (hanya angka 1-4 digit)
                if (preg_match('#^\d{1,4}$#', $customNomorSurat)) {
                    $nomorSurat = $this->generateNomorSuratUniversal($this->getNomorSuratPrefix(), $customNomorSurat);
                } else {
                    // Jika format lengkap sudah diberikan
                    $nomorSurat = $customNomorSurat;
                }

                // Validate uniqueness
                if (!$this->validateNomorSuratUnique($nomorSurat)) {
                    throw new \Exception('Nomor surat sudah digunakan. Silakan gunakan nomor lain.');
                }
            } else {
                $nomorSurat = $this->generateNomorSuratUniversal($this->getNomorSuratPrefix());
            }

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
                'is_custom' => !is_null($customNomorSurat),
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
     * Get next nomor surat preview
     */
    public function getNextNomorSuratPreview(): array
    {
        try {
            $nextNomor = $this->getNextNomorSurat();
            $lastNomor = $this->getLastUsedNomorSurat();

            return [
                'success' => true,
                'next_nomor' => $nextNomor,
                'last_nomor' => $lastNomor,
                'prefix' => $this->getNomorSuratPrefix(),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting nomor surat preview', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'next_nomor' => 'Error',
                'last_nomor' => null,
                'prefix' => $this->getNomorSuratPrefix(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Validate custom nomor surat
     */
    public function validateCustomNomorSurat(string $customNumber): array
    {
        // Check format
        if (!preg_match('#^\d{1,4}$#', $customNumber)) {
            return [
                'valid' => false,
                'message' => 'Format nomor tidak valid. Masukkan 1-4 digit angka.',
            ];
        }

        // Generate full nomor surat
        $nomorSurat = $this->generateNomorSuratUniversal($this->getNomorSuratPrefix(), $customNumber);

        // Check uniqueness
        if (!$this->validateNomorSuratUnique($nomorSurat)) {
            return [
                'valid' => false,
                'message' => 'Nomor surat sudah digunakan.',
                'nomor_surat' => $nomorSurat,
            ];
        }

        return [
            'valid' => true,
            'message' => 'Nomor surat tersedia.',
            'nomor_surat' => $nomorSurat,
        ];
    }

    /**
     * Sign surat by Kaprodi
     */
    public function signByKaprodi(SuratUsulanSkripsi $surat, int $kaprodiId, ?array $overrideInfo = null): bool
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

            // Store override info if provided
            if ($overrideInfo && isset($overrideInfo['is_override']) && $overrideInfo['is_override']) {
                $surat->setOverrideInfo('kaprodi', $overrideInfo);
                $updateData['override_info'] = $surat->override_info;
            }

            $surat->update($updateData);

            // Update pendaftaran status
            $surat->pendaftaranUjianHasil->update(['status' => 'menunggu_ttd_kajur']);

            DB::commit();

            Log::info('Surat signed by Kaprodi', [
                'surat_id' => $surat->id,
                'kaprodi_id' => $kaprodiId,
                'is_override' => ($overrideInfo && isset($overrideInfo['is_override'])) ? $overrideInfo['is_override'] : false,
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
    public function signByKajur(SuratUsulanSkripsi $surat, int $kajurId, ?array $overrideInfo = null): bool
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

            // Store override info if provided
            if ($overrideInfo && isset($overrideInfo['is_override']) && $overrideInfo['is_override']) {
                $surat->setOverrideInfo('kajur', $overrideInfo);
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
                'is_override' => ($overrideInfo && isset($overrideInfo['is_override'])) ? $overrideInfo['is_override'] : false,
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
        $surat->load(['ttdKaprodiBy', 'ttdKajurBy']);

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
            'show_kaprodi_signature' => $surat->isKaprodiSigned(),
            'show_kajur_signature' => $surat->isKajurSigned(),
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
        $prefix = method_exists($this, 'getNomorSuratPrefix') 
            ? $this->getNomorSuratPrefix() 
            : 'UN41.2/TI';
            
        return $this->generateNomorSuratUniversal($prefix);
    }
}
