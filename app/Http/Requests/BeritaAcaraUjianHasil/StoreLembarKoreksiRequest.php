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
            'koreksi' => ['nullable', 'array'],
            'koreksi.*.halaman' => ['nullable', 'string', 'max:50'],
            'koreksi.*.catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'koreksi.*.halaman.max' => 'Nomor halaman maksimal 50 karakter.',
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

        $validated = $this->validated();

        // Handle empty or null koreksi array
        if (empty($validated['koreksi'])) {
            return [];
        }

        foreach ($validated['koreksi'] as $item) {
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
