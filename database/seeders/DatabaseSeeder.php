<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ServiceSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            TahunAjaranSeeder::class,
            UserSeeder::class,
            KopSuratSeeder::class,
            // Seeder lainnya...
        ]);
    }
}
