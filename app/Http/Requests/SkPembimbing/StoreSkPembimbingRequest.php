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

    public function attributes(): array
    {
        return [
            'berita_acara_id' => 'Berita Acara Seminar Proposal',
            'judul_skripsi' => 'Judul Skripsi',
            'file_surat_permohonan' => 'Surat Permohonan',
            'file_slip_ukt' => 'Slip UKT',
            'file_proposal_revisi' => 'Proposal Revisi',
        ];
    }

    public function messages(): array
    {
        return [
            'berita_acara_id.required' => 'Silakan pilih Berita Acara Seminar Proposal.',
            'berita_acara_id.exists' => 'Berita Acara yang dipilih tidak valid.',
            'judul_skripsi.required' => 'Judul skripsi wajib diisi.',
            'judul_skripsi.max' => 'Judul skripsi maksimal 500 karakter.',
            'file_surat_permohonan.required' => 'Surat permohonan wajib diunggah.',
            'file_surat_permohonan.max' => 'Ukuran surat permohonan maksimal 2MB.',
            'file_slip_ukt.required' => 'Slip UKT wajib diunggah.',
            'file_slip_ukt.max' => 'Ukuran slip UKT maksimal 2MB.',
            'file_proposal_revisi.required' => 'Proposal revisi wajib diunggah.',
            'file_proposal_revisi.max' => 'Ukuran proposal revisi maksimal 10MB.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!$this->berita_acara_id) {
                return;
            }

            $beritaAcara = BeritaAcaraSeminarProposal::find($this->berita_acara_id);
            
            if (!$beritaAcara) {
                $validator->errors()->add('berita_acara_id', 'Berita Acara tidak ditemukan.');
                return;
            }

            if (!$beritaAcara->isSelesai()) {
                $validator->errors()->add('berita_acara_id', 'Berita Acara seminar proposal belum selesai.');
            }

            if (!in_array($beritaAcara->keputusan, ['Ya', 'Ya, dengan perbaikan'])) {
                $validator->errors()->add('berita_acara_id', 'Keputusan seminar proposal harus "Ya" atau "Ya, dengan perbaikan".');
            }
        });
    }
}