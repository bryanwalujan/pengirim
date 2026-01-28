<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lembar_koreksi_skripsis', function (Blueprint $table) {
            $table->id();

            // ========================================
            // FOREIGN KEYS
            // ========================================

            $table->foreignId('berita_acara_ujian_hasil_id')
                ->constrained('berita_acara_ujian_hasils')
                ->onDelete('cascade')
                ->name('fk_koreksi_ba');

            // Hanya PS1/PS2 yang bisa mengisi lembar koreksi
            $table->foreignId('dosen_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->name('fk_koreksi_dosen');

            // ========================================
            // KOREKSI DATA (JSON FORMAT)
            // ========================================

            // Format JSON sesuai UJIAN-HASIL.md:
            // [{"no": 1, "halaman": "12", "catatan": "Perbaiki Typo"}]
            $table->json('koreksi_data')->nullable();

            $table->timestamps();

            // ========================================
            // CONSTRAINTS
            // ========================================

            // Unique constraint - satu dosen PS hanya bisa submit 1x per BA
            $table->unique(
                ['berita_acara_ujian_hasil_id', 'dosen_id'],
                'uq_koreksi_ba_dosen'
            );

            // ========================================
            // INDEXES
            // ========================================

            $table->index('berita_acara_ujian_hasil_id', 'idx_koreksi_ba');
            $table->index('dosen_id', 'idx_koreksi_dosen');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lembar_koreksi_skripsis');
    }
};
