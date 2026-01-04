<?php
// filepath: app/Actions/BeritaAcaraSempro/ApproveOnBehalfAction.php

namespace App\Actions\BeritaAcaraSempro;

use App\Models\BeritaAcaraSeminarProposal;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApproveOnBehalfAction
{
    public function execute(
        User $staff,
        BeritaAcaraSeminarProposal $beritaAcara,
        int $dosenId,
        ?string $alasan = null
    ): array {
        // Validasi status BA
        if (!$beritaAcara->isMenungguTtdPembahas()) {
            return [
                'success' => false,
                'message' => 'Berita acara tidak dalam status menunggu persetujuan pembahas.',
            ];
        }

        // Validasi dosen adalah pembahas
        $isPembahas = $beritaAcara->jadwalSeminarProposal
            ->dosenPenguji()
            ->where('users.id', $dosenId)
            ->where('posisi', '!=', 'Ketua Pembahas')
            ->exists();

        if (!$isPembahas) {
            return [
                'success' => false,
                'message' => 'Dosen yang dipilih bukan pembahas untuk ujian ini.',
            ];
        }

        // Cek sudah sign
        if ($beritaAcara->hasSignedByPembahas($dosenId)) {
            return [
                'success' => false,
                'message' => 'Dosen ini sudah memberikan persetujuan.',
            ];
        }

        try {
            DB::beginTransaction();

            $dosen = User::findOrFail($dosenId);
            $signatures = $beritaAcara->ttd_dosen_pembahas ?? [];

            $newSignature = [
                'dosen_id' => $dosen->id,
                'dosen_name' => $dosen->name,
                'signed_at' => now()->toDateTimeString(),
                'approved_by_staff' => true,
                'staff_id' => $staff->id,
                'staff_name' => $staff->name,
            ];

            if ($alasan) {
                $newSignature['approval_reason'] = $alasan;
            }

            $signatures[] = $newSignature;

            $beritaAcara->update(['ttd_dosen_pembahas' => $signatures]);

            Log::info('Staff approved on behalf of pembahas', [
                'ba_id' => $beritaAcara->id,
                'dosen_id' => $dosen->id,
                'staff_id' => $staff->id,
                'alasan' => $alasan,
            ]);

            $beritaAcara->refresh();

            if ($beritaAcara->allPembahasHaveSigned()) {
                $beritaAcara->update(['status' => 'menunggu_ttd_pembimbing']);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => "Persetujuan atas nama {$dosen->name} berhasil dicatat.",
            ];

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Approve on behalf failed', [
                'ba_id' => $beritaAcara->id,
                'dosen_id' => $dosenId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal memberikan persetujuan: ' . $e->getMessage(),
            ];
        }
    }
}