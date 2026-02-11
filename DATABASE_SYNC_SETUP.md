# Panduan Setup Direct Database Access

## Konfigurasi .env di Server Production

Tambahkan konfigurasi berikut ke file `.env` di server E-Service:

```env
# ========================================
# TI UNIMA DATABASE CONNECTION (Direct Access)
# ========================================
# Aktifkan mode database (lebih cepat, tidak ada timeout)
TI_UNIMA_USE_DATABASE=true

# Database TI Unima (sama server)
TI_UNIMA_DB_HOST=127.0.0.1
TI_UNIMA_DB_PORT=3306
TI_UNIMA_DB_DATABASE=nama_database_ti_unima
TI_UNIMA_DB_USERNAME=username_database
TI_UNIMA_DB_PASSWORD=password_database

# ========================================
# TI UNIMA API CONNECTION (Fallback/Legacy)
# ========================================
# Jika suatu hari pindah server terpisah, set TI_UNIMA_USE_DATABASE=false
# dan gunakan konfigurasi API di bawah:
# TI_UNIMA_API_URL=https://ti.unima.ac.id/api/mahasiswa
# TI_UNIMA_API_TOKEN=your_token_here
# TI_UNIMA_API_HOST=ti.unima.ac.id
```

## Cara Mendapatkan Kredensial Database TI Unima

### Opsi 1: Via CloudPanel GUI
1. Login ke CloudPanel
2. Buka menu **Databases**
3. Cari database yang digunakan oleh aplikasi TI Unima
4. Lihat nama database, username, dan password-nya

### Opsi 2: Via File .env TI Unima
Jika Anda memiliki akses ke aplikasi TI Unima:
```bash
cat ~/htdocs/ti.unima.ac.id/.env | grep DB_
```

Output akan menampilkan:
```env
DB_DATABASE=ti_unima_db
DB_USERNAME=ti_unima_user
DB_PASSWORD=password123
```

### Opsi 3: Jika Punya Akses ke MySQL
```bash
mysql -u root -p
```
Lalu jalankan:
```sql
SHOW DATABASES;
SELECT user, host FROM mysql.user;
```

## Testing Koneksi

Setelah mengisi kredensial di `.env`, test koneksi:

```bash
php artisan tinker
```

Di dalam Tinker:
```php
DB::connection('ti_unima')->getPdo();
// Jika berhasil, akan muncul: PDO Object

DB::connection('ti_unima')->table('users')->count();
// Jika berhasil, akan muncul angka total user
```

## Menjalankan Sync

### Full Sync (Semua Status)
```bash
php artisan mahasiswa:sync
```

### Sync Status Tertentu
```bash
php artisan mahasiswa:sync --status=L --status=K
```

## Keuntungan Direct Database Access

| Aspek | API (Sebelumnya) | Database (Sekarang) |
|-------|------------------|---------------------|
| Kecepatan | 30+ detik (Timeout) | < 2 detik |
| Reliabilitas | ❌ Tergantung firewall | ✅ Langsung |
| Beban Server | 🔴 Tinggi (HTTP overhead) | 🟢 Rendah |
| Debugging | Sulit | Mudah |

## Troubleshooting

### Error: "Database connection failed"
- Pastikan kredensial di `.env` benar
- Cek apakah user database memiliki akses ke database TI Unima:
  ```sql
  GRANT SELECT ON ti_unima_db.users TO 'eservice_user'@'localhost';
  FLUSH PRIVILEGES;
  ```

### Error: "Table 'users' doesn't exist"
- Nama tabel mungkin berbeda di TI Unima
- Cek dengan: `DB::connection('ti_unima')->select('SHOW TABLES');`
- Update kode jika nama tabel bukan `users`

## Security Note

⚠️ **Penting**: Gunakan user database yang hanya punya akses `SELECT` (read-only) ke tabel `users` di database TI Unima untuk keamanan.

Jangan gunakan user dengan privilege `UPDATE`, `DELETE`, atau `DROP`.
