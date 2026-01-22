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

            // Validate PS1 and PS2 are from berita acara
            $pengajuan = $this->route('pengajuan');
            if ($pengajuan) {
                $pengajuan->load([
                    'beritaAcara.jadwalSeminarProposal.pendaftaranSeminarProposal',
                    'beritaAcara.jadwalSeminarProposal.dosenPenguji'
                ]);

                if ($pengajuan->beritaAcara && $pengajuan->beritaAcara->jadwalSeminarProposal) {
                    $jadwal = $pengajuan->beritaAcara->jadwalSeminarProposal;
                    $pendaftaran = $jadwal->pendaftaranSeminarProposal;

                    // Kumpulkan ID dosen yang eligible
                    $eligibleDosenIds = collect();

                    // Dosen pembimbing awal
                    if ($pendaftaran && $pendaftaran->dosen_pembimbing_id) {
                        $eligibleDosenIds->push($pendaftaran->dosen_pembimbing_id);
                    }

                    // Dosen penguji
                    $dosenPengujiIds = $jadwal->dosenPenguji()->pluck('users.id');
                    $eligibleDosenIds = $eligibleDosenIds->merge($dosenPengujiIds)->unique();

                    // Validasi PS1
                    if ($this->dosen_pembimbing_1_id && !$eligibleDosenIds->contains($this->dosen_pembimbing_1_id)) {
                        $validator->errors()->add('dosen_pembimbing_1_id', 'PS1 harus dipilih dari dosen yang ada di berita acara seminar proposal mahasiswa ini.');
                    }

                    // Validasi PS2
                    if ($this->dosen_pembimbing_2_id && !$eligibleDosenIds->contains($this->dosen_pembimbing_2_id)) {
                        $validator->errors()->add('dosen_pembimbing_2_id', 'PS2 harus dipilih dari dosen yang ada di berita acara seminar proposal mahasiswa ini.');
                    }
                }
            }
        });
    }
}