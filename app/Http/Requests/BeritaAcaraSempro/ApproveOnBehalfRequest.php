<?php
// filepath: app/Http/Requests/BeritaAcaraSempro/ApproveOnBehalfRequest.php

namespace App\Http\Requests\BeritaAcaraSempro;

use Illuminate\Foundation\Http\FormRequest;

class ApproveOnBehalfRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('approveOnBehalf', \App\Models\BeritaAcaraSeminarProposal::class);
    }

    public function rules(): array
    {
        return [
            'dosen_id' => ['required', 'exists:users,id'],
            'alasan' => ['nullable', 'string', 'max:500'],
            'confirmation' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'dosen_id.required' => 'Dosen harus dipilih.',
            'dosen_id.exists' => 'Dosen tidak valid.',
            'alasan.max' => 'Alasan maksimal 500 karakter.',
            'confirmation.required' => 'Anda harus menyetujui pernyataan.',
            'confirmation.accepted' => 'Anda harus mencentang checkbox persetujuan.',
        ];
    }
}