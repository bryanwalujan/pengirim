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
        // Create roles
        $roles = ['staff', 'dosen', 'mahasiswa'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Create specific test users
        $this->createSpecificUsers();

        // Generate bulk users
        $this->generateBulkUsers();
    }

    protected function createSpecificUsers(): void
    {
        // Staff
        User::factory()->create([
            'name' => 'Staff Administrasi',
            'username' => 'staff01',
            'email' => 'staff@unima.ac.id',
            'password' => 'password', // Will be hashed automatically
        ])->assignRole('staff');

        // Dosen
        User::factory()->create([
            'name' => 'Dr. Dosen Pertama, M.Kom',
            'username' => '123456',
            'nidn' => '123456',
            'email' => 'dosen1@unima.ac.id',
            'password' => 'password',
        ])->assignRole('dosen');

        User::factory()->create([
            'name' => 'Prof. Dosen Kedua, Ph.D',
            'username' => '654321',
            'nidn' => '654321',
            'email' => 'dosen2@unima.ac.id',
            'password' => 'password',
        ])->assignRole('dosen');

        // Mahasiswa
        User::factory()->create([
            'name' => 'Mahasiswa Pertama',
            'username' => '123456789',
            'nim' => '123456789',
            'email' => 'mahasiswa1@unima.ac.id',
            'password' => 'password',
        ])->assignRole('mahasiswa');

        User::factory()->create([
            'name' => 'Mahasiswa Kedua',
            'username' => '987654321',
            'nim' => '987654321',
            'email' => 'mahasiswa2@unima.ac.id',
            'password' => 'password',
        ])->assignRole('mahasiswa');
    }

    protected function generateBulkUsers(): void
    {
        // Generate 5 staff users
        User::factory()->count(5)->staff()->create();

        // Generate 10 dosen users
        User::factory()->count(10)->dosen()->create();

        // Generate 50 mahasiswa users
        User::factory()->count(10)->mahasiswa()->create();

        $this->command->info('User seeding completed!');
        $this->command->info('Staff test account: staff@unima.ac.id / password');
        $this->command->info('Dosen test account: dosen1@unima.ac.id / password');
        $this->command->info('Mahasiswa test account: mahasiswa1@unima.ac.id / password');
    }
}