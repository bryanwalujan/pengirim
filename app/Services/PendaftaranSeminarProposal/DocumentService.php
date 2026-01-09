<?php

namespace App\Services\PendaftaranSeminarProposal;

use App\Models\PendaftaranSeminarProposal;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentService
{
    /**
     * View file inline
     */
    public function viewFile(string $filePath, string $fileName): StreamedResponse
    {
        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        $path = Storage::disk('public')->path($filePath);
        $mimeType = mime_content_type($path);

        return response()->stream(function () use ($path) {
            readfile($path);
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ]);
    }

    /**
     * Download file
     */
    public function downloadFile(string $filePath, string $fileName): StreamedResponse
    {
        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        $path = Storage::disk('public')->path($filePath);

        return response()->streamDownload(function () use ($path) {
            readfile($path);
        }, $fileName);
    }

    /**
     * Get document filename
     */
    public function getDocumentFileName(
        PendaftaranSeminarProposal $pendaftaran,
        string $type
    ): string {
        $nim = $pendaftaran->user->nim;

        $fileNames = [
            'transkrip' => "Transkrip-{$nim}.pdf",
            'proposal' => "Proposal-{$nim}.pdf",
            'permohonan' => "Permohonan-{$nim}.pdf",
            'slip_ukt' => "Slip-UKT-{$nim}." . pathinfo($pendaftaran->file_slip_ukt, PATHINFO_EXTENSION),
        ];

        return $fileNames[$type] ?? "Document-{$nim}.pdf";
    }
}