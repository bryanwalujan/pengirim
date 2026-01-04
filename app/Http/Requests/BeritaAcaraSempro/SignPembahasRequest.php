<?php
// filepath: app/Http/Requests/BeritaAcaraSempro/SignPembahasRequest.php

namespace App\Http\Requests\BeritaAcaraSempro;

use Illuminate\Foundation\Http\FormRequest;

class SignPembahasRequest extends FormRequest
{
    public function authorize(): bool
    {
        $beritaAcara = $this->route('beritaAcara');
        return $this->user()->can('signAsPembahas', $beritaAcara);
    }

    public function rules(): array
    {
        return [
            'confirmation' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'confirmation.required' => 'Anda harus menyetujui pernyataan untuk melanjutkan.',
            'confirmation.accepted' => 'Anda harus mencentang checkbox persetujuan.',
        ];
    }
}