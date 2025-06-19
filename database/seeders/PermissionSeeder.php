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

            // Surat Cuti Akademik
            ['name' => 'manage surat cuti akademik', 'group' => 'surat-cuti-akademik'],
            ['name' => 'create surat cuti akademik', 'group' => 'surat-cuti-akademik'],
            ['name' => 'edit surat cuti akademik', 'group' => 'surat-cuti-akademik'],
            ['name' => 'delete surat cuti akademik', 'group' => 'surat-cuti-akademik'],

            // Surat Pindah
            ['name' => 'manage surat pindah', 'group' => 'surat-pindah'],
            ['name' => 'create surat pindah', 'group' => 'surat-pindah'],
            ['name' => 'edit surat pindah', 'group' => 'surat-pindah'],
            ['name' => 'delete surat pindah', 'group' => 'surat-pindah'],

            // Academic Calendar
            ['name' => 'manage academic calendar', 'group' => 'academic'],

            // Letterhead Management
            ['name' => 'manage kopsurat', 'group' => 'kop-surat'],

            // Peminjaman Proyektor
            ['name' => 'manage peminjaman proyektor', 'group' => 'peminjaman-proyektor'],

            // Peminjaman Laboratorium
            ['name' => 'manage peminjaman laboratorium', 'group' => 'peminjaman-laboratorium'],

            // Pendaftaran Sempro
            ['name' => 'manage pendaftaran sempro', 'group' => 'pendaftaran-sempro'],

            // Pendaftaran Ujian Hasil
            ['name' => 'manage pendaftaran hasil', 'group' => 'pendaftaran-hasil'],

            // Komisi Proposal
            ['name' => 'manage komisi proposal', 'group' => 'komisi-proposal'],

            // Komisi Hasil
            ['name' => 'manage komisi hasil', 'group' => 'komisi-hasil'],

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
            'create surat aktif kuliah',
            'edit surat aktif kuliah',
            'delete surat aktif kuliah',
            'manage surat ijin survey',
            'create surat ijin survey',
            'edit surat ijin survey',
            'delete surat ijin survey',
            'manage surat cuti akademik',
            'create surat cuti akademik',
            'edit surat cuti akademik',
            'delete surat cuti akademik',
            'manage surat pindah',
            'create surat pindah',
            'edit surat pindah',
            'delete surat pindah',
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
