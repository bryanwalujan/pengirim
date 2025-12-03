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

            $verificationCode = SuratUsulanProposal::generateVerificationCode();

            // Create surat record
            $surat = SuratUsulanProposal::create([
                'pendaftaran_seminar_proposal_id' => $pendaftaran->id,
                'nomor_surat' => $nomorSurat,
                'file_surat' => '',
                'tanggal_surat' => now(),
                'verification_code' => $verificationCode,
                'status' => 'menunggu_ttd_kaprodi',
            ]);

            // Generate PDF
            $filePath = $this->generatePdf($pendaftaran, $surat);

            // Update surat with file path
            $surat->update(['file_surat' => $filePath]);

            // Update pendaftaran status
            $pendaftaran->update(['status' => 'menunggu_ttd_kaprodi']);

            DB::commit();

            Log::info('Surat usulan generated', [
                'pendaftaran_id' => $pendaftaran->id,
                'nomor_surat' => $nomorSurat,
                'is_custom' => !is_null($customNomorSurat),
            ]);

            return $surat;

        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            Log::error('Error generating surat', ['error' => $e->getMessage()]);
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
     * Generate PDF file
     */
    private function generatePdf(
        PendaftaranSeminarProposal $pendaftaran,
        SuratUsulanProposal $surat
    ): string {
        $data = [
            'pendaftaran' => $pendaftaran,
            'surat' => $surat,
            'nomor_surat' => $surat->nomor_surat,
            'tanggal_surat' => now()->translatedFormat('d F Y'),
            'verification_code' => $surat->verification_code,
        ];

        $pdf = Pdf::loadView('admin.pendaftaran-seminar-proposal.pdf.surat-usulan', $data);
        $pdf->setPaper('a4', 'portrait');

        $fileName = 'surat-usulan-' . $pendaftaran->user->nim . '-' . now()->format('YmdHis') . '.pdf';
        $filePath = 'surat-usulan/' . now()->format('Y/m') . '/' . $fileName;

        // Ensure directory exists
        $directory = dirname($filePath);
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        Storage::disk('public')->put($filePath, $pdf->output());

        return $filePath;
    }

    /**
     * Regenerate PDF with QR codes
     */
    public function regeneratePdfWithQr(SuratUsulanProposal $surat): void
    {
        $surat->load([
            'pendaftaranSeminarProposal.user',
            'pendaftaranSeminarProposal.dosenPembimbing',
            'pendaftaranSeminarProposal.komisiProposal',
            'pendaftaranSeminarProposal.proposalPembahas.dosen',
            'ttdKaprodiBy',
            'ttdKajurBy'
        ]);

        $data = [
            'pendaftaran' => $surat->pendaftaranSeminarProposal,
            'surat' => $surat,
            'nomor_surat' => $surat->nomor_surat,
            'tanggal_surat' => $surat->tanggal_surat->translatedFormat('d F Y'),
            'verification_code' => $surat->verification_code,
        ];

        $pdf = Pdf::loadView('admin.pendaftaran-seminar-proposal.pdf.surat-usulan', $data);
        $pdf->setPaper('a4', 'portrait');

        Storage::disk('public')->put($surat->file_surat, $pdf->output());

        Log::info('PDF regenerated with QR', [
            'surat_id' => $surat->id,
            'has_kaprodi_qr' => !empty($surat->qr_code_kaprodi),
            'has_kajur_qr' => !empty($surat->qr_code_kajur),
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