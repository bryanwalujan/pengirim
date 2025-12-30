<?php
// filepath: database/migrations/2025_12_30_165847_restructure_lembar_catatan_seminar_proposals_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lembar_catatan_seminar_proposals', function (Blueprint $table) {
            // Remove old scoring fields
            $table->dropColumn(['nilai_kebaruan', 'nilai_metode', 'nilai_ketersediaan_data']);
            
            // Add new descriptive fields
            $table->text('catatan_kebaruan')->nullable()->after('dosen_id');
            $table->text('catatan_metode')->nullable()->after('catatan_kebaruan');
            $table->text('catatan_ketersediaan_data')->nullable()->after('catatan_metode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lembar_catatan_seminar_proposals', function (Blueprint $table) {
            // Restore old fields
            $table->integer('nilai_kebaruan')->nullable();
            $table->integer('nilai_metode')->nullable();
            $table->integer('nilai_ketersediaan_data')->nullable();
            
            // Remove new fields
            $table->dropColumn(['catatan_kebaruan', 'catatan_metode', 'catatan_ketersediaan_data']);
        });
    }
};
