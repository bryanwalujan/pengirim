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
        Schema::create('jadwal_seminar_proposals', function (Blueprint $table) {
            $table->id();

            // Foreign Key ke pendaftaran_seminar_proposals (One-to-One)
            $table->foreignId('pendaftaran_seminar_proposal_id')
                ->constrained('pendaftaran_seminar_proposals')
                ->onDelete('cascade');

            // File SK Proposal yang diupload mahasiswa
            $table->string('file_sk_proposal')->nullable();

            // Data Jadwal
            $table->date('tanggal')->nullable();
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
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
            $table->index('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_seminar_proposals');
    }
};
