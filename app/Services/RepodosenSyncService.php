<?php
// app/Services/RepodosenSyncService.php

namespace App\Services;

use App\Models\PendaftaranUjianHasil;
use App\Models\PendaftaranSeminarProposal;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RepodosenSyncService
{
    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.repodosen_sync.url'), '/');
        $this->token   = config('services.repodosen_sync.token');
    }

    /**
     * Sync dosen pembimbing (data awal, tanpa file skripsi)
     */
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

    /**
     * Sync lengkap skripsi + dosen + file
     * File akan dikirim sebagai base64 dan akan disimpan di repodosen
     * dengan struktur folder terpisah: skripsi/, sk_pembimbing/, proposal/
     */
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

        // Encode files ke base64 dengan metadata folder
        $files = $this->encodeFilesWithMetadata($pendaftaran);
        $hasFiles = !empty(array_filter($files, fn($f) => isset($f['content'])));

        if (!$hasFiles) {
            Log::warning('[RepodosenSync] Tidak ada file yang berhasil diencode', [
                'pendaftaran_id' => $pendaftaran->id,
                'file_skripsi_exists' => !empty($pendaftaran->file_skripsi),
                'file_sk_pembimbing_exists' => !empty($pendaftaran->file_sk_pembimbing),
                'file_proposal_exists' => !empty($pendaftaran->file_proposal),
            ]);
        }

        // Buat slug untuk folder name
        $folderName = $this->generateFolderName($pendaftaran);

        $payload = [
            'source'         => 'presma',
            'pendaftaran_id' => (string) $pendaftaran->id,
            'folder_name'    => $folderName,
            'mahasiswa'      => [
                'nama'     => $pendaftaran->user->name    ?? 'Unknown',
                'nim'      => $pendaftaran->user->nim     ?? null,
                'angkatan' => $pendaftaran->angkatan      ?? null,
            ],
            'judul_skripsi' => $pendaftaran->judul_skripsi ?? '',
            'dosen_list'    => $dosenList,
            'files'         => $files,
        ];

        $payloadSize = strlen(json_encode($payload));
        Log::info('[RepodosenSync] Sync skripsi lengkap', [
            'pendaftaran_id' => $pendaftaran->id,
            'folder_name' => $folderName,
            'files_included' => array_keys(array_filter($files, fn($f) => isset($f['content']))),
            'payload_size_kb' => round($payloadSize / 1024, 2),
            'has_files' => $hasFiles,
        ]);

        $endpoint = $this->baseUrl . '/api/sync/skripsi';

        return $this->post($endpoint, $payload, $pendaftaran->id);
    }

    /**
     * Sync seminar proposal ke repodosen
     */
    public function syncSeminarProposal(PendaftaranSeminarProposal $pendaftaran): array
    {
        $pendaftaran->loadMissing(['dosenPembimbing', 'user']);

        // Build dosen list untuk seminar proposal
        $dosenList = [];
        if ($pendaftaran->dosenPembimbing) {
            $dosenList[] = [
                'nama' => $pendaftaran->dosenPembimbing->name,
                'nip'  => $pendaftaran->dosenPembimbing->nip ?? null,
                'nidn' => $pendaftaran->dosenPembimbing->nidn ?? null,
                'role' => 'pembimbing',
            ];
        }

        if (empty($dosenList)) {
            return [
                'success' => false,
                'message' => 'Tidak ada data dosen pembimbing untuk disync.',
                'results' => [],
            ];
        }

        // Encode files
        $files = $this->encodeFilesSempro($pendaftaran);
        $hasFiles = !empty(array_filter($files));

        // Generate folder name
        $folderName = $this->generateFolderNameSempro($pendaftaran);

        $payload = [
            'source'         => 'presma',
            'pendaftaran_id' => (string) $pendaftaran->id,
            'type'           => 'seminar_proposal',
            'folder_name'    => $folderName,
            'mahasiswa'      => [
                'nama'     => $pendaftaran->user->name    ?? 'Unknown',
                'nim'      => $pendaftaran->user->nim     ?? null,
                'angkatan' => $pendaftaran->angkatan      ?? null,
            ],
            'judul_skripsi' => $pendaftaran->judul_skripsi ?? '',
            'dosen_list'    => $dosenList,
            'files'         => $files,
        ];

        $payloadSize = strlen(json_encode($payload));
        Log::info('[RepodosenSync] Sync seminar proposal', [
            'pendaftaran_id' => $pendaftaran->id,
            'folder_name' => $folderName,
            'files_included' => array_keys(array_filter($files)),
            'payload_size_kb' => round($payloadSize / 1024, 2),
        ]);

        $endpoint = $this->baseUrl . '/api/sync/skripsi';

        return $this->post($endpoint, $payload, $pendaftaran->id);
    }

    /**
     * Sync dosen pembimbing seminar proposal
     */
    public function syncDosenPembimbingSempro(PendaftaranSeminarProposal $pendaftaran): array
    {
        $pendaftaran->loadMissing(['dosenPembimbing', 'user']);

        $dosenList = [];
        if ($pendaftaran->dosenPembimbing) {
            $dosenList[] = [
                'nama' => $pendaftaran->dosenPembimbing->name,
                'nip'  => $pendaftaran->dosenPembimbing->nip ?? null,
                'nidn' => $pendaftaran->dosenPembimbing->nidn ?? null,
                'role' => 'pembimbing',
            ];
        }

        if (empty($dosenList)) {
            return [
                'success' => false,
                'message' => 'Tidak ada data dosen pembimbing untuk disync.',
                'results' => [],
            ];
        }

        $payload = [
            'source'     => 'presma',
            'type'       => 'seminar_proposal',
            'dosen_list' => $dosenList,
        ];

        $endpoint = $this->baseUrl . '/api/sync/dosen-pembimbing';

        Log::info('[RepodosenSync] Sync dosen pembimbing seminar proposal', [
            'pendaftaran_id' => $pendaftaran->id,
            'dosen_count'    => count($dosenList),
        ]);

        return $this->post($endpoint, $payload, $pendaftaran->id);
    }

    /**
     * Generate folder name dari nama mahasiswa dan judul skripsi untuk ujian hasil
     */
    private function generateFolderName(PendaftaranUjianHasil $pendaftaran): string
    {
        $nama = preg_replace('/[^a-zA-Z0-9\s]/', '', $pendaftaran->user->name ?? 'Unknown');
        $judul = preg_replace('/[^a-zA-Z0-9\s]/', '', $pendaftaran->judul_skripsi ?? 'Skripsi');

        $nama = str_replace(' ', '_', trim($nama));
        $judul = implode('_', array_slice(explode(' ', trim($judul)), 0, 5));

        return "{$nama}_{$judul}";
    }

    /**
     * Generate folder name untuk seminar proposal
     */
    private function generateFolderNameSempro(PendaftaranSeminarProposal $pendaftaran): string
    {
        $nama = preg_replace('/[^a-zA-Z0-9\s]/', '', $pendaftaran->user->name ?? 'Unknown');
        $judul = preg_replace('/[^a-zA-Z0-9\s]/', '', $pendaftaran->judul_skripsi ?? 'Proposal');

        $nama = str_replace(' ', '_', trim($nama));
        $judul = implode('_', array_slice(explode(' ', trim($judul)), 0, 5));

        return "sempro_{$nama}_{$judul}";
    }

    /**
     * Encode file-file ke base64 dengan metadata folder untuk ujian hasil
     */
    private function encodeFilesWithMetadata(PendaftaranUjianHasil $pendaftaran): array
    {
        $files = [];
        $fileMap = [
            'skripsi' => [
                'field' => 'file_skripsi',
                'folder' => 'skripsi',
                'filename' => 'Skripsi.pdf',
                'label' => 'Skripsi'
            ],
            'sk_pembimbing' => [
                'field' => 'file_sk_pembimbing',
                'folder' => 'sk_pembimbing',
                'filename' => 'SK_Pembimbing.pdf',
                'label' => 'SK_Pembimbing'
            ],
            'proposal' => [
                'field' => 'file_proposal',
                'folder' => 'proposal',
                'filename' => 'Proposal.pdf',
                'label' => 'Proposal'
            ],
        ];

        foreach ($fileMap as $key => $config) {
            $path = $pendaftaran->{$config['field']} ?? null;
            
            if (!$path) {
                continue;
            }

            $content = $this->readFile($path, $config['label']);
            if ($content !== null) {
                $originalSize = strlen($content);
                $maxSize = 15 * 1024 * 1024;

                if ($originalSize > $maxSize) {
                    Log::warning("[RepodosenSync] File {$key} terlalu besar");
                    continue;
                }

                $files[$key] = [
                    'content' => base64_encode($content),
                    'folder' => $config['folder'],
                    'filename' => $config['filename'],
                    'size' => $originalSize,
                ];

                Log::info("[RepodosenSync] File {$key} berhasil diencode", [
                    'folder' => $config['folder'],
                    'size_kb' => round($originalSize / 1024, 2),
                ]);
            }
        }

        return $files;
    }

    /**
     * Encode files untuk seminar proposal
     */
    private function encodeFilesSempro(PendaftaranSeminarProposal $pendaftaran): array
    {
        $files = [];
        
        // File Proposal
        if ($pendaftaran->file_proposal_penelitian) {
            $content = $this->readFile($pendaftaran->file_proposal_penelitian, 'Proposal');
            if ($content !== null) {
                $files['proposal'] = base64_encode($content);
                Log::info('[RepodosenSync] File proposal sempro berhasil diencode', [
                    'size_kb' => round(strlen($content) / 1024, 2),
                ]);
            }
        }
        
        // File Surat Permohonan
        if ($pendaftaran->file_surat_permohonan) {
            $content = $this->readFile($pendaftaran->file_surat_permohonan, 'Surat_Permohonan');
            if ($content !== null) {
                $files['surat_permohonan'] = base64_encode($content);
                Log::info('[RepodosenSync] File surat permohonan sempro berhasil diencode', [
                    'size_kb' => round(strlen($content) / 1024, 2),
                ]);
            }
        }

        return $files;
    }

    /**
     * Build dosen list untuk ujian hasil
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

    /**
     * HTTP POST request ke repodosen
     */
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

            Log::info('[RepodosenSync] Response details', [
                'pendaftaran_id' => $pendaftaranId,
                'endpoint' => $endpoint,
                'http_status' => $response->status(),
            ]);

            if ($response->successful()) {
                Log::info('[RepodosenSync] Berhasil', [
                    'pendaftaran_id' => $pendaftaranId,
                    'synced' => $body['synced'] ?? 0,
                    'failed' => $body['failed'] ?? 0,
                ]);

                return [
                    'success' => true,
                    'message' => $body['message'] ?? 'Sync berhasil',
                    'synced' => $body['synced'] ?? 0,
                    'failed' => $body['failed'] ?? 0,
                    'results' => $body['results'] ?? [],
                ];
            } else {
                Log::error('[RepodosenSync] HTTP ' . $response->status(), [
                    'pendaftaran_id' => $pendaftaranId,
                    'endpoint' => $endpoint,
                    'response_body' => $body,
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

    /**
     * Baca file dari disk
     */
    private function readFile(string $path, string $label): ?string
    {
        $disks = ['local', 'public'];

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
            }
        }

        Log::warning("[RepodosenSync] File '{$label}' tidak ditemukan", [
            'path' => $path,
            'checked_disks' => $disks
        ]);

        return null;
    }
}