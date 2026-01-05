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
        Schema::create('pengajuan_sk_pembimbing', function (Blueprint $table) {
            $table->id();

            // Foreign Keys - Core Relations
            $table->foreignId('berita_acara_id')
                ->constrained('berita_acara_seminar_proposals')
                ->cascadeOnDelete();

            $table->foreignId('mahasiswa_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Dosen Pembimbing (PS1 wajib, PS2 opsional)
            $table->foreignId('dosen_pembimbing_1_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('dosen_pembimbing_2_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Data Skripsi
            $table->text('judul_skripsi');

            // File Uploads (stored in private storage)
            $table->string('file_surat_permohonan');
            $table->string('file_slip_ukt');
            $table->string('file_proposal_revisi');
            $table->string('file_surat_sk')->nullable();

            // Status & Surat Info
            $table->string('status', 30)->default('draft');
            $table->string('nomor_surat', 100)->nullable();
            $table->date('tanggal_surat')->nullable();
            $table->string('verification_code', 50)->unique()->nullable();

            // Notes
            $table->text('catatan_staff')->nullable();
            $table->text('alasan_ditolak')->nullable();

            // Audit Trail - Verifikasi
            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            // Audit Trail - Penentuan PS
            $table->foreignId('ps_assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('ps_assigned_at')->nullable();

            // Audit Trail - TTD Kajur
            $table->foreignId('ttd_kajur_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('ttd_kajur_at')->nullable();

            // Audit Trail - TTD Korprodi
            $table->foreignId('ttd_korprodi_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('ttd_korprodi_at')->nullable();

            $table->timestamps();

            // Composite Indexes untuk query optimization
            $table->index(['status', 'created_at']);
            $table->index(['mahasiswa_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_sk_pembimbing');
    }
};