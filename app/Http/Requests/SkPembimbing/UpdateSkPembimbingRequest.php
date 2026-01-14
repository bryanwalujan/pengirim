<?php
// filepath: /c:/laragon/www/eservice-app/app/Http/Requests/SkPembimbing/UpdateSkPembimbingRequest.php

namespace App\Http\Requests\SkPembimbing;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateSkPembimbingRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization handled by Policy
        return true;
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

    public function attributes(): array
    {
        return [
            'judul_skripsi' => 'Judul Skripsi',
            'file_surat_permohonan' => 'Surat Permohonan',
            'file_slip_ukt' => 'Slip UKT',
            'file_proposal_revisi' => 'Proposal Revisi',
        ];
    }

    public function messages(): array
    {
        return [
            'judul_skripsi.required' => 'Judul skripsi wajib diisi.',
            'judul_skripsi.max' => 'Judul skripsi maksimal 500 karakter.',
            'file_surat_permohonan.max' => 'Ukuran surat permohonan maksimal 2MB.',
            'file_slip_ukt.max' => 'Ukuran slip UKT maksimal 2MB.',
            'file_proposal_revisi.max' => 'Ukuran proposal revisi maksimal 10MB.',
        ];
    }
}