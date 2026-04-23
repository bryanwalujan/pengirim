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

    // Cek file terlebih dahulu
    $files = $this->encodeFiles($pendaftaran);
    $hasFiles = !empty(array_filter($files));
    
    if (!$hasFiles) {
        Log::warning('[RepodosenSync] Tidak ada file yang berhasil diencode', [
            'pendaftaran_id' => $pendaftaran->id,
            'file_skripsi_exists' => !empty($pendaftaran->file_skripsi),
            'file_sk_pembimbing_exists' => !empty($pendaftaran->file_sk_pembimbing),
            'file_proposal_exists' => !empty($pendaftaran->file_proposal),
        ]);
    }

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

    // Log ukuran payload
    $payloadSize = strlen(json_encode($payload));
    Log::info('[RepodosenSync] Sync skripsi lengkap', [
        'pendaftaran_id' => $pendaftaran->id,
        'files_included' => array_keys(array_filter($files)),
        'payload_size_kb' => round($payloadSize / 1024, 2),
        'has_files' => $hasFiles,
    ]);

    $endpoint = $this->baseUrl . '/api/sync/skripsi';

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
        $response = Http::timeout(120)
            ->withHeaders([
                'X-Sync-Token' => $this->token,
                'Accept'       => 'application/json',
            ])
            ->post($endpoint, $payload);

        $body = $response->json() ?? [];

        // Log response untuk debugging
        Log::info('[RepodosenSync] Response details', [
            'pendaftaran_id' => $pendaftaranId,
            'endpoint' => $endpoint,
            'http_status' => $response->status(),
            'response_keys' => array_keys($body),
            'has_results' => isset($body['results']),
            'results_type' => isset($body['results']) ? gettype($body['results']) : 'null',
        ]);

        if ($response->successful()) {
            Log::info('[RepodosenSync] Berhasil', [
                'pendaftaran_id' => $pendaftaranId,
                'synced' => $body['synced'] ?? $body['results']['synced'] ?? 0,
                'failed' => $body['failed'] ?? $body['results']['failed'] ?? 0,
            ]);
            
            // Standardisasi response
            return [
                'success' => true,
                'message' => $body['message'] ?? 'Sync berhasil',
                'synced' => $body['synced'] ?? $body['results']['synced'] ?? 0,
                'failed' => $body['failed'] ?? $body['results']['failed'] ?? 0,
                'results' => $body['results'] ?? [],
            ];
        } else {
            Log::error('[RepodosenSync] HTTP ' . $response->status(), [
                'pendaftaran_id' => $pendaftaranId,
                'endpoint' => $endpoint,
                'response_body' => $body,
                'error_detail' => $response->body(),
            ]);
            
            return [
                'success' => false,
                'message' => $body['message'] ?? 'HTTP Error ' . $response->status(),
                'results' => [],
            ];
        }

    } catch (\Illuminate\Http\Client\ConnectionException $e) {
        Log::error('[RepodosenSync] Koneksi gagal', [
            'endpoint' => $endpoint,
            'error' => $e->getMessage(),
        ]);
        return [
            'success' => false, 
            'message' => 'Koneksi gagal: ' . $e->getMessage(), 
            'results' => []
        ];

    } catch (\Exception $e) {
        Log::error('[RepodosenSync] Error tidak terduga', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return [
            'success' => false, 
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(), 
            'results' => []
        ];
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
        if (!$path) {
            Log::info("[RepodosenSync] File {$key} - Path kosong untuk pendaftaran_id: {$pendaftaran->id}");
            continue;
        }

        $content = $this->readFile($path, $key);
        if ($content !== null) {
            $originalSize = strlen($content);
            $maxSize = 15 * 1024 * 1024; // 15MB max
            
            if ($originalSize > $maxSize) {
                Log::warning("[RepodosenSync] File {$key} terlalu besar ({$originalSize} bytes / " . round($originalSize/1024/1024, 2) . "MB), melebihi batas {$maxSize} bytes");
                continue;
            }
            
            $files[$key] = base64_encode($content);
            Log::info("[RepodosenSync] File {$key} berhasil diencode", [
                'size_kb' => round($originalSize / 1024, 2),
                'base64_size_kb' => round(strlen($files[$key]) / 1024, 2),
            ]);
        } else {
            Log::warning("[RepodosenSync] File {$key} gagal dibaca", [
                'path' => $path,
                'disk_checked' => 'local & public'
            ]);
        }
    }

    return $files;
}

    /**
     * Coba baca file dari disk 'local' dulu, fallback ke 'public'.
     */
   private function readFile(string $path, string $label): ?string
{
    $disks = ['local', 'public', 's3']; // tambahkan disk lain jika perlu
    
    foreach ($disks as $disk) {
        if (!config("filesystems.disks.{$disk}")) {
            continue;
        }
        
        if (Storage::disk($disk)->exists($path)) {
            try {
                $content = Storage::disk($disk)->get($path);
                Log::info("[RepodosenSync] File '{$label}' berhasil dibaca dari disk '{$disk}'", [
                    'path' => $path,
                    'size' => strlen($content)
                ]);
                return $content;
            } catch (\Exception $e) {
                Log::error("[RepodosenSync] Gagal baca file '{$label}' dari disk '{$disk}': " . $e->getMessage());
            }
        } else {
            Log::debug("[RepodosenSync] File '{$label}' tidak ditemukan di disk '{$disk}'", [
                'path' => $path
            ]);
        }
    }

    Log::warning("[RepodosenSync] File '{$label}' tidak ditemukan di disk manapun", [
        'path' => $path,
        'checked_disks' => $disks
    ]);
    
    return null;
}
}