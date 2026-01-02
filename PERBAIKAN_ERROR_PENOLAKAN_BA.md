# Perbaikan Error: Fitur Penolakan Berita Acara

## Masalah yang Terjadi

Error muncul ketika dosen pembimbing mencoba mengisi berita acara dengan keputusan "Tidak":

```
SQLSTATE[01000]: Warning: 1265 Data truncated for column 'status' at row 1
```

**Penyebab**: Kolom `status` di database menggunakan ENUM yang tidak termasuk nilai `'ditolak'`.

## Solusi yang Diterapkan

### 1. Migration untuk Menambahkan 'ditolak' ke ENUM Status

**File**: `2026_01_02_230533_add_ditolak_status_to_berita_acara_seminar_proposals_table.php`

```php
DB::statement("ALTER TABLE `berita_acara_seminar_proposals`
    MODIFY COLUMN `status` ENUM('draft', 'menunggu_ttd_pembahas', 'menunggu_ttd_pembimbing', 'selesai', 'ditolak')
    NOT NULL DEFAULT 'draft'");
```

**Status**: ✅ Migration berhasil dijalankan

### 2. Update View Fill By Pembimbing

**File**: `resources/views/admin/berita-acara-sempro/fill-by-pembimbing.blade.php`

**Perubahan:**

-   Menambahkan alert warning yang muncul ketika opsi "Tidak" dipilih
-   Alert menjelaskan konsekuensi dari penolakan proposal
-   JavaScript untuk toggle alert secara dinamis
-   Konfirmasi dialog yang berbeda untuk penolakan vs persetujuan

**Alert Warning:**

```html
<div class="alert alert-danger" id="alertTidakLayak">
    <h6>Perhatian: Proposal Tidak Layak</h6>
    <p>Jika Anda memilih "Tidak", maka:</p>
    <ul>
        <li>Berita acara akan ditandai sebagai DITOLAK</li>
        <li>Mahasiswa HARUS mengikuti ujian seminar proposal ulang</li>
        <li>Staff akan melakukan penjadwalan ulang untuk ujian berikutnya</li>
        <li>Jadwal ujian sebelumnya akan direset</li>
    </ul>
</div>
```

**JavaScript Features:**

1. **Toggle Alert**: Alert muncul/hilang berdasarkan pilihan radio button
2. **Different Confirmation**: Dialog konfirmasi berbeda untuk penolakan vs persetujuan
3. **Visual Warning**: Warna merah dan emoji ⚠️ untuk penolakan

### 3. Struktur Database Setelah Perbaikan

**Tabel**: `berita_acara_seminar_proposals`

Kolom baru yang ditambahkan:

-   `alasan_ditolak` (text, nullable) - Alasan penolakan proposal
-   `ditolak_at` (timestamp, nullable) - Waktu proposal ditolak
-   `status` ENUM updated - Sekarang termasuk `'ditolak'`

**Status ENUM Values:**

1. `'draft'` - Draft berita acara
2. `'menunggu_ttd_pembahas'` - Menunggu TTD dosen pembahas
3. `'menunggu_ttd_pembimbing'` - Menunggu pengisian pembimbing/ketua
4. `'selesai'` - Berita acara selesai, proposal diterima
5. **`'ditolak'`** - Proposal ditolak, perlu dijadwalkan ulang

## Alur Kerja Lengkap

### Skenario 1: Proposal Diterima (Ya / Ya dengan perbaikan)

```
Dosen mengisi BA → Pilih "Ya" atau "Ya, dengan perbaikan"
                 ↓
         Status BA = 'selesai'
                 ↓
         Status Jadwal = 'selesai'
                 ↓
         PDF ter-generate
                 ↓
         Mahasiswa lanjut penelitian
```

### Skenario 2: Proposal Ditolak (Tidak)

```
Dosen mengisi BA → Pilih "Tidak"
                 ↓
         Alert warning muncul (merah)
                 ↓
         Konfirmasi penolakan
                 ↓
         Status BA = 'ditolak'
                 ↓
         Status Jadwal = 'menunggu_jadwal'
                 ↓
         Jadwal direset (tanggal, waktu, ruangan = null)
                 ↓
         PDF dokumentasi ter-generate
                 ↓
         Staff jadwalkan ulang
                 ↓
         Mahasiswa ujian seminar proposal ulang
```

## Testing yang Disarankan

### Test 1: Pilih "Tidak" dan Lihat Warning

1. Login sebagai dosen pembimbing
2. Buka form fill berita acara
3. Pilih radio button "Tidak"
4. **Verifikasi**: Alert merah muncul dengan penjelasan konsekuensi

### Test 2: Submit dengan "Tidak"

1. Lanjutkan dari Test 1
2. Isi catatan kejadian
3. Klik "Submit & Selesaikan BA"
4. **Verifikasi**:
    - Dialog konfirmasi berwarna merah dengan judul "⚠️ Konfirmasi Penolakan Proposal"
    - Tombol konfirmasi "Ya, Tolak Proposal" berwarna merah
5. Klik "Ya, Tolak Proposal"
6. **Verifikasi**:
    - Tidak ada error SQL
    - Status BA = 'ditolak'
    - Status jadwal = 'menunggu_jadwal'
    - Pesan warning ditampilkan

### Test 3: Penjadwalan Ulang

1. Login sebagai staff
2. Buka menu Jadwal Seminar Proposal
3. Filter jadwal dengan status "Menunggu Jadwal"
4. **Verifikasi**: Jadwal mahasiswa yang ditolak muncul
5. Input jadwal baru
6. **Verifikasi**: Jadwal berhasil diupdate

## File yang Diubah

1. ✅ `database/migrations/2026_01_02_230533_add_ditolak_status_to_berita_acara_seminar_proposals_table.php` - Migration ENUM status
2. ✅ `resources/views/admin/berita-acara-sempro/fill-by-pembimbing.blade.php` - View dengan warning alert
3. ✅ `app/Http/Controllers/Admin/AdminBeritaAcaraSemproController.php` - Controller logic (sudah diupdate sebelumnya)
4. ✅ `app/Models/BeritaAcaraSeminarProposal.php` - Model dengan status ditolak (sudah diupdate sebelumnya)

## Catatan Penting

1. **Migration Sequence**: Pastikan migration dijalankan dalam urutan yang benar:

    - Migration 1: Add `alasan_ditolak` dan `ditolak_at` columns
    - Migration 2: Modify ENUM status to include 'ditolak'

2. **User Experience**: Alert warning memberikan feedback langsung kepada dosen tentang konsekuensi dari keputusan "Tidak"

3. **Data Integrity**: PDF tetap digenerate untuk dokumentasi meski proposal ditolak

4. **Workflow**: Jadwal direset tapi SK Proposal mahasiswa tetap tersimpan, sehingga tidak perlu upload ulang
