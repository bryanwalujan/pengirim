<?php
// filepath: app/Services/SkPembimbing/SkPembimbingPdfService.php

namespace App\Services\SkPembimbing;

use App\Models\KopSurat;
use App\Models\PengajuanSkPembimbing;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SkPembimbingPdfService
{
    /**
     * Generate Initial PDF (Draft - Tanpa Signature)
     */
    public function generateInitialPdf(PengajuanSkPembimbing $pengajuan): string
    {
        Log::info('Generating initial SK Pembimbing PDF (draft)', [
            'pengajuan_id' => $pengajuan->id,
        ]);

        $pengajuan->load([
            'mahasiswa',
            'dosenPembimbing1',
            'dosenPembimbing2',
            'beritaAcara.jadwalSeminarProposal.pendaftaranSeminarProposal',
        ]);

        $kopSurat = KopSurat::first();

        $pdf = Pdf::loadView('admin.sk-pembimbing.pdf', [
            'surat' => $pengajuan,
            'kopSurat' => $kopSurat,
            'show_korprodi_signature' => false,
            'show_kajur_signature' => false,
        ])->setPaper('a4', 'portrait');

        $nimSanitized = preg_replace('/[^a-zA-Z0-9]/', '', $pengajuan->mahasiswa->nim ?? 'unknown');
        $timestamp = now()->format('Ymd_His');
        $fileName = sprintf('sk_pembimbing_%s_%s_draft.pdf', $nimSanitized, $timestamp);

        $yearMonth = now()->format('Y/m');
        $filePath = "sk-pembimbing/{$yearMonth}/{$fileName}";

        $directory = dirname($filePath);
        if (!Storage::disk('local')->exists($directory)) {
            Storage::disk('local')->makeDirectory($directory);
        }

        Storage::disk('local')->put($filePath, $pdf->output());

        Log::info('Initial SK Pembimbing PDF generated', [
            'pengajuan_id' => $pengajuan->id,
            'path' => $filePath,
        ]);

        return $filePath;
    }

    /**
     * Regenerate PDF dengan QR codes (dipanggil setelah signature)
     */
    public function regeneratePdfWithQr(PengajuanSkPembimbing $pengajuan): void
    {
        $pengajuan->load([
            'mahasiswa',
            'dosenPembimbing1',
            'dosenPembimbing2',
            'ttdKorprodiUser',
            'ttdKajurUser',
            'beritaAcara.jadwalSeminarProposal.pendaftaranSeminarProposal',
        ]);

        Log::info('Regenerating SK Pembimbing PDF with QR codes', [
            'pengajuan_id' => $pengajuan->id,
            'korprodi_signed' => $pengajuan->isKorprodiSigned(),
            'kajur_signed' => $pengajuan->isKajurSigned(),
        ]);

        $kopSurat = KopSurat::first();

        $pdf = Pdf::loadView('admin.sk-pembimbing.pdf', [
            'surat' => $pengajuan,
            'kopSurat' => $kopSurat,
            'show_korprodi_signature' => $pengajuan->isKorprodiSigned(),
            'show_kajur_signature' => $pengajuan->isKajurSigned(),
        ])->setPaper('a4', 'portrait');

        $oldFilePath = $pengajuan->file_surat_sk;

        $nimSanitized = preg_replace('/[^a-zA-Z0-9]/', '', $pengajuan->mahasiswa->nim ?? 'unknown');
        $timestamp = now()->format('Ymd_His');

        if ($pengajuan->isFullySigned()) {
            $statusSuffix = 'final';
        } elseif ($pengajuan->isKorprodiSigned()) {
            $statusSuffix = 'korprodi_signed';
        } else {
            $statusSuffix = 'draft';
        }

        $fileName = sprintf('sk_pembimbing_%s_%s_%s.pdf', $nimSanitized, $timestamp, $statusSuffix);

        $yearMonth = now()->format('Y/m');
        $filePath = "sk-pembimbing/{$yearMonth}/{$fileName}";

        $directory = dirname($filePath);
        if (!Storage::disk('local')->exists($directory)) {
            Storage::disk('local')->makeDirectory($directory);
        }

        Storage::disk('local')->put($filePath, $pdf->output());

        $pengajuan->update(['file_surat_sk' => $filePath]);

        // Delete old file
        if ($oldFilePath && $oldFilePath !== $filePath && Storage::disk('local')->exists($oldFilePath)) {
            Storage::disk('local')->delete($oldFilePath);
        }

        Log::info('SK Pembimbing PDF regenerated with QR', [
            'pengajuan_id' => $pengajuan->id,
            'new_path' => $filePath,
            'status' => $statusSuffix,
        ]);
    }
}