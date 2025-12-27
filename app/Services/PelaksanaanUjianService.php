<?php
// filepath: app/Services/PelaksanaanUjianService.php

namespace App\Services;

use App\Models\{
    JadwalSeminarProposal,
    BeritaAcaraSeminarProposal,
    LembarCatatanSeminarProposal,
    User
};
use Illuminate\Support\Facades\{DB, Storage, Log, Notification};
use Illuminate\Support\Str;
use App\Notifications\UndanganSeminarProposal;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PelaksanaanUjianService
{
    /**
     * Send undangan to dosen penguji
     */
    public function sendUndanganPenguji(JadwalSeminarProposal $jadwal): array
    {
        $dosenList = $jadwal->dosenPenguji()->get();
        $berhasil = 0;
        $gagal = 0;

        foreach ($dosenList as $dosen) {
            try {
                $dosen->notify(new UndanganSeminarProposal($jadwal, $dosen->name));
                $berhasil++;

                Log::info('Undangan terkirim', [
                    'jadwal_id' => $jadwal->id,
                    'dosen_id' => $dosen->id,
                    'dosen_name' => $dosen->name,
                ]);
            } catch (\Exception $e) {
                $gagal++;

                Log::error('Gagal kirim undangan', [
                    'jadwal_id' => $jadwal->id,
                    'dosen_id' => $dosen->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'berhasil' => $berhasil,
            'gagal' => $gagal,
            'total' => $dosenList->count(),
        ];
    }

    /**
     * Replace dosen penguji (jika berhalangan)
     */
    public function replaceDosen(
        JadwalSeminarProposal $jadwal,
        string $posisi,
        int $dosenPenggantiId,
        ?string $keterangan = null
    ): bool {
        try {
            DB::beginTransaction();

            // Update pivot status dosen lama
            $jadwal->dosenPenguji()
                ->wherePivot('posisi', $posisi)
                ->updateExistingPivot($jadwal->dosenPenguji()
                    ->wherePivot('posisi', $posisi)
                    ->first()
                    ->id, [
                        'dosen_pengganti_id' => $dosenPenggantiId,
                        'keterangan' => $keterangan,
                    ]);

            // Attach dosen pengganti
            $jadwal->dosenPenguji()->attach($dosenPenggantiId, [
                'posisi' => $posisi,
            ]);

            DB::commit();

            Log::info('Dosen berhasil diganti', [
                'jadwal_id' => $jadwal->id,
                'posisi' => $posisi,
                'dosen_pengganti_id' => $dosenPenggantiId,
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Gagal mengganti dosen', [
                'jadwal_id' => $jadwal->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Create Berita Acara
     */
    public function createBeritaAcara(
        JadwalSeminarProposal $jadwal,
        array $data,
        int $pembuatId
    ): BeritaAcaraSeminarProposal {
        try {
            DB::beginTransaction();

            $beritaAcara = BeritaAcaraSeminarProposal::create([
                'jadwal_seminar_proposal_id' => $jadwal->id,
                'catatan_kejadian' => $data['catatan_kejadian'],
                'keputusan' => $data['keputusan'],
                'catatan_tambahan' => $data['catatan_tambahan'] ?? null,
                'dibuat_oleh_id' => $pembuatId,
            ]);

            // Update status jadwal
            $jadwal->update(['status' => 'selesai']);

            DB::commit();

            Log::info('Berita Acara created', [
                'berita_acara_id' => $beritaAcara->id,
                'jadwal_id' => $jadwal->id,
            ]);

            return $beritaAcara;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Gagal membuat Berita Acara', [
                'jadwal_id' => $jadwal->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Submit lembar catatan dosen
     */
    public function submitCatatanDosen(
        BeritaAcaraSeminarProposal $beritaAcara,
        int $dosenId,
        array $data
    ): LembarCatatanSeminarProposal {
        try {
            $catatan = LembarCatatanSeminarProposal::updateOrCreate(
                [
                    'berita_acara_seminar_proposal_id' => $beritaAcara->id,
                    'dosen_id' => $dosenId,
                ],
                $data
            );

            Log::info('Catatan dosen submitted', [
                'catatan_id' => $catatan->id,
                'dosen_id' => $dosenId,
                'berita_acara_id' => $beritaAcara->id,
            ]);

            return $catatan;

        } catch (\Exception $e) {
            Log::error('Gagal submit catatan dosen', [
                'berita_acara_id' => $beritaAcara->id,
                'dosen_id' => $dosenId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * ✅ Generate PDF preview (return base64 data URL untuk iframe)
     */
    public function generateBeritaAcaraPdfPreview(BeritaAcaraSeminarProposal $beritaAcara): string
    {
        $beritaAcara->load([
            'jadwalSeminarProposal.pendaftaranSeminarProposal.user',
            'jadwalSeminarProposal.pendaftaranSeminarProposal.dosenPembimbing',
            'jadwalSeminarProposal.dosenPenguji',
            'dosenPembimbingPengisi',
            'ketuaPenguji',
            'lembarCatatan.dosen',
        ]);

        $jadwal = $beritaAcara->jadwalSeminarProposal;
        $pendaftaran = $jadwal->pendaftaranSeminarProposal;
        $mahasiswa = $pendaftaran->user;
        $pembimbing = $pendaftaran->dosenPembimbing;

        // Generate QR Code as base64 PNG
        $qrCode = base64_encode(
            QrCode::format('png')
                ->size(150)
                ->errorCorrection('H')
                ->generate($beritaAcara->verification_url)
        );

        // ✅ FIX: Path ke template PDF yang benar
        $pdf = Pdf::loadView('admin.berita-acara-sempro.pdf', compact(
            'beritaAcara',
            'jadwal',
            'pendaftaran',
            'mahasiswa',
            'pembimbing',
            'qrCode'
        ));

        // Set paper size dan orientation
        $pdf->setPaper('a4', 'portrait');

        // Return as data URL untuk iframe
        $output = $pdf->output();
        return 'data:application/pdf;base64,' . base64_encode($output);
    }

    /**
     * ✅ Generate final PDF dan simpan ke storage
     */
    public function generateBeritaAcaraPdf(BeritaAcaraSeminarProposal $beritaAcara): ?string
    {
        try {
            // ✅ PENTING: Refresh data dari database untuk memastikan data terbaru
            $beritaAcara->refresh();

            $beritaAcara->load([
                'jadwalSeminarProposal.pendaftaranSeminarProposal.user',
                'jadwalSeminarProposal.pendaftaranSeminarProposal.dosenPembimbing',
                'jadwalSeminarProposal.dosenPenguji',
                'dosenPembimbingPengisi',
                'dosenPembimbingPenandatangan',
                'ketuaPenguji',
                'lembarCatatan.dosen',
            ]);

            $jadwal = $beritaAcara->jadwalSeminarProposal;
            $pendaftaran = $jadwal->pendaftaranSeminarProposal;
            $mahasiswa = $pendaftaran->user;
            $pembimbing = $pendaftaran->dosenPembimbing;

            Log::info('Generating PDF with data:', [
                'ba_id' => $beritaAcara->id,
                'catatan_kejadian' => $beritaAcara->catatan_kejadian,
                'keputusan' => $beritaAcara->keputusan,
                'ttd_pembimbing_at' => $beritaAcara->ttd_pembimbing_at,
                'ttd_ketua_at' => $beritaAcara->ttd_ketua_penguji_at,
            ]);

            // Generate QR Code
            $qrCode = base64_encode(
                QrCode::format('png')
                    ->size(150)
                    ->errorCorrection('H')
                    ->generate($beritaAcara->verification_url)
            );

            // ✅ Generate PDF
            $pdf = Pdf::loadView('admin.berita-acara-sempro.pdf', compact(
                'beritaAcara',
                'jadwal',
                'pendaftaran',
                'mahasiswa',
                'pembimbing',
                'qrCode'
            ));

            $pdf->setPaper('a4', 'portrait');

            // Generate filename
            $filename = sprintf(
                'berita-acara-sempro/%s_%s_%s.pdf',
                $mahasiswa->nim,
                Str::slug($mahasiswa->name),
                now()->format('YmdHis')
            );

            // Ensure directory exists
            $directory = dirname(storage_path('app/' . $filename));
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Save to storage
            Storage::disk('local')->put(
                $filename,
                $pdf->output()
            );

            Log::info('PDF Berita Acara generated successfully', [
                'ba_id' => $beritaAcara->id,
                'file_path' => $filename,
                'file_exists' => Storage::disk('local')->exists($filename),
            ]);

            return $filename;

        } catch (\Exception $e) {
            Log::error('Failed to generate PDF Berita Acara', [
                'ba_id' => $beritaAcara->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Sign Berita Acara (Ketua Penguji)
     */
    public function signBeritaAcara(
        BeritaAcaraSeminarProposal $beritaAcara,
        int $ketuaPengujiId
    ): bool {
        try {
            $beritaAcara->update([
                'ttd_ketua_penguji_at' => now(),
                'ttd_ketua_penguji_by' => $ketuaPengujiId,
            ]);

            // Regenerate PDF dengan tanda tangan
            $this->generateBeritaAcaraPdf($beritaAcara);

            Log::info('Berita Acara signed', [
                'berita_acara_id' => $beritaAcara->id,
                'ketua_penguji_id' => $ketuaPengujiId,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Gagal sign Berita Acara', [
                'berita_acara_id' => $beritaAcara->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}