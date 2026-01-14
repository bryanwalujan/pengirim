<?php
// filepath: /c:/laragon/www/eservice-app/app/Actions/SkPembimbing/SignByKajurAction.php

namespace App\Actions\SkPembimbing;

use App\Models\PengajuanSkPembimbing;
use App\Models\User;
use App\Services\SkPembimbingPdfService;
use Illuminate\Support\Facades\DB;

class SignByKajurAction
{
    public function __construct(
        private readonly SkPembimbingPdfService $pdfService
    ) {}

    public function execute(PengajuanSkPembimbing $pengajuan, User $kajur): array
    {
        if (!$pengajuan->canBeSignedByKajur($kajur)) {
            return ['success' => false, 'message' => 'Anda tidak dapat menandatangani pengajuan ini.'];
        }

        return DB::transaction(function () use ($pengajuan, $kajur) {
            // Generate nomor surat
            $nomorSurat = $this->generateNomorSurat();

            // Kajur signs LAST - Complete & Generate PDF
            $pengajuan->update([
                'status' => PengajuanSkPembimbing::STATUS_SELESAI,
                'ttd_kajur_by' => $kajur->id,
                'ttd_kajur_at' => now(),
                'nomor_surat' => $nomorSurat,
                'tanggal_surat' => now()->toDateString(),
            ]);

            // Generate PDF
            $pdfPath = $this->pdfService->generate($pengajuan);
            $pengajuan->update(['file_surat_sk' => $pdfPath]);

            return ['success' => true, 'message' => 'SK Pembimbing berhasil diterbitkan.'];
        });
    }

    private function generateNomorSurat(): string
    {
        $year = now()->year;
        $count = PengajuanSkPembimbing::whereYear('tanggal_surat', $year)
            ->whereNotNull('nomor_surat')
            ->count() + 1;

        return sprintf('%03d/SK-PMB/PTIK/%d', $count, $year);
    }
}