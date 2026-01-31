<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update enum untuk status berita_acara_ujian_hasils
        // Menambahkan status baru untuk workflow Panitia
        DB::statement("ALTER TABLE `berita_acara_ujian_hasils` 
            MODIFY COLUMN `status` ENUM(
                'draft',
                'menunggu_ttd_penguji',
                'menunggu_ttd_ketua',
                'menunggu_ttd_panitia_sekretaris',
                'menunggu_ttd_panitia_ketua',
                'selesai',
                'ditolak'
            ) DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback ke enum lama
        DB::statement("ALTER TABLE `berita_acara_ujian_hasils` 
            MODIFY COLUMN `status` ENUM(
                'draft',
                'menunggu_ttd_penguji',
                'menunggu_ttd_ketua',
                'selesai',
                'ditolak'
            ) DEFAULT 'draft'");
    }
};
