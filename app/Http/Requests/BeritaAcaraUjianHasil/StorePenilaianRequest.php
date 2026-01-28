<?php

namespace App\Http\Requests\BeritaAcaraUjianHasil;

use Illuminate\Foundation\Http\FormRequest;

class StorePenilaianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nilai_kebaruan' => ['required', 'integer', 'min:0', 'max:100'],
            'nilai_metode' => ['required', 'integer', 'min:0', 'max:100'],
            'nilai_data_software' => ['required', 'integer', 'min:0', 'max:100'],
            'nilai_referensi' => ['required', 'integer', 'min:0', 'max:100'],
            'nilai_penguasaan' => ['required', 'integer', 'min:0', 'max:100'],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nilai_kebaruan.required' => 'Nilai Kebaruan (Novelty) harus diisi.',
            'nilai_kebaruan.min' => 'Nilai Kebaruan minimal 0.',
            'nilai_kebaruan.max' => 'Nilai Kebaruan maksimal 100.',

            'nilai_metode.required' => 'Nilai Metode harus diisi.',
            'nilai_metode.min' => 'Nilai Metode minimal 0.',
            'nilai_metode.max' => 'Nilai Metode maksimal 100.',

            'nilai_data_software.required' => 'Nilai Data/Software harus diisi.',
            'nilai_data_software.min' => 'Nilai Data/Software minimal 0.',
            'nilai_data_software.max' => 'Nilai Data/Software maksimal 100.',

            'nilai_referensi.required' => 'Nilai Referensi harus diisi.',
            'nilai_referensi.min' => 'Nilai Referensi minimal 0.',
            'nilai_referensi.max' => 'Nilai Referensi maksimal 100.',

            'nilai_penguasaan.required' => 'Nilai Penguasaan Materi harus diisi.',
            'nilai_penguasaan.min' => 'Nilai Penguasaan Materi minimal 0.',
            'nilai_penguasaan.max' => 'Nilai Penguasaan Materi maksimal 100.',

            'catatan.max' => 'Catatan maksimal 2000 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nilai_kebaruan' => 'Nilai Kebaruan',
            'nilai_metode' => 'Nilai Metode',
            'nilai_data_software' => 'Nilai Data/Software',
            'nilai_referensi' => 'Nilai Referensi',
            'nilai_penguasaan' => 'Nilai Penguasaan Materi',
            'catatan' => 'Catatan',
        ];
    }
}
