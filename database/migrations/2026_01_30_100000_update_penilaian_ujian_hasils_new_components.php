<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to update penilaian_ujian_hasils table with new 7-component grading system.
 *
 * New components (total weight = 10):
 * 1. nilai_kebaruan (1.5) - Kebaruan dan signifikansi penelitian
 * 2. nilai_kesesuaian (1.5) - Kesesuaian Judul, Masalah, Tujuan, Pembahasan, Kesimpulan, Saran
 * 3. nilai_metode (1) - Metode Penelitian dan Pemecahan Masalah
 * 4. nilai_kajian_teori (1) - Kajian Teori
 * 5. nilai_hasil_penelitian (3) - Hasil Penelitian (Kesesuaian dengan Metode/Hasil)
 * 6. nilai_referensi (1) - Referensi
 * 7. nilai_tata_bahasa (1) - Tata Bahasa
 *
 * Formula: Nilai Skripsi = (Total Bobot / 10) * 4
 * Grade Scale: A (3.60-4.00), B (3.00-3.59), C (2.00-2.99), D (1.00-1.99), E (0.00-0.99)
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('penilaian_ujian_hasils', function (Blueprint $table) {
            // New columns for 7-component grading system
            // These are added after nilai_kebaruan

            // Kesesuaian Judul, Masalah, Tujuan, Pembahasan, Kesimpulan, Saran (bobot 1.5)
            $table->integer('nilai_kesesuaian')->nullable()->after('nilai_kebaruan');

            // Kajian Teori (bobot 1)
            $table->integer('nilai_kajian_teori')->nullable()->after('nilai_metode');

            // Hasil Penelitian - Kesesuaian dengan Metode/Hasil (bobot 3)
            $table->integer('nilai_hasil_penelitian')->nullable()->after('nilai_kajian_teori');

            // Tata Bahasa (bobot 1)
            $table->integer('nilai_tata_bahasa')->nullable()->after('nilai_referensi');

            // nilai_mutu stores the 4.0 scale value (0.00 - 4.00)
            $table->decimal('nilai_mutu', 3, 2)->nullable()->after('total_nilai');
        });

        // Drop old columns that are no longer used
        Schema::table('penilaian_ujian_hasils', function (Blueprint $table) {
            // nilai_data_software is replaced by nilai_hasil_penelitian
            $table->dropColumn('nilai_data_software');

            // nilai_penguasaan is replaced by nilai_tata_bahasa
            $table->dropColumn('nilai_penguasaan');
        });
    }

    public function down(): void
    {
        // Restore old columns
        Schema::table('penilaian_ujian_hasils', function (Blueprint $table) {
            $table->integer('nilai_data_software')->nullable()->after('nilai_metode');
            $table->integer('nilai_penguasaan')->nullable()->after('nilai_referensi');
        });

        // Drop new columns
        Schema::table('penilaian_ujian_hasils', function (Blueprint $table) {
            $table->dropColumn([
                'nilai_kesesuaian',
                'nilai_kajian_teori',
                'nilai_hasil_penelitian',
                'nilai_tata_bahasa',
                'nilai_mutu',
            ]);
        });
    }
};
