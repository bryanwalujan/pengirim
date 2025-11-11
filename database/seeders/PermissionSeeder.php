<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions with better grouping
        $permissions = [
            // Dashboard
            ['name' => 'view dashboard', 'group' => 'Dashboard'],

            // Service Management
            ['name' => 'manage services', 'group' => 'Layanan'],
            ['name' => 'create services', 'group' => 'Layanan'],
            ['name' => 'edit services', 'group' => 'Layanan'],
            ['name' => 'delete services', 'group' => 'Layanan'],

            // Surat Permissions - ONLY for approval authority (Koordinator & Pimpinan)
            ['name' => 'approve surat aktif kuliah', 'group' => 'Surat (Approval)'],
            ['name' => 'approve surat ijin survey', 'group' => 'Surat (Approval)'],
            ['name' => 'approve surat cuti akademik', 'group' => 'Surat (Approval)'],
            ['name' => 'approve surat pindah', 'group' => 'Surat (Approval)'],

            // Management permissions (for staff)
            ['name' => 'manage surat aktif kuliah', 'group' => 'Surat (Management)'],
            ['name' => 'manage surat ijin survey', 'group' => 'Surat (Management)'],
            ['name' => 'manage surat cuti akademik', 'group' => 'Surat (Management)'],
            ['name' => 'manage surat pindah', 'group' => 'Surat (Management)'],

            // Academic Calendar
            ['name' => 'manage academic calendar', 'group' => 'Akademik'],

            // Letterhead Management
            ['name' => 'manage kopsurat', 'group' => 'Template'],

            // Peminjaman Proyektor
            ['name' => 'manage peminjaman proyektor', 'group' => 'Peminjaman'],

            // Peminjaman Laboratorium
            ['name' => 'manage peminjaman laboratorium', 'group' => 'Peminjaman'],

            // Pendaftaran Sempro
            ['name' => 'manage pendaftaran sempro', 'group' => 'Tugas Akhir'],

            // Pendaftaran Ujian Hasil
            ['name' => 'manage pendaftaran hasil', 'group' => 'Tugas Akhir'],

            // Komisi Proposal - Available for ALL dosen
            ['name' => 'manage komisi proposal', 'group' => 'Tugas Akhir'],

            // Komisi Hasil - Available for ALL dosen
            ['name' => 'manage komisi hasil', 'group' => 'Tugas Akhir'],

            // User Management
            ['name' => 'manage students', 'group' => 'Pengguna'],
            ['name' => 'manage lecturers', 'group' => 'Pengguna'],
            ['name' => 'manage staff', 'group' => 'Pengguna'],

            // Role Management
            ['name' => 'manage roles', 'group' => 'Role & Permission'],

            // Tahun Ajaran & UKT
            ['name' => 'manage tahun ajaran', 'group' => 'Keuangan'],
            ['name' => 'manage ukt', 'group' => 'Keuangan'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create Staff Role with ALL permissions
        $staffRole = Role::create(['name' => 'staff']);
        $staffRole->givePermissionTo(Permission::all());

        // Create Dosen Role with BASE permissions (for ALL dosen)
        $dosenRole = Role::create(['name' => 'dosen']);
        $dosenRole->givePermissionTo([
            'view dashboard',
            'manage komisi proposal',
            'manage komisi hasil',
        ]);

        // Create Mahasiswa Role
        $mahasiswaRole = Role::create(['name' => 'mahasiswa']);
        $mahasiswaRole->givePermissionTo([
            'view dashboard'
        ]);

        // Note: Approval permissions akan di-assign secara dinamis 
        // berdasarkan jabatan saat create/update dosen
    }
}