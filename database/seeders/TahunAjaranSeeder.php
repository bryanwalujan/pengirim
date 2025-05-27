<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TahunAjaran;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TahunAjaran::create([
            'tahun' => '2024/2025',
            'semester' => 'ganjil',
            'status_aktif' => true,
        ]);

        TahunAjaran::create([
            'tahun' => '2023/2024',
            'semester' => 'genap',
            'status_aktif' => false,
        ]);
    }
}
