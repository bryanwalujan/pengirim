<?php
// filepath: /c:/laragon/www/eservice-app/app/Http/Requests/SkPembimbing/SignSkRequest.php

namespace App\Http\Requests\SkPembimbing;

use Illuminate\Foundation\Http\FormRequest;

class SignSkRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pengajuan = $this->route('pengajuan');
        $user = $this->user();

        return $pengajuan->canBeSignedByKajur() || 
               $pengajuan->canBeSignedByKorprodi();
    }

    public function rules(): array
    {
        return [
            'confirm' => ['required', 'accepted'],
        ];
    }
}