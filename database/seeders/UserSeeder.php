<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan role sudah ada, jika belum buat
        $roles = ['mahasiswa', 'staff', 'dosen'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Data user dengan role mahasiswa
        $mahasiswa = User::create([
            'nim' => '123456789',
            'name' => 'Mahasiswa Satu',
            'email' => 'mahasiswa1@unima.ac.id',
            'password' => Hash::make('password'),
        ]);
        $mahasiswa->assignRole('mahasiswa');

        $mahasiswa2 = User::create([
            'nim' => '987654321',
            'name' => 'Mahasiswa Dua',
            'email' => 'mahasiswa2@unima.ac.id',
            'password' => Hash::make('password'),
        ]);
        $mahasiswa2->assignRole('mahasiswa');

        // Data user dengan role staff
        $staff = User::create([
            'nim' => null, // Staff tidak perlu NIM
            'name' => 'Staff Satu',
            'email' => 'staff1@unima.ac.id',
            'password' => Hash::make('password'),
        ]);
        $staff->assignRole('staff');

        // Data user dengan role dosen
        $dosen = User::create([
            'nim' => null, // Dosen tidak perlu NIM
            'name' => 'Dosen Satu',
            'email' => 'dosen1@unima.ac.id',
            'password' => Hash::make('password'),
        ]);
        $dosen->assignRole('dosen');
    }
}
