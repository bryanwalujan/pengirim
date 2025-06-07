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
        Schema::create('surat_pindahs', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code', 12)->unique(); // Kode tracking unik
            $table->foreignId('mahasiswa_id')->constrained('users')->onDelete('cascade');
            // Informasi Pengajuan
            $table->string('universitas_tujuan'); // Tambahan untuk surat pindah
            $table->text('alasan_pengajuan'); // Tambahan untuk surat pindah (menggantikan tujuan_pengajuan)
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
        Schema::dropIfExists('surat_pindahs');
    }
};
