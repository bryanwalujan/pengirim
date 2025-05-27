<?php

namespace Database\Seeders;

use App\Models\KopSurat;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KopSuratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KopSurat::create([
            'logo' => 'kop-surat/logo-unima.png', // asumsi file sudah ada di storage/app/public/kop-surat/logo.png
            'kementerian' => 'KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI',
            'universitas' => 'UNIVERSITAS NEGERI MANADO',
            'fakultas' => 'FAKULTAS TEKNIK',
            'prodi' => 'PROGRAM STUDI S1 TEKNIK INFORMATIKA',
            'alamat' => 'Alamat : Kampus UNIMA Tondano 95618, Telp.(0431)7233580',
            'kontak' => 'Website : https://ti.unima.ac.id, Email : teknikinformatika@unima.ac.id'
        ]);
    }
}
