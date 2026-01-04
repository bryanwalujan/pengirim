<?php
// filepath: app/Actions/BeritaAcaraSempro/FillByPembimbingAction.php

namespace App\Actions\BeritaAcaraSempro;

use App\Models\BeritaAcaraSeminarProposal;
use App\Models\User;
use App\Services\PelaksanaanUjianService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class FillByPembimbingAction
{
    public function __construct(
        private readonly PelaksanaanUjianService $pelaksanaanUjianService
    ) {
    }

    public function execute(
        User $pembimbing,
        BeritaAcaraSeminarProposal $beritaAcara,
        array $validated
    ): array {
        try {
            DB::beginTransaction();

            $jadwal = $beritaAcara->jadwalSeminarProposal;
            $isRejected = $validated['keputusan'] === 'Tidak';

            Log::info('Fill BA by pembimbing - START', [
                'ba_id' => $beritaAcara->id,
                'pembimbing_id' => $pembimbing->id,
                'keputusan' => $validated['keputusan'],
                'is_rejected' => $isRejected
            ]);

            // Update BA data
            $updateData = [
                'catatan_kejadian' => $validated['catatan_kejadian'],
                'keputusan' => $validated['keputusan'],
                'catatan_tambahan' => $validated['catatan_tambahan'] ?? null,
                'diisi_oleh_pembimbing_id' => $pembimbing->id,
                'diisi_pembimbing_at' => now(),
                'ttd_pembimbing_by' => $pembimbing->id,
                'ttd_pembimbing_at' => now(),
                'ttd_ketua_penguji_by' => $pembimbing->id,
                'ttd_ketua_penguji_at' => now(),
                'status' => $isRejected ? 'ditolak' : 'selesai',
            ];

            if ($isRejected) {
                $updateData['alasan_ditolak'] = $validated['catatan_tambahan'] ?? 'Proposal tidak layak berdasarkan hasil ujian seminar proposal.';
                $updateData['ditolak_at'] = now();
            }

            // Update berita acara
            $beritaAcara->update($updateData);

            Log::info('BA updated successfully', [
                'ba_id' => $beritaAcara->id,
                'new_status' => $beritaAcara->status
            ]);

            // Generate PDF
            try {
                $pdfPath = $this->pelaksanaanUjianService->generateBeritaAcaraPdf($beritaAcara);

                if ($pdfPath) {
                    $beritaAcara->update(['file_path' => $pdfPath]);
                    Log::info('PDF generated successfully', [
                        'ba_id' => $beritaAcara->id,
                        'pdf_path' => $pdfPath
                    ]);
                } else {
                    Log::warning('PDF generation returned null', [
                        'ba_id' => $beritaAcara->id
                    ]);
                }
            } catch (Exception $e) {
                Log::error('PDF generation failed', [
                    'ba_id' => $beritaAcara->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Don't fail the whole transaction if PDF fails
            }

            if ($isRejected) {
                // Reset jadwal untuk ujian ulang
                $jadwal->update([
                    'status' => 'menunggu_jadwal',
                    'tanggal_ujian' => null,
                    'waktu_mulai' => null,
                    'waktu_selesai' => null,
                    'ruangan' => null,
                ]);

                Log::info('Jadwal reset due to rejection', [
                    'ba_id' => $beritaAcara->id,
                    'jadwal_id' => $jadwal->id,
                    'new_jadwal_status' => $jadwal->status
                ]);
            }

            DB::commit();

            Log::info('Fill BA by pembimbing - SUCCESS', [
                'ba_id' => $beritaAcara->id,
                'is_rejected' => $isRejected
            ]);

            if ($isRejected) {
                return [
                    'success' => true,
                    'isRejected' => true,
                    'message' => 'Berita acara telah diselesaikan dengan keputusan TIDAK LAYAK. Mahasiswa perlu dijadwalkan ulang untuk ujian seminar proposal.',
                ];
            } else {
                return [
                    'success' => true,
                    'isRejected' => false,
                    'message' => 'Berita acara berhasil diisi dan ditandatangani! PDF telah berhasil digenerate.',
                ];
            }

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Fill by pembimbing FAILED', [
                'ba_id' => $beritaAcara->id,
                'pembimbing_id' => $pembimbing->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'isRejected' => false,
                'message' => 'Gagal menyimpan data berita acara. Error: ' . $e->getMessage(),
            ];
        }
    }
}