<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSuratAktifKuliahRequest extends FormRequest
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
            'status' => 'required|in:diajukan,diproses,disetujui,ditolak,siap_diambil,sudah_diambil',
            'catatan_admin' => 'required|string|max:500',
            'catatan_internal' => 'nullable|string|max:500',
            'penandatangan_id' => 'required_if:status,disetujui,siap_diambil|nullable|exists:users,id',
            'jabatan_penandatangan' => 'required_if:status,disetujui,siap_diambil|nullable|string|max:100',
        ];
    }
}
