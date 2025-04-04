<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MahasiswaExport implements FromCollection, WithHeadings, WithMapping
{
    protected $isTemplate;

    public function __construct($isTemplate = false)
    {
        $this->isTemplate = $isTemplate;
    }

    public function collection()
    {
        if ($this->isTemplate) {
            return collect([]);
        }

        return User::role('mahasiswa')->get();
    }

    public function headings(): array
    {
        return [
            'NIM',
            'Nama',
            'Email',
            'Password (min 8 karakter)'
        ];
    }

    public function map($user): array
    {
        if ($this->isTemplate) {
            return [];
        }

        return [
            $user->nim,
            $user->name,
            $user->email,
            '' // Password dikosongkan untuk keamanan
        ];
    }
}