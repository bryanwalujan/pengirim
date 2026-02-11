<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncMahasiswaData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mahasiswa:sync 
                            {--create-missing : Create user if not exists}
                            {--status=* : Sync only specific status (A, L, C, N, K). Can be used multiple times}
                            {--batch=50 : Number of records per page}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync mahasiswa data (status_aktif) from TI Unima API. Use --status to sync specific statuses only.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiUrl = env('TI_UNIMA_API_URL');
        $apiToken = env('TI_UNIMA_API_TOKEN');
        
        if (!$apiUrl) {
            $this->error('TI_UNIMA_API_URL not set in .env');
            return 1;
        }

        if (!$apiToken) {
            $this->warn('TI_UNIMA_API_TOKEN not set in .env. Request might fail if authentication is required.');
        }

        // Ambil opsi dari command
        $batchSize = (int) $this->option('batch');
        $requestedStatuses = $this->option('status');
        
        // Jika user specify status tertentu, gunakan itu. Jika tidak, sync semua
        $statuses = !empty($requestedStatuses) ? $requestedStatuses : ['A', 'L', 'C', 'N', 'K'];
        
        // Validasi status yang diminta
        $validStatuses = ['A', 'L', 'C', 'N', 'K'];
        foreach ($statuses as $status) {
            if (!in_array($status, $validStatuses)) {
                $this->error("Invalid status: $status. Valid statuses are: A, L, C, N, K");
                return 1;
            }
        }

        $this->info("Syncing statuses: " . implode(', ', $statuses));
        $this->info("Batch size: $batchSize records per page");
        
        $updatedCount = 0;
        $createdCount = 0;
        $skippedCount = 0;

        foreach ($statuses as $statusKode) {
            $this->info("Starting sync for status: $statusKode");
            
            $page = 1;
            $hasMorePages = true;

            while ($hasMorePages) {
                $this->info("Fetching page $page for status $statusKode...");
                
                try {
                    $response = Http::withToken($apiToken)
                        ->connectTimeout(10) // Waktu maksimal untuk mencoba menyambung ke server
                        ->timeout(120)       // Waktu maksimal untuk menunggu respon (120 detik)
                        ->retry(3, 100)      // Coba lagi 3 kali jika gagal, dengan jeda 100ms
                        ->get($apiUrl, [
                            'page' => $page,
                            'per_page' => $batchSize,
                            'status_aktif' => $statusKode,
                        ]);

                    if ($response->failed()) {
                        $this->error("Failed to fetch page $page for status $statusKode: " . $response->status());
                        Log::error("SyncMahasiswa error: " . $response->body());
                        // Don't abort entire sync, just try next status or page? 
                        // Better to break this status loop and try next status
                        break; 
                    }

                    $data = $response->json();
                    
                    $students = $data['data'] ?? [];
                    $meta = $data['meta'] ?? [];
                    $links = $data['links'] ?? [];

                    if (empty($students)) {
                        $hasMorePages = false;
                        break;
                    }

                    foreach ($students as $studentData) {
                        $nim = $studentData['nim'] ?? null;
                        
                        if (!$nim) continue;

                        $rawStatus = $studentData['status_aktif'] ?? null;
                        
                        // Normalisasi status
                        $statusTerbaru = $this->normalizeStatus($rawStatus);

                        $user = User::where('nim', $nim)->first();

                        if ($user) {
                            if ($statusTerbaru && $user->status_aktif !== $statusTerbaru) {
                                $oldStatus = $user->status_aktif;
                                
                                $user->status_aktif = $statusTerbaru;
                                $user->save();

                                $this->info("Updated NIM $nim: $oldStatus -> $statusTerbaru");
                                $updatedCount++;
                            } else {
                                $skippedCount++;
                            }
                        } else {
                            if ($this->option('create-missing')) {
                                //$this->warn("User missing for NIM: $nim");
                            }
                            $skippedCount++;
                        }
                    }

                    // Check pagination
                    if (isset($meta['last_page']) && $page >= $meta['last_page']) {
                        $hasMorePages = false;
                    } elseif (isset($links['next']) && $links['next'] === null) {
                        $hasMorePages = false;
                    } else {
                        $page++;
                    }
                    
                    // Safety break per status
                    if ($page > 500) $hasMorePages = false;

                } catch (\Exception $e) {
                    $this->error("Exception for status $statusKode: " . $e->getMessage());
                    break; // Move to next status
                }
            }
        }

        $this->info("Sync completed.");
        $this->info("Updated: $updatedCount");
        $this->info("Skipped: $skippedCount");
        
        return 0;
    }

    /**
     * Normalisasi status dari API ke format kode internal
     */
    private function normalizeStatus($status)
    {
        if (!$status) return null;
        
        $status = strtoupper(trim($status));

        // Jika sudah 1 huruf valid, kembalikan
        if (in_array($status, ['A', 'L', 'C', 'N', 'K'])) {
            return $status;
        }

        // Mapping dari kata penuh ke kode
        return match ($status) {
            'AKTIF' => 'A',
            'LULUS' => 'L',
            'CUTI' => 'C',
            'NON-AKTIF', 'NON AKTIF', 'DROP OUT', 'DO' => 'N',
            'KELUAR', 'UNDUR DIRI' => 'K',
            default => 'A', // Default fallback ke Aktif jika tidak dikenali, atau bisa null
        };
    }
}
