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
            $table->unsignedBigInteger('komisi_proposal_id')->nullable();
            // Buat foreign key constraint
            $table->foreign('komisi_proposal_id')
                ->references('id')
                ->on('komisi_proposals')
                ->onDelete('restrict'); // Prevent deletion if used in seminar
            $table->string('angkatan');
            $table->string('judul_skripsi');
            $table->decimal('ipk', 3, 2);
            $table->string('file_transkrip_nilai');
            $table->string('file_proposal_penelitian');
            $table->string('file_surat_permohonan');
            $table->string('file_slip_ukt');
            $table->foreignId('dosen_pembimbing_id')->nullable()->constrained('users')->onDelete('set null');
            // Index untuk performa query
            $table->index('komisi_proposal_id');
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
