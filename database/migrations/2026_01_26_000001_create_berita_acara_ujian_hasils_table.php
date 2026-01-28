<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berita_acara_ujian_hasils', function (Blueprint $table) {
            $table->id();

            // ========================================
            // FOREIGN KEYS
            // ========================================

            $table->foreignId('jadwal_ujian_hasil_id')
                ->nullable()
                ->constrained('jadwal_ujian_hasils')
                ->onDelete('set null')
                ->name('fk_ba_ujian_hasil_jadwal');

            // ========================================
            // MAHASISWA INFO (untuk audit trail jika jadwal dihapus)
            // ========================================

            $table->foreignId('mahasiswa_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->name('fk_ba_ujian_hasil_mahasiswa');

            $table->string('mahasiswa_name')->nullable();
            $table->string('mahasiswa_nim')->nullable();
            $table->text('judul_skripsi')->nullable();

            // ========================================
            // SK DEKAN INFO
            // ========================================

            $table->string('nomor_sk_dekan')->nullable();
            $table->date('tanggal_sk_dekan')->nullable();
            $table->string('ruangan')->default('Ruangan Ujian Teknik Informatika');

            // ========================================
            // KEPUTUSAN & CATATAN
            // ========================================

            $table->enum('keputusan', [
                'Lulus',
                'Lulus dengan Perbaikan',
                'Tidak Lulus'
            ])->nullable();

            $table->text('catatan_tambahan')->nullable();
            $table->text('alasan_ditolak')->nullable();
            $table->timestamp('ditolak_at')->nullable();

            // ========================================
            // VALIDASI & FILE
            // ========================================

            $table->string('verification_code')->unique();
            $table->string('file_path')->nullable();

            // ========================================
            // WORKFLOW STATUS
            // ========================================

            $table->enum('status', [
                'draft',
                'menunggu_ttd_penguji',
                'menunggu_ttd_ketua',
                'selesai',
                'ditolak'
            ])->default('draft');

            // ========================================
            // TANDA TANGAN DOSEN PENGUJI (JSON)
            // ========================================

            // Format JSON: 
            // [
            //   {
            //     "dosen_id": 123,
            //     "dosen_name": "Dr. John Doe",
            //     "posisi": "Penguji 1",
            //     "signed_at": "2025-12-23 10:30:00"
            //   },
            //   ...
            // ]
            $table->json('ttd_dosen_penguji')->nullable();

            // ========================================
            // PENGISIAN OLEH KETUA PENGUJI
            // ========================================

            $table->foreignId('diisi_oleh_ketua_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->name('fk_ba_ujian_hasil_ketua_pengisi');

            $table->timestamp('diisi_ketua_at')->nullable();

            // ========================================
            // TANDA TANGAN KETUA PENGUJI
            // ========================================

            $table->timestamp('ttd_ketua_penguji_at')->nullable();

            $table->foreignId('ttd_ketua_penguji_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->name('fk_ba_ujian_hasil_ttd_ketua');

            // ========================================
            // OVERRIDE KETUA (STAFF)
            // ========================================

            $table->foreignId('override_ketua_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->name('fk_ba_ujian_hasil_override_ketua');

            $table->timestamp('override_ketua_at')->nullable();
            $table->text('override_ketua_reason')->nullable();

            // ========================================
            // METADATA
            // ========================================

            $table->foreignId('dibuat_oleh_id')
                ->constrained('users')
                ->onDelete('restrict')
                ->name('fk_ba_ujian_hasil_pembuat');

            $table->timestamps();

            // ========================================
            // INDEXES
            // ========================================

            $table->index('status');
            $table->index('verification_code');
            $table->index('keputusan');
            $table->index('diisi_oleh_ketua_id');
            $table->index('ttd_ketua_penguji_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita_acara_ujian_hasils');
    }
};
