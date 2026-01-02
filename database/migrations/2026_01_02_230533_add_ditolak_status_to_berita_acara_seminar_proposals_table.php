<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah ENUM status untuk menambahkan 'ditolak'
        DB::statement("ALTER TABLE `berita_acara_seminar_proposals` 
            MODIFY COLUMN `status` ENUM('draft', 'menunggu_ttd_pembahas', 'menunggu_ttd_pembimbing', 'selesai', 'ditolak') 
            NOT NULL DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ENUM status ke nilai awal (tanpa 'ditolak')
        DB::statement("ALTER TABLE `berita_acara_seminar_proposals` 
            MODIFY COLUMN `status` ENUM('draft', 'menunggu_ttd_pembahas', 'menunggu_ttd_pembimbing', 'selesai') 
            NOT NULL DEFAULT 'draft'");
    }
};
