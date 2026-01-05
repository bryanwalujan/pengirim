<?php
// filepath: /c:/laragon/www/eservice-app/database/migrations/2026_01_04_create_statistik_pembimbing_skripsi_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('statistik_pembimbing_skripsi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dosen_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajarans')
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('jumlah_ps1')->default(0);
            $table->unsignedSmallInteger('jumlah_ps2')->default(0);

            $table->timestamps();

            // Unique constraint - 1 record per dosen per tahun ajaran
            $table->unique(['dosen_id', 'tahun_ajaran_id'], 'statistik_dosen_tahun_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistik_pembimbing_skripsi');
    }
};