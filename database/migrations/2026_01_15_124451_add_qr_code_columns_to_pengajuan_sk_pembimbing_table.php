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
        Schema::table('pengajuan_sk_pembimbing', function (Blueprint $table) {
            $table->text('qr_code_korprodi')->nullable()->after('ttd_korprodi_at');
            $table->text('qr_code_kajur')->nullable()->after('ttd_kajur_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_sk_pembimbing', function (Blueprint $table) {
            $table->dropColumn(['qr_code_korprodi', 'qr_code_kajur']);
        });
    }
};
