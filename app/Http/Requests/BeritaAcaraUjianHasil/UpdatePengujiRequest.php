<?php

namespace App\Http\Requests\BeritaAcaraUjianHasil;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePengujiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(['staff', 'admin']);
    }

    public function rules(): array
    {
        return [
            'penguji' => ['required', 'array', 'min:1'],
            'penguji.*.dosen_id' => ['required', 'exists:users,id'],
            'penguji.*.posisi' => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'penguji.required' => 'Minimal harus ada satu dosen penguji.',
            'penguji.*.dosen_id.required' => 'Pilih dosen untuk setiap posisi penguji.',
            'penguji.*.dosen_id.exists' => 'Dosen tidak ditemukan.',
            'penguji.*.posisi.required' => 'Posisi penguji harus diisi.',
        ];
    }
}
