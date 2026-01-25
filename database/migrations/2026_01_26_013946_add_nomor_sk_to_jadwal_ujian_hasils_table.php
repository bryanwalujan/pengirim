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
        Schema::table('jadwal_ujian_hasils', function (Blueprint $table) {
            // Add nomor_sk column after file_sk_ujian_hasil
            $table->string('nomor_sk', 100)->nullable()->after('file_sk_ujian_hasil');
            
            // Add index for search performance
            $table->index('nomor_sk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_ujian_hasils', function (Blueprint $table) {
            // Drop index first, then column
            $table->dropIndex(['nomor_sk']);
            $table->dropColumn('nomor_sk');
        });
    }
};
