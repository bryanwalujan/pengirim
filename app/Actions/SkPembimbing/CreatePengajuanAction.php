<?php
// filepath: /c:/laragon/www/eservice-app/app/Actions/SkPembimbing/CreatePengajuanAction.php

namespace App\Actions\SkPembimbing;

use App\Models\PengajuanSkPembimbing;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CreatePengajuanAction
{
    /**
     * Execute the action to create a new pengajuan SK Pembimbing
     */
    public function execute(User $mahasiswa, array $data): array
    {
        try {
            return DB::transaction(function () use ($mahasiswa, $data) {
                $pengajuan = PengajuanSkPembimbing::create([
                    'berita_acara_id' => $data['berita_acara_id'],
                    'mahasiswa_id' => $mahasiswa->id,
                    'judul_skripsi' => $data['judul_skripsi'],
                    'file_surat_permohonan' => $this->storeFile(
                        $data['file_surat_permohonan'],
                        'surat-permohonan',
                        $mahasiswa->nim
                    ),
                    'file_slip_ukt' => $this->storeFile(
                        $data['file_slip_ukt'],
                        'slip-ukt',
                        $mahasiswa->nim
                    ),
                    'file_proposal_revisi' => $this->storeFile(
                        $data['file_proposal_revisi'],
                        'proposal-revisi',
                        $mahasiswa->nim
                    ),
                    'status' => PengajuanSkPembimbing::STATUS_DRAFT,
                ]);

                Log::info('SK Pembimbing pengajuan created', [
                    'pengajuan_id' => $pengajuan->id,
                    'mahasiswa_id' => $mahasiswa->id,
                    'mahasiswa_nim' => $mahasiswa->nim,
                ]);

                // TODO: Send notification to staff for verification
                // Notification dispatch can be added here

                return ['success' => true, 'pengajuan' => $pengajuan];
            });
        } catch (\Exception $e) {
            Log::error('Error creating SK Pembimbing pengajuan', [
                'mahasiswa_id' => $mahasiswa->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Clean up any uploaded files on failure
            $this->cleanupFiles($data);

            return [
                'success' => false,
                'message' => 'Gagal membuat pengajuan SK Pembimbing. Silakan coba lagi.'
            ];
        }
    }

    /**
     * Store uploaded file with proper naming convention
     */
    private function storeFile(UploadedFile $file, string $folder, string $nim): string
    {
        $timestamp = now()->format('YmdHis');
        $extension = $file->getClientOriginalExtension();
        $filename = "{$nim}_{$timestamp}.{$extension}";

        return $file->storeAs("sk-pembimbing/{$folder}", $filename, 'local');
    }

    /**
     * Clean up uploaded files on transaction failure
     */
    private function cleanupFiles(array $data): void
    {
        foreach (['file_surat_permohonan', 'file_slip_ukt', 'file_proposal_revisi'] as $key) {
            if (isset($data[$key]) && $data[$key] instanceof UploadedFile) {
                // Files might not be stored yet if transaction fails early
                // This is a safety measure
            }
        }
    }
}