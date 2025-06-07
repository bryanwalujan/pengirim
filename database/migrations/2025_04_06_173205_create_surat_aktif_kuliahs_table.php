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
            // Tracking Code
            $table->string('tracking_code', 12)->unique()->nullable();
            // Informasi Mahasiswa
            $table->foreignId('mahasiswa_id')->constrained('users')->onDelete('cascade');
            // Informasi Pengajuan
            $table->text('tujuan_pengajuan');
            $table->text('keterangan_tambahan')->nullable();
            $table->string('file_pendukung_path')->nullable();
            $table->string('file_surat_path')->nullable();

            // Sistem Persetujuan
            $table->string('signature_path')->nullable(); // Untuk menyimpan path QR code tanda tangan
            $table->timestamp('approved_at')->nullable(); // Waktu persetujuan
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null'); // Kaprodi yang menyetujui
            $table->string('verification_code')->nullable(); // Kode verifikasi umum (opsional)
            $table->string('verification_code_kaprodi')->nullable(); // Kode verifikasi unik untuk Kaprodi
            $table->string('verification_code_pimpinan')->nullable(); // Kode verifikasi unik untuk Pimpinan   

            // Informasi Surat
            $table->string('nomor_surat')->nullable();
            $table->date('tanggal_surat')->nullable();
            $table->string('tahun_ajaran');
            $table->string('semester');
            // Penandatangan
            $table->foreignId('penandatangan_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('jabatan_penandatangan')->nullable();
            $table->unsignedBigInteger('penandatangan_kaprodi_id')->nullable();
            $table->string('jabatan_penandatangan_kaprodi')->nullable();
            $table->foreign('penandatangan_kaprodi_id')->references('id')->on('users')->onDelete('set null');
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
