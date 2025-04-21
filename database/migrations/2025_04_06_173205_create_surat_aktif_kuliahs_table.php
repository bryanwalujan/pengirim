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
            // Informasi Mahasiswa
            $table->foreignId('mahasiswa_id')->constrained('users')->onDelete('cascade');
            // Informasi Pengajuan
            $table->text('tujuan_pengajuan');
            $table->text('keterangan_tambahan')->nullable();
            $table->string('file_pendukung_path')->nullable();
            $table->string('file_surat_path')->nullable();
            $table->string('draft_path')->nullable();

            // Sistem Persetujuan
            $table->string('signature_path')->nullable(); // Untuk menyimpan path QR code tanda tangan
            $table->timestamp('approved_at')->nullable(); // Waktu persetujuan
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null'); // Kaprodi yang menyetujui
            $table->string('verification_code')->nullable(); // Kode verifikasi unik

            // Informasi Surat
            $table->string('nomor_surat')->nullable();
            $table->date('tanggal_surat')->nullable();
            $table->string('tahun_ajaran');
            $table->string('semester');
            // Penandatangan
            $table->foreignId('penandatangan_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('jabatan_penandatangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
