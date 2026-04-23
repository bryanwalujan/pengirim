<?php
// app/Services/RepodosenSyncService.php

namespace App\Services;

use App\Models\PendaftaranUjianHasil;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RepodosenSyncService
{
    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        // Pastikan di .env e-service ada:
        //   REPODOSEN_SYNC_URL=https://repodosen.example.com
        //   REPODOSEN_SYNC_TOKEN=xxx
        $this->baseUrl = rtrim(config('services.repodosen_sync.url'), '/');
        $this->token   = config('services.repodosen_sync.token');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PUBLIC: Sync dosen pembimbing (data awal, tanpa file skripsi)
    // Memanggil POST /api/sync/dosen-pembimbing di repodosen
    // ──────────────────────────────────────────────────────────────────────────
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

        $payload = [
            'source'     => 'presma',
            'dosen_list' => $dosenList,
        ];

        $endpoint = $this->baseUrl . '/api/sync/dosen-pembimbing';

        Log::info('[RepodosenSync] Sync dosen pembimbing', [
            'pendaftaran_id' => $pendaftaran->id,
            'endpoint'       => $endpoint,
            'dosen_count'    => count($dosenList),
        ]);

        return $this->post($endpoint, $payload, $pendaftaran->id);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PUBLIC: Sync lengkap skripsi + dosen + file
    // Memanggil POST /api/sync/skripsi di repodosen
    // ──────────────────────────────────────────────────────────────────────────
    public function syncSkripsi(PendaftaranUjianHasil $pendaftaran): array
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

        $files = $this->encodeFiles($pendaftaran);

        $payload = [
            'source'         => 'presma',
            'pendaftaran_id' => (string) $pendaftaran->id,
            'mahasiswa'      => [
                'nama'     => $pendaftaran->user->name    ?? 'Unknown',
                'nim'      => $pendaftaran->user->nim     ?? null,
                'angkatan' => $pendaftaran->angkatan      ?? null,
            ],
            'judul_skripsi' => $pendaftaran->judul_skripsi ?? '',
            'dosen_list'    => $dosenList,
            'files'         => $files,
        ];

        $endpoint = $this->baseUrl . '/api/sync/skripsi';

        Log::info('[RepodosenSync] Sync skripsi lengkap', [
            'pendaftaran_id' => $pendaftaran->id,
            'endpoint'       => $endpoint,
            'files_included' => array_keys(array_filter($files)),
        ]);

        return $this->post($endpoint, $payload, $pendaftaran->id);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ──────────────────────────────────────────────────────────────────────────

    private function post(string $endpoint, array $payload, $pendaftaranId): array
    {
        if (empty($this->token)) {
            Log::error('[RepodosenSync] Token tidak dikonfigurasi di .env');
            return ['success' => false, 'message' => 'Token sync belum dikonfigurasi.', 'results' => []];
        }

        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'X-Sync-Token' => $this->token,
                    'Accept'       => 'application/json',
                ])
                ->post($endpoint, $payload);

            $body = $response->json() ?? [];

            if ($response->successful()) {
                Log::info('[RepodosenSync] Berhasil', [
                    'pendaftaran_id' => $pendaftaranId,
                    'http_status'    => $response->status(),
                    'synced'         => $body['synced'] ?? 0,
                ]);
            } else {
                Log::error('[RepodosenSync] HTTP ' . $response->status(), [
                    'pendaftaran_id' => $pendaftaranId,
                    'endpoint'       => $endpoint,
                    'response_body'  => $body,
                ]);
            }

            return [
                'success' => $response->successful(),
                'message' => $body['message'] ?? 'Tidak ada pesan dari server.',
                'results' => $body['results'] ?? [],
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('[RepodosenSync] Koneksi gagal', [
                'endpoint' => $endpoint,
                'error'    => $e->getMessage(),
            ]);
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
     * Encode file-file ke base64.
     * Mencoba disk 'local' dulu, lalu 'public' sebagai fallback.
     */
    private function encodeFiles(PendaftaranUjianHasil $pendaftaran): array
    {
        $files   = [];
        $fileMap = [
            'skripsi'       => $pendaftaran->file_skripsi       ?? null,
            'sk_pembimbing' => $pendaftaran->file_sk_pembimbing ?? null,
            'proposal'      => $pendaftaran->file_proposal      ?? null,
        ];

        foreach ($fileMap as $key => $path) {
            if (!$path) continue;

            $content = $this->readFile($path, $key);
            if ($content !== null) {
                $files[$key] = base64_encode($content);
            }
        }

        return $files;
    }

    /**
     * Coba baca file dari disk 'local' dulu, fallback ke 'public'.
     */
    private function readFile(string $path, string $label): ?string
    {
        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                try {
                    $content = Storage::disk($disk)->get($path);
                    Log::info("[RepodosenSync] File '{$label}' ditemukan di disk '{$disk}'");
                    return $content;
                } catch (\Exception $e) {
                    Log::error("[RepodosenSync] Gagal baca file '{$label}' dari disk '{$disk}': " . $e->getMessage());
                }
            }
        }

        Log::warning("[RepodosenSync] File '{$label}' tidak ditemukan di disk manapun: {$path}");
        return null;
    }
}