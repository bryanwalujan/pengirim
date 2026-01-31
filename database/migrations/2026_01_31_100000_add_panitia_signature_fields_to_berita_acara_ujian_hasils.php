<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('berita_acara_ujian_hasils', function (Blueprint $table) {
            // Panitia Sekretaris (Korprodi)
            $table->foreignId('ttd_panitia_sekretaris_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('ttd_panitia_sekretaris_at')->nullable();
            $table->string('panitia_sekretaris_name')->nullable();
            $table->string('panitia_sekretaris_nip')->nullable();

            // Panitia Ketua (Dekan)
            $table->foreignId('ttd_panitia_ketua_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('ttd_panitia_ketua_at')->nullable();
            $table->string('panitia_ketua_name')->nullable();
            $table->string('panitia_ketua_nip')->nullable();

            // Override fields for staff - Sekretaris
            $table->foreignId('override_panitia_sekretaris_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('override_panitia_sekretaris_at')->nullable();
            $table->text('override_panitia_sekretaris_reason')->nullable();

            // Override fields for staff - Ketua
            $table->foreignId('override_panitia_ketua_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('override_panitia_ketua_at')->nullable();
            $table->text('override_panitia_ketua_reason')->nullable();

            // QR Codes for panitia signatures
            $table->longText('qr_code_panitia_sekretaris')->nullable();
            $table->longText('qr_code_panitia_ketua')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('berita_acara_ujian_hasils', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ttd_panitia_sekretaris_by');
            $table->dropColumn(['ttd_panitia_sekretaris_at', 'panitia_sekretaris_name', 'panitia_sekretaris_nip']);

            $table->dropConstrainedForeignId('ttd_panitia_ketua_by');
            $table->dropColumn(['ttd_panitia_ketua_at', 'panitia_ketua_name', 'panitia_ketua_nip']);

            $table->dropConstrainedForeignId('override_panitia_sekretaris_by');
            $table->dropColumn(['override_panitia_sekretaris_at', 'override_panitia_sekretaris_reason']);

            $table->dropConstrainedForeignId('override_panitia_ketua_by');
            $table->dropColumn(['override_panitia_ketua_at', 'override_panitia_ketua_reason']);

            $table->dropColumn(['qr_code_panitia_sekretaris', 'qr_code_panitia_ketua']);
        });
    }
};
