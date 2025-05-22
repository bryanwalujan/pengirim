<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Dashboard
            ['name' => 'view dashboard', 'group' => 'dashboard'],

            // Service Management
            ['name' => 'manage services', 'group' => 'services'],
            ['name' => 'create services', 'group' => 'services'],
            ['name' => 'edit services', 'group' => 'services'],
            ['name' => 'delete services', 'group' => 'services'],

            // Surat Aktif Kuliah
            ['name' => 'manage surat aktif kuliah', 'group' => 'surat-aktif-kuliah'],
            ['name' => 'create surat aktif kuliah', 'group' => 'surat-aktif-kuliah'],
            ['name' => 'edit surat aktif kuliah', 'group' => 'surat-aktif-kuliah'],
            ['name' => 'delete surat aktif kuliah', 'group' => 'surat-aktif-kuliah'],

            // Surat Ijin Survey
            ['name' => 'manage surat ijin survey', 'group' => 'surat-ijin-survey'],
            ['name' => 'create surat ijin survey', 'group' => 'surat-ijin-survey'],
            ['name' => 'edit surat ijin survey', 'group' => 'surat-ijin-survey'],
            ['name' => 'delete surat ijin survey', 'group' => 'surat-ijin-survey'],

            // Academic Calendar
            ['name' => 'manage academic calendar', 'group' => 'academic'],

            // Letterhead Management
            ['name' => 'manage kopsurat', 'group' => 'kop-surat'],

            // User Management
            ['name' => 'manage students', 'group' => 'users'],
            ['name' => 'manage lecturers', 'group' => 'users'],
            ['name' => 'manage staff', 'group' => 'users'],

            // Role Management
            ['name' => 'manage roles', 'group' => 'roles'],

            ['name' => 'manage tahun ajaran', 'group' => 'tahun-ajaran'],
            ['name' => 'manage ukt', 'group' => 'ukt'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create roles and assign permissions
        $staffRole = Role::create(['name' => 'staff']);
        $staffRole->givePermissionTo(Permission::all());

        $lecturerRole = Role::create(['name' => 'dosen']);
        $lecturerRole->givePermissionTo([
            'view dashboard',
            'manage surat aktif kuliah',
        ]);

        // $staffRole = Role::create(['name' => 'staff']);
        // $staffRole->givePermissionTo([
        //     'view dashboard',
        //     'manage services',
        //     'create services',
        //     'edit services',
        //     'manage academic calendar',
        //     'manage letterheads',
        //     'manage students',
        //     'manage lecturers',
        //     'manage staff'
        // ]);

        // $lecturerRole = Role::create(['name' => 'dosen']);
        // $lecturerRole->givePermissionTo([
        //     'view dashboard',
        //     'manage academic calendar'
        // ]);

        // $studentRole = Role::create(['name' => 'mahasiswa']);
        // $studentRole->givePermissionTo([
        //     'view dashboard'
        // ]);
    }
}
