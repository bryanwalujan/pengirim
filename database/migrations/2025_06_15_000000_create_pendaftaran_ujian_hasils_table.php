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
            $table->foreignId('komisi_hasil_id')->nullable()->constrained()->onDelete('restrict');

            // Data mahasiswa
            $table->string('angkatan');
            $table->text('judul_skripsi');
            $table->decimal('ipk', 3, 2);

            // Files
            $table->string('file_transkrip_nilai');
            $table->string('file_skripsi');
            $table->string('file_surat_permohonan');
            $table->string('file_slip_ukt');

            // Pembimbing (dari KomisiHasil)
            $table->foreignId('dosen_pembimbing1_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('dosen_pembimbing2_id')->nullable()->constrained('users')->onDelete('set null');

            // Metadata penentuan penguji (untuk fase selanjutnya)
            $table->timestamp('tanggal_penentuan_penguji')->nullable();
            $table->foreignId('ditentukan_oleh_id')->nullable()->constrained('users')->onDelete('set null');

            // Status workflow
            $table->enum('status', [
                'pending',
                'penguji_ditentukan',
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_ujian_hasils');
    }
};
