<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSuratPindahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'catatan_admin' => 'required|string',
        ];

        if ($this->user()->hasRole('staff')) {
            $rules['status'] = 'required|in:diproses,ditolak,siap_diambil';

            if ($this->status === 'diproses' && $this->surat->status === 'diajukan') {
                $rules['nomor_surat'] = 'nullable|string|max:50|regex:/^\d{1,4}(\/UN41\.2\/TI\/PD\/\d{4})?$/';
            }
        }

        if ($this->user()->hasRole('dosen')) {
            $rules['status'] = 'required|in:disetujui_kaprodi,disetujui,ditolak';

            if ($this->status === 'disetujui_kaprodi' && $this->surat->status === 'diproses') {
                $rules['penandatangan_kaprodi_id'] = 'required|exists:users,id';
                $rules['jabatan_penandatangan_kaprodi'] = 'required|string|max:255';
            }

            if ($this->status === 'disetujui' && $this->surat->status === 'disetujui_kaprodi') {
                $rules['penandatangan_id'] = 'required|exists:users,id';
                $rules['jabatan_penandatangan'] = 'required|string|max:255';
            }
        }

        return $rules;
    }
}