<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $services = [
            [
                'name' => 'Surat Aktif Kuliah',
                'icon' => 'bx bx-file', // Changed from bi bi-file-earmark-text
                'description' => 'Pengajuan surat keterangan aktif kuliah yang diperlukan untuk berbagai keperluan administrasi seperti beasiswa, organisasi, atau kebutuhan lainnya.',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Surat Cuti Akademik',
                'icon' => 'bx bx-calendar-x', // Changed from bi bi-calendar-x
                'description' => 'Proses pengajuan cuti akademik untuk mahasiswa yang membutuhkan waktu istirahat dari perkuliahan.',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Surat Pindah',
                'icon' => 'bx bx-transfer-alt', // Changed from bi bi-arrow-right-circle
                'description' => 'Pengajuan surat pindah ke perguruan tinggi lain atau antar program studi di lingkungan Universitas Negeri Manado.',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Surat Ijin Survey',
                'icon' => 'bx bx-search-alt', // Changed from bi bi-search
                'description' => 'Pengajuan surat ijin untuk melakukan survey atau penelitian sebagai bagian dari tugas akademik.',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Peminjaman Laboratorium',
                'icon' => 'bx bx-desktop', // Changed from bi bi-pc-display
                'description' => 'Pengajuan peminjaman laboratorium komputer untuk keperluan penelitian atau kegiatan akademik.',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Peminjaman Proyektor',
                'icon' => 'bx bx-slideshow', // Changed from bi bi-projector
                'description' => 'Pengajuan peminjaman proyektor untuk keperluan presentasi atau kegiatan akademik lainnya.',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Pendaftaran Seminar Proposal',
                'icon' => 'bx bx-book-open', // Changed from bi bi-megaphone
                'description' => 'Pendaftaran untuk seminar proposal skripsi sebagai syarat kelulusan mata kuliah skripsi.',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Pendaftaran Ujian Hasil',
                'icon' => 'bx bx-book-bookmark', // Changed from bi bi-journal-text
                'description' => 'Pendaftaran ujian hasil setelah menyelesaikan semua persyaratan penelitian dan penulisan skripsi.',
                'order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Komisi Proposal',
                'icon' => 'bx bx-detail',
                'description' => 'Pengajuan komisi proposal untuk mendapatkan persetujuan dari dosen pembimbing sebelum melanjutkan ke tahap seminar proposal.',
                'order' => 9,
                'is_active' => true,
            ],
            [
                'name' => 'Komisi Hasil',
                'icon' => 'bx bx-book-content',
                'description' => 'Pengajuan komisi hasil/skripsi untuk mendapatkan persetujuan dari dosen pembimbing I dan pembimbing II sebelum melanjutkan ke tahap ujian hasil/skripsi.',
                'order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Jadwal Seminar Proposal',
                'icon' => 'bx bx-calendar-event',
                'description' => 'Menu ini merupakan tahapan lanjutan setelah proses pendaftaran dan penerbitan Surat Usulan Proposal selesai. Di sini, mahasiswa wajib mengunggah Surat Keputusan (SK) Proposal yang telah diterbitkan oleh Fakultas/Jurusan. ',
                'order' => 11,
                'is_active' => true,
            ],

        ];

        foreach ($services as $service) {
            Service::create([
                'name' => $service['name'],
                'slug' => Str::slug($service['name']),
                'icon' => $service['icon'],
                'description' => $service['description'],
                'order' => $service['order'],
                'is_active' => $service['is_active'],
            ]);
        }

        $this->command->info('Berhasil menambahkan ' . count($services) . ' layanan!');
    }
}