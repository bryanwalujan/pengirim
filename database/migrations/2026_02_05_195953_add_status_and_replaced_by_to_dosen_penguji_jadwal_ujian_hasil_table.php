<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('dosen_penguji_jadwal_ujian_hasil', function (Blueprint $table) {
            if (!Schema::hasColumn('dosen_penguji_jadwal_ujian_hasil', 'status')) {
                $table->string('status')->default('active')->after('posisi');
            }
            if (!Schema::hasColumn('dosen_penguji_jadwal_ujian_hasil', 'replaced_by_id')) {
                $table->foreignId('replaced_by_id')->nullable()->after('status')->constrained('users')->onDelete('set null');
            }
            
            // Re-adding this because it might have failed or not run
            try {
                $table->index('jadwal_ujian_hasil_id', 'idx_jadwal_ujian_hasil_fk');
            } catch (\Exception $e) {}
        });

        Schema::table('dosen_penguji_jadwal_ujian_hasil', function (Blueprint $table) {
            try {
                $table->dropUnique('penguji_ujian_hasil_unique');
            } catch (\Exception $e) {}
            
            try {
                $table->index(['jadwal_ujian_hasil_id', 'status'], 'idx_jadwal_status_penguji');
            } catch (\Exception $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('dosen_penguji_jadwal_ujian_hasil', function (Blueprint $table) {
            try { $table->dropIndex('idx_jadwal_status_penguji'); } catch (\Exception $e) {}
            try { $table->dropIndex('idx_jadwal_ujian_hasil_fk'); } catch (\Exception $e) {}
            try { $table->dropForeign(['replaced_by_id']); } catch (\Exception $e) {}
            try { $table->dropColumn(['status', 'replaced_by_id']); } catch (\Exception $e) {}
            
            try {
                $table->unique(['jadwal_ujian_hasil_id', 'dosen_id', 'posisi'], 'penguji_ujian_hasil_unique');
            } catch (\Exception $e) {}
        });
    }
};
