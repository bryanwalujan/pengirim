<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penguji_ujian_hasils', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pendaftaran_ujian_hasil_id')
                ->constrained('pendaftaran_ujian_hasils')
                ->onDelete('cascade');

            $table->foreignId('dosen_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Posisi: Penguji 1, Penguji 2, Penguji 3, Penguji Tambahan
            $table->string('posisi');

            // Keterangan tambahan (opsional)
            $table->text('keterangan')->nullable();

            // Sumber penguji: 'berita_acara' atau 'manual'
            $table->string('sumber')->default('manual');

            $table->timestamps();

            // Composite unique: satu posisi per pendaftaran
            $table->unique(
                ['pendaftaran_ujian_hasil_id', 'posisi'],
                'unique_penguji_posisi'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penguji_ujian_hasils');
    }
};
