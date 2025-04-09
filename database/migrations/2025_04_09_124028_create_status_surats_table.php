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
        Schema::create('status_surats', function (Blueprint $table) {
            $table->id();
            $table->string('surat_type');
            $table->unsignedBigInteger('surat_id');
            $table->string('status');
            $table->text('catatan_admin')->nullable(); // Catatan resmi dari admin
            $table->text('catatan_internal')->nullable(); // Catatan untuk internal staff
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
            $table->index(['surat_type', 'surat_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_surats');
    }
};
