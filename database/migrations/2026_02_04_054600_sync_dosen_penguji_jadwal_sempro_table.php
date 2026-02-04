<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Disable foreign key constraints to allow dropping the table
        Schema::disableForeignKeyConstraints();

        // Drop existing table if exists
        Schema::dropIfExists('dosen_penguji_jadwal_sempro');

        // Recreate the table with the structure used in local, including the 'status' column
        Schema::create('dosen_penguji_jadwal_sempro', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jadwal_seminar_proposal_id')
                ->constrained('jadwal_seminar_proposals')
                ->onDelete('cascade');

            $table->foreignId('dosen_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Posisi: Pembimbing, Penguji 1, Penguji 2, Penguji 3
            $table->string('posisi');

            // ✅ MODIFIED: Additional columns for replacement logic
            $table->string('status')->default('active'); // active, replaced
            $table->foreignId('replaced_by_id')->nullable()->constrained('users')->onDelete('set null');

            $table->text('keterangan')->nullable();

            $table->timestamps();
            
            // Added index for performance
            $table->index(['jadwal_seminar_proposal_id', 'status'], 'idx_jadwal_status');
        });

        // Re-enable foreign key constraints
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::dropIfExists('dosen_penguji_jadwal_sempro');
    }
};
