<?php
// filepath: /c:/laragon/www/eservice-app/database/migrations/2025_12_16_021333_create_berita_acara_seminar_proposals_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('berita_acara_seminar_proposals', function (Blueprint $table) {
            $table->id();

            // Relasi ke jadwal seminar proposal (One-to-One)
            // Menggunakan nama constraint manual yang lebih pendek
            $table->foreignId('jadwal_seminar_proposal_id')
                ->constrained('jadwal_seminar_proposals', 'id', 'ba_sempro_jadwal_fk')
                ->onDelete('cascade');

            // ========== REALISASI DOSEN (Yang Hadir Saat Hari-H) ==========
            // Dosen PA sebagai Ketua Pembahas
            $table->foreignId('dosen_pa_id')
                ->nullable()
                ->constrained('users', 'id', 'ba_sempro_dosen_pa_fk')
                ->onDelete('set null');

            // Dosen Pembahas 1
            $table->foreignId('dosen_pembahas_1_id')
                ->nullable()
                ->constrained('users', 'id', 'ba_sempro_pembahas1_fk')
                ->onDelete('set null');

            // Dosen Pembahas 2
            $table->foreignId('dosen_pembahas_2_id')
                ->nullable()
                ->constrained('users', 'id', 'ba_sempro_pembahas2_fk')
                ->onDelete('set null');

            // Dosen Pembahas 3
            $table->foreignId('dosen_pembahas_3_id')
                ->nullable()
                ->constrained('users', 'id', 'ba_sempro_pembahas3_fk')
                ->onDelete('set null');

            // ========== CHECKLIST BERITA ACARA ==========
            // Checklist 1: Catatan Kejadian Seminar
            $table->enum('catatan_kejadian', ['lancar', 'perbaikan'])
                ->default('lancar');

            // Checklist 2: Kesimpulan Kelayakan Seminar
            $table->enum('keputusan_seminar', [
                'layak',
                'layak_dengan_perbaikan',
                'tidak_layak'
            ])->default('layak');

            // ========== METADATA ==========
            // Tanggal pelaksanaan seminar (untuk TTD)
            $table->date('tanggal_seminar');

            // Path file PDF berita acara
            $table->string('file_path')->nullable();

            // Token verifikasi untuk QR Code (32 karakter)
            $table->string('verification_token', 32)->unique();

            $table->timestamps();

            // ========== INDEXES ==========
            $table->index('jadwal_seminar_proposal_id', 'ba_sempro_jadwal_idx');
            $table->index('tanggal_seminar', 'ba_sempro_tanggal_idx');
            $table->index('keputusan_seminar', 'ba_sempro_keputusan_idx');
            $table->index('verification_token', 'ba_sempro_token_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berita_acara_seminar_proposals');
    }
};