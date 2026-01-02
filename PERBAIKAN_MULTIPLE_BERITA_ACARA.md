# Perbaikan: Multiple Berita Acara untuk Ujian Ulangan

## Masalah yang Diselesaikan

Ketika proposal ditolak (keputusan "Tidak"), berita acara lama sudah memiliki persetujuan/TTD dari dosen-dosen. Untuk ujian ulangan, perlu dibuat **berita acara baru yang terpisah** dengan persetujuan baru, bukan menggunakan berita acara lama.

## Solusi yang Diterapkan

### 1. **Relasi One-to-Many untuk Berita Acara**

Sebelumnya: Satu jadwal hanya punya **satu** berita acara (One-to-One)
Sekarang: Satu jadwal bisa punya **multiple** berita acara (One-to-Many)

**Model: `JadwalSeminarProposal.php`**

```php
// ❌ OLD: One-to-One (deprecated tapi tetap ada untuk backward compatibility)
public function beritaAcaraSeminarProposal()
{
    return $this->hasOne(BeritaAcaraSeminarProposal::class);
}

// ✅ NEW: One-to-Many - Semua BA termasuk yang ditolak
public function beritaAcaras()
{
    return $this->hasMany(BeritaAcaraSeminarProposal::class);
}

// ✅ NEW: Get BA yang AKTIF (bukan yang ditolak)
public function beritaAcaraAktif()
{
    return $this->hasOne(BeritaAcaraSeminarProposal::class)
        ->whereNotIn('status', ['ditolak'])
        ->latest();
}

// ✅ NEW: Get BA yang DITOLAK (arsip)
public function beritaAcarasDitolak()
{
    return $this->hasMany(BeritaAcaraSeminarProposal::class)
        ->where('status', 'ditolak')
        ->orderBy('ditolak_at', 'desc');
}
```

### 2. **Update Controller untuk Membuat BA Baru**

**File: `AdminBeritaAcaraSemproController.php`**

**Method `create()`:**

```php
// ✅ Check hanya BA yang AKTIF (bukan yang ditolak)
$existingActiveBA = $jadwal->beritaAcaraSeminarProposal()
    ->whereNotIn('status', ['ditolak'])
    ->first();

if ($existingActiveBA) {
    // Sudah ada BA aktif, redirect ke BA tersebut
    return redirect()->route('admin.berita-acara-sempro.show', $existingActiveBA);
}

// ✅ Jika ada BA ditolak, log info tapi tetap lanjut buat BA baru
$rejectedBA = $jadwal->beritaAcaraSeminarProposal()
    ->where('status', 'ditolak')
    ->first();

if ($rejectedBA) {
    Log::info('Creating new BA for rescheduled exam (previous BA was rejected)');
}
```

**Method `storeFillByPembimbing()` - Flow Penolakan:**

```php
if ($isRejected) {
    // ✅ CATATAN PENTING:
    // - Berita acara LAMA tetap disimpan dengan status 'ditolak' (untuk dokumentasi/arsip)
    // - Berita acara BARU akan dibuat oleh staff setelah jadwal ulang diatur
    // - Ini memastikan setiap ujian punya berita acara terpisah dengan persetujuan masing-masing

    $beritaAcara->update([
        'status' => 'ditolak',
        'alasan_ditolak' => $validated['catatan_tambahan'],
        'ditolak_at' => now(),
        // ... TTD pembimbing/ketua tetap dicatat
    ]);

    // Generate PDF untuk arsip
    $pdfPath = $this->pelaksanaanUjianService->generateBeritaAcaraPdf($beritaAcara);

    // Reset jadwal untuk penjadwalan ulang
    $jadwal->update([
        'status' => 'menunggu_jadwal',
        'tanggal_ujian' => null,
        'waktu_mulai' => null,
        'waktu_selesai' => null,
        'ruangan' => null,
    ]);
}
```

### 3. **Helper Methods Baru**

**Model: `JadwalSeminarProposal.php`**

```php
// ✅ UPDATED: Check BA aktif (bukan yang ditolak)
public function hasBeritaAcara(): bool
{
    return $this->beritaAcaraAktif()->exists();
}

// ✅ NEW: Check apakah pernah ditolak
public function hasRejectedBeritaAcara(): bool
{
    return $this->beritaAcarasDitolak()->exists();
}
```

## Alur Kerja Lengkap

### Skenario: Proposal Ditolak dan Ujian Ulangan

```
┌─────────────────────────────────────────────────────────────┐
│ UJIAN PERTAMA                                               │
└─────────────────────────────────────────────────────────────┘
1. Staff buat jadwal ujian pertama
2. Staff buat Berita Acara #1
3. Dosen pembahas approve (TTD)
4. Pembimbing/ketua isi dengan keputusan "Tidak"
   ↓
   BA #1 status = 'ditolak' (TETAP ADA untuk arsip)
   Jadwal status = 'menunggu_jadwal'
   Tanggal/waktu/ruangan = null

┌─────────────────────────────────────────────────────────────┐
│ UJIAN ULANGAN                                               │
└─────────────────────────────────────────────────────────────┘
5. Staff input jadwal baru (tanggal, waktu, ruangan)
   Jadwal status = 'dijadwalkan'

6. Staff klik "Buat Berita Acara"
   ✅ Sistem cek: Ada BA ditolak? Ya, tapi itu arsip
   ✅ Sistem buat Berita Acara #2 (BARU, terpisah dari #1)

7. Dosen pembahas approve BA #2 (TTD baru)
8. Pembimbing/ketua isi BA #2
   - Jika "Ya" → BA #2 status = 'selesai'
   - Jika "Tidak" → BA #2 status = 'ditolak', ulangi lagi

┌─────────────────────────────────────────────────────────────┐
│ HASIL AKHIR                                                 │
└─────────────────────────────────────────────────────────────┘
Jadwal punya 2 Berita Acara:
- BA #1: status='ditolak', ditolak_at='2026-01-02', keputusan='Tidak'
- BA #2: status='selesai', keputusan='Ya'

Kedua BA tersimpan sebagai arsip lengkap!
```

## Keuntungan Solusi Ini

### 1. **Audit Trail Lengkap**

-   Setiap ujian punya dokumentasi terpisah
-   Riwayat penolakan tersimpan dengan lengkap
-   TTD dosen tidak hilang/tertimpa

### 2. **Integritas Data**

-   BA yang ditolak tetap valid sebagai dokumen arsip
-   Tidak ada data yang hilang atau tertimpa
-   Setiap BA punya persetujuan independen

### 3. **Fleksibilitas**

-   Mahasiswa bisa ujian ulang berkali-kali jika perlu
-   Setiap ujian punya BA terpisah dengan dosen yang berbeda (jika perlu)
-   Staff bisa lihat riwayat semua ujian

### 4. **Backward Compatibility**

-   Relasi lama `beritaAcaraSeminarProposal()` tetap ada
-   Code lama tidak break
-   Migrasi smooth tanpa perlu update banyak code

## Database Structure

**Tabel: `berita_acara_seminar_proposals`**

| Field                        | Type      | Keterangan                                                                            |
| ---------------------------- | --------- | ------------------------------------------------------------------------------------- |
| `id`                         | bigint    | Primary key                                                                           |
| `jadwal_seminar_proposal_id` | bigint    | Foreign key (bisa ada multiple BA untuk 1 jadwal)                                     |
| `status`                     | enum      | 'draft', 'menunggu_ttd_pembahas', 'menunggu_ttd_pembimbing', 'selesai', **'ditolak'** |
| `keputusan`                  | enum      | 'Ya', 'Ya, dengan perbaikan', 'Tidak'                                                 |
| `alasan_ditolak`             | text      | Alasan penolakan (nullable)                                                           |
| `ditolak_at`                 | timestamp | Waktu ditolak (nullable)                                                              |
| `ttd_dosen_pembahas`         | json      | Array TTD pembahas                                                                    |
| `ttd_pembimbing_by`          | bigint    | ID pembimbing yang TTD                                                                |
| `ttd_ketua_penguji_by`       | bigint    | ID ketua yang TTD                                                                     |

**Contoh Data:**

```sql
-- BA Ujian Pertama (Ditolak)
INSERT INTO berita_acara_seminar_proposals VALUES (
    1,                          -- id
    100,                        -- jadwal_id
    'ditolak',                  -- status
    'Tidak',                    -- keputusan
    'Metodologi kurang kuat',   -- alasan_ditolak
    '2026-01-02 15:30:00',      -- ditolak_at
    ...
);

-- BA Ujian Ulangan (Lulus)
INSERT INTO berita_acara_seminar_proposals VALUES (
    2,                          -- id
    100,                        -- jadwal_id (SAMA!)
    'selesai',                  -- status
    'Ya',                       -- keputusan
    NULL,                       -- alasan_ditolak
    NULL,                       -- ditolak_at
    ...
);
```

## Testing Checklist

### Test 1: Buat BA Baru Setelah Penolakan

-   [x] Tolak proposal (keputusan "Tidak")
-   [x] BA lama status = 'ditolak'
-   [x] Jadwal status = 'menunggu_jadwal'
-   [ ] Staff input jadwal baru
-   [ ] Staff klik "Buat Berita Acara"
-   [ ] Sistem buat BA baru (bukan update yang lama)
-   [ ] Verifikasi: Ada 2 BA untuk 1 jadwal

### Test 2: Lihat Riwayat BA

-   [ ] Buka halaman detail jadwal
-   [ ] Verifikasi: Tampil semua BA (yang ditolak dan yang aktif)
-   [ ] Klik BA yang ditolak
-   [ ] Verifikasi: Bisa lihat detail dan download PDF arsip

### Test 3: Multiple Rejection

-   [ ] Tolak proposal 2x berturut-turut
-   [ ] Verifikasi: Ada 2 BA dengan status 'ditolak'
-   [ ] Buat BA ke-3 untuk ujian ke-3
-   [ ] Verifikasi: Sistem buat BA baru, bukan update yang lama

## File yang Diubah

1. ✅ `app/Models/JadwalSeminarProposal.php`

    - Tambah relasi `beritaAcaras()`, `beritaAcaraAktif()`, `beritaAcarasDitolak()`
    - Update `hasBeritaAcara()` untuk cek BA aktif saja
    - Tambah `hasRejectedBeritaAcara()`

2. ✅ `app/Http/Controllers/Admin/AdminBeritaAcaraSemproController.php`
    - Update `create()` untuk allow multiple BA
    - Update `storeFillByPembimbing()` dengan dokumentasi lengkap

## Catatan Penting

1. **Relasi Lama Tetap Ada**: `beritaAcaraSeminarProposal()` masih berfungsi untuk backward compatibility

2. **Gunakan Relasi Baru**: Untuk code baru, gunakan `beritaAcaraAktif()` atau `beritaAcaras()`

3. **PDF Arsip**: Setiap BA (termasuk yang ditolak) punya PDF sendiri untuk dokumentasi

4. **Tidak Ada Data yang Hilang**: Semua riwayat ujian tersimpan lengkap
