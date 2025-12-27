<?php
// filepath: database/migrations/2025_12_15_120000_create_dosen_penguji_jadwal_sempro_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dosen_penguji_jadwal_sempro', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jadwal_seminar_proposal_id')
                ->constrained('jadwal_seminar_proposals')
                ->onDelete('cascade');

            $table->foreignId('dosen_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Posisi: Pembimbing, Penguji 1, Penguji 2, Penguji 3
            $table->string('posisi');

            // Dosen Pengganti (jika berhalangan)
            $table->foreignId('dosen_pengganti_id')->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->text('keterangan')->nullable();

            $table->timestamps();

            // Composite unique
            $table->unique([
                'jadwal_seminar_proposal_id',
                'posisi'
            ], 'unique_posisi_dosen');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosen_penguji_jadwal_sempro');
    }
};