<?php

namespace App\Actions\BeritaAcaraUjianHasil;

use App\Models\BeritaAcaraUjianHasil;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApproveOnBehalfAction
{
    public function execute(
        User $staff,
        BeritaAcaraUjianHasil $beritaAcara,
        int $dosenId,
        ?string $alasan = null
    ): array {
        try {
            DB::beginTransaction();

            $dosen = User::findOrFail($dosenId);
            $jadwal = $beritaAcara->jadwalUjianHasil;

            // Verify dosen is a penguji for this jadwal
            $pengujiData = $jadwal->dosenPenguji()
                ->where('users.id', $dosenId)
                ->first();

            if (!$pengujiData) {
                return [
                    'success' => false,
                    'message' => 'Dosen tersebut bukan penguji untuk ujian ini.',
                ];
            }

            // Check if already signed
            if ($beritaAcara->hasSignedByPenguji($dosenId)) {
                return [
                    'success' => false,
                    'message' => 'Dosen tersebut sudah menandatangani berita acara ini.',
                ];
            }

            $posisi = $pengujiData->pivot->posisi ?? 'Penguji';

            // Add signature on behalf
            $signatures = $beritaAcara->ttd_dosen_penguji ?? [];
            $signatures[] = [
                'dosen_id' => $dosenId,
                'dosen_name' => $dosen->name,
                'posisi' => $posisi,
                'signed_at' => now()->toDateTimeString(),
                'signed_by_staff' => true,
                'staff_id' => $staff->id,
                'staff_name' => $staff->name,
                'alasan' => $alasan,
            ];

            $beritaAcara->update([
                'ttd_dosen_penguji' => $signatures,
            ]);

            // Check if all penguji have signed
            if ($beritaAcara->fresh()->allPengujiHaveSigned()) {
                $beritaAcara->update([
                    'status' => 'menunggu_ttd_ketua',
                ]);
            }

            DB::commit();

            Log::info('Staff approved on behalf of penguji', [
                'ba_id' => $beritaAcara->id,
                'dosen_id' => $dosenId,
                'staff_id' => $staff->id,
                'alasan' => $alasan,
            ]);

            return [
                'success' => true,
                'message' => "Persetujuan atas nama {$dosen->name} berhasil.",
            ];

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Failed to approve on behalf', [
                'ba_id' => $beritaAcara->id,
                'dosen_id' => $dosenId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal menyetujui atas nama dosen: ' . $e->getMessage(),
            ];
        }
    }
}
