<?php
// filepath: database/migrations/2025_12_16_021334_create_lembar_catatan_seminar_proposals_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lembar_catatan_seminar_proposals', function (Blueprint $table) {
            $table->id();

            // Foreign Keys dengan nama custom
            $table->foreignId('berita_acara_seminar_proposal_id')
                ->constrained('berita_acara_seminar_proposals')
                ->onDelete('cascade')
                ->name('fk_lc_sempro_ba'); // ✅ Custom name

            $table->foreignId('dosen_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->name('fk_lc_sempro_dosen'); // ✅ Custom name

            // Penilaian
            $table->integer('nilai_kebaruan')->nullable();
            $table->integer('nilai_metode')->nullable();
            $table->integer('nilai_ketersediaan_data')->nullable();

            // Catatan Per Bab
            $table->text('catatan_bab1')->nullable();
            $table->text('catatan_bab2')->nullable();
            $table->text('catatan_bab3')->nullable();

            // Catatan Lainnya
            $table->text('catatan_jadwal')->nullable();
            $table->text('catatan_referensi')->nullable();
            $table->text('catatan_umum')->nullable();

            $table->timestamps();

            // Unique constraint - satu dosen hanya bisa submit 1x per berita acara
            $table->unique(
                ['berita_acara_seminar_proposal_id', 'dosen_id'],
                'uq_lc_sempro_ba_dosen' // ✅ Custom name
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lembar_catatan_seminar_proposals');
    }
};