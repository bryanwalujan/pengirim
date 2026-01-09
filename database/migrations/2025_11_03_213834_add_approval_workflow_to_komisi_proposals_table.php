<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('komisi_proposals', function (Blueprint $table) {
            // Tambah kolom untuk penandatangan PA
            $table->foreignId('penandatangan_pa_id')
                ->nullable()
                ->after('status')
                ->constrained('users')
                ->onDelete('set null');

            $table->timestamp('tanggal_persetujuan_pa')
                ->nullable()
                ->after('penandatangan_pa_id');

            // Tambah kolom untuk penandatangan Korprodi
            $table->foreignId('penandatangan_korprodi_id')
                ->nullable()
                ->after('tanggal_persetujuan_pa')
                ->constrained('users')
                ->onDelete('set null');

            $table->timestamp('tanggal_persetujuan_korprodi')
                ->nullable()
                ->after('penandatangan_korprodi_id');

            // Tambah kolom untuk file PDF dengan QR PA saja
            $table->string('file_komisi_pa')
                ->nullable()
                ->after('tanggal_persetujuan_korprodi')
                ->comment('PDF dengan QR code PA saja');

            // Tambah kolom verification code untuk QR
            $table->string('verification_code', 50)
                ->nullable()
                ->unique()
                ->after('file_komisi_pa')
                ->comment('Kode verifikasi untuk QR code');

            // Tambah index untuk performa
            $table->index('status');
            $table->index('penandatangan_pa_id');
            $table->index('penandatangan_korprodi_id');
            $table->index('verification_code');
        });

        // Update enum status menggunakan raw SQL karena Laravel tidak support modify enum
        DB::statement("ALTER TABLE `komisi_proposals` MODIFY COLUMN `status` ENUM('pending', 'approved_pa', 'approved', 'rejected') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('komisi_proposals', function (Blueprint $table) {
            // Drop foreign keys dulu
            $table->dropForeign(['penandatangan_pa_id']);
            $table->dropForeign(['penandatangan_korprodi_id']);

            // Drop indexes
            $table->dropIndex(['status']);
            $table->dropIndex(['penandatangan_pa_id']);
            $table->dropIndex(['penandatangan_korprodi_id']);
            $table->dropIndex(['verification_code']);

            // Drop columns
            $table->dropColumn([
                'penandatangan_pa_id',
                'tanggal_persetujuan_pa',
                'penandatangan_korprodi_id',
                'tanggal_persetujuan_korprodi',
                'file_komisi_pa',
                'verification_code'
            ]);
        });

        // Kembalikan enum status ke nilai original
        DB::statement("ALTER TABLE `komisi_proposals` MODIFY COLUMN `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    }
};
