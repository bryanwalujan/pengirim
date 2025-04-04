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
        // Jika password tidak diisi, generate random password
        $password = $row['password'] ?? $this->generateRandomPassword();

        // Buat user terlebih dahulu
        $user = User::create([
            'nim' => $row['nim'],
            'name' => $row['nama'],
            'email' => $row['email'],
            'username' => $row['nim'],
            'password' => Hash::make($password),
        ]);

        // Pastikan role mahasiswa ada
        Role::firstOrCreate(['name' => 'mahasiswa']);

        // Assign role mahasiswa
        $user->assignRole('mahasiswa');

        return $user;
    }

    public function rules(): array
    {
        return [
            'nim' => 'required|unique:users,nim',
            'nama' => 'required',
            'email' => 'required|email|unique:users,email',
        ];
    }

    private function generateRandomPassword($length = 12)
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
    }
}