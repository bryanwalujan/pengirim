<?php
// filepath: /c:/laragon/www/eservice-app/app/Services/SkPembimbingPdfService.php

namespace App\Services;

use App\Models\PengajuanSkPembimbing;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class SkPembimbingPdfService
{
    public function generate(PengajuanSkPembimbing $pengajuan): string
    {
        $pengajuan->load([
            'mahasiswa',
            'dosenPembimbing1',
            'dosenPembimbing2',
            'ttdKajurUser',
            'ttdKorprodiUser',
            'beritaAcara.jadwalSeminarProposal.pendaftaranSeminarProposal',
        ]);

        $pdf = Pdf::loadView('pdf.sk-pembimbing', [
            'pengajuan' => $pengajuan,
            'qrCodeUrl' => $pengajuan->verification_url,
        ]);

        $filename = sprintf(
            'sk-pembimbing/%s_%s.pdf',
            $pengajuan->mahasiswa->nim,
            now()->format('YmdHis')
        );

        Storage::disk('local')->put($filename, $pdf->output());

        return $filename;
    }
}