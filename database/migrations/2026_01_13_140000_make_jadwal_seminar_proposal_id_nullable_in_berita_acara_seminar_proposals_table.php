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
        // Check if foreign key exists before dropping
        $foreignKeyExists = DB::select("
            SELECT COUNT(*) as count 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE CONSTRAINT_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'berita_acara_seminar_proposals' 
            AND CONSTRAINT_NAME = 'fk_ba_sempro_jadwal'
        ");

        if ($foreignKeyExists[0]->count > 0) {
            // Drop foreign key if exists
            Schema::table('berita_acara_seminar_proposals', function (Blueprint $table) {
                $table->dropForeign('fk_ba_sempro_jadwal');
            });
        }

        // Modify column to be nullable using raw SQL (more reliable)
        DB::statement('ALTER TABLE `berita_acara_seminar_proposals` 
                       MODIFY `jadwal_seminar_proposal_id` BIGINT UNSIGNED NULL');

        // Re-add foreign key with SET NULL on delete
        Schema::table('berita_acara_seminar_proposals', function (Blueprint $table) {
            $table->foreign('jadwal_seminar_proposal_id', 'fk_ba_sempro_jadwal')
                ->references('id')
                ->on('jadwal_seminar_proposals')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the nullable foreign key
        Schema::table('berita_acara_seminar_proposals', function (Blueprint $table) {
            $table->dropForeign('fk_ba_sempro_jadwal');
        });

        // Make column NOT nullable
        DB::statement('ALTER TABLE `berita_acara_seminar_proposals` 
                       MODIFY `jadwal_seminar_proposal_id` BIGINT UNSIGNED NOT NULL');

        // Re-add foreign key with cascade delete
        Schema::table('berita_acara_seminar_proposals', function (Blueprint $table) {
            $table->foreign('jadwal_seminar_proposal_id', 'fk_ba_sempro_jadwal')
                ->references('id')
                ->on('jadwal_seminar_proposals')
                ->onDelete('cascade');
        });
    }
};
