<?php
// filepath: app/Http/Requests/BeritaAcaraSempro/FillOnBehalfRequest.php

namespace App\Http\Requests\BeritaAcaraSempro;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FillOnBehalfRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('fillOnBehalf', \App\Models\BeritaAcaraSeminarProposal::class);
    }

    public function rules(): array
    {
        return [
            'keputusan' => [
                'required',
                Rule::in(['Ya', 'Ya, dengan perbaikan', 'Tidak']),
            ],
            'catatan_tambahan' => ['nullable', 'string', 'max:1000'],
            'alasan_override' => ['required', 'string', 'max:500'],
            'confirmation' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'keputusan.required' => 'Kesimpulan kelayakan harus dipilih.',
            'keputusan.in' => 'Kesimpulan kelayakan tidak valid.',
            'catatan_tambahan.max' => 'Catatan tambahan maksimal 1000 karakter.',
            'alasan_override.required' => 'Alasan override wajib diisi.',
            'alasan_override.max' => 'Alasan override maksimal 500 karakter.',
            'confirmation.required' => 'Anda harus menyetujui pernyataan.',
            'confirmation.accepted' => 'Anda harus mencentang checkbox persetujuan.',
        ];
    }
}
