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
        Schema::create('pendaftaran_seminar_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('angkatan');
            $table->string('judul_skripsi');
            $table->decimal('ipk', 3, 2);
            $table->string('file_transkrip_nilai');
            $table->string('file_proposal_penelitian');
            $table->string('file_surat_permohonan');
            $table->foreignId('dosen_pembimbing_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_seminar_proposals');
    }
};
