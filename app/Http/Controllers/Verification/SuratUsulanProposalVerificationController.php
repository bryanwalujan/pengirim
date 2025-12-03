<?php

namespace App\Http\Controllers\Verification;

use App\Http\Controllers\Controller;
use App\Models\SuratUsulanProposal;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

            return $this->showInvalid(
                'Kode verifikasi surat usulan proposal tidak ditemukan atau sudah tidak valid.'
            );
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
     * Download verified document
     */
    public function download($code)
    {
        $surat = SuratUsulanProposal::where('verification_code', $code)->first();

        if (!$surat) {
            abort(404, 'Dokumen tidak ditemukan');
        }

        // Only allow download if fully signed
        if (!$surat->isFullySigned()) {
            Log::warning('Download attempt on incomplete document', [
                'surat_id' => $surat->id,
                'code' => $code,
                'status' => $surat->status,
            ]);

            return back()->with('error', 'Dokumen belum ditandatangani lengkap. Download tidak diizinkan.');
        }

        $filePath = storage_path('app/public/' . $surat->file_surat);

        if (!file_exists($filePath)) {
            Log::error('File not found for download', [
                'surat_id' => $surat->id,
                'file_path' => $surat->file_surat,
            ]);

            abort(404, 'File dokumen tidak ditemukan');
        }

        Log::info('Document downloaded via verification', [
            'surat_id' => $surat->id,
            'code' => $code,
            'ip' => request()->ip(),
            'file_size' => filesize($filePath),
        ]);

        $filename = sprintf(
            'Surat_Usulan_Proposal_%s_%s.pdf',
            str_replace('/', '_', $surat->nomor_surat),
            now()->format('Ymd')
        );

        return response()->download($filePath, $filename);
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
            'tanggal_surat' => $surat->tanggal_surat?->format('d F Y') ?? '-',
            'verification_code' => $surat->verification_code,
            'status' => $this->getStatusText($surat->status),
            'status_code' => $surat->status,
            'created_at' => $surat->created_at->format('d F Y'),
            'verified_at' => now()->format('d F Y H:i'),
        ];

        // Mahasiswa data
        if ($pendaftaran && $pendaftaran->user) {
            $data['mahasiswa'] = [
                'name' => $pendaftaran->user->name,
                'nim' => $pendaftaran->user->nim,
                'program_studi' => 'S1 Pendidikan Teknik Informatika dan Komputer',
                'angkatan' => $pendaftaran->angkatan ?? '-',
                'ipk' => $pendaftaran->ipk ?? '-',
            ];
        }

        // Proposal data
        if ($pendaftaran) {
            $data['proposal'] = [
                'judul' => $pendaftaran->judul_skripsi ?? '-',
                'dosen_pembimbing' => $pendaftaran->dosenPembimbing->name ?? '-',
                'dosen_pembimbing_nip' => $pendaftaran->dosenPembimbing->nip ?? '-',
            ];

            // Pembahas list
            $pembahasList = [];

            // Pembimbing sebagai Pembahas 1
            if ($pendaftaran->dosenPembimbing) {
                $pembahasList[] = [
                    'posisi' => 1,
                    'name' => $pendaftaran->dosenPembimbing->name,
                    'nip' => $pendaftaran->dosenPembimbing->nip,
                    'keterangan' => 'Pembimbing',
                ];
            }

            // Pembahas 2, 3, 4 dari proposal_pembahas
            if ($pendaftaran->proposalPembahas) {
                foreach ($pendaftaran->proposalPembahas->sortBy('posisi') as $index => $pembahas) {
                    $pembahasList[] = [
                        'posisi' => $index + 2,
                        'name' => $pembahas->dosen->name,
                        'nip' => $pembahas->dosen->nip,
                        'keterangan' => 'Pembahas ' . ($index + 2),
                    ];
                }
            }

            $data['pembahas'] = $pembahasList;
        }

        // ✅ Signature data (2-tier approval)
        $data['signatures'] = [];

        // Kaprodi Signature
        if ($surat->isKaprodiSigned()) {
            $data['signatures']['kaprodi'] = [
                'name' => $surat->ttdKaprodiBy->name,
                'nip' => $surat->ttdKaprodiBy->nip ?? '-',
                'jabatan' => $surat->ttdKaprodiBy->jabatan ?? 'Koordinator Program Studi',
                'tanggal_ttd' => $surat->ttd_kaprodi_at->format('d F Y H:i'),
                'is_signed' => true,
            ];

            // Check override
            if ($surat->isKaprodiOverride()) {
                $overrideInfo = $surat->getOverrideInfo('kaprodi');
                $data['signatures']['kaprodi']['override'] = [
                    'override_by' => $overrideInfo['override_name'] ?? '-',
                    'override_role' => $overrideInfo['override_role'] ?? '-',
                    'override_at' => $overrideInfo['override_at'] ?? '-',
                    'original_name' => $overrideInfo['original_kaprodi_name'] ?? '-',
                ];
            }
        } else {
            $data['signatures']['kaprodi'] = [
                'name' => 'Menunggu Tanda Tangan',
                'is_signed' => false,
            ];
        }

        // Kajur Signature
        if ($surat->isKajurSigned()) {
            $data['signatures']['kajur'] = [
                'name' => $surat->ttdKajurBy->name,
                'nip' => $surat->ttdKajurBy->nip ?? '-',
                'jabatan' => $surat->ttdKajurBy->jabatan ?? 'Ketua Jurusan Teknik Elektro',
                'tanggal_ttd' => $surat->ttd_kajur_at->format('d F Y H:i'),
                'is_signed' => true,
            ];

            // Check override
            if ($surat->isKajurOverride()) {
                $overrideInfo = $surat->getOverrideInfo('kajur');
                $data['signatures']['kajur']['override'] = [
                    'override_by' => $overrideInfo['override_name'] ?? '-',
                    'override_role' => $overrideInfo['override_role'] ?? '-',
                    'override_at' => $overrideInfo['override_at'] ?? '-',
                    'original_name' => $overrideInfo['original_kajur_name'] ?? '-',
                ];
            }
        } else {
            $data['signatures']['kajur'] = [
                'name' => 'Menunggu Tanda Tangan',
                'is_signed' => false,
            ];
        }

        // Document availability
        $data['can_download'] = $surat->isFullySigned();
        $data['download_url'] = $surat->isFullySigned()
            ? route('document.verify.download', $surat->verification_code)
            : null;

        return $data;
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

    /**
     * Show invalid verification page
     */
    protected function showInvalid(string $message)
    {
        return view('verification.invalid', [
            'message' => $message,
            'type' => 'surat-usulan-proposal'
        ]);
    }
}