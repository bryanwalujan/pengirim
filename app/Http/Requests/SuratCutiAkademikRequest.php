<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuratCutiAkademikRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'alasan_pengajuan' => 'required|string|max:500',
            'tahun_ajaran' => 'required|string|regex:/^\d{4}\/\d{4}$/',
            'semester' => 'required|in:ganjil,genap',
            'keterangan_tambahan' => 'nullable|string|max:500',
            'file_pendukung' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'tahun_ajaran.regex' => 'Format tahun ajaran harus YYYY/YYYY, misal 2023/2024.',
            'alasan_pengajuan.required' => 'Alasan cuti harus diisi.',
            'semester.required' => 'Semester cuti harus dipilih.',
        ];
    }
}