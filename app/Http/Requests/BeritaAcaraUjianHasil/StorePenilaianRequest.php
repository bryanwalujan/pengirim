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
            // 7 Komponen Penilaian (nilai 0-100)
            'nilai_kebaruan' => ['required', 'integer', 'min:0', 'max:100'],
            'nilai_kesesuaian' => ['required', 'integer', 'min:0', 'max:100'],
            'nilai_metode' => ['required', 'integer', 'min:0', 'max:100'],
            'nilai_kajian_teori' => ['required', 'integer', 'min:0', 'max:100'],
            'nilai_hasil_penelitian' => ['required', 'integer', 'min:0', 'max:100'],
            'nilai_referensi' => ['required', 'integer', 'min:0', 'max:100'],
            'nilai_tata_bahasa' => ['required', 'integer', 'min:0', 'max:100'],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nilai_kebaruan.required' => 'Nilai Kebaruan dan Signifikansi harus diisi.',
            'nilai_kebaruan.min' => 'Nilai Kebaruan minimal 0.',
            'nilai_kebaruan.max' => 'Nilai Kebaruan maksimal 100.',

            'nilai_kesesuaian.required' => 'Nilai Kesesuaian Judul harus diisi.',
            'nilai_kesesuaian.min' => 'Nilai Kesesuaian minimal 0.',
            'nilai_kesesuaian.max' => 'Nilai Kesesuaian maksimal 100.',

            'nilai_metode.required' => 'Nilai Metode Penelitian harus diisi.',
            'nilai_metode.min' => 'Nilai Metode minimal 0.',
            'nilai_metode.max' => 'Nilai Metode maksimal 100.',

            'nilai_kajian_teori.required' => 'Nilai Kajian Teori harus diisi.',
            'nilai_kajian_teori.min' => 'Nilai Kajian Teori minimal 0.',
            'nilai_kajian_teori.max' => 'Nilai Kajian Teori maksimal 100.',

            'nilai_hasil_penelitian.required' => 'Nilai Hasil Penelitian harus diisi.',
            'nilai_hasil_penelitian.min' => 'Nilai Hasil Penelitian minimal 0.',
            'nilai_hasil_penelitian.max' => 'Nilai Hasil Penelitian maksimal 100.',

            'nilai_referensi.required' => 'Nilai Referensi harus diisi.',
            'nilai_referensi.min' => 'Nilai Referensi minimal 0.',
            'nilai_referensi.max' => 'Nilai Referensi maksimal 100.',

            'nilai_tata_bahasa.required' => 'Nilai Tata Bahasa harus diisi.',
            'nilai_tata_bahasa.min' => 'Nilai Tata Bahasa minimal 0.',
            'nilai_tata_bahasa.max' => 'Nilai Tata Bahasa maksimal 100.',

            'catatan.max' => 'Catatan maksimal 2000 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nilai_kebaruan' => 'Kebaruan dan Signifikansi Penelitian',
            'nilai_kesesuaian' => 'Kesesuaian Judul, Masalah, Tujuan, Pembahasan, Kesimpulan, dan Saran',
            'nilai_metode' => 'Metode Penelitian dan Pemecahan Masalah',
            'nilai_kajian_teori' => 'Kajian Teori',
            'nilai_hasil_penelitian' => 'Hasil Penelitian',
            'nilai_referensi' => 'Referensi',
            'nilai_tata_bahasa' => 'Tata Bahasa',
            'catatan' => 'Catatan',
        ];
    }
}
