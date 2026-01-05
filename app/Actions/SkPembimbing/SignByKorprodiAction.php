<?php
// filepath: /c:/laragon/www/eservice-app/app/Actions/SkPembimbing/SignByKorprodiAction.php

namespace App\Actions\SkPembimbing;

use App\Models\PengajuanSkPembimbing;
use App\Models\User;
use App\Services\SkPembimbingPdfService;
use Illuminate\Support\Facades\DB;

class SignByKorprodiAction
{
    public function __construct(
        private readonly SkPembimbingPdfService $pdfService
    ) {}

    public function execute(PengajuanSkPembimbing $pengajuan, User $korprodi): array
    {
        if (!$pengajuan->canBeSignedByKorprodi($korprodi)) {
            return ['success' => false, 'message' => 'Anda tidak dapat menandatangani pengajuan ini.'];
        }

        return DB::transaction(function () use ($pengajuan, $korprodi) {
            // Generate nomor surat
            $nomorSurat = $this->generateNomorSurat();

            // Update pengajuan
            $pengajuan->update([
                'status' => PengajuanSkPembimbing::STATUS_SELESAI,
                'ttd_korprodi_by' => $korprodi->id,
                'ttd_korprodi_at' => now(),
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