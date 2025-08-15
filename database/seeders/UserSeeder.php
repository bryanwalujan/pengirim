<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles first
        $this->createRoles();

        // Run specific seeders
        $this->call([
            StaffSeeder::class,
            DosenSeeder::class,
            MahasiswaSeeder::class,
        ]);

        $this->command->info('User seeding completed!');
        $this->command->info('Test Accounts:');
        $this->command->info('Staff: staff@unima.ac.id / password');
        $this->command->info('Dosen: dosen@unima.ac.id / password');
        $this->command->info('Mahasiswa: 20210047@unima.ac.id / password');
    }

    protected function createRoles(): void
    {
        $roles = ['staff', 'dosen', 'mahasiswa'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}