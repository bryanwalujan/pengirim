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
        Schema::table('pendaftaran_ujian_hasils', function (Blueprint $table) {
            // Add nomor_sk_pembimbing column after file_sk_pembimbing
            $table->string('nomor_sk_pembimbing', 100)->nullable()->after('file_sk_pembimbing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_ujian_hasils', function (Blueprint $table) {
            $table->dropColumn('nomor_sk_pembimbing');
        });
    }
};
