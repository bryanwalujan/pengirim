<?php
// filepath: app/Http/Requests/BeritaAcaraSempro/FillByPembimbingRequest.php

namespace App\Http\Requests\BeritaAcaraSempro;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;

class FillByPembimbingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $beritaAcara = $this->route('beritaAcara');
        return $this->user()->can('fillAsPembimbing', $beritaAcara);
    }

    public function rules(): array
    {
        return [
            'catatan_kejadian' => [
                'required',
                Rule::in([
                    'Lancar',
                    'Ada beberapa perbaikan yang harus diubah'
                ])
            ],
            'keputusan' => [
                'required',
                'string',
                Rule::in([
                    'Ya',
                    'Ya, dengan perbaikan',  // ✅ Tanpa escape, langsung string biasa
                    'Tidak'
                ])
            ],
            'catatan_tambahan' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'catatan_kejadian.required' => 'Catatan kejadian wajib dipilih.',
            'catatan_kejadian.in' => 'Catatan kejadian tidak valid.',
            'keputusan.required' => 'Kesimpulan kelayakan wajib dipilih.',
            'keputusan.in' => 'Kesimpulan kelayakan tidak valid. Pilihan yang valid: Ya, Ya dengan perbaikan, atau Tidak.',
            'catatan_tambahan.max' => 'Catatan tambahan maksimal 1000 karakter.',
        ];
    }

    /**
     * Get custom attributes for validator errors
     */
    public function attributes(): array
    {
        return [
            'catatan_kejadian' => 'catatan kejadian',
            'keputusan' => 'kesimpulan kelayakan',
            'catatan_tambahan' => 'catatan tambahan',
        ];
    }

    /**
     * Handle a failed validation attempt
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        Log::error('FillByPembimbingRequest - Validation Failed', [
            'errors' => $validator->errors()->toArray(),
            'input_data' => $this->all(),
            'ba_id' => $this->route('beritaAcara')?->id
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Configure the validator instance
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            Log::info('FillByPembimbingRequest - Validation Attempt', [
                'input_data' => $this->all(),
                'ba_id' => $this->route('beritaAcara')?->id,
                'user_id' => $this->user()?->id
            ]);
        });
    }
}