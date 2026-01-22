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
        Schema::table('komisi_hasils', function (Blueprint $table) {
            // Reference ke SK Pembimbing untuk tracing data
            $table->foreignId('pengajuan_sk_id')
                ->nullable()
                ->after('dosen_pembimbing2_id')
                ->constrained('pengajuan_sk_pembimbing')
                ->nullOnDelete();
            
            // Flag untuk menandai apakah data diinput manual (legacy mode)
            $table->boolean('is_manual_input')
                ->default(false)
                ->after('pengajuan_sk_id')
                ->comment('True jika mahasiswa input manual (tidak melalui sistem SK Pembimbing)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('komisi_hasils', function (Blueprint $table) {
            $table->dropForeign(['pengajuan_sk_id']);
            $table->dropColumn(['pengajuan_sk_id', 'is_manual_input']);
        });
    }
};
