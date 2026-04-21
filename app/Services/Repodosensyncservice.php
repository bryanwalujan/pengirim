<?php

namespace App\Services;

use App\Models\PendaftaranUjianHasil;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * RepodosenSyncService — Web Pengirim (presma)
 *
 * Mengirim data dosen pembimbing dari PendaftaranUjianHasil
 * ke endpoint API repodosen via HTTP (dengan token autentikasi).
 *
 * Konfigurasi di .env presma:
 *   REPODOSEN_SYNC_URL=https://repodosen.ti.unima.ac.id/api/sync/dosen-pembimbing
 *   REPODOSEN_SYNC_TOKEN=isi_dengan_token_rahasia_yang_sama
 */
class RepodosenSyncService
{
    private string $url;
    private string $token;

    public function __construct()
    {
        $this->url   = config('services.repodosen_sync.url');
        $this->token = config('services.repodosen_sync.token');
    }

    /**
     * Sync dosen pembimbing dari satu pendaftaran ujian hasil.
     *
     * @param  PendaftaranUjianHasil  $pendaftaran
     * @return array{success: bool, message: string, results: array}
     */
    public function syncDosenPembimbing(PendaftaranUjianHasil $pendaftaran): array
    {
        // Load relasi dosen jika belum
        $pendaftaran->loadMissing(['dosenPembimbing1', 'dosenPembimbing2']);

        $dosenList = $this->buildDosenList($pendaftaran);

        if (empty($dosenList)) {
            return [
                'success' => false,
                'message' => 'Tidak ada data dosen pembimbing untuk disync.',
                'results' => [],
            ];
        }

        Log::info('[RepodosenSync] Memulai sync dosen pembimbing', [
            'pendaftaran_id' => $pendaftaran->id,
            'mahasiswa_nim'  => $pendaftaran->user->nim ?? '-',
            'jumlah_dosen'   => count($dosenList),
        ]);

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'X-Sync-Token' => $this->token,
                    'Accept'       => 'application/json',
                ])
                ->post($this->url, [
                    'source'     => 'presma',
                    'dosen_list' => $dosenList,
                ]);

            $body = $response->json();

            if ($response->successful()) {
                Log::info('[RepodosenSync] Sync berhasil', [
                    'pendaftaran_id' => $pendaftaran->id,
                    'synced'         => $body['synced'] ?? 0,
                    'failed'         => $body['failed'] ?? 0,
                ]);
            } else {
                Log::error('[RepodosenSync] Sync gagal — HTTP ' . $response->status(), [
                    'pendaftaran_id' => $pendaftaran->id,
                    'response'       => $body,
                ]);
            }

            return [
                'success' => $response->successful(),
                'message' => $body['message'] ?? 'Tidak ada pesan dari server.',
                'results' => $body['results'] ?? [],
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('[RepodosenSync] Koneksi ke repodosen gagal', [
                'pendaftaran_id' => $pendaftaran->id,
                'error'          => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Koneksi ke repodosen gagal: ' . $e->getMessage(),
                'results' => [],
            ];

        } catch (\Exception $e) {
            Log::error('[RepodosenSync] Error tidak terduga', [
                'pendaftaran_id' => $pendaftaran->id,
                'error'          => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'results' => [],
            ];
        }
    }

    /**
     * Susun array dosen dari relasi pendaftaran.
     * Pembimbing 1 dan 2 disatukan, null dibuang.
     */
    private function buildDosenList(PendaftaranUjianHasil $pendaftaran): array
    {
        $list = [];

        $map = [
            'pembimbing_1' => $pendaftaran->dosenPembimbing1,
            'pembimbing_2' => $pendaftaran->dosenPembimbing2,
        ];

        foreach ($map as $role => $dosen) {
            if (!$dosen) continue;

            $list[] = [
                'nama' => $dosen->name,
                'nip'  => $dosen->nip  ?? null,
                'nidn' => $dosen->nidn ?? null,
                'role' => $role,
            ];
        }

        return $list;
    }
}