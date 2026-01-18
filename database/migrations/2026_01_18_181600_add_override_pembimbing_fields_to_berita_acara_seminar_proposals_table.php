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
            // Override fields for staff override pembimbing
            $table->foreignId('override_pembimbing_by')->nullable()->after('ttd_ketua_penguji_at')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('override_pembimbing_at')->nullable()->after('override_pembimbing_by');
            $table->string('override_pembimbing_reason', 500)->nullable()->after('override_pembimbing_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berita_acara_seminar_proposals', function (Blueprint $table) {
            $table->dropForeign(['override_pembimbing_by']);
            $table->dropColumn([
                'override_pembimbing_by',
                'override_pembimbing_at',
                'override_pembimbing_reason',
            ]);
        });
    }
};
