<?php

namespace App\Enums;

/**
 * Status enum for Berita Acara documents.
 *
 * Workflow Baru (setelah update):
 * DRAFT -> MENUNGGU_TTD_PENGUJI -> MENUNGGU_TTD_PANITIA_SEKRETARIS -> MENUNGGU_TTD_PANITIA_KETUA -> SELESAI
 *
 * Note: MENUNGGU_TTD_KETUA is deprecated and kept for backward compatibility only.
 */
enum BeritaAcaraStatus: string
{
    case DRAFT = 'draft';
    case MENUNGGU_TTD_PENGUJI = 'menunggu_ttd_penguji';

    /**
     * @deprecated Tidak digunakan lagi dalam workflow baru.
     * Setelah semua penguji TTD, langsung ke Sekretaris Panitia.
     * Kept for backward compatibility with existing data.
     */
    case MENUNGGU_TTD_KETUA = 'menunggu_ttd_ketua';

    case MENUNGGU_TTD_PANITIA_SEKRETARIS = 'menunggu_ttd_panitia_sekretaris';
    case MENUNGGU_TTD_PANITIA_KETUA = 'menunggu_ttd_panitia_ketua';
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
            self::MENUNGGU_TTD_KETUA => 'Menunggu Ketua Penguji (Deprecated)',
            self::MENUNGGU_TTD_PANITIA_SEKRETARIS => 'Menunggu TTD Sekretaris Panitia',
            self::MENUNGGU_TTD_PANITIA_KETUA => 'Menunggu TTD Ketua Panitia',
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
            self::MENUNGGU_TTD_PANITIA_SEKRETARIS => '<span class="badge bg-label-warning"><i class="bx bx-pen me-1"></i>Menunggu TTD Sekretaris</span>',
            self::MENUNGGU_TTD_PANITIA_KETUA => '<span class="badge bg-label-primary"><i class="bx bx-pen me-1"></i>Menunggu TTD Ketua Panitia</span>',
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
            self::MENUNGGU_TTD_PANITIA_SEKRETARIS => 'Menunggu Sekretaris Panitia (Korprodi) menandatangani',
            self::MENUNGGU_TTD_PANITIA_KETUA => 'Menunggu Ketua Panitia (Dekan) menandatangani',
            self::SELESAI => 'Berita acara telah selesai dan ditandatangani',
            self::DITOLAK => 'Berita acara ditolak',
        };
    }

    /**
     * Get color for the status
     */
    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'secondary',
            self::MENUNGGU_TTD_PENGUJI => 'info',
            self::MENUNGGU_TTD_KETUA => 'primary',
            self::MENUNGGU_TTD_PANITIA_SEKRETARIS => 'warning',
            self::MENUNGGU_TTD_PANITIA_KETUA => 'primary',
            self::SELESAI => 'success',
            self::DITOLAK => 'danger',
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
