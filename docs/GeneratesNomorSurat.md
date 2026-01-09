# Dokumentasi GeneratesNomorSurat Trait

## Overview

Trait `GeneratesNomorSurat` telah dioptimalkan untuk menangani sistem penomoran surat secara universal dengan fitur reset otomatis berdasarkan semester genap.

## Fitur Utama

### 1. **Reset Otomatis Berdasarkan Semester Genap**

Sistem akan otomatis mereset penomoran surat ketika tahun ajaran berada di semester **Genap**.

**Contoh:**

-   Tahun Ajaran: `2025/2026` Semester: `Ganjil` → Nomor surat: `0001/UN41.2/TI/2025`
-   Tahun Ajaran: `2025/2026` Semester: `Genap` → Nomor surat: `0001/UN41.2/TI/2026` (RESET)
-   Tahun Ajaran: `2026/2027` Semester: `Ganjil` → Nomor surat: `0001/UN41.2/TI/2026` (Lanjut dari semester Genap sebelumnya)
-   Tahun Ajaran: `2026/2027` Semester: `Genap` → Nomor surat: `0001/UN41.2/TI/2027` (RESET lagi)

### 2. **Penomoran Universal**

Semua jenis surat menggunakan counter yang sama untuk menghindari duplikasi nomor:

-   `SuratAktifKuliah`
-   `SuratIjinSurvey`
-   `SuratCutiAkademik`
-   `SuratPindah`
-   `SuratUsulanProposal`

### 3. **Caching untuk Performance**

-   Cache tahun ajaran aktif selama 1 jam
-   Cache nomor surat terakhir selama 5 menit
-   Auto-clear cache saat generate nomor baru

### 4. **Type Safety (Laravel 12)**

Semua method menggunakan type hints dan return types untuk keamanan tipe data.

## Method-Method Utama

### `generateNomorSuratUniversal(string $prefix = 'UN41.2/TI', ?int $customNumber = null): string`

Generate nomor surat baru dengan format: `XXXX/PREFIX/TAHUN`

**Parameter:**

-   `$prefix`: Prefix surat (default: 'UN41.2/TI')
-   `$customNumber`: Nomor custom jika diperlukan (1-9999)

**Return:** String nomor surat lengkap

**Contoh Penggunaan:**

```php
// Generate nomor surat otomatis
$nomorSurat = $this->generateNomorSuratUniversal('UN41.2/TI');
// Output: 0001/UN41.2/TI/2026

// Generate dengan nomor custom
$nomorSurat = $this->generateNomorSuratUniversal('UN41.2/TI', 123);
// Output: 0123/UN41.2/TI/2026
```

### `validateNomorSuratUnique(string $nomorSurat, ?int $excludeId = null, ?string $excludeType = null): bool`

Validasi apakah nomor surat unik di semua jenis surat.

**Parameter:**

-   `$nomorSurat`: Nomor surat yang akan divalidasi
-   `$excludeId`: ID record yang dikecualikan (untuk update)
-   `$excludeType`: Class model yang dikecualikan

**Return:** `true` jika unik, `false` jika sudah ada

**Contoh Penggunaan:**

```php
// Cek apakah nomor surat unik
if ($this->validateNomorSuratUnique('0001/UN41.2/TI/2026')) {
    // Nomor surat tersedia
}

// Cek saat update (exclude current record)
if ($this->validateNomorSuratUnique('0001/UN41.2/TI/2026', $suratId, SuratAktifKuliah::class)) {
    // Valid untuk update
}
```

### `getLastUsedNomorSurat(): ?string`

Mendapatkan nomor surat terakhir yang digunakan untuk tahun ajaran aktif.

**Return:** String nomor surat atau `null` jika belum ada

**Contoh Penggunaan:**

```php
$lastNumber = $this->getLastUsedNomorSurat();
// Output: 0042/UN41.2/TI/2026
```

### `getNextNomorSurat(): string`

Mendapatkan nomor surat berikutnya (shortcut method).

**Return:** String nomor surat berikutnya

**Contoh Penggunaan:**

```php
$nextNumber = $this->getNextNomorSurat();
// Output: 0043/UN41.2/TI/2026
```

### `resetNomorSuratCounter(): string`

Reset counter nomor surat (hanya jika semester Genap).

**Return:** String pesan status reset

**Contoh Penggunaan:**

```php
$message = $this->resetNomorSuratCounter();
// Output: "Nomor surat telah direset dan akan dimulai dari 0001 untuk tahun ajaran 2025/2026 (Semester Genap)"
```

### `getNomorSuratStatistics(): array`

Mendapatkan statistik penggunaan nomor surat.

**Return:** Array berisi statistik lengkap

**Contoh Penggunaan:**

```php
$stats = $this->getNomorSuratStatistics();
/*
Output:
[
    'tahun_ajaran' => '2025/2026',
    'semester' => 'Genap',
    'academic_year_id' => '2026',
    'will_reset_on_genap' => true,
    'models' => [
        'SuratAktifKuliah' => [
            'count' => 15,
            'latest_number' => 42,
            'latest_nomor_surat' => '0042/UN41.2/TI/2026'
        ],
        'SuratIjinSurvey' => [
            'count' => 8,
            'latest_number' => 35,
            'latest_nomor_surat' => '0035/UN41.2/TI/2026'
        ],
        // ... model lainnya
    ]
]
*/
```

## Method Protected (Internal)

### `getSuratModels(): array`

Mendapatkan daftar semua model surat yang menggunakan sistem penomoran.

### `getActiveTahunAjaran(): TahunAjaran`

Mendapatkan tahun ajaran aktif dengan caching.

### `shouldResetCounter(TahunAjaran $tahunAjaran): bool`

Menentukan apakah counter harus direset (true jika semester Genap).

### `getAcademicYearIdentifier(TahunAjaran $tahunAjaran): string`

Mendapatkan identifier tahun akademik untuk penomoran.

-   Semester Ganjil: Tahun pertama (2025/2026 → 2025)
-   Semester Genap: Tahun kedua (2025/2026 → 2026)

### `getLatestNomorSuratNumber(string $prefix, string $academicYearId): int`

Mendapatkan nomor terakhir yang digunakan untuk prefix dan tahun tertentu.

## Cara Menambahkan Model Surat Baru

Jika Anda membuat model surat baru, tambahkan ke method `getSuratModels()`:

```php
protected function getSuratModels(): array
{
    return [
        SuratAktifKuliah::class,
        SuratIjinSurvey::class,
        SuratCutiAkademik::class,
        SuratPindah::class,
        SuratUsulanProposal::class,
        SuratBaruAnda::class, // Tambahkan di sini
    ];
}
```

## Integrasi dengan Event TahunAjaranChanged

Ketika tahun ajaran berubah, event `TahunAjaranChanged` akan di-trigger dan cache akan di-clear otomatis.

**File:** `app/Models/TahunAjaran.php`

```php
protected static function booted()
{
    static::updated(function ($tahunAjaran) {
        if ($tahunAjaran->wasChanged('status_aktif') && $tahunAjaran->status_aktif) {
            // Clear cache
            Cache::forget('current_academic_year');
            Cache::forget('last_nomor_surat');

            // Broadcast event
            event(new \App\Events\TahunAjaranChanged($tahunAjaran));
        }
    });
}
```

## Best Practices

1. **Selalu gunakan `generateNomorSuratUniversal()`** untuk generate nomor surat baru
2. **Validasi dengan `validateNomorSuratUnique()`** sebelum menyimpan nomor surat custom
3. **Gunakan `getNomorSuratStatistics()`** untuk monitoring dan debugging
4. **Jangan manual manipulasi nomor surat** - biarkan trait yang handle
5. **Cache akan auto-clear** saat generate nomor baru, tidak perlu manual clear

## Logging

Semua operasi reset akan tercatat di log dengan informasi lengkap:

```
[2026-01-08 21:32:20] local.INFO: Nomor surat counter DIRESET untuk tahun ajaran: 2025/2026 (Semester Genap)
{"tahun_ajaran":"2025/2026","semester":"Genap","academic_year_id":"2026"}
```

## Testing

Untuk testing, Anda bisa menggunakan method `getNomorSuratStatistics()` untuk memverifikasi:

```php
// Test case: Semester Genap harus reset
$tahunAjaran = TahunAjaran::create([
    'tahun' => '2025/2026',
    'semester' => 'Genap',
    'status_aktif' => true
]);

$stats = $model->getNomorSuratStatistics();
$this->assertEquals('2026', $stats['academic_year_id']);
$this->assertTrue($stats['will_reset_on_genap']);
```

## Troubleshooting

### Nomor surat tidak reset di semester Genap

-   Pastikan field `semester` di database bernilai "Genap" (case-insensitive)
-   Clear cache: `php artisan cache:clear`
-   Cek log untuk melihat apakah reset dipanggil

### Nomor surat duplikat

-   Pastikan semua model surat sudah terdaftar di `getSuratModels()`
-   Gunakan `validateNomorSuratUnique()` sebelum save
-   Cek apakah ada race condition (multiple request bersamaan)

### Performance issue

-   Cache sudah diimplementasikan, tapi jika masih lambat:
    -   Tambahkan index di kolom `nomor_surat`
    -   Pertimbangkan untuk menggunakan Redis cache
    -   Monitor query dengan Laravel Telescope
