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
        Schema::create('surat_usulan_skripsis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pendaftaran_ujian_hasil_id')
                ->constrained('pendaftaran_ujian_hasils')
                ->onDelete('cascade');

            // Document info
            $table->string('nomor_surat');
            $table->date('tanggal_surat');
            $table->string('file_surat')->nullable();

            // Verification code (untuk QR code)
            $table->string('verification_code')->unique();

            // QR Codes (Base64 encoded)
            $table->text('qr_code_kaprodi')->nullable();
            $table->text('qr_code_kajur')->nullable();

            // Kaprodi signature
            $table->foreignId('ttd_kaprodi_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamp('ttd_kaprodi_at')->nullable();

            // Kajur signature
            $table->foreignId('ttd_kajur_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamp('ttd_kajur_at')->nullable();

            // Override info (JSON) - untuk staff override signature
            $table->json('override_info')->nullable();

            // Status workflow
            $table->enum('status', [
                'draft',
                'menunggu_ttd_kaprodi',
                'menunggu_ttd_kajur',
                'selesai'
            ])->default('draft');

            $table->timestamps();

            // Index
            $table->index('status');
            $table->index('verification_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_usulan_skripsis');
    }
};
