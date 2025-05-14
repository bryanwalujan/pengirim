<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

abstract class DocumentController extends Controller
{
    protected function processSignature($document, $signer, Request $request)
    {
        $additionalData = [
            'document_number' => $document->nomor_surat ?? null,
            'document_date' => $document->tanggal_surat ?? null,
            'student' => [
                'name' => $document->mahasiswa->name,
                'nim' => $document->mahasiswa->nim,
            ],
            'position' => $request->jabatan_penandatangan,
        ];

        return $document->generateQrSignature($signer, $additionalData);
    }
}
