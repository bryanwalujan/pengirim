<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DosenSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dosenUsers = [
            [
                'name' => 'Kristofel Santa, S.ST, M.MT',
                'nip' => '19870531 201504 1 003',
                'jabatan' => 'Koordinator Program Studi',
                'email' => 'dosen@unima.ac.id',
                'password' => 'password',
            ],
            [
                'name' => 'Arje C. Djamen. ST, MT',
                'nip' => '19870712 201012 1 006',
                'jabatan' => 'Pimpinan Jurusan PTIK',
                'email' => 'dosen2@unima.ac.id',
                'password' => 'password',
            ],
            [
                'name' => 'DR. AUDY A. KENAP, S.T, M.Eng',
                'nip' => '1987035712 201012 1 006',
                'jabatan' => 'Dosen',
                'email' => 'dosen3@unima.ac.id',
                'password' => 'password',
            ],
            [
                'name' => 'SONDY C. KUMAJAS, S.T, M.T',
                'nip' => '1987075312 201012 1 006',
                'jabatan' => 'Dosen',
                'email' => 'dosen4@unima.ac.id',
                'password' => 'password',
            ],
            [
                'name' => 'DR. IRENE TANGKAWAROW, S.T, MISD',
                'nip' => '19873530712 201012 1 006',
                'jabatan' => 'Dosen',
                'email' => 'dosen5@unima.ac.id',
                'password' => 'password',
            ],
            [
                'name' => 'VIVI P RANTUNG, S.T, MISD',
                'nip' => '1987071432 201012 1 006',
                'jabatan' => 'Dosen',
                'email' => 'dosen6@unima.ac.id',
                'password' => 'password',
            ],
        ];

        foreach ($dosenUsers as $userData) {
            $user = User::factory()->create($userData);
            $user->assignRole('dosen');
        }

        $this->command->info('Dosen users seeded successfully!');
    }
}
