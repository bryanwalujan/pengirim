<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuratIjinSurveyRequest extends FormRequest
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
            'judul' => 'required|string|max:500',
            'tempat_survey' => 'required|string|max:500',
            'keterangan_tambahan' => 'nullable|string|max:500',
            'file_pendukung' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'semester' => 'required|in:ganjil,genap',
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul survey wajib diisi.',
            'tempat_survey.required' => 'Tempat survey wajib diisi.',
            'semester.in' => 'Semester harus berupa ganjil atau genap.',
        ];
    }
}