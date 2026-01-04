<?php
// filepath: app/Http/Requests/BeritaAcaraSempro/UpdatePembahasRequest.php

namespace App\Http\Requests\BeritaAcaraSempro;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePembahasRequest extends FormRequest
{
    public function authorize(): bool
    {
        $beritaAcara = $this->route('beritaAcara');
        return $this->user()->can('managePembahas', $beritaAcara);
    }

    public function rules(): array
    {
        return [
            'pembahas' => 'required|array|min:1',
            'pembahas.*.dosen_id' => 'required|exists:users,id',
            'pembahas.*.posisi' => [
                'required',
                'string',
                'in:Anggota Pembahas 1,Anggota Pembahas 2,Anggota Pembahas 3'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'pembahas.required' => 'Data pembahas wajib diisi.',
            'pembahas.*.dosen_id.required' => 'Dosen wajib dipilih.',
            'pembahas.*.dosen_id.exists' => 'Dosen tidak valid.',
            'pembahas.*.posisi.required' => 'Posisi wajib diisi.',
            'pembahas.*.posisi.in' => 'Posisi harus Anggota Pembahas 1, 2, atau 3.',
        ];
    }
}