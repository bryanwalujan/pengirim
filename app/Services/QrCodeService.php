<?php

namespace App\Services;

use App\Models\SuratAktifKuliah;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    public function generateForSurat(SuratAktifKuliah $surat)
    {
        $verificationUrl = route('surat.verify', $surat->verification_code);
        $qrCode = QrCode::format('png')
            ->size(200)
            ->generate($verificationUrl);

        $path = "surat-aktif-kuliah/qrcodes/{$surat->id}.png";
        Storage::put($path, $qrCode);

        return $path;
    }
}