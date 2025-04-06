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
        Schema::create('surat_aktif_kuliahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('users'); // Relasi ke tabel users (nim mahasiswa)
            $table->text('tujuan_pengajuan'); // Contoh: "Beasiswa Beyond Borders"
            $table->string('nomor_surat')->nullable(); // Auto-generate: 3108/UN41.2/TI/2024
            $table->date('tanggal_surat')->nullable(); // Auto-set saat disetujui
            $table->string('tahun_ajaran'); // Auto-set: 2024/2025
            $table->foreignId('penandatangan_id')->nullable()->constrained('users'); // Relasi ke dosen/staff penandatangan
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_aktif_kuliahs');
    }
};
