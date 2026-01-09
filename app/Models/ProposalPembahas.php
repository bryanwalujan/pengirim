<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ProposalPembahas extends Model
{
    protected $fillable = [
        'pendaftaran_seminar_proposal_id',
        'dosen_id',
        'posisi',
    ];

    protected $casts = [
        'posisi' => 'integer',
    ];

    // ========== RELATIONS ==========
    public function pendaftaranSeminarProposal()
    {
        return $this->belongsTo(PendaftaranSeminarProposal::class, 'pendaftaran_seminar_proposal_id');
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    // ========== HELPER METHODS ==========
    public function getPosisiLabelAttribute(): string
    {
        $labels = [
            1 => 'Pembahas 1',
            2 => 'Pembahas 2',
            3 => 'Pembahas 3',
        ];

        return $labels[$this->posisi] ?? 'Unknown';
    }

    // ========== VALIDATION HELPERS ==========
    public static function canAssignPembahas(int $pendaftaranId, int $dosenId, int $posisi): array
    {
        // Check if posisi is valid
        if (!in_array($posisi, [1, 2, 3])) {
            return [
                'can_assign' => false,
                'message' => 'Posisi pembahas harus 1, 2, atau 3.',
            ];
        }

        // Check if dosen is already assigned to this pendaftaran
        $existingAssignment = self::where('pendaftaran_seminar_proposal_id', $pendaftaranId)
            ->where('dosen_id', $dosenId)
            ->first();

        if ($existingAssignment) {
            return [
                'can_assign' => false,
                'message' => 'Dosen ini sudah ditugaskan sebagai ' . $existingAssignment->posisi_label . ' untuk pendaftaran ini.',
            ];
        }

        // Check if posisi is already filled
        $posisiTaken = self::where('pendaftaran_seminar_proposal_id', $pendaftaranId)
            ->where('posisi', $posisi)
            ->exists();

        if ($posisiTaken) {
            return [
                'can_assign' => false,
                'message' => 'Posisi Pembahas ' . $posisi . ' sudah terisi.',
            ];
        }

        // Check if dosen is the pembimbing
        $pendaftaran = PendaftaranSeminarProposal::find($pendaftaranId);
        if ($pendaftaran && $pendaftaran->dosen_pembimbing_id == $dosenId) {
            return [
                'can_assign' => false,
                'message' => 'Dosen pembimbing tidak dapat menjadi pembahas.',
            ];
        }

        return [
            'can_assign' => true,
            'message' => 'Pembahas dapat ditugaskan.',
        ];
    }

    // ========== BOOT METHOD ==========
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            Log::info('Creating ProposalPembahas', [
                'pendaftaran_id' => $model->pendaftaran_seminar_proposal_id,
                'dosen_id' => $model->dosen_id,
                'posisi' => $model->posisi,
            ]);
        });

        static::created(function ($model) {
            Log::info('ProposalPembahas created successfully', [
                'id' => $model->id,
                'pendaftaran_id' => $model->pendaftaran_seminar_proposal_id,
                'dosen_id' => $model->dosen_id,
                'posisi' => $model->posisi,
            ]);

            // Check if all 3 pembahas are assigned
            $pendaftaran = $model->pendaftaranSeminarProposal;
            if ($pendaftaran && $pendaftaran->proposalPembahas()->count() === 3) {
                // Update status to pembahas_ditentukan
                $pendaftaran->update(['status' => 'pembahas_ditentukan']);

                Log::info('All pembahas assigned, status updated', [
                    'pendaftaran_id' => $pendaftaran->id,
                    'status' => 'pembahas_ditentukan',
                ]);
            }
        });

        static::deleting(function ($model) {
            Log::info('Deleting ProposalPembahas', [
                'id' => $model->id,
                'pendaftaran_id' => $model->pendaftaran_seminar_proposal_id,
                'dosen_id' => $model->dosen_id,
                'posisi' => $model->posisi,
            ]);
        });
    }
}