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
        Schema::create('kop_surats', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable();
            $table->string('kementerian'); // KEMENTERIAN PENDIDIKAN TINGGI...
            $table->string('universitas'); // UNIVERSITAS NEGERI MANADO
            $table->string('fakultas');    // FAKULTAS TEKNIK
            $table->string('prodi');       // PROGRAM STUDI S1 TEKNIK INFORMATIKA
            $table->string('alamat');      // Kampus UNIMA Tondano...
            $table->string('kontak');      // Telp, Website, Email
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kop_surats');
    }
};
