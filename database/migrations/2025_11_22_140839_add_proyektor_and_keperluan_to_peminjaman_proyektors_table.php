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
        Schema::table('peminjaman_proyektors', function (Blueprint $table) {
            $table->string('proyektor_code', 50)->nullable()->after('user_id');
            $table->string('keperluan')->nullable()->after('proyektor_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman_proyektors', function (Blueprint $table) {
            $table->dropColumn(['proyektor_code', 'keperluan']);
        });
    }
};