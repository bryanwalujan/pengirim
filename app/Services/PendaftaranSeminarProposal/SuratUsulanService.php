<?php

namespace App\Services\PendaftaranSeminarProposal;

use App\Models\PendaftaranSeminarProposal;
use App\Models\SuratUsulanProposal;
use App\Traits\GeneratesNomorSurat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class SuratUsulanService
{
    use GeneratesNomorSurat;

    /**
     * Get prefix for nomor surat usulan proposal
     */
    protected function getNomorSuratPrefix(): string
    {
        return 'UN41.2/TI'; // Sesuaikan prefix jika berbeda
    }

    /**
     * Generate surat usulan dengan nomor surat custom atau auto
     */
    public function generateSurat(
        PendaftaranSeminarProposal $pendaftaran,
        ?string $customNomorSurat = null
    ): SuratUsulanProposal {
        DB::beginTransaction();
        try {
            $pendaftaran->load([
                'user',
                'dosenPembimbing',
                'komisiProposal',
                'proposalPembahas.dosen'
            ]);

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

            // ✅ Verification code: gunakan yang lama jika ada, atau generate baru
            $existingSurat = SuratUsulanProposal::where('pendaftaran_seminar_proposal_id', $pendaftaran->id)->first();
            $verificationCode = $existingSurat 
                ? $existingSurat->verification_code 
                : SuratUsulanProposal::generateVerificationCode();

            // Create or Update surat record
            // Menggunakan updateOrCreate untuk menghindari Duplicate Entry error
            $surat = SuratUsulanProposal::updateOrCreate(
                ['pendaftaran_seminar_proposal_id' => $pendaftaran->id],
                [
                    'nomor_surat' => $nomorSurat,
                    'file_surat' => '', // Temporary, akan diupdate setelah PDF dibuat
                    'tanggal_surat' => now(),
                    'verification_code' => $verificationCode,
                    'status' => 'menunggu_ttd_kaprodi',
                ]
            );

            // ✅ Generate initial PDF (tanpa signature)
            $filePath = $this->generateInitialPdf($pendaftaran, $surat);

            // Update surat with file path
            $surat->update(['file_surat' => $filePath]);

            // Update pendaftaran status
            $pendaftaran->update(['status' => 'menunggu_ttd_kaprodi']);

            DB::commit();

            Log::info('Surat usulan generated - SUCCESS', [
                'pendaftaran_id' => $pendaftaran->id,
                'surat_id' => $surat->id,
                'nomor_surat' => $nomorSurat,
                'verification_code' => $verificationCode,
                'is_custom' => !is_null($customNomorSurat),
            ]);

            return $surat;

        } catch (\Exception $e) {
            DB::rollBack();

            // Cleanup jika ada file yang sudah dibuat
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            Log::error('Error generating surat - FAILED', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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
     * ✅ NEW: Generate Initial PDF (Draft - Tanpa Signature)
     */
    private function generateInitialPdf(
        PendaftaranSeminarProposal $pendaftaran,
        SuratUsulanProposal $surat
    ): string {
        Log::info('Generating initial PDF (draft)', [
            'surat_id' => $surat->id,
            'pendaftaran_id' => $pendaftaran->id,
        ]);

        // Prepare data untuk PDF
        $data = [
            'surat' => $surat,
            'pendaftaran' => $pendaftaran,
            'mahasiswa' => $pendaftaran->user,
            'komisi' => $pendaftaran->komisiProposal,
            'pembimbing' => $pendaftaran->dosenPembimbing,
            'pembahas' => $pendaftaran->proposalPembahas,

            // ✅ Signature flags - Semua false untuk draft
            'show_kaprodi_signature' => false,
            'show_kajur_signature' => false,
            'qr_kaprodi' => null,
            'qr_kajur' => null,

            'verification_code' => $surat->verification_code,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('admin.pendaftaran-seminar-proposal.surat-usulan-pdf', $data)
            ->setPaper('a4', 'portrait');

        // Generate file path
        $nimSanitized = preg_replace('/[^a-zA-Z0-9]/', '', $pendaftaran->user->nim ?? 'unknown');
        $timestamp = now()->format('Ymd_His');
        $fileName = sprintf('surat_usulan_%s_%s_draft.pdf', $nimSanitized, $timestamp);

        $yearMonth = now()->format('Y/m');
        $filePath = "surat_usulan_proposal/{$yearMonth}/{$fileName}";

        // Ensure directory exists
        $directory = dirname($filePath);
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // Save PDF
        Storage::disk('public')->put($filePath, $pdf->output());

        Log::info('Initial PDF generated - SUCCESS', [
            'surat_id' => $surat->id,
            'path' => $filePath,
            'size' => Storage::disk('public')->size($filePath),
        ]);

        return $filePath;
    }

    /**
     * ✅ UPDATED: Regenerate PDF dengan QR codes (dipanggil dari SignatureService)
     */
    public function regeneratePdfWithQr(SuratUsulanProposal $surat): void
    {
        // Load all required relations
        $surat->load([
            'pendaftaranSeminarProposal.user',
            'pendaftaranSeminarProposal.dosenPembimbing',
            'pendaftaranSeminarProposal.komisiProposal',
            'pendaftaranSeminarProposal.proposalPembahas.dosen',
            'ttdKaprodiBy',
            'ttdKajurBy'
        ]);

        $pendaftaran = $surat->pendaftaranSeminarProposal;

        Log::info('Regenerating PDF with QR codes', [
            'surat_id' => $surat->id,
            'kaprodi_signed' => $surat->isKaprodiSigned(),
            'kajur_signed' => $surat->isKajurSigned(),
        ]);

        // ✅ Prepare QR codes (data URL format untuk DomPDF)
        $qrKaprodi = $surat->qr_code_kaprodi
            ? 'data:image/png;base64,' . $surat->qr_code_kaprodi
            : null;

        $qrKajur = $surat->qr_code_kajur
            ? 'data:image/png;base64,' . $surat->qr_code_kajur
            : null;

        // Prepare data untuk PDF
        $data = [
            'surat' => $surat,
            'pendaftaran' => $pendaftaran,
            'mahasiswa' => $pendaftaran->user,
            'komisi' => $pendaftaran->komisiProposal,
            'pembimbing' => $pendaftaran->dosenPembimbing,
            'pembahas' => $pendaftaran->proposalPembahas,

            // ✅ QR Codes
            'qr_kaprodi' => $qrKaprodi,
            'qr_kajur' => $qrKajur,

            // ✅ Signature visibility flags
            'show_kaprodi_signature' => $surat->isKaprodiSigned(),
            'show_kajur_signature' => $surat->isKajurSigned(),

            'verification_code' => $surat->verification_code,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('admin.pendaftaran-seminar-proposal.surat-usulan-pdf', $data)
            ->setPaper('a4', 'portrait');

        // ✅ Save dengan nama file yang berbeda berdasarkan status
        $oldFilePath = $surat->file_surat;

        $nimSanitized = preg_replace('/[^a-zA-Z0-9]/', '', $pendaftaran->user->nim ?? 'unknown');
        $timestamp = now()->format('Ymd_His');

        // Tentukan suffix berdasarkan status signature
        if ($surat->isFullySigned()) {
            $statusSuffix = 'final';
        } elseif ($surat->isKaprodiSigned()) {
            $statusSuffix = 'kaprodi_signed';
        } else {
            $statusSuffix = 'draft';
        }

        $fileName = sprintf('surat_usulan_%s_%s_%s.pdf', $nimSanitized, $timestamp, $statusSuffix);

        $yearMonth = now()->format('Y/m');
        $filePath = "surat_usulan_proposal/{$yearMonth}/{$fileName}";

        // Ensure directory exists
        $directory = dirname($filePath);
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // Save new PDF
        Storage::disk('public')->put($filePath, $pdf->output());

        // Update file path di database
        $surat->update(['file_surat' => $filePath]);

        // ✅ Delete old file (jika berbeda)
        if ($oldFilePath && $oldFilePath !== $filePath && Storage::disk('public')->exists($oldFilePath)) {
            Storage::disk('public')->delete($oldFilePath);
            Log::info('Old PDF deleted', ['path' => $oldFilePath]);
        }

        Log::info('PDF regenerated with QR - SUCCESS', [
            'surat_id' => $surat->id,
            'new_path' => $filePath,
            'old_path' => $oldFilePath,
            'status' => $statusSuffix,
            'has_kaprodi_qr' => !empty($surat->qr_code_kaprodi),
            'has_kajur_qr' => !empty($surat->qr_code_kajur),
            'file_size' => Storage::disk('public')->size($filePath),
        ]);
    }

    /**
     * Validate if surat can be generated
     */
    public function canGenerateSurat(PendaftaranSeminarProposal $pendaftaran): array
    {
        if (!$pendaftaran->isPembahasDitentukan()) {
            return [
                'can_generate' => false,
                'message' => 'Pembahas belum ditentukan.'
            ];
        }

        if ($pendaftaran->status !== 'pembahas_ditentukan') {
            return [
                'can_generate' => false,
                'message' => 'Status tidak valid untuk generate surat.'
            ];
        }

        if ($pendaftaran->suratUsulan) {
            return [
                'can_generate' => false,
                'message' => 'Surat usulan sudah digenerate.'
            ];
        }

        return [
            'can_generate' => true,
            'message' => 'Surat dapat digenerate.'
        ];
    }
}