<?php

namespace App\Enums;

enum BeritaAcaraStatus: string
{
    case DRAFT = 'draft';
    case MENUNGGU_TTD_PENGUJI = 'menunggu_ttd_penguji';
    case MENUNGGU_TTD_KETUA = 'menunggu_ttd_ketua';
    case SELESAI = 'selesai';
    case DITOLAK = 'ditolak';

    /**
     * Get human-readable label for the status
     */
    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::MENUNGGU_TTD_PENGUJI => 'Menunggu TTD Penguji',
            self::MENUNGGU_TTD_KETUA => 'Menunggu Ketua Penguji',
            self::SELESAI => 'Selesai',
            self::DITOLAK => 'Ditolak',
        };
    }

    /**
     * Get badge HTML for the status
     */
    public function badge(): string
    {
        return match ($this) {
            self::DRAFT => '<span class="badge bg-label-secondary"><i class="bx bx-edit me-1"></i>Draft</span>',
            self::MENUNGGU_TTD_PENGUJI => '<span class="badge bg-label-info"><i class="bx bx-time me-1"></i>Menunggu TTD Penguji</span>',
            self::MENUNGGU_TTD_KETUA => '<span class="badge bg-label-primary"><i class="bx bx-pen me-1"></i>Menunggu Ketua Penguji</span>',
            self::SELESAI => '<span class="badge bg-label-success"><i class="bx bx-check-circle me-1"></i>Selesai</span>',
            self::DITOLAK => '<span class="badge bg-label-danger"><i class="bx bx-x-circle me-1"></i>Ditolak - Perlu Dijadwalkan Ulang</span>',
        };
    }

    /**
     * Get workflow message for the status
     */
    public function workflowMessage(int $signed = 0, int $total = 0): string
    {
        return match ($this) {
            self::DRAFT => 'Draft berita acara',
            self::MENUNGGU_TTD_PENGUJI => "Menunggu persetujuan dari dosen penguji ({$signed}/{$total} sudah TTD)",
            self::MENUNGGU_TTD_KETUA => 'Menunggu ketua penguji menandatangani berita acara',
            self::SELESAI => 'Berita acara telah selesai dan ditandatangani',
            self::DITOLAK => 'Berita acara ditolak',
        };
    }

    /**
     * Check if status is final (completed or rejected)
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::SELESAI, self::DITOLAK], true);
    }

    /**
     * Check if status allows editing
     */
    public function isEditable(): bool
    {
        return in_array($this, [self::DRAFT, self::MENUNGGU_TTD_PENGUJI], true);
    }
}