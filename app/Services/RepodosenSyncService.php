<?php
// app/Services/RepodosenSyncService.php

namespace App\Services;

use App\Models\PendaftaranUjianHasil;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RepodosenSyncService
{
    private string $url;
    private string $token;

    public function __construct()
    {
        $this->url   = config('services.repodosen_sync.url');
        $this->token = config('services.repodosen_sync.token');
    }

    public function syncDosenPembimbing(PendaftaranUjianHasil $pendaftaran): array
    {
        $pendaftaran->loadMissing(['dosenPembimbing1', 'dosenPembimbing2', 'user']);

        $dosenList = $this->buildDosenList($pendaftaran);

        if (empty($dosenList)) {
            return [
                'success' => false,
                'message' => 'Tidak ada data dosen pembimbing untuk disync.',
                'results' => [],
            ];
        }

        // Encode file ke base64
        $files = $this->encodeFiles($pendaftaran);

        $payload = [
            'source'         => 'presma',
            'pendaftaran_id' => (string) $pendaftaran->id,
            'mahasiswa'      => [
                'nama'     => $pendaftaran->user->name    ?? 'Unknown',
                'nim'      => $pendaftaran->user->nim     ?? null,
                'angkatan' => $pendaftaran->angkatan      ?? null,
            ],
            'judul_skripsi'  => $pendaftaran->judul_skripsi ?? '',
            'dosen_list'     => $dosenList,
            'files'          => $files,
        ];

        Log::info('[RepodosenSync] Memulai sync', [
            'pendaftaran_id' => $pendaftaran->id,
            'files_included' => array_keys(array_filter($files)),
        ]);

        try {
            $response = Http::timeout(30) // lebih lama karena ada file
                ->withHeaders([
                    'X-Sync-Token' => $this->token,
                    'Accept'       => 'application/json',
                ])
                ->post($this->url, $payload);

            $body = $response->json();

            if ($response->successful()) {
                Log::info('[RepodosenSync] Sync berhasil', [
                    'pendaftaran_id' => $pendaftaran->id,
                    'synced'         => $body['synced'] ?? 0,
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
            Log::error('[RepodosenSync] Koneksi gagal', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Koneksi gagal: ' . $e->getMessage(), 'results' => []];

        } catch (\Exception $e) {
            Log::error('[RepodosenSync] Error tidak terduga', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage(), 'results' => []];
        }
    }

    private function buildDosenList(PendaftaranUjianHasil $pendaftaran): array
    {
        $list = [];
        $map  = [
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

    /**
     * Encode file-file dari pendaftaran ke base64.
     * Hanya file yang ada di storage yang disertakan.
     */
    private function encodeFiles(PendaftaranUjianHasil $pendaftaran): array
    {
        $files = [];

        $fileMap = [
            'skripsi'       => $pendaftaran->file_skripsi,
            'sk_pembimbing' => $pendaftaran->file_sk_pembimbing,
            'proposal'      => $pendaftaran->file_proposal ?? null,
        ];

        foreach ($fileMap as $key => $path) {
            if (!$path) continue;

            if (!Storage::disk('local')->exists($path)) {
                Log::warning("[RepodosenSync] File tidak ditemukan, skip: {$path}");
                continue;
            }

            try {
                $content    = Storage::disk('local')->get($path);
                $files[$key] = base64_encode($content);
            } catch (\Exception $e) {
                Log::error("[RepodosenSync] Gagal encode file {$key}: " . $e->getMessage());
            }
        }

        return $files;
    }
}