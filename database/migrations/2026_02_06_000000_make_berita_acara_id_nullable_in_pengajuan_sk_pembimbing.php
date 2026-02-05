<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengajuan_sk_pembimbing', function (Blueprint $table) {
            // Make berita_acara_id nullable to support students who did sempro outside e-service
            $table->foreignId('berita_acara_id')
                ->nullable()
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_sk_pembimbing', function (Blueprint $table) {
            // Revert back to NOT NULL (but this might fail if there are null values)
            $table->foreignId('berita_acara_id')
                ->nullable(false)
                ->change();
        });
    }
};
