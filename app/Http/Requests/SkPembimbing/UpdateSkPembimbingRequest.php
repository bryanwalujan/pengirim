<?php
// filepath: /c:/laragon/www/eservice-app/app/Http/Requests/SkPembimbing/UpdateSkPembimbingRequest.php

namespace App\Http\Requests\SkPembimbing;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateSkPembimbingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pengajuan = $this->route('pengajuan');
        return $pengajuan->canBeEditedByMahasiswa() &&
            $pengajuan->mahasiswa_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'judul_skripsi' => ['required', 'string', 'max:500'],
            'file_surat_permohonan' => [
                'nullable',
                File::types(['pdf', 'jpg', 'jpeg', 'png'])->max(2 * 1024),
            ],
            'file_slip_ukt' => [
                'nullable',
                File::types(['pdf', 'jpg', 'jpeg', 'png'])->max(2 * 1024),
            ],
            'file_proposal_revisi' => [
                'nullable',
                File::types(['pdf'])->max(10 * 1024),
            ],
        ];
    }
}