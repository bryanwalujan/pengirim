<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratAktifKuliah;

class DocumentVerificationController extends Controller
{
    public function verify($code)
    {
        $document = SuratAktifKuliah::with(['mahasiswa', 'penandatangan', 'penandatanganKaprodi', 'status'])
            ->where('verification_code', $code)
            ->first();

        if (!$document) {
            return view('verification.invalid', [
                'message' => 'Dokumen tidak ditemukan atau kode verifikasi tidak valid'
            ]);
        }

        // Pastikan status tersedia
        $status = is_object($document->status) ? $document->status->status : ($document->status ?? 'unknown');

        return view('user.verification.show', [
            'document' => $document,
            'verification_data' => $this->prepareVerificationData($document, $status)
        ]);
    }

    protected function prepareVerificationData($document, $status)
    {
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
                'study_program' => 'S1 Teknik Informatika' // Pastikan string
            ],
            'signers' => [
                'kaprodi' => $document->penandatanganKaprodi ? [
                    'name' => $document->penandatanganKaprodi->name ?? '-',
                    'position' => is_string($document->jabatan_penandatangan_kaprodi)
                        ? $document->jabatan_penandatangan_kaprodi
                        : ($document->jabatan_penandatangan_kaprodi['title'] ?? 'Koordinator Program Studi'),
                    'nip' => $document->penandatanganKaprodi->nip ?? '-',
                    'signature_date' => optional($document->approved_at)->format('d F Y H:i') ?? '-'
                ] : null,
                'pimpinan' => $document->penandatangan ? [
                    'name' => $document->penandatangan->name ?? '-',
                    'position' => is_string($document->jabatan_penandatangan)
                        ? $document->jabatan_penandatangan
                        : ($document->jabatan_penandatangan['title'] ?? 'Pimpinan Jurusan PTIK'),
                    'nip' => $document->penandatangan->nip ?? '-',
                    'signature_date' => optional($document->approved_at)->format('d F Y H:i') ?? '-'
                ] : null,
            ],
            'verification' => [
                'status' => $status,
                'verified_at' => now()->format('d F Y H:i'),
                'verification_code' => $document->verification_code ?? '-'
            ]
        ];
    }
}