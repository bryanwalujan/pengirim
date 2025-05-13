<?php

namespace App\Traits;

use App\Models\DokumenPendukung;

trait HasDokumenPendukung
{
    public function dokumenPendukung()
    {
        return $this->morphMany(DokumenPendukung::class, 'model');
    }

    public function attachDokumenPendukung($files)
    {
        foreach ($files as $file) {
            $path = $file->store('dokumen-pendukung', 'public');

            $this->dokumenPendukung()->create([
                'path' => $path,
                'nama_asli' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize()
            ]);
        }
    }
}