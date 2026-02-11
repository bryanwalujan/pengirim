# E-Service Production Deployment Checklist

## Pre-Deployment

### Server Setup
- [ ] Server Linux (Ubuntu/Debian) sudah ready
- [ ] PHP 8.1+ terinstall
- [ ] Composer terinstall
- [ ] MySQL/MariaDB terinstall dan running
- [ ] Nginx/Apache terinstall dan terkonfigurasi
- [ ] Git terinstall
- [ ] SSL Certificate terinstall (untuk HTTPS)
- [ ] Firewall dikonfigurasi (port 80, 443, 22)

### Database Setup
- [ ] Database production sudah dibuat
- [ ] User database dengan privileges yang sesuai
- [ ] Password database yang kuat
- [ ] Backup database development (jika ada data yang perlu dimigrate)

### Email Setup
- [ ] SMTP server/service sudah ready (Gmail/SendGrid/Mailgun)
- [ ] Email credentials sudah disiapkan
- [ ] App-specific password sudah dibuat (jika pakai Gmail)
- [ ] Test kirim email manual sudah berhasil

## Installation

### 1. Clone Repository
```bash
- [ ] cd /var/www
- [ ] git clone <repository-url> eservice-app
- [ ] cd eservice-app
```

### 2. Install Dependencies
```bash
- [ ] composer install --no-dev --optimize-autoloader
- [ ] npm install
- [ ] npm run build
```

### 3. Environment Configuration
```bash
- [ ] cp .env.production.example .env
- [ ] nano .env
```

Edit `.env` dan isi:
- [ ] APP_ENV=production
- [ ] APP_DEBUG=false
- [ ] APP_URL (dengan https://)
- [ ] Database credentials
- [ ] Mail credentials
- [ ] TI Unima API credentials
- [ ] QUEUE_CONNECTION=database

```bash
- [ ] php artisan key:generate
- [ ] chmod 600 .env
```

### 4. Database Migration
```bash
- [ ] php artisan migrate --force
- [ ] php artisan db:seed --force (jika ada seeder)
```

### 5. Storage & Permissions
```bash
- [ ] php artisan storage:link
- [ ] chown -R www-data:www-data /var/www/eservice-app
- [ ] chmod -R 775 storage
- [ ] chmod -R 775 bootstrap/cache
```

### 6. Optimization
```bash
- [ ] php artisan config:cache
- [ ] php artisan route:cache
- [ ] php artisan view:cache
```

## Supervisor Setup (CRITICAL untuk Queue)

### 1. Install Supervisor
```bash
- [ ] sudo apt-get update
- [ ] sudo apt-get install supervisor
- [ ] sudo systemctl enable supervisor
- [ ] sudo systemctl start supervisor
```

### 2. Configure Supervisor
```bash
- [ ] sudo cp deployment/supervisor/eservice-worker.conf /etc/supervisor/conf.d/
- [ ] sudo nano /etc/supervisor/conf.d/eservice-worker.conf
```

Edit konfigurasi:
- [ ] Sesuaikan path: /var/www/eservice-app
- [ ] Sesuaikan user: www-data (atau user web server Anda)
- [ ] Sesuaikan numprocs (jumlah worker)

```bash
- [ ] sudo supervisorctl reread
- [ ] sudo supervisorctl update
- [ ] sudo supervisorctl start eservice-worker:*
```

### 3. Verify Supervisor
```bash
- [ ] sudo supervisorctl status
```
Pastikan semua worker status: RUNNING

## Web Server Configuration

### Nginx Configuration
```bash
- [ ] sudo nano /etc/nginx/sites-available/eservice
```

Contoh konfigurasi minimal:
```nginx
server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com;
    root /var/www/eservice-app/public;

    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
- [ ] sudo ln -s /etc/nginx/sites-available/eservice /etc/nginx/sites-enabled/
- [ ] sudo nginx -t
- [ ] sudo systemctl reload nginx
```

## Testing

### Application Testing
- [ ] Akses aplikasi via browser
- [ ] Test login
- [ ] Test register (jika ada)
- [ ] Test reset password
- [ ] Test upload file
- [ ] Test generate PDF

### Queue Testing
- [ ] Test submit jadwal seminar proposal
- [ ] Cek apakah email terkirim
- [ ] Cek log worker: `tail -f storage/logs/worker.log`
- [ ] Cek failed jobs: `php artisan queue:failed`

### Performance Testing
- [ ] Cek response time halaman
- [ ] Cek memory usage: `free -h`
- [ ] Cek disk usage: `df -h`

## Monitoring Setup

### Log Files
```bash
- [ ] tail -f /var/www/eservice-app/storage/logs/laravel.log
- [ ] tail -f /var/www/eservice-app/storage/logs/worker.log
- [ ] tail -f /var/log/nginx/error.log
- [ ] tail -f /var/log/supervisor/supervisord.log
```

### Cron Jobs (Opsional)
Jika ada scheduled tasks:
```bash
- [ ] crontab -e
```
Tambahkan:
```
* * * * * cd /var/www/eservice-app && php artisan schedule:run >> /dev/null 2>&1
```

## Backup Setup

### Database Backup
```bash
- [ ] Setup automated database backup
```

Contoh cron job untuk backup harian:
```bash
0 2 * * * mysqldump -u username -p'password' eservice_production > /backups/eservice_$(date +\%Y\%m\%d).sql
```

### Application Backup
```bash
- [ ] Setup automated application backup (storage folder)
```

## Security Checklist

- [ ] `.env` file permissions: `chmod 600 .env`
- [ ] Storage permissions: `chmod -R 775 storage`
- [ ] Database password yang kuat
- [ ] `APP_DEBUG=false`
- [ ] HTTPS enabled
- [ ] Firewall configured
- [ ] SSH key-based authentication
- [ ] Disable root SSH login
- [ ] Regular security updates: `apt-get update && apt-get upgrade`

## Documentation

- [ ] Update README.md dengan informasi production
- [ ] Dokumentasi credentials (simpan di tempat aman)
- [ ] Dokumentasi server configuration
- [ ] Dokumentasi deployment process untuk tim

## Post-Deployment

### Immediate Actions
- [ ] Announce deployment ke tim
- [ ] Monitor logs selama 1 jam pertama
- [ ] Test semua fitur critical
- [ ] Verify email notifications terkirim

### Regular Monitoring (Harian)
- [ ] Cek status supervisor: `sudo supervisorctl status`
- [ ] Cek failed jobs: `php artisan queue:failed`
- [ ] Cek disk space: `df -h`
- [ ] Review error logs

### Weekly Tasks
- [ ] Review application logs
- [ ] Check database size
- [ ] Verify backups
- [ ] Update dependencies (jika ada security patches)

## Rollback Plan

Jika terjadi masalah:

```bash
- [ ] Enable maintenance mode: php artisan down
- [ ] Git checkout ke commit sebelumnya: git checkout <commit-hash>
- [ ] Composer install: composer install --no-dev
- [ ] Clear caches: php artisan config:clear
- [ ] Restart workers: sudo supervisorctl restart eservice-worker:*
- [ ] Disable maintenance: php artisan up
```

## Contact Information

**System Administrator:**
- Name: ___________________
- Email: ___________________
- Phone: ___________________

**Developer:**
- Name: ___________________
- Email: ___________________
- Phone: ___________________

**Hosting Provider:**
- Provider: ___________________
- Support: ___________________

---

**Deployment Date:** ___________________

**Deployed By:** ___________________

**Notes:**
_______________________________________________
_______________________________________________
_______________________________________________
