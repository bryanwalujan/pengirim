<?php

namespace App\Http\Requests\BeritaAcaraUjianHasil;

use Illuminate\Foundation\Http\FormRequest;

class ApproveOnBehalfRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(['staff', 'admin']);
    }

    public function rules(): array
    {
        return [
            'dosen_id' => ['required', 'exists:users,id'],
            'alasan' => ['nullable', 'string', 'max:500'],
            'lembar_koreksi' => ['nullable', 'array'],
            'lembar_koreksi.*.halaman' => ['nullable', 'string', 'max:50'],
            'lembar_koreksi.*.catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'dosen_id.required' => 'Pilih dosen penguji yang akan disetujui.',
            'dosen_id.exists' => 'Dosen penguji tidak ditemukan.',
        ];
    }
}
