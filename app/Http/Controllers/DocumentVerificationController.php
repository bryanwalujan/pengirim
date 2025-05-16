<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratAktifKuliah;

class DocumentVerificationController extends Controller
{
    public function verify($code)
    {
        $document = SuratAktifKuliah::with(['mahasiswa', 'penandatangan'])
            ->where('verification_code', $code)
            ->first();

        if (!$document) {
            return view('verification.invalid');
        }

        return view('verification.show', [
            'document' => $document,
            'qrData' => $document->qr_code_data
        ]);
    }

    public function verifyByQr(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|json',
        ]);

        $qrData = json_decode($request->qr_data, true);

        $document = SuratAktifKuliah::with(['mahasiswa', 'penandatangan'])
            ->where('verification_code', $qrData['verification_code'] ?? null)
            ->first();

        if (!$document) {
            return response()->json(['valid' => false], 404);
        }

        return response()->json([
            'valid' => true,
            'document' => $document,
            'verification_data' => $document->qr_code_data
        ]);
    }
}