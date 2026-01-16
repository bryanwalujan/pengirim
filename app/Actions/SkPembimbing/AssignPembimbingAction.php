<?php
// filepath: app/Actions/SkPembimbing/AssignPembimbingAction.php

namespace App\Actions\SkPembimbing;

use App\Models\PengajuanSkPembimbing;
use App\Models\User;
use App\Services\SkPembimbing\SkPembimbingPdfService;
use App\Traits\GeneratesNomorSurat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignPembimbingAction
{
    use GeneratesNomorSurat;

    public function __construct(
        protected SkPembimbingPdfService $pdfService
    ) {
    }

    public function execute(PengajuanSkPembimbing $pengajuan, User $staff, array $data): array
    {
        // Block only finished or rejected pengajuan - all other statuses can be assigned
        $blockedStatuses = [
            PengajuanSkPembimbing::STATUS_MENUNGGU_TTD_KORPRODI,
            PengajuanSkPembimbing::STATUS_MENUNGGU_TTD_KAJUR,
            PengajuanSkPembimbing::STATUS_SELESAI,
            PengajuanSkPembimbing::STATUS_DITOLAK,
        ];
        
        if (in_array($pengajuan->status, $blockedStatuses)) {
            return ['success' => false, 'message' => 'Pengajuan tidak dapat diassign pembimbing.'];
        }

        return DB::transaction(function () use ($pengajuan, $staff, $data) {
            // Generate nomor surat
            $customNumber = null;
            if (isset($data['nomor_surat_type']) && $data['nomor_surat_type'] === 'custom' && isset($data['custom_nomor_surat'])) {
                $customNumber = (int) $data['custom_nomor_surat'];
            }

            $nomorSurat = $this->generateNomorSuratUniversal('UN41.2/TI', $customNumber);

            $pengajuan->update([
                'dosen_pembimbing_1_id' => $data['dosen_pembimbing_1_id'],
                'dosen_pembimbing_2_id' => $data['dosen_pembimbing_2_id'] ?? null,
                'catatan_staff' => $data['catatan_staff'] ?? null,
                'nomor_surat' => $nomorSurat,
                'tanggal_surat' => $data['tanggal_surat'],
                'status' => PengajuanSkPembimbing::STATUS_MENUNGGU_TTD_KORPRODI, // Langsung ke TTD Korprodi
                'ps_assigned_by' => $staff->id,
                'ps_assigned_at' => now(),
                'verified_by' => $pengajuan->verified_by ?? $staff->id,
                'verified_at' => $pengajuan->verified_at ?? now(),
            ]);

            // Generate initial PDF (draft)
            $filePath = $this->pdfService->generateInitialPdf($pengajuan);
            $pengajuan->update(['file_surat_sk' => $filePath]);

            Log::info('Pembimbing assigned and initial PDF generated', [
                'pengajuan_id' => $pengajuan->id,
                'nomor_surat' => $nomorSurat,
                'file_path' => $filePath,
            ]);

            return ['success' => true, 'message' => 'Pembimbing berhasil ditentukan. Menunggu TTD Koordinator Prodi.'];
        });
    }
}