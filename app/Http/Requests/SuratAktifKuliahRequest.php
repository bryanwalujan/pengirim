<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuratAktifKuliahRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tujuan_pengajuan' => 'required|string|max:500',
            'keterangan_tambahan' => 'nullable|string|max:500',
            'file_pendukung' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'tahun_ajaran' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => 'required|in:ganjil,genap',
        ];
    }

    public function messages(): array
    {
        return [
            'tahun_ajaran.regex' => 'Format tahun ajaran harus YYYY/YYYY, misal 2023/2024.',
        ];
    }
}
