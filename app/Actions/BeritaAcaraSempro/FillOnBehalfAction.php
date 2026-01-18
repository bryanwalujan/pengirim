<?php
// filepath: app/Actions/BeritaAcaraSempro/FillOnBehalfAction.php

namespace App\Actions\BeritaAcaraSempro;

use App\Models\BeritaAcaraSeminarProposal;
use App\Models\User;
use App\Services\PelaksanaanUjianService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class FillOnBehalfAction
{
    public function __construct(
        private readonly PelaksanaanUjianService $pelaksanaanUjianService
    ) {
    }

    public function execute(
        User $staff,
        BeritaAcaraSeminarProposal $beritaAcara,
        array $validated
    ): array {
        // Validasi status BA
        if (!$beritaAcara->isMenungguTtdPembimbing()) {
            return [
                'success' => false,
                'message' => 'Berita acara tidak dalam status menunggu persetujuan pembimbing.',
            ];
        }

        try {
            DB::beginTransaction();

            $jadwal = $beritaAcara->jadwalSeminarProposal;
            $pembimbing = $jadwal->pendaftaranSeminarProposal->dosenPembimbing;
            $isRejected = $validated['keputusan'] === 'Tidak';

            Log::info('Fill BA on behalf of pembimbing - START', [
                'ba_id' => $beritaAcara->id,
                'staff_id' => $staff->id,
                'pembimbing_id' => $pembimbing->id,
                'keputusan' => $validated['keputusan'],
                'is_rejected' => $isRejected,
                'alasan_override' => $validated['alasan_override'],
            ]);

            // Update BA data with override information
            $updateData = [
                'keputusan' => $validated['keputusan'],
                'catatan_tambahan' => $validated['catatan_tambahan'] ?? null,
                'diisi_oleh_pembimbing_id' => $pembimbing->id,
                'diisi_pembimbing_at' => now(),
                'ttd_pembimbing_by' => $pembimbing->id,
                'ttd_pembimbing_at' => now(),
                'ttd_ketua_penguji_by' => $pembimbing->id,
                'ttd_ketua_penguji_at' => now(),
                'status' => $isRejected ? 'ditolak' : 'selesai',
                // Override info
                'override_pembimbing_by' => $staff->id,
                'override_pembimbing_at' => now(),
                'override_pembimbing_reason' => $validated['alasan_override'],
            ];

            if ($isRejected) {
                $updateData['alasan_ditolak'] = $validated['catatan_tambahan'] 
                    ?? 'Proposal tidak layak berdasarkan hasil ujian seminar proposal.';
                $updateData['ditolak_at'] = now();
            }

            // Update berita acara
            $beritaAcara->update($updateData);

            Log::info('BA updated successfully (override by staff)', [
                'ba_id' => $beritaAcara->id,
                'new_status' => $beritaAcara->status,
                'staff_name' => $staff->name,
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
                }
            } catch (Exception $e) {
                Log::error('PDF generation failed', [
                    'ba_id' => $beritaAcara->id,
                    'error' => $e->getMessage(),
                ]);
                // Don't fail the whole transaction if PDF fails
            }

            if ($isRejected) {
                // Update status pendaftaran sempro menjadi 'ditolak'
                $pendaftaran = $jadwal->pendaftaranSeminarProposal;
                $pendaftaran->update([
                    'status' => 'ditolak',
                    'alasan_penolakan' => $validated['catatan_tambahan'] 
                        ?? 'Proposal tidak layak berdasarkan hasil ujian seminar proposal.',
                ]);

                Log::info('Pendaftaran sempro rejected (via staff override)', [
                    'ba_id' => $beritaAcara->id,
                    'pendaftaran_id' => $pendaftaran->id,
                    'staff_id' => $staff->id,
                ]);

                // Nullify foreign key to preserve berita acara
                $beritaAcara->update([
                    'jadwal_seminar_proposal_id' => null,
                ]);

                // Delete jadwal sempro
                $jadwalId = $jadwal->id;
                $jadwal->delete();

                Log::info('Jadwal sempro deleted due to rejection (staff override)', [
                    'ba_id' => $beritaAcara->id,
                    'jadwal_id_deleted' => $jadwalId,
                ]);
            }

            DB::commit();

            Log::info('Fill BA on behalf of pembimbing - SUCCESS', [
                'ba_id' => $beritaAcara->id,
                'staff_id' => $staff->id,
                'is_rejected' => $isRejected,
            ]);

            if ($isRejected) {
                return [
                    'success' => true,
                    'isRejected' => true,
                    'message' => "Berita acara telah diselesaikan (override oleh {$staff->name}) dengan keputusan TIDAK LAYAK. Pendaftaran seminar proposal mahasiswa telah ditolak.",
                ];
            } else {
                return [
                    'success' => true,
                    'isRejected' => false,
                    'message' => "Berita acara berhasil diisi atas nama {$pembimbing->name} oleh {$staff->name}. PDF telah berhasil digenerate.",
                ];
            }

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Fill on behalf of pembimbing FAILED', [
                'ba_id' => $beritaAcara->id,
                'staff_id' => $staff->id,
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
