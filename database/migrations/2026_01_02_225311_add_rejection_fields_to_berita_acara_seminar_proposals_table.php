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
            // Kolom untuk alasan ditolak (optional, untuk catatan tambahan saat ditolak)
            $table->text('alasan_ditolak')->nullable()->after('catatan_tambahan');
            
            // Timestamp kapan proposal ditolak
            $table->timestamp('ditolak_at')->nullable()->after('alasan_ditolak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berita_acara_seminar_proposals', function (Blueprint $table) {
            $table->dropColumn(['alasan_ditolak', 'ditolak_at']);
        });
    }
};
