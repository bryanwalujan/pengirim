<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

// Models (sesuaikan dengan punyamu)
use App\Models\User;
use App\Models\StatusSurat;
use App\Models\TrackingSurat;
use App\Models\SuratAktifKuliah;
use App\Models\SuratCutiAkademik;
use App\Models\SuratIjinSurvey;
use App\Models\SuratPindah;

class DummySuratSeeder extends Seeder
{
    public function run(): void
    {
        $countPerType = (int) (env('DUMMY_SURAT_COUNT', 2000));
        $tahun = now()->year;
        $prefixNomor = 'UN41.2/TI';

        // Hanya user dengan role 'mahasiswa'
        $query = User::query();
        if (method_exists($query->getModel(), 'role')) {
            $query->role('mahasiswa');
        } elseif (Schema::hasColumn('users', 'role')) {
            $query->where('role', 'mahasiswa');
        }
        $mahasiswaIds = $query->pluck('id')->all();

        if (empty($mahasiswaIds)) {
            $this->command->warn('Tidak ada user ber-role "mahasiswa". Seeder dibatalkan.');
            return;
        }

        DB::transaction(function () use ($countPerType, $tahun, $prefixNomor, $mahasiswaIds) {

            // 1) Aktif Kuliah
            for ($i = 1; $i <= $countPerType; $i++) {
                $table = (new SuratAktifKuliah)->getTable();
                $surat = SuratAktifKuliah::create($this->withOptionalFields([
                    'mahasiswa_id' => $this->pick($mahasiswaIds),
                    'nomor_surat' => sprintf('%04d/%s/%d', $i, $prefixNomor, $tahun),
                    'tujuan_pengajuan' => fake()->sentence(6),
                    'keterangan_tambahan' => fake()->optional()->sentence(8),
                    // tanggal_surat/tahun_ajaran/semester akan diisi jika kolomnya ada
                ], $table));

                $this->seedStatusAndTracking($surat, SuratAktifKuliah::class);
            }

            // 2) Cuti Akademik
            for ($i = 1; $i <= $countPerType; $i++) {
                $table = (new SuratCutiAkademik)->getTable();
                $surat = SuratCutiAkademik::create($this->withOptionalFields([
                    'mahasiswa_id' => $this->pick($mahasiswaIds),
                    'nomor_surat' => sprintf('%04d/%s/%d', $i + 10000, $prefixNomor, $tahun),
                    'alasan_pengajuan' => fake()->sentence(8),
                    'keterangan_tambahan' => fake()->optional()->sentence(8),
                    // tanggal_surat/tahun_ajaran/semester akan diisi jika kolomnya ada
                ], $table));

                $this->seedStatusAndTracking($surat, SuratCutiAkademik::class);
            }

            // 3) Ijin Survey
            for ($i = 1; $i <= $countPerType; $i++) {
                $table = (new SuratIjinSurvey)->getTable();
                $surat = SuratIjinSurvey::create($this->withOptionalFields([
                    'mahasiswa_id' => $this->pick($mahasiswaIds),
                    'nomor_surat' => sprintf('%04d/%s/%d', $i + 20000, $prefixNomor, $tahun),
                    'judul' => 'Analisis ' . fake()->words(3, true),
                    'tempat_survey' => 'Lokasi ' . fake()->city(),
                    // tanggal_surat/semester akan diisi jika kolomnya ada (semester seharusnya wajib di skema ini)
                ], $table));

                $this->seedStatusAndTracking($surat, SuratIjinSurvey::class);
            }

            // 4) Pindah
            for ($i = 1; $i <= $countPerType; $i++) {
                $table = (new SuratPindah)->getTable();
                $surat = SuratPindah::create($this->withOptionalFields([
                    'mahasiswa_id' => $this->pick($mahasiswaIds),
                    'nomor_surat' => sprintf('%04d/%s/%d', $i + 30000, $prefixNomor, $tahun),
                    'universitas_tujuan' => 'Universitas ' . strtoupper(fake()->lastName()),
                    'alasan_pengajuan' => fake()->sentence(8),
                    // semester/tanggal_surat akan diisi jika kolomnya ada
                ], $table));

                $this->seedStatusAndTracking($surat, SuratPindah::class);
            }
        });

        $this->command->info('Dummy surat selesai (tracking_code diset, tanpa file).');
    }

    private function seedStatusAndTracking($surat, string $suratType): void
    {
        $start = Carbon::now()->subDays(rand(5, 60))->startOfDay();
        $steps = [
            ['aksi' => 'diajukan', 'ket' => 'Pengajuan surat baru'],
            ['aksi' => 'diproses', 'ket' => 'Diproses oleh staff'],
            ['aksi' => 'disetujui_kaprodi', 'ket' => 'Disetujui Kaprodi'],
            ['aksi' => 'disetujui', 'ket' => 'Disetujui pimpinan'],
            ['aksi' => 'siap_diambil', 'ket' => 'Siap diambil'],
            ['aksi' => 'sudah_diambil', 'ket' => 'Telah diambil'],
        ];

        foreach ($steps as $idx => $s) {
            $at = (clone $start)->addHours($idx * 8);
            TrackingSurat::create([
                'surat_type' => $suratType,
                'surat_id' => $surat->id,
                'aksi' => $s['aksi'],
                'keterangan' => $s['ket'],
                'mahasiswa_id' => $surat->mahasiswa_id,
                'created_at' => $at,
                'updated_at' => $at,
                'confirmed_at' => $s['aksi'] === 'sudah_diambil' ? $at : null,
            ]);
        }

        StatusSurat::create([
            'surat_type' => $suratType,
            'surat_id' => $surat->id,
            'status' => 'sudah_diambil',
            'updated_by' => $surat->mahasiswa_id,
            'created_at' => (clone $start)->addHours(40),
            'updated_at' => (clone $start)->addHours(40),
        ]);
    }

    // Tambahkan tracking_code dan isi kolom opsional hanya jika ada di tabel
    private function withOptionalFields(array $data, string $table): array
    {
        // tracking_code unik (12 char)
        if (Schema::hasColumn($table, 'tracking_code')) {
            $data['tracking_code'] = $this->generateTrackingCode($table);
        }

        // kolom file_surat_path dibuat null jika ada
        if (Schema::hasColumn($table, 'file_surat_path')) {
            $data['file_surat_path'] = null;
        }

        // isi tanggal_surat bila kolom ada dan belum diisi
        if (Schema::hasColumn($table, 'tanggal_surat') && !array_key_exists('tanggal_surat', $data)) {
            $data['tanggal_surat'] = now()->subDays(rand(1, 90))->toDateString();
        }

        // isi semester bila kolom ada dan belum diisi
        if (Schema::hasColumn($table, 'semester') && !array_key_exists('semester', $data)) {
            $data['semester'] = $this->randomSemester();
        }

        // isi tahun_ajaran bila kolom ada dan belum diisi
        if (Schema::hasColumn($table, 'tahun_ajaran') && !array_key_exists('tahun_ajaran', $data)) {
            $data['tahun_ajaran'] = $this->randomTahunAjaran();
        }

        return $data;
    }

    private function generateTrackingCode(string $table): string
    {
        // Pastikan unik terhadap constraint unique di kolom tracking_code
        do {
            $code = strtoupper(Str::random(12));
        } while (DB::table($table)->where('tracking_code', $code)->exists());

        return $code;
    }

    private function randomSemester(): string
    {
        return rand(0, 1) ? 'ganjil' : 'genap';
    }

    private function randomTahunAjaran(): string
    {
        $start = rand(now()->year - 2, now()->year);
        return "{$start}/" . ($start + 1);
    }

    private function pick(array $arr)
    {
        return $arr[array_rand($arr)];
    }
}