<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratAktifKuliah;
use App\Models\SuratIjinSurvey;

class DocumentVerificationController extends Controller
{
    public function verify($code)
    {
        // Cari dokumen berdasarkan kode verifikasi di kedua model
        $document = $this->findDocumentByVerificationCode($code);

        if (!$document) {
            return view('verification.invalid', [
                'message' => 'Dokumen tidak ditemukan atau kode verifikasi tidak valid'
            ]);
        }

        $status = is_object($document->status) ? $document->status->status : ($document->status ?? 'unknown');
        $verificationCodeUsed = $code;

        $verificationData = $this->prepareVerificationData($document, $status, $verificationCodeUsed);

        // Tentukan penandatangan aktif berdasarkan kode yang discan
        $activeSigner = null;
        if ($verificationCodeUsed === $document->verification_code_kaprodi && !empty($verificationData['signers']['kaprodi'])) {
            $activeSigner = $verificationData['signers']['kaprodi'];
        } elseif ($verificationCodeUsed === $document->verification_code_pimpinan && !empty($verificationData['signers']['pimpinan'])) {
            $activeSigner = $verificationData['signers']['pimpinan'];
        }

        // Tentukan view berdasarkan jenis dokumen
        $view = $document instanceof SuratAktifKuliah
            ? 'qrcode.surat-aktif-kuliah.show'
            : 'qrcode.surat-ijin-survey.show';

        return view($view, [
            'document' => $document,
            'verification_data' => $verificationData,
            'active_signer' => $activeSigner,
        ]);
    }

    protected function findDocumentByVerificationCode($code)
    {
        // Cari di SuratAktifKuliah
        $document = SuratAktifKuliah::with(['mahasiswa', 'penandatangan', 'penandatanganKaprodi', 'status'])
            ->where(function ($query) use ($code) {
                $query->where('verification_code', $code)
                    ->orWhere('verification_code_kaprodi', $code)
                    ->orWhere('verification_code_pimpinan', $code);
            })
            ->first();

        if ($document) {
            return $document;
        }

        // Cari di SuratIjinSurvey
        return SuratIjinSurvey::with(['mahasiswa', 'penandatangan', 'penandatanganKaprodi', 'status'])
            ->where(function ($query) use ($code) {
                $query->where('verification_code', $code)
                    ->orWhere('verification_code_kaprodi', $code)
                    ->orWhere('verification_code_pimpinan', $code);
            })
            ->first();
    }

    protected function prepareVerificationData($document, $status, $verificationCodeUsed)
    {
        $signers = [
            'kaprodi' => $document->penandatanganKaprodi ? [
                'name' => $document->penandatanganKaprodi->name ?? '-',
                'position' => $document->jabatan_penandatangan_kaprodi ?? 'Koordinator Program Studi',
                'nip' => $document->penandatanganKaprodi->nip ?? '-',
                'signature_date' => optional($document->approved_at)->format('d F Y H:i') ?? '-',
                'verification_code' => $document->verification_code_kaprodi,
            ] : null,
            'pimpinan' => $document->penandatangan ? [
                'name' => $document->penandatangan->name ?? '-',
                'position' => $document->jabatan_penandatangan ?? 'Pimpinan Jurusan PTIK',
                'nip' => $document->penandatangan->nip ?? '-',
                'signature_date' => optional($document->approved_at)->format('d F Y H:i') ?? '-',
                'verification_code' => $document->verification_code_pimpinan,
            ] : null,
        ];

        if ($document instanceof SuratAktifKuliah) {
            return [
                'document' => [
                    'type' => 'Surat Aktif Kuliah',
                    'number' => $document->nomor_surat ?? '-',
                    'date' => optional($document->tanggal_surat)->format('d F Y') ?? '-',
                    'academic_year' => $document->tahun_ajaran ?? '-',
                    'semester' => ucfirst($document->semester ?? '-') ?? '-',
                    'purpose' => $document->tujuan_pengajuan ?? '-',
                ],
                'student' => [
                    'name' => $document->mahasiswa->name ?? '-',
                    'nim' => $document->mahasiswa->nim ?? '-',
                    'study_program' => 'S1 Teknik Informatika',
                ],
                'signers' => $signers,
                'verification' => [
                    'status' => $status,
                    'verified_at' => now()->format('d F Y H:i'),
                    'verification_code' => $verificationCodeUsed,
                ],
            ];
        } elseif ($document instanceof SuratIjinSurvey) {
            return [
                'document' => [
                    'type' => 'Surat Ijin Survey',
                    'number' => $document->nomor_surat ?? '-',
                    'date' => optional($document->tanggal_surat)->format('d F Y') ?? '-',
                    'title' => $document->judul ?? '-',
                    'survey_location' => $document->tempat_survey ?? '-',
                    'purpose' => $document->tujuan_pengajuan ?? '-',
                ],
                'student' => [
                    'name' => $document->mahasiswa->name ?? '-',
                    'nim' => $document->mahasiswa->nim ?? '-',
                    'study_program' => 'S1 Teknik Informatika',
                ],
                'signers' => $signers,
                'verification' => [
                    'status' => $status,
                    'verified_at' => now()->format('d F Y H:i'),
                    'verification_code' => $verificationCodeUsed,
                ],
            ];
        }

        return [];
    }
}