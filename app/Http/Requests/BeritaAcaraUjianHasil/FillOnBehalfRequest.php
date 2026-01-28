<?php

namespace App\Http\Requests\BeritaAcaraUjianHasil;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk staff menandatangani atas nama ketua penguji.
 * 
 * CATATAN: Berita acara ujian hasil TIDAK memerlukan keputusan (Lulus/Tidak Lulus).
 * Staff hanya perlu menandatangani untuk mengesahkan pelaksanaan ujian.
 */
class FillOnBehalfRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(['staff', 'admin']);
    }

    public function rules(): array
    {
        return [
            'catatan_tambahan' => ['nullable', 'string', 'max:2000'],
            'alasan_override' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'alasan_override.required' => 'Alasan override harus diisi.',
        ];
    }

    public function attributes(): array
    {
        return [
            'catatan_tambahan' => 'Catatan Tambahan',
            'alasan_override' => 'Alasan Override',
        ];
    }
}
