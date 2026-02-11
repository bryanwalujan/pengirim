# Deployment Files

Folder ini berisi file-file yang dibutuhkan untuk deployment aplikasi e-service ke production server.

## Struktur Folder

```
deployment/
├── supervisor/
│   └── eservice-worker.conf    # Konfigurasi Supervisor untuk queue workers
├── deploy.sh                    # Script otomatis deployment
└── README.md                    # File ini
```

## File-File Penting

### 1. supervisor/eservice-worker.conf

File konfigurasi Supervisor untuk menjalankan Laravel queue workers.

**Cara Install:**
```bash
# Copy ke server production
sudo cp deployment/supervisor/eservice-worker.conf /etc/supervisor/conf.d/

# Sesuaikan path dan user di file tersebut
sudo nano /etc/supervisor/conf.d/eservice-worker.conf

# Update supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start eservice-worker:*
```

**Yang Perlu Disesuaikan:**
- Path aplikasi: `/var/www/eservice-app` → sesuaikan dengan path server Anda
- User: `www-data` → sesuaikan dengan user web server Anda (bisa nginx, apache, dll)
- Jumlah worker: `numprocs=2` → sesuaikan dengan kebutuhan

### 2. deploy.sh

Script bash untuk otomasi deployment.

**Cara Menggunakan:**
```bash
# Pertama kali, buat executable
chmod +x deployment/deploy.sh

# Jalankan deployment
sudo ./deployment/deploy.sh
```

**Script ini akan:**
1. Enable maintenance mode
2. Pull latest code dari Git
3. Install/update dependencies
4. Clear caches
5. Run migrations
6. Optimize application
7. Set permissions
8. **Restart queue workers** (PENTING!)
9. Disable maintenance mode

## Dokumentasi Lengkap

Lihat file `DEPLOYMENT.md` di root project untuk dokumentasi lengkap tentang:
- Setup Supervisor
- Deployment manual
- Monitoring
- Troubleshooting
- Backup strategy
- Security checklist

Atau lihat `.agent/workflows/setup-supervisor.md` untuk panduan detail setup Supervisor.

## Quick Reference

### Perintah Supervisor

```bash
# Status workers
sudo supervisorctl status eservice-worker:*

# Restart workers (WAJIB setelah deployment!)
sudo supervisorctl restart eservice-worker:*

# Stop workers
sudo supervisorctl stop eservice-worker:*

# Start workers
sudo supervisorctl start eservice-worker:*

# View logs
sudo tail -f /var/www/eservice-app/storage/logs/worker.log
```

### Perintah Queue

```bash
# Lihat failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry <job-id>

# Retry semua failed jobs
php artisan queue:retry all

# Flush failed jobs
php artisan queue:flush
```

## Catatan Penting

⚠️ **SELALU restart queue workers setelah deployment!**

Queue workers menyimpan aplikasi di memory. Jika tidak direstart, workers akan tetap menggunakan code lama.

```bash
sudo supervisorctl restart eservice-worker:*
```

## Fitur yang Menggunakan Queue

Sistem e-service menggunakan queue untuk:
1. **Email Undangan Seminar Proposal** - Notifikasi ke dosen pembahas dan mahasiswa
2. **Email Undangan Ujian Hasil** - Notifikasi ke dosen penguji dan mahasiswa
3. **Email Approval** - Notifikasi approval komisi proposal dan hasil
4. **Email Reset Password** - Email reset password untuk user

Semua email ini dikirim melalui queue untuk performa yang lebih baik.

## Support

Jika ada masalah saat deployment:
1. Cek log aplikasi: `storage/logs/laravel.log`
2. Cek log worker: `storage/logs/worker.log`
3. Cek log supervisor: `/var/log/supervisor/supervisord.log`
4. Cek status worker: `sudo supervisorctl status`
5. Review dokumentasi di `DEPLOYMENT.md`
