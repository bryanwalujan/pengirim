<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuratPindahRequest extends FormRequest
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
            'universitas_tujuan' => 'required|string|max:255',
            'alasan_pengajuan' => 'required|string|max:500',
            'keterangan_tambahan' => 'nullable|string|max:500',
            'file_pendukung_path' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'semester' => 'required|in:ganjil,genap',
        ];
    }

    public function messages(): array
    {
        return [
            'universitas_tujuan.required' => 'Nama universitas tujuan wajib diisi',
            'alasan_pengajuan.required' => 'Alasan pengajuan wajib diisi',
        ];
    }
}