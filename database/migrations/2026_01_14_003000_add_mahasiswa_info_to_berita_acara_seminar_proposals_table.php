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
            // Tambah field untuk menyimpan data mahasiswa (untuk audit trail)
            // Agar tetap ada record meski jadwal dihapus
            $table->unsignedBigInteger('mahasiswa_id')->nullable()->after('jadwal_seminar_proposal_id');
            $table->string('mahasiswa_name')->nullable()->after('mahasiswa_id');
            $table->string('mahasiswa_nim')->nullable()->after('mahasiswa_name');
            $table->string('judul_skripsi', 500)->nullable()->after('mahasiswa_nim');
            
            // Add foreign key
            $table->foreign('mahasiswa_id')->references('id')->on('users')->onDelete('set null');
            
            // Add index
            $table->index('mahasiswa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berita_acara_seminar_proposals', function (Blueprint $table) {
            $table->dropForeign(['mahasiswa_id']);
            $table->dropColumn(['mahasiswa_id', 'mahasiswa_name', 'mahasiswa_nim', 'judul_skripsi']);
        });
    }
};
