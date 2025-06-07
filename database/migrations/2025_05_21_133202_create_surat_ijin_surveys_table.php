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
        Schema::create('surat_ijin_surveys', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code', 12)->unique(); // Kode tracking unik
            // Informasi Mahasiswa
            $table->foreignId('mahasiswa_id')->constrained('users')->onDelete('cascade');
            // Informasi Pengajuan
            $table->string('judul'); // Tambahan untuk surat ijin survey
            $table->string('tempat_survey'); // Tambahan untuk surat ijin survey
            $table->text('keterangan_tambahan')->nullable();
            $table->string('file_pendukung_path')->nullable();
            $table->string('file_surat_path')->nullable();

            // Sistem Persetujuan
            $table->string('signature_path')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('verification_code')->nullable();
            $table->string('verification_code_kaprodi')->nullable();
            $table->string('verification_code_pimpinan')->nullable();

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
        Schema::dropIfExists('surat_ijin_surveys');
    }
};
