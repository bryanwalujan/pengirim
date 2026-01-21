<?php

namespace App\Services\PendaftaranUjianHasil;

use App\Models\BeritaAcaraSeminarProposal;
use App\Models\PendaftaranUjianHasil;
use App\Models\PengujiUjianHasil;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PengujiService
{
    /**
     * Get available penguji from Berita Acara Seminar Proposal
     */
    public function getAvailablePengujiFromBA(int $userId): array
    {
        // Find the student's completed Berita Acara Sempro
        $beritaAcara = BeritaAcaraSeminarProposal::whereHas('jadwalSeminarProposal.pendaftaranSeminarProposal', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
        ->where('status', 'selesai')
        ->where('keputusan', 'Lanjut')
        ->with(['jadwalSeminarProposal.dosenPenguji'])
        ->latest()
        ->first();

        if (!$beritaAcara || !$beritaAcara->jadwalSeminarProposal) {
            return [];
        }

        // Get penguji from jadwal (excluding pembimbing/ketua)
        $penguji = $beritaAcara->jadwalSeminarProposal->dosenPenguji
            ->filter(function ($dosen) {
                // Only get Anggota Pembahas (the actual penguji)
                return str_contains($dosen->pivot->posisi, 'Anggota');
            })
            ->values()
            ->toArray();

        return $penguji;
    }

    /**
     * Assign penguji to pendaftaran
     */
    public function assignPenguji(
        PendaftaranUjianHasil $pendaftaran,
        array $pengujiIds,
        int $assignedBy
    ): bool {
        DB::beginTransaction();
        try {
            $isUpdate = $pendaftaran->pengujiUjianHasil()->exists();

            // Delete existing penguji
            $pendaftaran->pengujiUjianHasil()->delete();

            // Delete existing surat if updating
            if ($isUpdate && $pendaftaran->suratUsulanSkripsi) {
                $this->deleteSuratWithFile($pendaftaran);
            }

            // Create penguji assignments
            $posisiMap = [
                'penguji_1_id' => 'Penguji 1',
                'penguji_2_id' => 'Penguji 2',
                'penguji_3_id' => 'Penguji 3',
                'penguji_tambahan_id' => 'Penguji Tambahan',
            ];

            foreach ($posisiMap as $key => $posisi) {
                if (!empty($pengujiIds[$key])) {
                    PengujiUjianHasil::create([
                        'pendaftaran_ujian_hasil_id' => $pendaftaran->id,
                        'dosen_id' => $pengujiIds[$key],
                        'posisi' => $posisi,
                        'sumber' => in_array($key, ['penguji_1_id', 'penguji_2_id', 'penguji_3_id']) 
                            ? 'berita_acara' 
                            : 'manual',
                    ]);
                }
            }

            // Update pendaftaran status
            $pendaftaran->update([
                'tanggal_penentuan_penguji' => now(),
                'ditentukan_oleh_id' => $assignedBy,
                'status' => 'penguji_ditentukan'
            ]);

            DB::commit();

            Log::info('Penguji assigned/updated', [
                'pendaftaran_id' => $pendaftaran->id,
                'is_update' => $isUpdate,
                'assigned_by' => $assignedBy,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error assigning penguji', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Reset penguji assignment
     */
    public function resetPenguji(PendaftaranUjianHasil $pendaftaran, int $resetBy): bool
    {
        DB::beginTransaction();
        try {
            // Delete penguji assignments
            $pendaftaran->pengujiUjianHasil()->delete();

            // Delete surat if exists
            if ($pendaftaran->suratUsulanSkripsi) {
                $this->deleteSuratWithFile($pendaftaran);
            }

            // Update status back to pending
            $pendaftaran->update([
                'tanggal_penentuan_penguji' => null,
                'ditentukan_oleh_id' => null,
                'status' => 'pending'
            ]);

            DB::commit();

            Log::info('Penguji reset', [
                'pendaftaran_id' => $pendaftaran->id,
                'reset_by' => $resetBy
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error resetting penguji', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Delete surat with its file
     */
    private function deleteSuratWithFile(PendaftaranUjianHasil $pendaftaran): void
    {
        if ($pendaftaran->suratUsulanSkripsi) {
            $pendaftaran->suratUsulanSkripsi->delete();
        }
    }

    /**
     * Validate penguji assignment
     */
    public function canAssignPenguji(PendaftaranUjianHasil $pendaftaran): array
    {
        // Check if already signed
        if ($pendaftaran->suratUsulanSkripsi && 
            ($pendaftaran->suratUsulanSkripsi->isKaprodiSigned() || 
             $pendaftaran->suratUsulanSkripsi->isKajurSigned())) {
            return [
                'can_assign' => false,
                'message' => 'Tidak dapat mengubah penguji karena surat sudah ditandatangani.'
            ];
        }

        // Check status
        if (!in_array($pendaftaran->status, ['pending', 'penguji_ditentukan'])) {
            return [
                'can_assign' => false,
                'message' => 'Status tidak valid untuk penentuan penguji.'
            ];
        }

        return [
            'can_assign' => true,
            'message' => 'Penguji dapat ditentukan.'
        ];
    }

    /**
     * Get all dosen available for penguji selection
     */
    public function getAvailableDosen(PendaftaranUjianHasil $pendaftaran): \Illuminate\Support\Collection
    {
        // Get dosen excluding pembimbing 1 & 2
        return User::role('dosen')
            ->where('id', '!=', $pendaftaran->dosen_pembimbing1_id)
            ->where('id', '!=', $pendaftaran->dosen_pembimbing2_id)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get penguji statistics
     */
    public function getPengujiStatistics(): array
    {
        return PendaftaranUjianHasil::getPengujiStatistics();
    }
}
