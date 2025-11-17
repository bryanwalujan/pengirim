<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('komisi_hasils', function (Blueprint $table) {
            // Penandatangan dan tanggal approval Pembimbing 1
            $table->foreignId('penandatangan_pembimbing1_id')
                ->nullable()
                ->after('dosen_pembimbing2_id')
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamp('tanggal_persetujuan_pembimbing1')
                ->nullable()
                ->after('penandatangan_pembimbing1_id');

            // Penandatangan dan tanggal approval Pembimbing 2
            $table->foreignId('penandatangan_pembimbing2_id')
                ->nullable()
                ->after('tanggal_persetujuan_pembimbing1')
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamp('tanggal_persetujuan_pembimbing2')
                ->nullable()
                ->after('penandatangan_pembimbing2_id');

            // Penandatangan dan tanggal approval Korprodi
            $table->foreignId('penandatangan_korprodi_id')
                ->nullable()
                ->after('tanggal_persetujuan_pembimbing2')
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamp('tanggal_persetujuan_korprodi')
                ->nullable()
                ->after('penandatangan_korprodi_id');

            // File PDF untuk setiap tahap
            $table->string('file_komisi_pembimbing1')
                ->nullable()
                ->after('tanggal_persetujuan_korprodi')
                ->comment('PDF setelah Pembimbing 1 approve');

            $table->string('file_komisi_pembimbing2')
                ->nullable()
                ->after('file_komisi_pembimbing1')
                ->comment('PDF setelah Pembimbing 2 approve');

            // Verification code untuk QR
            $table->string('verification_code', 50)
                ->unique()
                ->nullable()
                ->after('file_komisi_hasil');
        });

        // Update enum status - gunakan raw SQL untuk MySQL
        DB::statement("ALTER TABLE komisi_hasils MODIFY COLUMN status ENUM('pending', 'approved_pembimbing1', 'approved_pembimbing2', 'approved', 'rejected') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('komisi_hasils', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['penandatangan_pembimbing1_id']);
            $table->dropForeign(['penandatangan_pembimbing2_id']);
            $table->dropForeign(['penandatangan_korprodi_id']);

            // Drop columns
            $table->dropColumn([
                'penandatangan_pembimbing1_id',
                'tanggal_persetujuan_pembimbing1',
                'penandatangan_pembimbing2_id',
                'tanggal_persetujuan_pembimbing2',
                'penandatangan_korprodi_id',
                'tanggal_persetujuan_korprodi',
                'file_komisi_pembimbing1',
                'file_komisi_pembimbing2',
                'verification_code'
            ]);
        });

        // Rollback enum status ke versi lama
        DB::statement("ALTER TABLE komisi_hasils MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    }
};