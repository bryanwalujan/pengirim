<?php
// filepath: /c:/laragon/www/eservice-app/app/Http/Requests/SkPembimbing/StoreSkPembimbingRequest.php

namespace App\Http\Requests\SkPembimbing;

use App\Models\BeritaAcaraSeminarProposal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreSkPembimbingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('mahasiswa');
    }

    public function rules(): array
    {
        return [
            'berita_acara_id' => ['required', 'exists:berita_acara_seminar_proposals,id'],
            'judul_skripsi' => ['required', 'string', 'max:500'],
            'file_surat_permohonan' => [
                'required',
                File::types(['pdf', 'jpg', 'jpeg', 'png'])->max(2 * 1024),
            ],
            'file_slip_ukt' => [
                'required',
                File::types(['pdf', 'jpg', 'jpeg', 'png'])->max(2 * 1024),
            ],
            'file_proposal_revisi' => [
                'required',
                File::types(['pdf'])->max(10 * 1024),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file_surat_permohonan.max' => 'Surat permohonan maksimal 2MB.',
            'file_slip_ukt.max' => 'Slip UKT maksimal 2MB.',
            'file_proposal_revisi.max' => 'Proposal revisi maksimal 10MB.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $beritaAcara = BeritaAcaraSeminarProposal::find($this->berita_acara_id);
            
            if (!$beritaAcara?->isSelesai()) {
                $validator->errors()->add('berita_acara_id', 'Berita Acara belum selesai.');
            }

            if ($beritaAcara && !in_array($beritaAcara->keputusan, ['Ya', 'Ya, dengan perbaikan'])) {
                $validator->errors()->add('berita_acara_id', 'Keputusan seminar proposal tidak valid.');
            }
        });
    }
}