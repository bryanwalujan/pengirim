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
        // Disable foreign key constraints to allow dropping the table with dependencies
        Schema::disableForeignKeyConstraints();

        // Drop existing table if exists (user confirmed they don't care about the data)
        Schema::dropIfExists('pendaftaran_seminar_proposals');

        // Recreate the table with the structure used in local
        Schema::create('pendaftaran_seminar_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('komisi_proposal_id')->nullable()->constrained()->onDelete('restrict');

            // Data mahasiswa
            $table->string('angkatan');
            $table->text('judul_skripsi');
            $table->decimal('ipk', 3, 2);

            // Files
            $table->string('file_transkrip_nilai');
            $table->string('file_proposal_penelitian');
            $table->string('file_surat_permohonan');
            $table->string('file_slip_ukt');

            // Pembimbing
            $table->foreignId('dosen_pembimbing_id')->nullable()->constrained('users')->onDelete('set null');

            // Metadata penentuan pembahas
            $table->timestamp('tanggal_penentuan_pembahas')->nullable();
            $table->foreignId('ditentukan_oleh_id')->nullable()->constrained('users')->onDelete('set null');

            // Status workflow
            $table->enum('status', [
                'pending',
                'pembahas_ditentukan',
                'surat_diproses',
                'menunggu_ttd_kaprodi',
                'menunggu_ttd_kajur',
                'selesai',
                'ditolak'
            ])->default('pending');

            // Catatan/Keterangan (opsional)
            $table->text('catatan')->nullable();
            $table->text('alasan_penolakan')->nullable();

            $table->timestamps();

            // Index
            $table->index('status');
            $table->index('angkatan');
            $table->index('ditentukan_oleh_id');
        });

        // Re-enable foreign key constraints
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Since this is a synchronization migration, the down() method might not be perfectly reversible 
        // without knowing the exact state before. However, the original migration file handles 
        // the base drop which is standard.
    }
};
