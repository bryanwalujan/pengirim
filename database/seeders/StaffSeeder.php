<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StaffSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffUsers = [
            [
                'name' => 'Staff Admin',
                'email' => 'staff@unima.ac.id',
                'password' => 'password',
            ],
            [
                'name' => 'Staff Akademik',
                'email' => 'akademik@unima.ac.id',
                'password' => 'password',
            ],
            [
                'name' => 'Staff Keuangan',
                'email' => 'keuangan@unima.ac.id',
                'password' => 'password',
            ],
        ];

        foreach ($staffUsers as $userData) {
            $user = User::factory()->create($userData);
            $user->assignRole('staff');
        }

        $this->command->info('Staff users seeded successfully!');
    }
}
