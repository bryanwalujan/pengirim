<?php

namespace App\Actions\BeritaAcaraUjianHasil;

use App\Models\BeritaAcaraUjianHasil;
use App\Models\LembarKoreksiSkripsi;
use App\Models\PenilaianUjianHasil;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApproveOnBehalfAction
{
    public function execute(
        User $staff,
        BeritaAcaraUjianHasil $beritaAcara,
        int $dosenId,
        ?string $alasan = null,
        array $lembarKoreksiData = [],
        ?float $nilaiMutu = null,
        ?string $catatanPenilaian = null
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

            // Save Penilaian if nilai_mutu is provided (Staff override - direct input)
            if ($nilaiMutu !== null) {
                // Calculate total_nilai from nilai_mutu (reverse formula)
                // nilai_mutu = (total_nilai / 10) * 4
                // total_nilai = (nilai_mutu / 4) * 10
                $totalNilai = ($nilaiMutu / 4) * 10;

                PenilaianUjianHasil::updateOrCreate(
                    [
                        'berita_acara_ujian_hasil_id' => $beritaAcara->id,
                        'dosen_id' => $dosenId,
                    ],
                    [
                        // Set semua komponen ke nilai rata-rata agar konsisten
                        // Nilai rata-rata = (nilai_mutu / 4) * 100
                        'nilai_kebaruan' => round(($nilaiMutu / 4) * 100),
                        'nilai_kesesuaian' => round(($nilaiMutu / 4) * 100),
                        'nilai_metode' => round(($nilaiMutu / 4) * 100),
                        'nilai_kajian_teori' => round(($nilaiMutu / 4) * 100),
                        'nilai_hasil_penelitian' => round(($nilaiMutu / 4) * 100),
                        'nilai_referensi' => round(($nilaiMutu / 4) * 100),
                        'nilai_tata_bahasa' => round(($nilaiMutu / 4) * 100),
                        'total_nilai' => round($totalNilai, 2),
                        'nilai_mutu' => round($nilaiMutu, 2),
                        'catatan' => $catatanPenilaian ?? 'Penilaian diinput oleh Staff atas nama dosen.',
                    ]
                );

                Log::info('Staff created penilaian on behalf of dosen', [
                    'ba_id' => $beritaAcara->id,
                    'dosen_id' => $dosenId,
                    'staff_id' => $staff->id,
                    'nilai_mutu' => $nilaiMutu,
                ]);
            }

            // Save Lembar Koreksi if provided
            if (!empty($lembarKoreksiData)) {
                $lembarKoreksi = LembarKoreksiSkripsi::updateOrCreate(
                    [
                        'berita_acara_ujian_hasil_id' => $beritaAcara->id,
                        'dosen_id' => $dosenId,
                    ],
                    [
                        'koreksi_data' => null // Will set using setKoreksiFromArray
                    ]
                );

                $lembarKoreksi->setKoreksiFromArray($lembarKoreksiData);
                $lembarKoreksi->save();
            }

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
