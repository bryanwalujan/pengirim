<?php

namespace App\Http\Requests\BeritaAcaraUjianHasil;

use Illuminate\Foundation\Http\FormRequest;

class StoreLembarKoreksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'koreksi' => ['required', 'array', 'min:1'],
            'koreksi.*.halaman' => ['required', 'string', 'max:50'],
            'koreksi.*.catatan' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'koreksi.required' => 'Minimal satu item koreksi harus diisi.',
            'koreksi.min' => 'Minimal satu item koreksi harus diisi.',
            'koreksi.*.halaman.required' => 'Nomor halaman harus diisi.',
            'koreksi.*.halaman.max' => 'Nomor halaman maksimal 50 karakter.',
            'koreksi.*.catatan.required' => 'Catatan koreksi harus diisi.',
            'koreksi.*.catatan.max' => 'Catatan koreksi maksimal 1000 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'koreksi' => 'Data Koreksi',
            'koreksi.*.halaman' => 'Halaman',
            'koreksi.*.catatan' => 'Catatan',
        ];
    }

    /**
     * Get validated koreksi data formatted for storage
     */
    public function getFormattedKoreksiData(): array
    {
        $koreksi = [];
        $no = 1;

        foreach ($this->validated()['koreksi'] as $item) {
            if (!empty($item['halaman']) || !empty($item['catatan'])) {
                $koreksi[] = [
                    'no' => $no++,
                    'halaman' => $item['halaman'] ?? '',
                    'catatan' => $item['catatan'] ?? '',
                ];
            }
        }

        return $koreksi;
    }
}
