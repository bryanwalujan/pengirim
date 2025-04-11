<?php

namespace App\Imports;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MahasiswaImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $password = $row['password'] ?? $this->generateRandomPassword();
        $email = $row['nim'] . '@unima.ac.id'; // Email otomatis dari nim

        $user = User::create([
            'nim' => $row['nim'],
            'name' => $row['nama'],
            'email' => $email,
            'username' => $row['nim'],
            'password' => Hash::make($password),
        ]);

        Role::firstOrCreate(['name' => 'mahasiswa']);
        $user->assignRole('mahasiswa');

        return $user;
    }

    public function rules(): array
    {
        return [
            'nim' => 'required|unique:users,nim',
            'nama' => 'required',
        ];
    }

    private function generateRandomPassword($length = 12)
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
    }
}
