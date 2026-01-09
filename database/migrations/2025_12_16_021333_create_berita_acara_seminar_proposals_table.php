<?php
// filepath: database/migrations/2025_12_16_021333_create_berita_acara_seminar_proposals_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('berita_acara_seminar_proposals', function (Blueprint $table) {
            $table->id();

            // ========================================
            // FOREIGN KEYS
            // ========================================

            $table->foreignId('jadwal_seminar_proposal_id')
                ->constrained('jadwal_seminar_proposals')
                ->onDelete('cascade')
                ->name('fk_ba_sempro_jadwal');

            // ========================================
            // KONTEN BERITA ACARA
            // ========================================

            // Catatan Kejadian (Diisi oleh Dosen Pembimbing/Ketua)
            $table->enum('catatan_kejadian', [
                'Lancar',
                'Ada beberapa perbaikan yang harus diubah'
            ])->nullable();

            // Kesimpulan/Keputusan (Diisi oleh Dosen Pembimbing/Ketua)
            $table->enum('keputusan', [
                'Ya',
                'Ya, dengan perbaikan',
                'Tidak'
            ])->nullable();

            // Catatan Tambahan (Optional)
            $table->text('catatan_tambahan')->nullable();

            // Alasan ditolak (untuk catatan tambahan saat ditolak)
            $table->text('alasan_ditolak')->nullable();

            // Timestamp kapan proposal ditolak
            $table->timestamp('ditolak_at')->nullable();

            // ========================================
            // VALIDASI & FILE
            // ========================================

            // QR Code untuk Validasi
            $table->string('verification_code')->unique();

            // File PDF Final
            $table->string('file_path')->nullable();

            // ========================================
            // WORKFLOW STATUS
            // ========================================

            $table->enum('status', [
                'draft',                    // Staff buat draft
                'menunggu_ttd_pembahas',   // Menunggu TTD dari semua pembahas
                'menunggu_ttd_pembimbing', // Semua pembahas sudah TTD, tunggu pembimbing isi + TTD
                'selesai',                 // Pembimbing sudah TTD, BA selesai & PDF generated
                'ditolak'                  // Berita Acara ditolak
            ])->default('draft');

            // ========================================
            // TANDA TANGAN DOSEN PEMBAHAS (JSON)
            // ========================================

            // Format JSON: 
            // [
            //   {
            //     "dosen_id": 123,
            //     "dosen_name": "Dr. John Doe",
            //     "signed_at": "2025-12-23 10:30:00"
            //   },
            //   ...
            // ]
            $table->json('ttd_dosen_pembahas')->nullable();

            // ========================================
            // PENGISIAN OLEH PEMBIMBING
            // ========================================

            // Tracking siapa yang mengisi catatan_kejadian & keputusan
            $table->foreignId('diisi_oleh_pembimbing_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->name('fk_ba_sempro_pembimbing_pengisi');

            $table->timestamp('diisi_pembimbing_at')->nullable();

            // ========================================
            // TANDA TANGAN PEMBIMBING/KETUA
            // ========================================

            // TTD Pembimbing (sekaligus finalisasi BA)
            $table->timestamp('ttd_pembimbing_at')->nullable();

            $table->foreignId('ttd_pembimbing_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->name('fk_ba_sempro_ttd_pembimbing');

            // ========================================
            // TTD KETUA PENGUJI (Backup - karena pembimbing = ketua)
            // ========================================

            $table->timestamp('ttd_ketua_penguji_at')->nullable();

            $table->foreignId('ttd_ketua_penguji_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->name('fk_ba_sempro_ttd_ketua');

            // ========================================
            // METADATA
            // ========================================

            // Staff yang membuat draft BA
            $table->foreignId('dibuat_oleh_id')
                ->constrained('users')
                ->onDelete('restrict')
                ->name('fk_ba_sempro_pembuat');

            $table->timestamps();

            // ========================================
            // INDEXES
            // ========================================

            $table->index('status');
            $table->index('verification_code');
            $table->index('keputusan');
            $table->index('diisi_oleh_pembimbing_id');
            $table->index('ttd_pembimbing_by');
            $table->index('ttd_ketua_penguji_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita_acara_seminar_proposals');
    }
};