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
        Schema::create('pendaftaran_ujian_hasils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('angkatan');
            $table->string('nim');
            $table->string('nama');
            $table->decimal('ipk', 3, 2);
            $table->string('judul_skripsi', 255);

            // File paths
            $table->string('transkrip_nilai');
            $table->string('file_skripsi');
            $table->string('komisi_hasil');
            $table->string('surat_permohonan_hasil');

            // Foreign keys for lecturers
            $table->foreignId('dosen_pa_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('dosen_pembimbing1_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('dosen_pembimbing2_id')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_ujian_hasils');
    }
};
