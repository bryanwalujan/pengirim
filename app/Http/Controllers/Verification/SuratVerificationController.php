<?php

namespace App\Http\Controllers\Verification;

use App\Http\Controllers\Controller;
use App\Models\SuratAktifKuliah;
use App\Models\SuratIjinSurvey;
use App\Models\SuratCutiAkademik;
use App\Models\SuratPindah;
use Illuminate\Support\Facades\Log;

class SuratVerificationController extends Controller
{
    /**
     * Verifikasi dokumen surat berdasarkan kode
     */
    public function verify($code)
    {
        Log::info('Surat verification attempt', ['code' => $code]);

        $document = $this->findDocument($code);

        if (!$document) {
            Log::warning('Surat not found', ['code' => $code]);
            return view('verification.invalid', [
                'message' => 'Dokumen surat tidak ditemukan atau kode verifikasi tidak valid',
                'type' => 'surat'
            ]);
        }

        // Determine status
        $status = is_object($document->status)
            ? $document->status->status
            : ($document->status ?? 'unknown');

        // Log successful verification
        Log::info('Surat verified successfully', [
            'document_type' => class_basename($document),
            'document_id' => $document->id,
            'mahasiswa_nim' => $document->mahasiswa->nim ?? '-',
            'status' => $status
        ]);

        $verificationData = $this->prepareVerificationData($document, $status, $code);

        // Determine active signer
        $activeSigner = $this->determineActiveSigner($document, $code, $verificationData);

        // Determine view
        $view = $this->getViewName($document);

        return view($view, [
            'document' => $document,
            'verification_data' => $verificationData,
            'active_signer' => $activeSigner,
        ]);
    }

    /**
     * Find document by verification code
     */
    protected function findDocument($code)
    {
        $models = [
            SuratAktifKuliah::class,
            SuratIjinSurvey::class,
            SuratCutiAkademik::class,
            SuratPindah::class,
        ];

        foreach ($models as $model) {
            $document = $model::with(['mahasiswa', 'penandatangan', 'penandatanganKaprodi', 'status'])
                ->where(function ($query) use ($code) {
                    $query->where('verification_code', $code)
                        ->orWhere('verification_code_kaprodi', $code)
                        ->orWhere('verification_code_pimpinan', $code);
                })
                ->first();

            if ($document) {
                return $document;
            }
        }

        return null;
    }

    /**
     * Determine active signer based on code
     */
    protected function determineActiveSigner($document, $code, $verificationData)
    {
        if ($code === $document->verification_code_kaprodi && !empty($verificationData['signers']['kaprodi'])) {
            return $verificationData['signers']['kaprodi'];
        }

        if ($code === $document->verification_code_pimpinan && !empty($verificationData['signers']['pimpinan'])) {
            return $verificationData['signers']['pimpinan'];
        }

        return null;
    }

    /**
     * Get view name based on document type
     */
    protected function getViewName($document): string
    {
        return match (true) {
            $document instanceof SuratAktifKuliah => 'qrcode.surat-aktif-kuliah.show',
            $document instanceof SuratIjinSurvey => 'qrcode.surat-ijin-survey.show',
            $document instanceof SuratCutiAkademik => 'qrcode.surat-cuti-akademik.show',
            $document instanceof SuratPindah => 'qrcode.surat-pindah.show',
            default => 'verification.invalid'
        };
    }

    /**
     * Prepare verification data
     */
    protected function prepareVerificationData($document, $status, $verificationCodeUsed): array
    {
        $signers = [
            'kaprodi' => $document->penandatanganKaprodi ? [
                'name' => $document->penandatanganKaprodi->name ?? '-',
                'position' => $document->jabatan_penandatangan_kaprodi ?? 'Koordinator Program Studi',
                'nip' => $document->penandatanganKaprodi->nip ?? '-',
                'signature_date' => optional($document->approved_at)->format('d F Y') ?? '-',
                'verification_code' => $document->verification_code_kaprodi,
            ] : null,
            'pimpinan' => $document->penandatangan ? [
                'name' => $document->penandatangan->name ?? '-',
                'position' => $document->jabatan_penandatangan ?? 'Pimpinan Jurusan PTIK',
                'nip' => $document->penandatangan->nip ?? '-',
                'signature_date' => optional($document->approved_at)->format('d F Y') ?? '-',
                'verification_code' => $document->verification_code_pimpinan,
            ] : null,
        ];

        $studentData = [
            'name' => $document->mahasiswa->name ?? '-',
            'nim' => $document->mahasiswa->nim ?? '-',
            'study_program' => 'S1 Teknik Informatika',
        ];

        $verificationInfo = [
            'status' => $status,
            'verified_at' => now()->format('d F Y H:i'),
            'verification_code' => $verificationCodeUsed,
        ];

        return match (true) {
            $document instanceof SuratAktifKuliah => [
                'document' => [
                    'type' => 'Surat Aktif Kuliah',
                    'number' => $document->nomor_surat ?? '-',
                    'date' => optional($document->tanggal_surat)->format('d F Y') ?? '-',
                    'academic_year' => $document->tahun_ajaran ?? '-',
                    'semester' => ucfirst($document->semester ?? '-'),
                    'purpose' => $document->tujuan_pengajuan ?? '-',
                ],
                'student' => $studentData,
                'signers' => $signers,
                'verification' => $verificationInfo,
            ],

            $document instanceof SuratIjinSurvey => [
                'document' => [
                    'type' => 'Surat Ijin Survey',
                    'number' => $document->nomor_surat ?? '-',
                    'date' => optional($document->tanggal_surat)->format('d F Y') ?? '-',
                    'title' => $document->judul ?? '-',
                    'survey_location' => $document->tempat_survey ?? '-',
                    'purpose' => $document->tujuan_pengajuan ?? '-',
                ],
                'student' => $studentData,
                'signers' => $signers,
                'verification' => $verificationInfo,
            ],

            $document instanceof SuratCutiAkademik => [
                'document' => [
                    'type' => 'Surat Cuti Akademik',
                    'number' => $document->nomor_surat ?? '-',
                    'date' => optional($document->tanggal_surat)->format('d F Y') ?? '-',
                    'academic_year' => $document->tahun_ajaran ?? '-',
                    'semester' => ucfirst($document->semester ?? '-'),
                    'reason' => $document->alasan_pengajuan ?? '-',
                    'additional_info' => $document->keterangan_tambahan ?? '-',
                ],
                'student' => $studentData,
                'signers' => $signers,
                'verification' => $verificationInfo,
            ],

            $document instanceof SuratPindah => [
                'document' => [
                    'type' => 'Surat Pindah',
                    'number' => $document->nomor_surat ?? '-',
                    'date' => optional($document->tanggal_surat)->format('d F Y') ?? '-',
                    'semester' => ucfirst($document->semester ?? '-'),
                    'university_destination' => $document->universitas_tujuan ?? '-',
                    'reason' => $document->alasan_pengajuan ?? '-',
                    'additional_info' => $document->keterangan_tambahan ?? '-',
                ],
                'student' => $studentData,
                'signers' => $signers,
                'verification' => $verificationInfo,
            ],

            default => []
        };
    }
}