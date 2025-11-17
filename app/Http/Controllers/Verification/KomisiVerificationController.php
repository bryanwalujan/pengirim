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
            ->with(['user', 'pembimbingAkademik', 'penandatanganPA', 'penandatanganKorprodi'])
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
     * Verifikasi Komisi Hasil (3-Tier Approval)
     */
    protected function verifyKomisiHasil($code)
    {
        $komisi = KomisiHasil::where('verification_code', $code)
            ->with([
                'user',
                'pembimbing1',
                'pembimbing2',
                'penandatanganPembimbing1',
                'penandatanganPembimbing2',
                'penandatanganKorprodi'
            ])
            ->first();

        if (!$komisi) {
            Log::warning('Komisi hasil not found', ['code' => $code]);
            return $this->showInvalid('Kode verifikasi komisi hasil tidak ditemukan atau sudah tidak valid.');
        }

        // Validasi: Hanya dokumen approved yang bisa diverifikasi
        if ($komisi->status !== 'approved') {
            $statusMessages = [
                'pending' => 'Dokumen masih menunggu persetujuan Pembimbing 1',
                'approved_pembimbing1' => 'Dokumen sudah disetujui Pembimbing 1, menunggu Pembimbing 2',
                'approved_pembimbing2' => 'Dokumen sudah disetujui Pembimbing 1 & 2, menunggu Koordinator Prodi',
                'rejected' => 'Dokumen ditolak'
            ];

            Log::info('Komisi hasil verification blocked - not fully approved', [
                'komisi_id' => $komisi->id,
                'status' => $komisi->status
            ]);

            return $this->showInvalid(
                $statusMessages[$komisi->status] ?? 'Status dokumen tidak diketahui.'
            );
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
            'status' => $this->getStatusTextProposal($komisi->status),
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
        if ($komisi->pembimbingAkademik && $komisi->pembimbingAkademik->id !== $komisi->penandatanganPA?->id) {
            $data['dosen_pembimbing'] = [
                'name' => $komisi->pembimbingAkademik->name,
                'nip' => $komisi->pembimbingAkademik->nip,
                'jabatan' => $komisi->pembimbingAkademik->jabatan ?? 'Dosen Pembimbing',
            ];
        }

        return $data;
    }

    /**
     * Prepare data untuk Komisi Hasil (3-Tier Approval)
     */
    protected function prepareKomisiHasilData(KomisiHasil $komisi): array
    {
        $data = [
            'type' => 'Komisi Hasil Skripsi',
            'mahasiswa_name' => $komisi->user->name,
            'nim' => $komisi->user->nim,
            'program_studi' => 'S1 Teknik Informatika',
            'judul' => $komisi->judul_skripsi ?? '-',
            'status' => $this->getStatusTextHasil($komisi->status),
            'status_code' => $komisi->status,
            'verification_code' => $komisi->verification_code,
            'created_at' => $komisi->created_at->format('d F Y'),
        ];

        // Data Pembimbing 1
        if ($komisi->pembimbing1) {
            $data['pembimbing1'] = [
                'name' => $komisi->pembimbing1->name,
                'nip' => $komisi->pembimbing1->nip,
                'jabatan' => $komisi->pembimbing1->jabatan ?? 'Dosen Pembimbing I',
            ];

            if ($komisi->penandatanganPembimbing1) {
                $data['pembimbing1']['penandatangan'] = $komisi->penandatanganPembimbing1->name;
                $data['pembimbing1']['penandatangan_nip'] = $komisi->penandatanganPembimbing1->nip;
                $data['pembimbing1']['tanggal_persetujuan'] = $komisi->tanggal_persetujuan_pembimbing1?->format('d F Y H:i');
            }
        }

        // Data Pembimbing 2
        if ($komisi->pembimbing2) {
            $data['pembimbing2'] = [
                'name' => $komisi->pembimbing2->name,
                'nip' => $komisi->pembimbing2->nip,
                'jabatan' => $komisi->pembimbing2->jabatan ?? 'Dosen Pembimbing II',
            ];

            if ($komisi->penandatanganPembimbing2) {
                $data['pembimbing2']['penandatangan'] = $komisi->penandatanganPembimbing2->name;
                $data['pembimbing2']['penandatangan_nip'] = $komisi->penandatanganPembimbing2->nip;
                $data['pembimbing2']['tanggal_persetujuan'] = $komisi->tanggal_persetujuan_pembimbing2?->format('d F Y H:i');
            }
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

        return $data;
    }

    /**
     * Get status text untuk Komisi Proposal
     */
    protected function getStatusTextProposal($status): string
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
     * Get status text untuk Komisi Hasil (3-Tier)
     */
    protected function getStatusTextHasil($status): string
    {
        return match ($status) {
            'pending' => 'Menunggu Persetujuan Pembimbing 1',
            'approved_pembimbing1' => 'Disetujui Pembimbing 1, Menunggu Pembimbing 2',
            'approved_pembimbing2' => 'Disetujui Pembimbing 1 & 2, Menunggu Korprodi',
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