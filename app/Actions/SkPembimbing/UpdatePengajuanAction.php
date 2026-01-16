<?php
// filepath: /c:/laragon/www/eservice-app/app/Actions/SkPembimbing/UpdatePengajuanAction.php

namespace App\Actions\SkPembimbing;

use App\Models\PengajuanSkPembimbing;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UpdatePengajuanAction
{
    /**
     * Execute the action to update pengajuan SK Pembimbing
     */
    public function execute(PengajuanSkPembimbing $pengajuan, array $data): array
    {
        try {
            return DB::transaction(function () use ($pengajuan, $data) {
                $updateData = ['judul_skripsi' => $data['judul_skripsi']];
                $oldFiles = [];

                // Handle file updates
                foreach (['file_surat_permohonan', 'file_slip_ukt', 'file_proposal_revisi'] as $field) {
                    if (isset($data[$field]) && $data[$field] instanceof UploadedFile) {
                        // Store old file path for cleanup
                        if ($pengajuan->$field) {
                            $oldFiles[] = $pengajuan->$field;
                        }

                        // Store new file with proper naming
                        $folder = str_replace('file_', '', $field);
                        $timestamp = now()->format('YmdHis');
                        $extension = $data[$field]->getClientOriginalExtension();
                        $filename = "{$pengajuan->mahasiswa->nim}_{$timestamp}.{$extension}";

                        $updateData[$field] = $data[$field]->storeAs(
                            "sk-pembimbing/{$folder}",
                            $filename,
                            'local'
                        );
                    }
                }

                $pengajuan->update($updateData);

                // Delete old files after successful update
                foreach ($oldFiles as $oldFile) {
                    if (Storage::disk('local')->exists($oldFile)) {
                        Storage::disk('local')->delete($oldFile);
                    }
                }

                Log::info('SK Pembimbing pengajuan updated', [
                    'pengajuan_id' => $pengajuan->id,
                    'mahasiswa_id' => $pengajuan->mahasiswa_id,
                    'files_updated' => array_keys(array_filter($updateData, fn($key) => str_starts_with($key, 'file_'), ARRAY_FILTER_USE_KEY)),
                ]);

                // TODO: Send notification if status changed
                // Notification dispatch can be added here

                return ['success' => true, 'pengajuan' => $pengajuan->fresh()];
            });
        } catch (\Exception $e) {
            Log::error('Error updating SK Pembimbing pengajuan', [
                'pengajuan_id' => $pengajuan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal memperbarui pengajuan SK Pembimbing. Silakan coba lagi.'
            ];
        }
    }
}