<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TahunAjaran;
use App\Models\PembayaranUkt;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MahasiswaSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mahasiswaUsers = [
            [
                'name' => 'Patrick Rompas',
                'nim' => '20210047',
                'email' => '20210047@unima.ac.id',
                'password' => 'password',
            ],
            [
                'name' => 'Maria Sari',
                'nim' => '20210048',
                'email' => '20210048@unima.ac.id',
                'password' => 'password',
            ],
            [
                'name' => 'Ahmad Rizki',
                'nim' => '20210049',
                'email' => '20210049@unima.ac.id',
                'password' => 'password',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'nim' => '20210050',
                'email' => '20210050@unima.ac.id',
                'password' => 'password',
            ],
        ];

        // Ambil tahun ajaran aktif
        $tahunAjaranAktif = TahunAjaran::where('status_aktif', true)->first();

        foreach ($mahasiswaUsers as $userData) {
            $user = User::factory()->create($userData);
            $user->assignRole('mahasiswa');

            // Set status pembayaran UKT menjadi lunas (bayar)
            if ($tahunAjaranAktif) {
                PembayaranUkt::create([
                    'mahasiswa_id' => $user->id,
                    'tahun_ajaran_id' => $tahunAjaranAktif->id,
                    'status' => 'bayar', // lunas
                ]);
            }
        }

        // Generate bulk mahasiswa users if needed and set their UKT as well
        // $bulkMahasiswas = User::factory()->count(50)->mahasiswa()->create();
        // foreach ($bulkMahasiswas as $mhs) {
        //     if ($tahunAjaranAktif) {
        //         PembayaranUkt::create([
        //             'mahasiswa_id' => $mhs->id,
        //             'tahun_ajaran_id' => $tahunAjaranAktif->id,
        //             'status' => 'bayar',
        //         ]);
        //     }
        // }

        $this->command->info('Mahasiswa users seeded successfully and UKT status set to PAID!');
    }
}
