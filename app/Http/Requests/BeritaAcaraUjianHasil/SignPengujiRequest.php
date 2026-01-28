<?php

namespace App\Http\Requests\BeritaAcaraUjianHasil;

use Illuminate\Foundation\Http\FormRequest;

class SignPengujiRequest extends FormRequest
{
    public function authorize(): bool
    {
        $beritaAcara = $this->route('beritaAcara');
        return $beritaAcara->canBeSignedByPenguji($this->user()->id);
    }

    public function rules(): array
    {
        return [];
    }
}
