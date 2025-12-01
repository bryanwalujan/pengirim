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
        Schema::create('surat_usulan_proposals', function (Blueprint $table) {
            $table->id();

            // One-to-One dengan pendaftaran
            $table->foreignId('pendaftaran_seminar_proposal_id')
                ->unique()
                ->constrained('pendaftaran_seminar_proposals')
                ->onDelete('cascade');

            // Data surat
            $table->string('nomor_surat')->unique();
            $table->string('file_surat');
            $table->timestamp('tanggal_surat');

            // Verification Code (untuk QR)
            $table->string('verification_code')->nullable()->unique();

            // QR Code
            $table->text('qr_code_kaprodi')->nullable();
            $table->text('qr_code_kajur')->nullable();

            // Tanda tangan Kaprodi
            $table->timestamp('ttd_kaprodi_at')->nullable();
            $table->foreignId('ttd_kaprodi_by')->nullable()->constrained('users')->onDelete('set null');

            // Tanda tangan Kajur
            $table->timestamp('ttd_kajur_at')->nullable();
            $table->foreignId('ttd_kajur_by')->nullable()->constrained('users')->onDelete('set null');

            // Override Info (untuk staff override approval)
            $table->text('override_info')->nullable()->comment('JSON: info override approval jika dilakukan staff');

            // Status
            $table->enum('status', [
                'draft',
                'menunggu_ttd_kaprodi',
                'menunggu_ttd_kajur',
                'selesai'
            ])->default('draft');

            $table->timestamps();

            // Index
            $table->index('nomor_surat');
            $table->index('verification_code');
            $table->index('status');
            $table->index('ttd_kaprodi_at');
            $table->index('ttd_kajur_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_usulan_proposals');
    }
};
