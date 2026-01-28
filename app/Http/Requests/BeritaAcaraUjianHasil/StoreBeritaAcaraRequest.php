<?php

namespace App\Http\Requests\BeritaAcaraUjianHasil;

use Illuminate\Foundation\Http\FormRequest;

class StoreBeritaAcaraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(['staff', 'admin']);
    }

    public function rules(): array
    {
        return [
            'catatan_tambahan' => ['nullable', 'string', 'max:1000'],
            'ruangan' => ['nullable', 'string', 'max:255'],
        ];
    }
}
