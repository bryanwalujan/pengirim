<?php

namespace Database\Seeders;

use App\Models\User;
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

        foreach ($mahasiswaUsers as $userData) {
            $user = User::factory()->create($userData);
            $user->assignRole('mahasiswa');
        }

        // Generate bulk mahasiswa users if needed
        // User::factory()->count(50)->mahasiswa()->create();

        $this->command->info('Mahasiswa users seeded successfully!');
    }
}
