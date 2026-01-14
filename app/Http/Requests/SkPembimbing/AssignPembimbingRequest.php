<?php
// filepath: /c:/laragon/www/eservice-app/app/Http/Requests/SkPembimbing/AssignPembimbingRequest.php

namespace App\Http\Requests\SkPembimbing;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class AssignPembimbingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(['staff', 'admin']);
    }

    public function rules(): array
    {
        return [
            'dosen_pembimbing_1_id' => ['required', 'exists:users,id'],
            'dosen_pembimbing_2_id' => ['nullable', 'exists:users,id', 'different:dosen_pembimbing_1_id'],
            'catatan_staff' => ['nullable', 'string', 'max:1000'],
            'nomor_surat_type' => ['required', 'in:auto,custom'],
            'custom_nomor_surat' => ['required_if:nomor_surat_type,custom', 'nullable', 'numeric', 'min:1', 'max:9999'],
            'tanggal_surat' => ['required', 'date'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate PS1 is dosen
            $ps1 = User::find($this->dosen_pembimbing_1_id);
            if ($ps1 && !$ps1->hasRole('dosen')) {
                $validator->errors()->add('dosen_pembimbing_1_id', 'PS1 harus dosen.');
            }

            // Validate PS2 is dosen (if provided)
            if ($this->dosen_pembimbing_2_id) {
                $ps2 = User::find($this->dosen_pembimbing_2_id);
                if ($ps2 && !$ps2->hasRole('dosen')) {
                    $validator->errors()->add('dosen_pembimbing_2_id', 'PS2 harus dosen.');
                }
            }
        });
    }
}