# Panduan Optimasi Sync Mahasiswa

## Perubahan yang Dilakukan

### 1. Jadwal Sync Dikurangi
- **Sebelumnya**: Setiap hari pukul 03:00
- **Sekarang**: Setiap **Minggu** pukul 03:00
- **Alasan**: Data status mahasiswa jarang berubah, tidak perlu sync harian untuk 1000+ data

### 2. Opsi Sync Fleksibel
Command sekarang mendukung beberapa opsi untuk kontrol lebih baik:

#### Sync Semua Status (Default)
```bash
php artisan mahasiswa:sync
```

#### Sync Status Tertentu Saja
Jika Anda tahu hanya status "Lulus" yang perlu diupdate:
```bash
php artisan mahasiswa:sync --status=L
```

Atau beberapa status sekaligus:
```bash
php artisan mahasiswa:sync --status=L --status=K
```

#### Kontrol Ukuran Batch
Untuk mengurangi beban server, kurangi jumlah data per request:
```bash
php artisan mahasiswa:sync --batch=25
```

Default: 50 records per page

### 3. Kombinasi Opsi
Contoh sync hanya mahasiswa Lulus dengan batch kecil:
```bash
php artisan mahasiswa:sync --status=L --batch=20
```

## Rekomendasi Penggunaan

### Untuk Production (1000+ Mahasiswa)

**Jadwal Otomatis (Sudah dikonfigurasi):**
- Sync lengkap setiap Minggu pukul 03:00

**Sync Manual (Saat Dibutuhkan):**
- **Awal semester**: Sync semua status
  ```bash
  php artisan mahasiswa:sync
  ```

- **Setelah wisuda**: Sync hanya status Lulus
  ```bash
  php artisan mahasiswa:sync --status=L
  ```

- **Monitoring cepat**: Gunakan batch kecil
  ```bash
  php artisan mahasiswa:sync --batch=10
  ```

## Estimasi Beban Server

### Sebelum Optimasi
- Frekuensi: 365 kali/tahun
- Data per sync: ~1000+ mahasiswa
- Total request/tahun: ~365,000 records

### Setelah Optimasi
- Frekuensi: 52 kali/tahun (setiap Minggu)
- Data per sync: ~1000+ mahasiswa
- Total request/tahun: ~52,000 records
- **Pengurangan beban: ~85%**

### Sync Manual Targeted
Jika hanya sync status "Lulus" (estimasi 50 mahasiswa):
- Request: ~50 records
- **Pengurangan beban: ~95%**

## Tips Tambahan

1. **Monitor Log**: Cek `storage/logs/laravel.log` untuk error
2. **Timeout**: Jika API lambat, tingkatkan timeout di command (default 30 detik)
3. **Batch Size**: Untuk server lemah, gunakan `--batch=10` atau `--batch=25`
4. **Status Codes**:
   - A = Aktif
   - L = Lulus
   - C = Cuti
   - N = Non-Aktif
   - K = Keluar

## Troubleshooting

### Jika Sync Terlalu Lama
```bash
# Kurangi batch size
php artisan mahasiswa:sync --batch=10
```

### Jika Hanya Perlu Update Status Tertentu
```bash
# Sync hanya yang berubah, misal Lulus dan Keluar
php artisan mahasiswa:sync --status=L --status=K
```

### Jika Server Production Lambat
Ubah jadwal di `routes/console.php` menjadi 2 minggu sekali:
```php
Schedule::command('mahasiswa:sync')->cron('0 3 1,15 * *'); // Tanggal 1 dan 15
```
