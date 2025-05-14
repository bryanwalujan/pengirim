<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

trait HasQrSignature
{
    public function generateQrSignature($signer, $additionalData = [])
    {
        $baseData = [
            'document_type' => class_basename($this),
            'document_id' => $this->id,
            'signer' => [
                'name' => $signer->name,
                'position' => $additionalData['position'] ?? null,
                'nip' => $signer->nip ?? null,
                'signer_id' => $signer->id,
            ],
            'signature_date' => now()->toDateTimeString(),
            'verification_contacts' => [
                'email' => config('app.verification_email', 'admin@univ.ac.id'),
                'phone' => config('app.verification_phone', '+62 123 4567 8910'),
            ],
            'verification_url' => route('document.verify', ['code' => $this->verification_code]),
        ];

        $signatureData = array_merge($baseData, $additionalData);

        $qrPath = 'signatures/' . strtolower(class_basename($this)) . '_' . $this->id . '_' . md5(time()) . '.svg';

        Storage::disk('public')->put($qrPath, QrCode::size(300)
            ->errorCorrection('H')
            ->generate(json_encode($signatureData)));

        return $qrPath;
    }

    public function getQrCodeUrlAttribute()
    {
        return $this->signature_path ? Storage::url($this->signature_path) : null;
    }
}
