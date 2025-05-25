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
        $rules = [
            'catatan_admin' => 'required|string',
        ];

        if ($this->user()->hasRole('staff')) {
            $rules['status'] = 'required|in:diproses,ditolak,siap_diambil';

            if ($this->status === 'diproses' && $this->surat->status === 'diajukan') {
                $rules['nomor_surat'] = 'nullable|string|max:50|regex:/^\d{1,4}(\/UN41\.2\/TI\/\d{4})?$/';
            }

            // if ($this->status === 'siap_diambil' && $this->surat->status === 'disetujui') {
            //     $rules['penandatangan_id'] = 'required|exists:users,id';
            //     $rules['jabatan_penandatangan'] = 'required|string|max:255';
            // }
        }

        // ... rules untuk dosen ...
        if ($this->user()->hasRole('dosen')) {
            $rules['status'] = 'required|in:disetujui_kaprodi,disetujui_pimpinan,ditolak';

            if ($this->status === 'disetujui_kaprodi' && $this->surat->status === 'diproses') {
                $rules['penandatangan_kaprodi_id'] = 'required|exists:users,id';
                $rules['jabatan_penandatangan_kaprodi'] = 'required|string|max:255';
            }

            if ($this->status === 'disetujui_pimpinan' && $this->surat->status === 'disetujui_kaprodi') {
                $rules['penandatangan_id'] = 'required|exists:users,id';
                $rules['jabatan_penandatangan'] = 'required|string|max:255';
            }
        }


        return $rules;
    }
}
