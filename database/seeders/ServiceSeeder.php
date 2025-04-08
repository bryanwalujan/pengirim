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
                'icon' => 'bi bi-file-earmark-text',
                'description' => 'Pengajuan surat keterangan aktif kuliah yang diperlukan untuk berbagai keperluan administrasi seperti beasiswa, organisasi, atau kebutuhan lainnya.',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Surat Cuti Akademik',
                'icon' => 'bi bi-calendar-x',
                'description' => 'Proses pengajuan cuti akademik untuk mahasiswa yang membutuhkan waktu istirahat dari perkuliahan.',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Surat Pindah',
                'icon' => 'bi bi-arrow-right-circle',
                'description' => 'Pengajuan surat pindah ke perguruan tinggi lain atau antar program studi di lingkungan Universitas Negeri Manado.',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Surat Izin Survey',
                'icon' => 'bi bi-search',
                'description' => 'Pengajuan surat izin untuk melakukan survey atau penelitian sebagai bagian dari tugas akademik.',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Permohonan Transkrip',
                'icon' => 'bi bi-file-text',
                'description' => 'Pengajuan transkrip nilai resmi yang telah dilegalisir oleh pihak akademik.',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Kelayakan Skripsi',
                'icon' => 'bi bi-check-circle',
                'description' => 'Proses verifikasi kelayakan proposal skripsi sebelum dapat melanjutkan ke tahap seminar proposal.',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Pendaftaran Seminar Proposal',
                'icon' => 'bi bi-megaphone',
                'description' => 'Pendaftaran untuk seminar proposal skripsi sebagai syarat kelulusan mata kuliah skripsi.',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Pendaftaran Ujian Skripsi',
                'icon' => 'bi bi-journal-text',
                'description' => 'Pendaftaran ujian skripsi setelah menyelesaikan semua persyaratan penelitian dan penulisan skripsi.',
                'order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Pendaftaran Ujian Komprehensif',
                'icon' => 'bi bi-journal-bookmark',
                'description' => 'Pendaftaran ujian komprehensif untuk program studi tertentu yang memerlukannya.',
                'order' => 9,
                'is_active' => true,
            ],
            [
                'name' => 'Pengajuan Surat Keterangan Lulus',
                'icon' => 'bi bi-award',
                'description' => 'Pengajuan surat keterangan lulus untuk keperluan administrasi setelah menyelesaikan studi.',
                'order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Peminjaman Laboratorium',
                'icon' => 'bi bi-pc-display',
                'description' => 'Pengajuan peminjaman laboratorium komputer untuk keperluan penelitian atau kegiatan akademik.',
                'order' => 11,
                'is_active' => false,
            ],
            [
                'name' => 'Peminjaman Proyektor',
                'icon' => 'bi bi-projector',
                'description' => 'Pengajuan peminjaman proyektor untuk keperluan presentasi atau kegiatan akademik lainnya.',
                'order' => 12,
                'is_active' => false,
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