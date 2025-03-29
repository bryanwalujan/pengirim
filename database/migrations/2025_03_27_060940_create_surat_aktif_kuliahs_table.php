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
            $table->foreignId('user_id')->constrained(); // Relasi ke tabel users
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->date('tgl_pengajuan');
            $table->date('tgl_approve')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->string('dokumen_path'); // Untuk upload KHS atau dokumen pendukung
            $table->string('surat_path')->nullable(); // File surat yang sudah digenerate
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
