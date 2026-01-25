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
        Schema::create('jadwal_ujian_hasils', function (Blueprint $table) {
            $table->id();

            // Foreign Key ke pendaftaran_ujian_hasils (One-to-One)
            $table->foreignId('pendaftaran_ujian_hasil_id')
                ->constrained('pendaftaran_ujian_hasils')
                ->onDelete('cascade');

            // File SK Ujian Hasil yang diupload mahasiswa
            $table->string('file_sk_ujian_hasil')->nullable();

            // Data Jadwal
            $table->date('tanggal_ujian')->nullable();
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->string('ruangan')->nullable();

            // Status Penjadwalan
            $table->enum('status', [
                'menunggu_sk',
                'menunggu_jadwal',
                'dijadwalkan',
                'selesai'
            ])->default('menunggu_sk');

            $table->timestamps();

            // Index untuk performa query
            $table->index('status');
            $table->index('tanggal_ujian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_ujian_hasils');
    }
};
