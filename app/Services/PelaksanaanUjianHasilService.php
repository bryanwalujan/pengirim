<?php

namespace App\Services;

use App\Models\BeritaAcaraUjianHasil;
use Illuminate\Support\Facades\{Log, Storage};
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PelaksanaanUjianHasilService
{
    /**
     * Generate PDF preview (return base64 data URL untuk iframe)
     */
    public function generateBeritaAcaraPdfPreview(BeritaAcaraUjianHasil $beritaAcara): string
    {
        $beritaAcara->load([
            'jadwalUjianHasil.pendaftaranUjianHasil.user',
            'jadwalUjianHasil.dosenPenguji',
            'ketuaPenguji',
            'lembarKoreksis.dosen',
        ]);

        $jadwal = $beritaAcara->jadwalUjianHasil;
        $pendaftaran = $jadwal?->pendaftaranUjianHasil;
        $mahasiswa = $pendaftaran?->user ?? $beritaAcara->mahasiswa;

        // Generate QR Code
        $qrCode = base64_encode(
            QrCode::format('png')
                ->size(100)
                ->generate($beritaAcara->verification_url)
        );

        // Get penguji list
        $pengujiHadir = $jadwal?->dosenPenguji()->orderByRaw("
            CASE 
                WHEN posisi = 'Ketua Penguji' THEN 1 
                WHEN posisi = 'Penguji 1' THEN 2 
                WHEN posisi = 'Penguji 2' THEN 3 
                WHEN posisi = 'Penguji 3' THEN 4 
                ELSE 5 
            END
        ")->get() ?? collect();

        $pdf = Pdf::loadView('admin.berita-acara-ujian-hasil.pdf', [
            'beritaAcara' => $beritaAcara,
            'jadwal' => $jadwal,
            'pendaftaran' => $pendaftaran,
            'mahasiswa' => $mahasiswa,
            'pengujiHadir' => $pengujiHadir,
            'qrCode' => $qrCode,
            'isPreview' => true,
        ])->setPaper('a4', 'portrait');

        return 'data:application/pdf;base64,' . base64_encode($pdf->output());
    }

    /**
     * Generate final PDF dan simpan ke storage
     */
    public function generateBeritaAcaraPdf(BeritaAcaraUjianHasil $beritaAcara): ?string
    {
        try {
            $beritaAcara->load([
                'jadwalUjianHasil.pendaftaranUjianHasil.user',
                'jadwalUjianHasil.dosenPenguji',
                'ketuaPenguji',
                'lembarKoreksis.dosen',
            ]);

            $jadwal = $beritaAcara->jadwalUjianHasil;
            $pendaftaran = $jadwal?->pendaftaranUjianHasil;
            $mahasiswa = $pendaftaran?->user ?? $beritaAcara->mahasiswa;

            // Generate QR Code
            $qrCode = base64_encode(
                QrCode::format('png')
                    ->size(100)
                    ->generate($beritaAcara->verification_url)
            );

            // Get penguji list ordered
            $pengujiHadir = $jadwal?->dosenPenguji()->orderByRaw("
                CASE 
                    WHEN posisi = 'Ketua Penguji' THEN 1 
                    WHEN posisi = 'Penguji 1' THEN 2 
                    WHEN posisi = 'Penguji 2' THEN 3 
                    WHEN posisi = 'Penguji 3' THEN 4 
                    ELSE 5 
                END
            ")->get() ?? collect();

            $pdf = Pdf::loadView('admin.berita-acara-ujian-hasil.pdf', [
                'beritaAcara' => $beritaAcara,
                'jadwal' => $jadwal,
                'pendaftaran' => $pendaftaran,
                'mahasiswa' => $mahasiswa,
                'pengujiHadir' => $pengujiHadir,
                'qrCode' => $qrCode,
                'isPreview' => false,
            ])->setPaper('a4', 'portrait');

            // Create directory if not exists
            $directory = 'berita-acara-ujian-hasil';
            if (!Storage::disk('local')->exists($directory)) {
                Storage::disk('local')->makeDirectory($directory);
            }

            // Generate filename
            $nim = $mahasiswa?->nim ?? $beritaAcara->mahasiswa_nim ?? 'unknown';
            $filename = "BA_UjianHasil_{$nim}_" . now()->format('YmdHis') . '.pdf';
            $filePath = "{$directory}/{$filename}";

            // Delete old file if exists
            if ($beritaAcara->file_path && Storage::disk('local')->exists($beritaAcara->file_path)) {
                Storage::disk('local')->delete($beritaAcara->file_path);
            }

            // Save new file
            Storage::disk('local')->put($filePath, $pdf->output());

            Log::info('Berita Acara Ujian Hasil PDF generated', [
                'ba_id' => $beritaAcara->id,
                'file_path' => $filePath,
                'mahasiswa_nim' => $nim,
            ]);

            return $filePath;

        } catch (\Exception $e) {
            Log::error('Failed to generate Berita Acara Ujian Hasil PDF', [
                'ba_id' => $beritaAcara->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }
}
