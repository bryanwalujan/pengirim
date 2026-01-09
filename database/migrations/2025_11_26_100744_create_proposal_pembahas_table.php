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
        Schema::create('proposal_pembahas', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('pendaftaran_seminar_proposal_id')
                ->constrained('pendaftaran_seminar_proposals')
                ->onDelete('cascade');

            $table->foreignId('dosen_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Posisi pembahas (1, 2, 3)
            $table->tinyInteger('posisi')->comment('1=Pembahas 1, 2=Pembahas 2, 3=Pembahas 3');

            $table->timestamps();

            // Unique constraint: 1 pendaftaran + 1 posisi = 1 dosen
            $table->unique(['pendaftaran_seminar_proposal_id', 'posisi'], 'unique_pendaftaran_posisi');

            // Unique constraint: 1 dosen tidak boleh di posisi yang sama untuk 1 pendaftaran
            $table->unique(['pendaftaran_seminar_proposal_id', 'dosen_id'], 'unique_pendaftaran_dosen');

            // Index
            $table->index('dosen_id');
            $table->index('posisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_pembahas');
    }
};
