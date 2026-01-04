<?php
// filepath: app/Http/Requests/BeritaAcaraSempro/StoreBeritaAcaraRequest.php

namespace App\Http\Requests\BeritaAcaraSempro;

use Illuminate\Foundation\Http\FormRequest;

class StoreBeritaAcaraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\BeritaAcaraSeminarProposal::class);
    }

    public function rules(): array
    {
        return [
            'catatan_tambahan' => 'nullable|string|max:1000',
        ];
    }
}