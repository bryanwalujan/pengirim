<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('berita_acara_seminar_proposals', function (Blueprint $table) {
            $table->dropColumn('catatan_kejadian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berita_acara_seminar_proposals', function (Blueprint $table) {
            $table->enum('catatan_kejadian', [
                'Lancar',
                'Ada beberapa perbaikan yang harus diubah'
            ])->nullable()->after('jadwal_seminar_proposal_id');
        });
    }
};
