<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_ujian_hasils', function (Blueprint $table) {
            $table->id();

            // ========================================
            // FOREIGN KEYS
            // ========================================

            $table->foreignId('berita_acara_ujian_hasil_id')
                ->constrained('berita_acara_ujian_hasils')
                ->onDelete('cascade')
                ->name('fk_penilaian_uh_ba');

            $table->foreignId('dosen_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->name('fk_penilaian_uh_dosen');

            // ========================================
            // KRITERIA PENILAIAN (sesuai UJIAN-HASIL.md Section 4)
            // ========================================

            // Kebaruan (Novelty): Relevansi dan orisinalitas temuan
            $table->integer('nilai_kebaruan')->nullable();

            // Metode: Ketepatan algoritma/metodologi yang digunakan
            $table->integer('nilai_metode')->nullable();

            // Ketersediaan Data/Resource: Validitas software, hardware, dan dataset
            $table->integer('nilai_data_software')->nullable();

            // Referensi: Kualitas pustaka (minimal 5 tahun terakhir)
            $table->integer('nilai_referensi')->nullable();

            // Penguasaan Materi: Performa mahasiswa saat tanya jawab
            $table->integer('nilai_penguasaan')->nullable();

            // ========================================
            // CALCULATED FIELDS
            // ========================================

            // Total nilai (rata-rata dari semua kriteria)
            $table->float('total_nilai')->nullable();

            // Catatan tambahan dari penguji
            $table->text('catatan')->nullable();

            $table->timestamps();

            // ========================================
            // CONSTRAINTS
            // ========================================

            // Unique constraint - satu dosen hanya bisa submit 1x per BA
            $table->unique(
                ['berita_acara_ujian_hasil_id', 'dosen_id'],
                'uq_penilaian_uh_ba_dosen'
            );

            // ========================================
            // INDEXES
            // ========================================

            $table->index('berita_acara_ujian_hasil_id', 'idx_penilaian_uh_ba');
            $table->index('dosen_id', 'idx_penilaian_uh_dosen');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_ujian_hasils');
    }
};
