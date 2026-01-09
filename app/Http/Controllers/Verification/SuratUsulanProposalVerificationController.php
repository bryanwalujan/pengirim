<?php
// filepath: /c:/laragon/www/eservice-app/app/Http/Controllers/Verification/SuratUsulanProposalVerificationController.php

namespace App\Http\Controllers\Verification;

use App\Http\Controllers\Controller;
use App\Models\SuratUsulanProposal;
use Illuminate\Support\Facades\Log;

class SuratUsulanProposalVerificationController extends Controller
{
    /**
     * Verify Surat Usulan Proposal by verification code
     */
    public function verify($code)
    {
        Log::info('Surat Usulan Proposal verification attempt', [
            'code' => $code,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Find surat by verification code
        $surat = SuratUsulanProposal::where('verification_code', $code)
            ->with([
                'pendaftaranSeminarProposal.user',
                'pendaftaranSeminarProposal.dosenPembimbing',
                'pendaftaranSeminarProposal.komisiProposal',
                'pendaftaranSeminarProposal.proposalPembahas.dosen',
                'ttdKaprodiBy',
                'ttdKajurBy'
            ])
            ->first();

        if (!$surat) {
            Log::warning('Surat Usulan Proposal not found', [
                'code' => $code,
            ]);

          
            return view('verification.invalid', [
                'type' => 'Surat Usulan Seminar Proposal',
                'message' => 'Kode verifikasi surat usulan proposal tidak ditemukan atau sudah tidak valid.',
            ]);
        }

        // Prepare document data
        $document = $this->prepareSuratUsulanData($surat);

        Log::info('Surat Usulan Proposal verified successfully', [
            'surat_id' => $surat->id,
            'nomor_surat' => $surat->nomor_surat,
            'mahasiswa_nim' => $surat->pendaftaranSeminarProposal->user->nim ?? '-',
            'status' => $surat->status,
            'kaprodi_signed' => $surat->isKaprodiSigned(),
            'kajur_signed' => $surat->isKajurSigned(),
        ]);

        return view('verification.surat-usulan-proposal', compact('document'));
    }

    /**
     * Prepare Surat Usulan Proposal data for verification view
     */
    protected function prepareSuratUsulanData(SuratUsulanProposal $surat): array
    {
        $pendaftaran = $surat->pendaftaranSeminarProposal;

        $data = [
            'type' => 'Surat Usulan Seminar Proposal',
            'nomor_surat' => $surat->nomor_surat ?? '-',
            'tanggal_surat' => $surat->tanggal_surat?->translatedFormat('d F Y') ?? '-',
            'verification_code' => $surat->verification_code,
            'status' => $this->getStatusText($surat->status),
            'status_code' => $surat->status,
            'created_at' => $surat->created_at->translatedFormat('d F Y'),
            'verified_at' => now()->translatedFormat('d F Y H:i'),
        ];

        // Mahasiswa data
        if ($pendaftaran && $pendaftaran->user) {
            $data['mahasiswa'] = [
                'name' => $pendaftaran->user->name,
                'nim' => $pendaftaran->user->nim,
                'program_studi' => 'S1 Teknik Informatika', // Fixed value
                'angkatan' => $pendaftaran->angkatan ?? '-',
            ];
        } else {
            $data['mahasiswa'] = [
                'name' => '-',
                'nim' => '-',
                'program_studi' => 'S1 Teknik Informatika',
                'angkatan' => '-',
            ];
        }

        // Proposal data
        if ($pendaftaran) {
            $data['proposal'] = [
                'judul' => strip_tags($pendaftaran->judul_skripsi ?? '-'),
                'dosen_pembimbing' => $pendaftaran->dosenPembimbing->name ?? '-',
                'dosen_pembimbing_nip' => $pendaftaran->dosenPembimbing->nip ?? '-',
            ];

            // Pembahas list
            $pembahasList = [];

            // Pembahas dari proposal_pembahas
            if ($pendaftaran->proposalPembahas && $pendaftaran->proposalPembahas->count() > 0) {
                foreach ($pendaftaran->proposalPembahas->sortBy('posisi') as $pembahas) {
                    $pembahasList[] = [
                        'posisi' => $pembahas->posisi,
                        'name' => $pembahas->dosen->name ?? '-',
                        'nip' => $pembahas->dosen->nip ?? '-',
                        'keterangan' => $pembahas->keterangan ?? null,
                    ];
                }
            }

            $data['pembahas'] = $pembahasList;
        } else {
            $data['proposal'] = [
                'judul' => '-',
                'dosen_pembimbing' => '-',
                'dosen_pembimbing_nip' => '-',
            ];
            $data['pembahas'] = [];
        }

        // Signature data (2-tier approval)
        $data['signatures'] = [
            'kaprodi' => $this->formatKaprodiSignature($surat),
            'kajur' => $this->formatKajurSignature($surat),
        ];

        // Document can download (tidak digunakan, tapi tetap disediakan)
        $data['can_download'] = $surat->isFullySigned();

        return $data;
    }

    /**
     * Format Kaprodi signature data
     */
    protected function formatKaprodiSignature(SuratUsulanProposal $surat): array
    {
        if ($surat->isKaprodiSigned()) {
            $data = [
                'is_signed' => true,
                'name' => $surat->ttdKaprodiBy->name ?? '-',
                'nip' => $surat->ttdKaprodiBy->nip ?? '-',
                'jabatan' => $surat->ttdKaprodiBy->jabatan ?? 'Koordinator Program Studi',
                'tanggal_ttd' => $surat->ttd_kaprodi_at->translatedFormat('d F Y, H:i') . ' WITA',
            ];

            // Check for override
            if ($surat->isKaprodiOverride()) {
                $overrideInfo = $surat->getOverrideInfo('kaprodi');
                $data['override'] = [
                    'override_by' => $overrideInfo['override_by_name'] ?? '-',
                    'override_role' => ucfirst($overrideInfo['override_by_role'] ?? '-'),
                    'original_name' => $overrideInfo['original_kaprodi_name'] ?? '-',
                    'override_at' => $overrideInfo['override_at'] ?? '-',
                ];
            }

            return $data;
        }

        return [
            'is_signed' => false,
            'name' => 'Menunggu Tanda Tangan',
        ];
    }

    /**
     * Format Kajur signature data
     */
    protected function formatKajurSignature(SuratUsulanProposal $surat): array
    {
        if ($surat->isKajurSigned()) {
            $data = [
                'is_signed' => true,
                'name' => $surat->ttdKajurBy->name ?? '-',
                'nip' => $surat->ttdKajurBy->nip ?? '-',
                'jabatan' => $surat->ttdKajurBy->jabatan ?? 'Ketua Jurusan',
                'tanggal_ttd' => $surat->ttd_kajur_at->translatedFormat('d F Y, H:i') . ' WITA',
            ];

            // Check for override
            if ($surat->isKajurOverride()) {
                $overrideInfo = $surat->getOverrideInfo('kajur');
                $data['override'] = [
                    'override_by' => $overrideInfo['override_by_name'] ?? '-',
                    'override_role' => ucfirst($overrideInfo['override_by_role'] ?? '-'),
                    'original_name' => $overrideInfo['original_kajur_name'] ?? '-',
                    'override_at' => $overrideInfo['override_at'] ?? '-',
                ];
            }

            return $data;
        }

        return [
            'is_signed' => false,
            'name' => 'Menunggu Tanda Tangan',
        ];
    }

    /**
     * Get status text
     */
    protected function getStatusText(string $status): string
    {
        return match ($status) {
            'draft' => 'Draft',
            'menunggu_ttd_kaprodi' => 'Menunggu Tanda Tangan Kaprodi',
            'menunggu_ttd_kajur' => 'Menunggu Tanda Tangan Kajur',
            'selesai' => 'Selesai - Dokumen Valid',
            'ditolak' => 'Ditolak',
            default => 'Status Tidak Diketahui'
        };
    }
}