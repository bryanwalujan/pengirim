<?php

namespace App\Http\Controllers\Verification;

use App\Http\Controllers\Controller;
use App\Models\SuratUsulanSkripsi;
use Illuminate\Support\Facades\Log;

class SuratUsulanSkripsiVerificationController extends Controller
{
    /**
     * Verify Surat Usulan Skripsi by verification code
     */
    public function verify($code)
    {
        Log::info('Surat Usulan Skripsi verification attempt', [
            'code' => $code,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Find surat by verification code
        $surat = SuratUsulanSkripsi::where('verification_code', $code)
            ->with([
                'pendaftaranUjianHasil.user',
                'pendaftaranUjianHasil.dosenPembimbing1',
                'pendaftaranUjianHasil.dosenPembimbing2',
                'pendaftaranUjianHasil.komisiHasil',
                'pendaftaranUjianHasil.pengujiUjianHasil.dosen',
                'ttdKaprodiBy',
                'ttdKajurBy'
            ])
            ->first();

        if (!$surat) {
            Log::warning('Surat Usulan Skripsi not found', [
                'code' => $code,
            ]);

            return view('verification.invalid', [
                'type' => 'Surat Usulan Ujian Hasil (Skripsi)',
                'message' => 'Kode verifikasi surat usulan ujian hasil tidak ditemukan atau sudah tidak valid.',
            ]);
        }

        // Prepare document data
        $document = $this->prepareSuratUsulanData($surat);

        Log::info('Surat Usulan Skripsi verified successfully', [
            'surat_id' => $surat->id,
            'nomor_surat' => $surat->nomor_surat,
            'mahasiswa_nim' => $surat->pendaftaranUjianHasil->user->nim ?? '-',
            'status' => $surat->status,
            'kaprodi_signed' => $surat->isKaprodiSigned(),
            'kajur_signed' => $surat->isKajurSigned(),
        ]);

        return view('verification.surat-usulan-skripsi', compact('document'));
    }

    /**
     * Prepare Surat Usulan Skripsi data for verification view
     */
    protected function prepareSuratUsulanData(SuratUsulanSkripsi $surat): array
    {
        $pendaftaran = $surat->pendaftaranUjianHasil;

        $data = [
            'type' => 'Surat Usulan Ujian Hasil (Skripsi)',
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

        // Skripsi data
        if ($pendaftaran) {
            $data['skripsi'] = [
                'judul' => strip_tags($pendaftaran->judul_skripsi ?? '-'),
                'ipk' => $pendaftaran->ipk ?? '-',
                'dosen_pembimbing1' => $pendaftaran->dosenPembimbing1->name ?? '-',
                'dosen_pembimbing1_nip' => $pendaftaran->dosenPembimbing1->nip ?? '-',
                'dosen_pembimbing2' => $pendaftaran->dosenPembimbing2->name ?? '-',
                'dosen_pembimbing2_nip' => $pendaftaran->dosenPembimbing2->nip ?? '-',
            ];

            // Penguji list
            $pengujiList = [];

            // Penguji dari penguji_ujian_hasil
            if ($pendaftaran->pengujiUjianHasil && $pendaftaran->pengujiUjianHasil->count() > 0) {
                foreach ($pendaftaran->pengujiUjianHasil->sortBy('posisi') as $penguji) {
                    $pengujiList[] = [
                        'posisi' => $penguji->posisi,
                        'name' => $penguji->dosen->name ?? '-',
                        'nip' => $penguji->dosen->nip ?? '-',
                        'keterangan' => $penguji->keterangan ?? null,
                    ];
                }
            }

            $data['penguji'] = $pengujiList;
        } else {
            $data['skripsi'] = [
                'judul' => '-',
                'ipk' => '-',
                'dosen_pembimbing1' => '-',
                'dosen_pembimbing1_nip' => '-',
                'dosen_pembimbing2' => '-',
                'dosen_pembimbing2_nip' => '-',
            ];
            $data['penguji'] = [];
        }

        // Signature data (2-tier approval)
        $data['signatures'] = [
            'kaprodi' => $this->formatKaprodiSignature($surat),
            'kajur' => $this->formatKajurSignature($surat),
        ];

        // Document can download
        $data['can_download'] = $surat->isFullySigned();

        return $data;
    }

    /**
     * Format Kaprodi signature data
     */
    protected function formatKaprodiSignature(SuratUsulanSkripsi $surat): array
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
    protected function formatKajurSignature(SuratUsulanSkripsi $surat): array
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
