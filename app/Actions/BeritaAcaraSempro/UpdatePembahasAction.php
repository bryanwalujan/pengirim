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
            
            // Loop through input data (new assignments)
            foreach ($pembahasData as $data) {
                $posisi = $data['posisi'];
                $newDosenId = (int) $data['dosen_id'];

                // Find currently ACTIVE pembahas for this position
                $currentActive = DB::table('dosen_penguji_jadwal_sempro')
                    ->where('jadwal_seminar_proposal_id', $jadwal->id)
                    ->where('posisi', $posisi)
                    ->where('status', 'active')
                    ->first();

                if ($currentActive) {
                    // Use loose comparison to handle string/int differences
                    if ($currentActive->dosen_id != $newDosenId) {
                        // Check if old dosen already signed
                        if (in_array($currentActive->dosen_id, $signedDosenIds)) {
                            $oldDosen = User::find($currentActive->dosen_id);
                            DB::rollBack();
                            return [
                                'success' => false,
                                'message' => "Dosen {$oldDosen->name} di posisi {$posisi} sudah memberikan persetujuan (TTD), tidak dapat diganti.",
                            ];
                        }

                        // Log replacement info
                        $oldDosen = User::find($currentActive->dosen_id);
                        $newDosen = User::find($newDosenId);

                        $replacements[] = [
                            'posisi' => $posisi,
                            'old_dosen' => $oldDosen->name,
                            'new_dosen' => $newDosen->name,
                        ];

                        // 1. Mark old record as replaced
                        DB::table('dosen_penguji_jadwal_sempro')
                            ->where('id', $currentActive->id)
                            ->update([
                                'status' => 'replaced',
                                'replaced_by_id' => $newDosenId,
                                'updated_at' => now(),
                            ]);

                        // 2. Create NEW active record
                        DB::table('dosen_penguji_jadwal_sempro')->insert([
                            'jadwal_seminar_proposal_id' => $jadwal->id,
                            'dosen_id' => $newDosenId,
                            'posisi' => $posisi,
                            'status' => 'active',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        Log::info('Lecturer replaced', [
                            'posisi' => $posisi,
                            'old_id' => $currentActive->dosen_id,
                            'new_id' => $newDosenId
                        ]);

                    } else {
                        // Sameosen, do nothing (or update timestamps if needed)
                    }
                } else {
                    // No active record for this position (new assignment)
                    DB::table('dosen_penguji_jadwal_sempro')->insert([
                        'jadwal_seminar_proposal_id' => $jadwal->id,
                        'dosen_id' => $newDosenId,
                        'posisi' => $posisi,
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // ✅ SINKRONISASI: Update tabel proposal_pembahas untuk pendaftaran seminar proposal
            // Ini memastikan count beban dosen di Pendaftaran Seminar Proposal tetap akurat
            $pendaftaran = $jadwal->pendaftaranSeminarProposal;
            
            if ($pendaftaran) {
                // Mapping posisi: "Anggota Pembahas 1", "Anggota Pembahas 2", "Anggota Pembahas 3" -> 1, 2, 3
                $posisiMapping = [
                    'Anggota Pembahas 1' => 1,
                    'Anggota Pembahas 2' => 2,
                    'Anggota Pembahas 3' => 3,
                ];

                foreach ($pembahasData as $data) {
                    $posisi = $data['posisi'];
                    $newDosenId = (int) $data['dosen_id'];

                    // Hanya update non-Ketua (Ketua Pembahas tidak ada di proposal_pembahas)
                    if (isset($posisiMapping[$posisi])) {
                        $posisiNumeric = $posisiMapping[$posisi];

                        // Update atau create proposal_pembahas
                        DB::table('proposal_pembahas')
                            ->updateOrInsert(
                                [
                                    'pendaftaran_seminar_proposal_id' => $pendaftaran->id,
                                    'posisi' => $posisiNumeric,
                                ],
                                [
                                    'dosen_id' => $newDosenId,
                                    'updated_at' => now(),
                                ]
                            );

                        Log::info('✅ Proposal pembahas synchronized', [
                            'pendaftaran_id' => $pendaftaran->id,
                            'posisi' => $posisiNumeric,
                            'dosen_id' => $newDosenId,
                        ]);
                    }
                }
            }

            // Update signatures - remove signatures from replaced dosen if any (redundant check but safe)
            // Note: We already blocked replacement if signed, so technically no signatures to remove.
            // But we keep this just in case logic changes.
            
            $newSignatures = [];
            // Get IDs from input data
            $activeDosenIds = array_map(fn($d) => (int)$d['dosen_id'], $pembahasData);

            foreach ($existingSignatures as $signature) {
                if (in_array($signature['dosen_id'], $activeDosenIds)) {
                    $newSignatures[] = $signature;
                }
            }
            
            // Only update if changes
            if (count($existingSignatures) !== count($newSignatures)) {
                 $beritaAcara->update(['ttd_dosen_pembahas' => $newSignatures]);
            }

            DB::commit();

            $message = 'Daftar pembahas berhasil diperbarui.';
            if (count($replacements) > 0) {
                $message .= ' ' . count($replacements) . ' dosen telah diganti. Status beban dosen telah disinkronkan.';
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