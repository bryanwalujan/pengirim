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
        Schema::create('pembayaran_ukts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->enum('status', ['bayar', 'belum_bayar'])->default('belum_bayar');
            $table->timestamps();

            $table->unique(['mahasiswa_id', 'tahun_ajaran_id']); // Satu mahasiswa per tahun ajaran
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_ukts');
    }
};
