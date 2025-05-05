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
        Schema::create('tracking_surats', function (Blueprint $table) {
            $table->id();
            $table->string('surat_type'); // Contoh: App\Models\SuratAktifKuliah
            $table->unsignedBigInteger('surat_id'); // ID dari surat terkait
            $table->string('aksi'); // diajukan, diproses, disetujui, dll.
            $table->text('keterangan')->nullable();
            $table->foreignId('mahasiswa_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('confirmed_at')->nullable(); // Waktu konfirmasi pengambilan surat
            $table->timestamps();
            // Index untuk performa query
            $table->index(['surat_type', 'surat_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_surats');
    }
};
