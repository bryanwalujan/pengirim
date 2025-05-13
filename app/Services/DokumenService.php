<?php

namespace App\Services;

use App\Models\DokumenPendukung;
use Illuminate\Http\UploadedFile;

class DokumenService
{
    public function uploadDokumen($model, array $files)
    {
        foreach ($files as $file) {
            $this->uploadSingle($model, $file);
        }
    }

    protected function uploadSingle($model, UploadedFile $file)
    {
        $path = $file->store('dokumen-pendukung', 'public');

        return $model->dokumenPendukung()->create([
            'path' => $path,
            'nama_asli' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize()
        ]);
    }
}