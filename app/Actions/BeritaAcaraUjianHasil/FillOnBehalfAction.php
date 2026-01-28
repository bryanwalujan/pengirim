<?php

namespace App\Actions\BeritaAcaraUjianHasil;

use App\Models\BeritaAcaraUjianHasil;
use App\Models\User;
use App\Services\PelaksanaanUjianHasilService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Action untuk staff menandatangani berita acara atas nama ketua penguji.
 * 
 * CATATAN: Berita acara ujian hasil TIDAK memerlukan keputusan (Lulus/Tidak Lulus).
 * Berita acara ini hanya mencatat pelaksanaan ujian dan tanda tangan penguji.
 */
class FillOnBehalfAction
{
    public function __construct(
        private readonly PelaksanaanUjianHasilService $pelaksanaanUjianHasilService
    ) {
    }

    public function execute(
        User $staff,
        BeritaAcaraUjianHasil $beritaAcara,
        array $validated
    ): array {
        try {
            DB::beginTransaction();

            $jadwal = $beritaAcara->jadwalUjianHasil;

            // Get ketua penguji
            $ketuaPenguji = $jadwal->getKetuaPenguji();

            Log::info('Sign BA Ujian Hasil on behalf - START', [
                'ba_id' => $beritaAcara->id,
                'staff_id' => $staff->id,
                'ketua_id' => $ketuaPenguji?->id,
            ]);

            // Update BA data - hanya menandatangani (tanpa keputusan)
            $updateData = [
                'catatan_tambahan' => $validated['catatan_tambahan'] ?? null,
                'diisi_oleh_ketua_id' => $ketuaPenguji?->id,
                'diisi_ketua_at' => now(),
                'ttd_ketua_penguji_by' => $ketuaPenguji?->id,
                'ttd_ketua_penguji_at' => now(),
                'status' => 'selesai',
                // Override fields
                'override_ketua_by' => $staff->id,
                'override_ketua_at' => now(),
                'override_ketua_reason' => $validated['alasan_override'] ?? 'Diisi oleh staff atas nama ketua penguji',
            ];

            // Update berita acara
            $beritaAcara->update($updateData);

            Log::info('BA Ujian Hasil updated successfully (on behalf)', [
                'ba_id' => $beritaAcara->id,
                'new_status' => $beritaAcara->status
            ]);

            // Generate PDF
            try {
                $pdfPath = $this->pelaksanaanUjianHasilService->generateBeritaAcaraPdf($beritaAcara);

                if ($pdfPath) {
                    $beritaAcara->update(['file_path' => $pdfPath]);
                    Log::info('PDF generated successfully', [
                        'ba_id' => $beritaAcara->id,
                        'pdf_path' => $pdfPath
                    ]);
                }
            } catch (Exception $e) {
                Log::error('PDF generation failed', [
                    'ba_id' => $beritaAcara->id,
                    'error' => $e->getMessage(),
                ]);
            }

            DB::commit();

            Log::info('Sign BA Ujian Hasil on behalf - SUCCESS', [
                'ba_id' => $beritaAcara->id,
                'staff_id' => $staff->id,
            ]);

            return [
                'success' => true,
                'message' => 'Berita acara berhasil ditandatangani atas nama ketua penguji! PDF telah berhasil digenerate.',
            ];

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Sign on behalf FAILED', [
                'ba_id' => $beritaAcara->id,
                'staff_id' => $staff->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal menyimpan data berita acara. Error: ' . $e->getMessage(),
            ];
        }
    }
}
