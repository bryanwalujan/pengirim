<?php
// filepath: /c:/laragon/www/eservice-app/app/Actions/SkPembimbing/AssignPembimbingAction.php

namespace App\Actions\SkPembimbing;

use App\Models\PengajuanSkPembimbing;
use App\Models\User;
use App\Traits\GeneratesNomorSurat;
use Illuminate\Support\Facades\DB;

class AssignPembimbingAction
{
    use GeneratesNomorSurat;

    public function execute(PengajuanSkPembimbing $pengajuan, User $staff, array $data): array
    {
        if (
            !in_array($pengajuan->status, [
                PengajuanSkPembimbing::STATUS_MENUNGGU_VERIFIKASI,
                PengajuanSkPembimbing::STATUS_PS_DITENTUKAN,
            ])
        ) {
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
                'status' => PengajuanSkPembimbing::STATUS_PS_DITENTUKAN,
                'ps_assigned_by' => $staff->id,
                'ps_assigned_at' => now(),
                'verified_by' => $pengajuan->verified_by ?? $staff->id,
                'verified_at' => $pengajuan->verified_at ?? now(),
            ]);

            return ['success' => true, 'message' => 'Pembimbing berhasil ditentukan.'];
        });
    }
}