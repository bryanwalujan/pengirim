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
        Schema::create('dosen_penguji_jadwal_ujian_hasil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_ujian_hasil_id')
                ->constrained('jadwal_ujian_hasils')
                ->onDelete('cascade');
            $table->foreignId('dosen_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->string('posisi'); // Ketua Penguji, Penguji 1, Penguji 2, Penguji 3, dll
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Unique constraint: satu dosen hanya bisa punya satu posisi per jadwal
            // Using custom short name to avoid MySQL identifier length limit
            $table->unique(['jadwal_ujian_hasil_id', 'dosen_id', 'posisi'], 'penguji_ujian_hasil_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosen_penguji_jadwal_ujian_hasil');
    }
};
