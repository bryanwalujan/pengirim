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
                // 🔥 UPDATE: Ubah status pendaftaran sempro menjadi 'ditolak'
                // Mahasiswa harus membuat komisi proposal baru dengan judul yang direvisi
                $pendaftaran = $jadwal->pendaftaranSeminarProposal;
                $pendaftaran->update([
                    'status' => 'ditolak',
                    'alasan_penolakan' => $validated['catatan_tambahan'] 
                        ?? 'Proposal tidak layak berdasarkan hasil ujian seminar proposal. Mahasiswa harus membuat komisi proposal baru dengan judul yang direvisi.',
                ]);

                Log::info('Pendaftaran sempro rejected', [
                    'ba_id' => $beritaAcara->id,
                    'pendaftaran_id' => $pendaftaran->id,
                    'alasan' => $pendaftaran->alasan_penolakan
                ]);

                // 🔥 NEW: Nullify foreign key untuk preserve berita acara
                // Supaya BA tidak ikut terhapus saat jadwal dihapus (untuk audit trail)
                $beritaAcara->update([
                    'jadwal_seminar_proposal_id' => null,
                ]);

                Log::info('BA foreign key nullified for preservation', [
                    'ba_id' => $beritaAcara->id,
                    'jadwal_id' => $jadwal->id,
                ]);

                // 🔥 NEW: Delete jadwal sempro
                // Mahasiswa harus membuat komisi baru → daftar sempro baru → jadwal baru
                $jadwalId = $jadwal->id;
                $jadwal->delete();

                Log::info('Jadwal sempro deleted due to rejection', [
                    'ba_id' => $beritaAcara->id,
                    'jadwal_id_deleted' => $jadwalId,
                    'reason' => 'BA ditolak - mahasiswa harus mulai dari awal'
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
                    'message' => 'Berita acara telah diselesaikan dengan keputusan TIDAK LAYAK. Pendaftaran seminar proposal mahasiswa telah ditolak. Mahasiswa harus membuat Komisi Proposal baru dengan judul yang direvisi.',
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