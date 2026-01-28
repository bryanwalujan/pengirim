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
