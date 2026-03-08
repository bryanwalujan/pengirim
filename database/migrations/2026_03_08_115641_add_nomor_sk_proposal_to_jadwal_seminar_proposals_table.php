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
        Schema::table('jadwal_seminar_proposals', function (Blueprint $table) {
            // Add nomor_sk_proposal column after file_sk_proposal
            $table->string('nomor_sk_proposal', 100)->nullable()->after('file_sk_proposal');
            $table->index('nomor_sk_proposal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_seminar_proposals', function (Blueprint $table) {
            $table->dropIndex(['nomor_sk_proposal']);
            $table->dropColumn('nomor_sk_proposal');
        });
    }
};
