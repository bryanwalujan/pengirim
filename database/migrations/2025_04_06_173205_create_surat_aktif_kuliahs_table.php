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
            $table->foreignId('mahasiswa_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Relasi ke tabel users (mahasiswa pengaju)');
            // Informasi Pengajuan
            $table->text('tujuan_pengajuan')
                ->comment('Tujuan pengajuan surat');
            $table->text('keterangan_tambahan')
                ->nullable()
                ->comment('Keterangan tambahan dari mahasiswa');
            $table->string('file_pendukung_path')
                ->nullable()
                ->comment('Path dokumen pendukung jika ada');
            // Informasi Surat
            $table->string('nomor_surat')
                ->nullable()
                ->comment('Format: 3108/UN41.2/TI/2024');
            $table->date('tanggal_surat')
                ->nullable()
                ->comment('Tanggal surat, diisi saat disetujui');
            $table->string('tahun_ajaran')
                ->comment('Format: 2024/2025');
            $table->string('semester')
                ->comment('Contoh: Ganjil/Genap');
            // Penandatangan
            $table->foreignId('penandatangan_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Relasi ke tabel users (dosen/staff penandatangan)');
            $table->string('jabatan_penandatangan')
                ->nullable()
                ->comment('Jabatan penandatangan surat');
            // Status dan Verifikasi
            $table->enum('status', ['draft', 'pending', 'diproses', 'disetujui', 'ditolak', 'selesai'])
                ->default('draft')
                ->comment('Status pengajuan surat');
            $table->text('catatan_admin')
                ->nullable()
                ->comment('Catatan dari admin/staff');
            $table->timestamp('diproses_pada')
                ->nullable();
            $table->timestamp('disetujui_pada')
                ->nullable();
            $table->timestamp('ditolak_pada')
                ->nullable();
            $table->timestamp('selesai_pada')
                ->nullable();
            // Tracking
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
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
