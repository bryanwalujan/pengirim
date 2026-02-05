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
            $table->string('file_sk_pembimbing')->after('file_slip_ukt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_ujian_hasils', function (Blueprint $table) {
            $table->dropColumn('file_sk_pembimbing');
        });
    }
};
