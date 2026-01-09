<?php

namespace App\Services\PendaftaranSeminarProposal;

use App\Models\PendaftaranSeminarProposal;
use App\Models\ProposalPembahas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PembahasService
{
    /**
     * Assign pembahas to pendaftaran
     */
    public function assignPembahas(
        PendaftaranSeminarProposal $pendaftaran,
        array $pembahasIds,
        int $assignedBy
    ): bool {
        DB::beginTransaction();
        try {
            $isUpdate = $pendaftaran->proposalPembahas()->exists();

            // Delete existing pembahas
            $pendaftaran->proposalPembahas()->delete();

            // Delete existing surat if updating
            if ($isUpdate && $pendaftaran->suratUsulan) {
                $this->deleteSuratWithFile($pendaftaran);
            }

            // Create new pembahas assignments
            foreach ([1, 2, 3] as $posisi) {
                ProposalPembahas::create([
                    'pendaftaran_seminar_proposal_id' => $pendaftaran->id,
                    'dosen_id' => $pembahasIds["pembahas_{$posisi}_id"],
                    'posisi' => $posisi,
                ]);
            }

            // Update pendaftaran
            $pendaftaran->update([
                'tanggal_penentuan_pembahas' => now(),
                'ditentukan_oleh_id' => $assignedBy,
                'status' => 'pembahas_ditentukan'
            ]);

            $this->syncPengujiToJadwal($pendaftaran);

            DB::commit();

            Log::info('Pembahas assigned/updated', [
                'pendaftaran_id' => $pendaftaran->id,
                'is_update' => $isUpdate,
                'assigned_by' => $assignedBy,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error assigning pembahas', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * ✅ NEW: Sync penguji ke jadwal setelah pembahas ditentukan
     */
    private function syncPengujiToJadwal(PendaftaranSeminarProposal $pendaftaran): void
    {
        // Cek apakah sudah ada jadwal
        $jadwal = $pendaftaran->jadwalSeminarProposal;

        if (!$jadwal) {
            return;
        }

        // Clear existing penguji
        $jadwal->dosenPenguji()->detach();

        // Attach pembimbing sebagai Ketua Penguji
        $jadwal->dosenPenguji()->attach($pendaftaran->dosen_pembimbing_id, [
            'posisi' => 'Ketua Pembahas',
        ]);

        // Attach pembahas 1, 2, 3
        foreach ($pendaftaran->proposalPembahas as $index => $pembahas) {
            $jadwal->dosenPenguji()->attach($pembahas->dosen_id, [
                'posisi' => 'Anggota Pembahas ' . ($index + 1),
            ]);
        }

        Log::info('Pemmbahas synced to jadwal', [
            'jadwal_id' => $jadwal->id,
            'total_penguji' => $jadwal->dosenPenguji()->count(),
        ]);
    }

    /**
     * Reset pembahas assignment
     */
    public function resetPembahas(
        PendaftaranSeminarProposal $pendaftaran,
        int $resetBy
    ): bool {
        DB::beginTransaction();
        try {
            // Delete pembahas assignments
            $pendaftaran->proposalPembahas()->delete();

            // Delete surat if exists
            if ($pendaftaran->suratUsulan) {
                $this->deleteSuratWithFile($pendaftaran);
            }

            // Update status back to pending
            $pendaftaran->update([
                'tanggal_penentuan_pembahas' => null,
                'ditentukan_oleh_id' => null,
                'status' => 'pending'
            ]);

            DB::commit();

            Log::info('Pembahas reset', [
                'pendaftaran_id' => $pendaftaran->id,
                'reset_by' => $resetBy
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error resetting pembahas', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete surat with its file
     */
    private function deleteSuratWithFile(PendaftaranSeminarProposal $pendaftaran): void
    {
        $oldFilePath = $pendaftaran->suratUsulan->file_surat;
        $pendaftaran->suratUsulan->delete();

        if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
            Storage::disk('public')->delete($oldFilePath);
        }
    }

    /**
     * Validate pembahas assignment
     */
    public function canAssignPembahas(PendaftaranSeminarProposal $pendaftaran): array
    {
        // Check if already signed
        if ($pendaftaran->isKaprodiSigned() || $pendaftaran->isKajurSigned()) {
            return [
                'can_assign' => false,
                'message' => 'Tidak dapat mengubah pembahas karena surat sudah ditandatangani.'
            ];
        }

        // Check status
        if (!in_array($pendaftaran->status, ['pending', 'pembahas_ditentukan'])) {
            return [
                'can_assign' => false,
                'message' => 'Status tidak valid untuk penentuan pembahas.'
            ];
        }

        return [
            'can_assign' => true,
            'message' => 'Pembahas dapat ditentukan.'
        ];
    }

    /**
     * Get pembahas statistics
     */
    public function getPembahasStatistics(): array
    {
        return PendaftaranSeminarProposal::getPembahasStatistics();
    }
}