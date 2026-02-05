<?php

namespace App\Actions\BeritaAcaraUjianHasil;

use App\Models\BeritaAcaraUjianHasil;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdatePengujiAction
{
    public function execute(BeritaAcaraUjianHasil $beritaAcara, array $pengujiData): array
    {
        try {
            DB::beginTransaction();

            $jadwal = $beritaAcara->jadwalUjianHasil;
            $existingSignatures = $beritaAcara->ttd_dosen_penguji ?? [];
            $signedDosenIds = collect($existingSignatures)->pluck('dosen_id')->toArray();

            $replacements = [];
            
            // Loop through input data (new assignments)
            foreach ($pengujiData as $data) {
                $posisi = $data['posisi'];
                $newDosenId = (int) $data['dosen_id'];

                // Find currently ACTIVE penguji for this position
                $currentActive = DB::table('dosen_penguji_jadwal_ujian_hasil')
                    ->where('jadwal_ujian_hasil_id', $jadwal->id)
                    ->where('posisi', $posisi)
                    ->where('status', 'active')
                    ->first();

                if ($currentActive) {
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
                        DB::table('dosen_penguji_jadwal_ujian_hasil')
                            ->where('id', $currentActive->id)
                            ->update([
                                'status' => 'replaced',
                                'replaced_by_id' => $newDosenId,
                                'updated_at' => now(),
                            ]);

                        // 2. Create NEW active record
                        DB::table('dosen_penguji_jadwal_ujian_hasil')->insert([
                            'jadwal_ujian_hasil_id' => $jadwal->id,
                            'dosen_id' => $newDosenId,
                            'posisi' => $posisi,
                            'status' => 'active',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        Log::info('Lecturer (Ujian Hasil) replaced', [
                            'posisi' => $posisi,
                            'old_id' => $currentActive->dosen_id,
                            'new_id' => $newDosenId
                        ]);

                    }
                } else {
                    // No active record for this position
                    DB::table('dosen_penguji_jadwal_ujian_hasil')->insert([
                        'jadwal_ujian_hasil_id' => $jadwal->id,
                        'dosen_id' => $newDosenId,
                        'posisi' => $posisi,
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // SINKRONISASI: Update tabel pendaftaran_ujian_hasils atau penguji_ujian_hasils
            $pendaftaran = $jadwal->pendaftaranUjianHasil;
            
            if ($pendaftaran) {
                // Positions: "Penguji 1", "Penguji 2", "Penguji 3" -> Sync to penguji_ujian_hasils
                // Positions: "Penguji 4 (PS1)", "Penguji 5 (PS2)" -> Sync to pendaftaran_ujian_hasils
                
                foreach ($pengujiData as $data) {
                    $posisi = $data['posisi'];
                    $newDosenId = (int) $data['dosen_id'];

                    if (in_array($posisi, ['Penguji 1', 'Penguji 2', 'Penguji 3'])) {
                        DB::table('penguji_ujian_hasils')
                            ->updateOrInsert(
                                [
                                    'pendaftaran_ujian_hasil_id' => $pendaftaran->id,
                                    'posisi' => $posisi,
                                ],
                                [
                                    'dosen_id' => $newDosenId,
                                    'updated_at' => now(),
                                ]
                            );
                    } elseif ($posisi === 'Penguji 4 (PS1)') {
                        $pendaftaran->update(['dosen_pembimbing1_id' => $newDosenId]);
                    } elseif ($posisi === 'Penguji 5 (PS2)') {
                        $pendaftaran->update(['dosen_pembimbing2_id' => $newDosenId]);
                    }
                }
            }

            DB::commit();

            $message = 'Susunan penguji berhasil diperbarui.';
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
            Log::error('Update penguji failed', [
                'ba_id' => $beritaAcara->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal memperbarui penguji: ' . $e->getMessage(),
            ];
        }
    }
}
