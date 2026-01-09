<?php
// filepath: app/Actions/BeritaAcaraSempro/UpdatePembahasAction.php

namespace App\Actions\BeritaAcaraSempro;

use App\Models\BeritaAcaraSeminarProposal;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdatePembahasAction
{
    public function execute(BeritaAcaraSeminarProposal $beritaAcara, array $pembahasData): array
    {
        try {
            DB::beginTransaction();

            $jadwal = $beritaAcara->jadwalSeminarProposal;
            $existingSignatures = $beritaAcara->ttd_dosen_pembahas ?? [];
            $signedDosenIds = collect($existingSignatures)->pluck('dosen_id')->toArray();

            $replacements = [];
            $newPembahasData = [];

            // Validate & collect changes
            foreach ($pembahasData as $data) {
                $posisi = $data['posisi'];
                $newDosenId = (int) $data['dosen_id'];

                $oldPivot = DB::table('dosen_penguji_jadwal_sempro')
                    ->where('jadwal_seminar_proposal_id', $jadwal->id)
                    ->where('posisi', $posisi)
                    ->first();

                if ($oldPivot && $oldPivot->dosen_id != $newDosenId) {
                    // Check if old dosen already signed
                    if (in_array($oldPivot->dosen_id, $signedDosenIds)) {
                        $oldDosen = User::find($oldPivot->dosen_id);

                        DB::rollBack();

                        return [
                            'success' => false,
                            'message' => "Dosen {$oldDosen->name} di posisi {$posisi} sudah memberikan persetujuan (TTD), tidak dapat diganti.",
                        ];
                    }

                    $oldDosen = User::find($oldPivot->dosen_id);
                    $newDosen = User::find($newDosenId);

                    $replacements[] = [
                        'posisi' => $posisi,
                        'old_dosen' => $oldDosen->name,
                        'new_dosen' => $newDosen->name,
                    ];
                }

                $newPembahasData[$posisi] = $newDosenId;
            }

            // Update pivot table
            foreach ($newPembahasData as $posisi => $newDosenId) {
                $affected = DB::table('dosen_penguji_jadwal_sempro')
                    ->where('jadwal_seminar_proposal_id', $jadwal->id)
                    ->where('posisi', $posisi)
                    ->update([
                        'dosen_id' => $newDosenId,
                        'updated_at' => now(),
                    ]);

                Log::info('Pivot updated', [
                    'posisi' => $posisi,
                    'new_dosen_id' => $newDosenId,
                    'rows_affected' => $affected,
                ]);
            }

            // Update signatures - remove signatures from replaced dosen
            $newSignatures = [];
            $newDosenIds = array_values($newPembahasData);

            foreach ($existingSignatures as $signature) {
                if (in_array($signature['dosen_id'], $newDosenIds)) {
                    $newSignatures[] = $signature;
                }
            }

            $beritaAcara->update(['ttd_dosen_pembahas' => $newSignatures]);

            DB::commit();

            $message = 'Daftar pembahas berhasil diperbarui.';
            if (count($replacements) > 0) {
                $message .= ' ' . count($replacements) . ' dosen telah diganti.';
            }

            return [
                'success' => true,
                'message' => $message,
                'replacements' => $replacements,
            ];

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Update pembahas failed', [
                'ba_id' => $beritaAcara->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal memperbarui pembahas: ' . $e->getMessage(),
            ];
        }
    }
}