<?php

namespace App\Traits;

use App\Traits\GeneratesNomorSurat;

trait BaseSuratController
{
    use GeneratesNomorSurat;

    /**
     * Prefix untuk nomor surat (contoh: UN41.2/TI)
     */
    abstract protected function getNomorSuratPrefix();

    /**
     * Generate nomor surat untuk jenis surat tertentu
     */
    protected function generateNomorSurat($customNumber = null)
    {
        return $this->generateNomorSuratUniversal($this->getNomorSuratPrefix(), $customNumber);
    }

    /**
     * Validasi format nomor surat
     */
    protected function validateNomorSuratFormat($nomorSurat, $prefix)
    {
        $pattern = '#^\d{4}/' . preg_quote($prefix, '#') . '/\d{4}$#';
        return preg_match($pattern, $nomorSurat);
    }
}