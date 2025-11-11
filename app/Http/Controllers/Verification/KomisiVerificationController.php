<?php

namespace App\Http\Controllers\Verification;

use App\Http\Controllers\Controller;
use App\Models\KomisiProposal;
use App\Models\KomisiHasil;
use Illuminate\Support\Facades\Log;

class KomisiVerificationController extends Controller
{
    /**
     * Verifikasi dokumen komisi berdasarkan kode
     */
    public function verify($code)
    {
        Log::info('Komisi verification attempt', ['code' => $code]);

        // Cek prefix untuk menentukan jenis komisi
        $prefix = substr($code, 0, 3);

        // Verifikasi berdasarkan prefix
        return match ($prefix) {
            'KP-' => $this->verifyKomisiProposal($code),
            'KH-' => $this->verifyKomisiHasil($code),
            default => $this->showInvalid('Kode verifikasi tidak valid. Pastikan Anda menggunakan QR code yang benar.')
        };
    }

    /**
     * Verifikasi Komisi Proposal
     */
    protected function verifyKomisiProposal($code)
    {
        $komisi = KomisiProposal::where('verification_code', $code)
            ->with(['user', 'pembimbing', 'penandatanganPA', 'penandatanganKorprodi'])
            ->first();

        if (!$komisi) {
            Log::warning('Komisi proposal not found', ['code' => $code]);
            return $this->showInvalid('Kode verifikasi komisi proposal tidak ditemukan atau sudah tidak valid.');
        }

        // Log successful verification
        Log::info('Komisi proposal verified successfully', [
            'komisi_id' => $komisi->id,
            'mahasiswa_nim' => $komisi->user->nim,
            'status' => $komisi->status
        ]);

        $document = $this->prepareKomisiProposalData($komisi);

        return view('verification.komisi-proposal', compact('document'));
    }

    /**
     * Verifikasi Komisi Hasil
     */
    protected function verifyKomisiHasil($code)
    {
        $komisi = KomisiHasil::where('verification_code', $code)
            ->with(['user', 'pembimbing', 'penandatanganPA', 'penandatanganKorprodi'])
            ->first();

        if (!$komisi) {
            Log::warning('Komisi hasil not found', ['code' => $code]);
            return $this->showInvalid('Kode verifikasi komisi hasil tidak ditemukan atau sudah tidak valid.');
        }

        Log::info('Komisi hasil verified successfully', [
            'komisi_id' => $komisi->id,
            'mahasiswa_nim' => $komisi->user->nim,
            'status' => $komisi->status
        ]);

        $document = $this->prepareKomisiHasilData($komisi);

        return view('verification.komisi-hasil', compact('document'));
    }

    /**
     * Prepare data untuk Komisi Proposal
     */
    protected function prepareKomisiProposalData(KomisiProposal $komisi): array
    {
        $data = [
            'type' => 'Komisi Proposal Skripsi',
            'mahasiswa_name' => $komisi->user->name,
            'nim' => $komisi->user->nim,
            'program_studi' => 'S1 Teknik Informatika',
            'judul' => $komisi->judul_skripsi,
            'status' => $this->getStatusText($komisi->status),
            'status_code' => $komisi->status,
            'verification_code' => $komisi->verification_code,
            'created_at' => $komisi->created_at->format('d F Y'),
        ];

        // Data Pembimbing Akademik
        if ($komisi->penandatanganPA) {
            $data['dosen_pa'] = [
                'name' => $komisi->penandatanganPA->name,
                'nip' => $komisi->penandatanganPA->nip,
                'jabatan' => $komisi->penandatanganPA->jabatan ?? 'Pembimbing Akademik',
                'tanggal_persetujuan' => $komisi->tanggal_persetujuan_pa?->format('d F Y H:i'),
            ];
        }

        // Data Koordinator Prodi
        if ($komisi->penandatanganKorprodi) {
            $data['korprodi'] = [
                'name' => $komisi->penandatanganKorprodi->name,
                'nip' => $komisi->penandatanganKorprodi->nip,
                'jabatan' => $komisi->penandatanganKorprodi->jabatan ?? 'Koordinator Program Studi',
                'tanggal_persetujuan' => $komisi->tanggal_persetujuan_korprodi?->format('d F Y H:i'),
            ];
        }

        // Data Pembimbing (jika berbeda dengan PA)
        if ($komisi->pembimbing && $komisi->pembimbing->id !== $komisi->penandatanganPA?->id) {
            $data['dosen_pembimbing'] = [
                'name' => $komisi->pembimbing->name,
                'nip' => $komisi->pembimbing->nip,
                'jabatan' => $komisi->pembimbing->jabatan ?? 'Dosen Pembimbing',
            ];
        }

        return $data;
    }

    /**
     * Prepare data untuk Komisi Hasil
     */
    protected function prepareKomisiHasilData(KomisiHasil $komisi): array
    {
        $data = [
            'type' => 'Komisi Hasil Skripsi',
            'mahasiswa_name' => $komisi->user->name,
            'nim' => $komisi->user->nim,
            'program_studi' => 'S1 Teknik Informatika',
            'judul' => $komisi->judul_skripsi ?? '-',
            'status' => $this->getStatusText($komisi->status),
            'status_code' => $komisi->status,
            'verification_code' => $komisi->verification_code,
            'created_at' => $komisi->created_at->format('d F Y'),
        ];

        // Similar structure dengan komisi proposal
        if ($komisi->penandatanganPA) {
            $data['dosen_pa'] = [
                'name' => $komisi->penandatanganPA->name,
                'nip' => $komisi->penandatanganPA->nip,
                'jabatan' => $komisi->penandatanganPA->jabatan ?? 'Pembimbing Akademik',
                'tanggal_persetujuan' => $komisi->tanggal_persetujuan_pa?->format('d F Y H:i'),
            ];
        }

        if ($komisi->penandatanganKorprodi) {
            $data['korprodi'] = [
                'name' => $komisi->penandatanganKorprodi->name,
                'nip' => $komisi->penandatanganKorprodi->nip,
                'jabatan' => $komisi->penandatanganKorprodi->jabatan ?? 'Koordinator Program Studi',
                'tanggal_persetujuan' => $komisi->tanggal_persetujuan_korprodi?->format('d F Y H:i'),
            ];
        }

        if ($komisi->pembimbing && $komisi->pembimbing->id !== $komisi->penandatanganPA?->id) {
            $data['dosen_pembimbing'] = [
                'name' => $komisi->pembimbing->name,
                'nip' => $komisi->pembimbing->nip,
                'jabatan' => $komisi->pembimbing->jabatan ?? 'Dosen Pembimbing',
            ];
        }

        return $data;
    }

    /**
     * Get status text
     */
    protected function getStatusText($status): string
    {
        return match ($status) {
            'pending' => 'Menunggu Persetujuan PA',
            'approved_pa' => 'Disetujui PA, Menunggu Korprodi',
            'approved' => 'Disetujui Lengkap',
            'rejected' => 'Ditolak',
            default => 'Status Tidak Diketahui'
        };
    }

    /**
     * Show invalid verification page
     */
    protected function showInvalid(string $message)
    {
        return view('verification.invalid', [
            'message' => $message,
            'type' => 'komisi'
        ]);
    }
}