<?php

namespace App\Actions\BeritaAcaraUjianHasil;

use App\Enums\BeritaAcaraStatus;
use App\Models\BeritaAcaraUjianHasil;
use App\Models\User;
use App\Services\PelaksanaanUjianHasilService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @deprecated Tidak digunakan dalam workflow baru.
 * 
 * Workflow baru: Penguji → Sekretaris Panitia (Korprodi) → Ketua Panitia (Dekan) → Selesai
 * 
 * Action ini dipertahankan hanya untuk backward compatibility dengan data existing
 * yang mungkin masih dalam status 'menunggu_ttd_ketua'.
 * 
 * CATATAN: Berita acara ujian hasil TIDAK memerlukan step Ketua Penguji lagi.
 * Setelah semua penguji menandatangani, status langsung ke menunggu_ttd_panitia_sekretaris.
 */
class FillByKetuaAction
{
    public function __construct(
        private readonly PelaksanaanUjianHasilService $pelaksanaanUjianHasilService
    ) {}

    public function execute(
        User $ketua,
        BeritaAcaraUjianHasil $beritaAcara,
        array $validated
    ): array {
        try {
            DB::beginTransaction();

            Log::info('Sign BA Ujian Hasil by ketua - START', [
                'ba_id' => $beritaAcara->id,
                'ketua_id' => $ketua->id,
            ]);

            // Update BA data - setelah Ketua sign, transisi ke menunggu TTD Panitia Sekretaris
            $updateData = [
                'catatan_tambahan' => $validated['catatan_tambahan'] ?? null,
                'diisi_oleh_ketua_id' => $ketua->id,
                'diisi_ketua_at' => now(),
                'ttd_ketua_penguji_by' => $ketua->id,
                'ttd_ketua_penguji_at' => now(),
                'status' => BeritaAcaraStatus::MENUNGGU_TTD_PANITIA_SEKRETARIS->value,
            ];

            // Update berita acara
            $beritaAcara->update($updateData);

            Log::info('BA Ujian Hasil updated successfully', [
                'ba_id' => $beritaAcara->id,
                'new_status' => $beritaAcara->status,
            ]);

            // Generate PDF (draft version - akan diupdate lagi setelah panitia sign)
            try {
                $pdfPath = $this->pelaksanaanUjianHasilService->generateBeritaAcaraPdf($beritaAcara);

                if ($pdfPath) {
                    $beritaAcara->update(['file_path' => $pdfPath]);
                    Log::info('PDF generated successfully', [
                        'ba_id' => $beritaAcara->id,
                        'pdf_path' => $pdfPath,
                    ]);
                } else {
                    Log::warning('PDF generation returned null', [
                        'ba_id' => $beritaAcara->id,
                    ]);
                }
            } catch (Exception $e) {
                Log::error('PDF generation failed', [
                    'ba_id' => $beritaAcara->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                // Don't fail the whole transaction if PDF fails
            }

            DB::commit();

            Log::info('Sign BA Ujian Hasil by ketua - SUCCESS', [
                'ba_id' => $beritaAcara->id,
            ]);

            return [
                'success' => true,
                'message' => 'Berita acara berhasil ditandatangani! Menunggu tanda tangan Sekretaris Panitia (Korprodi).',
            ];

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Sign by ketua FAILED', [
                'ba_id' => $beritaAcara->id,
                'ketua_id' => $ketua->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal menyimpan data berita acara. Error: '.$e->getMessage(),
            ];
        }
    }
}
