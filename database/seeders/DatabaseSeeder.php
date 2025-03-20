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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Patrick Rompas',
            'email' => '20210047@unima.ac.id',
        ]);
        User::factory()->create([
            'name' => 'Willem Louis',
            'email' => '2021004490@unima.ac.id',
        ]);
        User::factory()->create([
            'name' => 'Testing',
            'email' => 'patrick@gmail.com',
        ]);
    }
}
