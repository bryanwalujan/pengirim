<?php
// filepath: /c:/laragon/www/eservice-app/app/Http/Requests/SkPembimbing/VerifyDokumenRequest.php

namespace App\Http\Requests\SkPembimbing;

use Illuminate\Foundation\Http\FormRequest;

class VerifyDokumenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(['staff', 'admin']);
    }

    public function rules(): array
    {
        return [
            'action' => ['required', 'in:approve,reject'],
            'alasan_ditolak' => ['required_if:action,reject', 'nullable', 'string', 'max:1000'],
        ];
    }
}