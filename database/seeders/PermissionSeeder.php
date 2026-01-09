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

            // Peminjaman
            ['name' => 'manage peminjaman proyektor', 'group' => 'Peminjaman'],
            ['name' => 'manage peminjaman laboratorium', 'group' => 'Peminjaman'],

            // Penjadwalan & Pendaftaran (Staff)
            ['name' => 'manage pendaftaran sempro', 'group' => 'Tugas Akhir'],
            ['name' => 'manage jadwal sempro', 'group' => 'Tugas Akhir'],

            // ✅ NEW: Berita Acara & Lembar Catatan Sempro
            ['name' => 'manage berita acara sempro', 'group' => 'Tugas Akhir'],
            ['name' => 'sign berita acara sempro', 'group' => 'Tugas Akhir'],
            ['name' => 'submit lembar catatan sempro', 'group' => 'Tugas Akhir'],
            ['name' => 'view berita acara sempro', 'group' => 'Tugas Akhir'],

            // Komisi Proposal - Available for ALL dosen
            ['name' => 'manage komisi proposal', 'group' => 'Tugas Akhir'],

            // Komisi Hasil - Available for ALL dosen
            ['name' => 'manage komisi hasil', 'group' => 'Tugas Akhir'],

            // ✅ NEW: Pendaftaran Hasil & Jadwal Hasil (untuk masa depan)
            ['name' => 'manage pendaftaran hasil', 'group' => 'Tugas Akhir'],
            ['name' => 'manage jadwal hasil', 'group' => 'Tugas Akhir'],
            ['name' => 'manage berita acara hasil', 'group' => 'Tugas Akhir'],

            // User Management
            ['name' => 'manage students', 'group' => 'Pengguna'],
            ['name' => 'manage lecturers', 'group' => 'Pengguna'],
            ['name' => 'manage staff', 'group' => 'Pengguna'],

            // Role Management
            ['name' => 'manage roles', 'group' => 'Role & Permission'],

            // Tahun Ajaran & UKT
            ['name' => 'manage tahun ajaran', 'group' => 'Keuangan'],
            ['name' => 'manage ukt', 'group' => 'Keuangan'],

            // Kop Surat
            ['name' => 'manage kopsurat', 'group' => 'Template'],

            // Academic Calendar
            ['name' => 'manage academic calendar', 'group' => 'Akademik'],
        ];

        foreach ($permissions as $permission) {
            // PERUBAHAN UTAMA DI SINI:
            // Menggunakan updateOrCreate agar tidak error jika data sudah ada
            Permission::updateOrCreate(
                ['name' => $permission['name']], // Kunci pencarian (cek berdasarkan nama)
                ['group' => $permission['group']] // Data yang diupdate/insert
            );
        }

        // --- ROLE STAFF ---
        // Gunakan firstOrCreate agar tidak error jika role sudah ada
        $staffRole = Role::firstOrCreate(['name' => 'staff']);

        // Sync semua permission ke staff (timpa yang lama dengan semua yang baru)
        $staffRole->syncPermissions(Permission::all());

        // --- ROLE DOSEN ---
        // Gunakan firstOrCreate
        $dosenRole = Role::firstOrCreate(['name' => 'dosen']);

        $dosenPermissions = [
            'view dashboard',

            // Surat Permissions - ONLY for approval authority (Koordinator & Pimpinan)
            'approve surat aktif kuliah',
            'approve surat ijin survey',
            'approve surat cuti akademik',
            'approve surat pindah',

            // Surat-surat yang bisa dikelola dosen (bukan approval)
            'manage surat aktif kuliah',
            'manage surat ijin survey',
            'manage surat cuti akademik',
            'manage surat pindah',

            // Tugas Akhir - Terbatas sesuai peran
            'manage komisi proposal',
            'manage komisi hasil',
            'manage pendaftaran sempro',
            'manage jadwal sempro',

            // ✅ Berita Acara - Dosen bisa view, sign, dan submit catatan
            'view berita acara sempro',
            'sign berita acara sempro',
            'submit lembar catatan sempro',
        ];

        // Gunakan givePermissionTo (aman dijalankan berulang, tidak akan duplikat)
        foreach ($dosenPermissions as $pName) {
            // Cek dulu apakah permission ada di DB untuk menghindari error jika typo
            if (Permission::where('name', $pName)->exists()) {
                $dosenRole->givePermissionTo($pName);
            }
        }

        // Note: Approval permissions akan di-assign secara dinamis 
        // berdasarkan jabatan saat create/update dosen
    }
}