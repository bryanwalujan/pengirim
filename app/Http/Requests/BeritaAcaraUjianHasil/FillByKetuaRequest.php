<?php

namespace App\Http\Requests\BeritaAcaraUjianHasil;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk ketua penguji menandatangani berita acara ujian hasil.
 * 
 * CATATAN: Berita acara ujian hasil TIDAK memerlukan keputusan (Lulus/Tidak Lulus).
 * Ketua hanya perlu menandatangani untuk mengesahkan pelaksanaan ujian.
 */
class FillByKetuaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $beritaAcara = $this->route('beritaAcara');
        return $beritaAcara->canBeFilledByKetua($this->user()->id);
    }

    public function rules(): array
    {
        return [
            'catatan_tambahan' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'catatan_tambahan' => 'Catatan Tambahan',
        ];
    }
}
