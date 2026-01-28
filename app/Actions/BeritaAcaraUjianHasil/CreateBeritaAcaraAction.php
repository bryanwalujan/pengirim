<?php

namespace App\Actions\BeritaAcaraUjianHasil;

use App\Models\BeritaAcaraUjianHasil;
use App\Models\JadwalUjianHasil;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateBeritaAcaraAction
{
    public function execute(
        JadwalUjianHasil $jadwal,
        User $creator,
        array $data
    ): array {
        try {
            DB::beginTransaction();

            // Ambil data mahasiswa untuk audit trail
            $pendaftaran = $jadwal->pendaftaranUjianHasil;
            $mahasiswa = $pendaftaran->user;

            $beritaAcara = BeritaAcaraUjianHasil::create([
                'jadwal_ujian_hasil_id' => $jadwal->id,
                // Simpan data mahasiswa untuk audit trail
                'mahasiswa_id' => $mahasiswa->id,
                'mahasiswa_name' => $mahasiswa->name,
                'mahasiswa_nim' => $mahasiswa->nim,
                'judul_skripsi' => $pendaftaran->judul_skripsi ?? $pendaftaran->komisiHasil->judul_skripsi ?? null,
                'ruangan' => $data['ruangan'] ?? $jadwal->ruangan ?? 'Ruangan Ujian Teknik Informatika',
                'catatan_tambahan' => $data['catatan_tambahan'] ?? null,
                'dibuat_oleh_id' => $creator->id,
                'status' => 'menunggu_ttd_penguji',
            ]);

            DB::commit();

            Log::info('Berita Acara Ujian Hasil created', [
                'ba_id' => $beritaAcara->id,
                'jadwal_id' => $jadwal->id,
                'mahasiswa_id' => $mahasiswa->id,
                'created_by' => $creator->id,
            ]);

            return [
                'success' => true,
                'beritaAcara' => $beritaAcara,
                'message' => 'Berita acara berhasil dibuat. Menunggu persetujuan dari dosen penguji.',
            ];

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Failed to create Berita Acara Ujian Hasil', [
                'jadwal_id' => $jadwal->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal membuat berita acara: ' . $e->getMessage(),
            ];
        }
    }
}
