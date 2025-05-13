<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MahasiswaImport implements ToCollection, WithHeadingRow
{
    protected $importedCount = 0;
    protected $skippedCount = 0;
    protected $nonMahasiswaCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip if NIM is empty
            if (empty($row['nim'])) {
                $this->skippedCount++;
                continue;
            }

            // Check if student already exists
            $existingStudent = User::where('nim', $row['nim'])->first();

            if ($existingStudent) {
                $this->skippedCount++;
                continue;
            }

            try {
                // Create new student
                $user = User::create([
                    'nim' => $row['nim'],
                    'name' => $row['nama'] ?? $row['name'] ?? 'Mahasiswa Baru',
                    'email' => $row['email'] ?? $row['nim'] . '@unima.ac.id',
                    'password' => Hash::make($row['password'] ?? $this->generateRandomPassword())
                ]);

                $user->assignRole('mahasiswa');
                $this->importedCount++;

            } catch (\Exception $e) {
                Log::error('Error importing student: ' . $e->getMessage());
                $this->skippedCount++;
            }
        }
    }

    public function getRowCount()
    {
        return $this->importedCount;
    }

    public function getSkippedCount()
    {
        return $this->skippedCount;
    }

    public function getNonMahasiswaCount()
    {
        return $this->nonMahasiswaCount;
    }

    private function generateRandomPassword($length = 12)
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
    }


}