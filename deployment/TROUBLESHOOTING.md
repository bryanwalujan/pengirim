# Troubleshooting Guide - E-Service Queue & Email

## Queue Workers Issues

### Problem: Workers tidak running

**Symptoms:**
- `sudo supervisorctl status` menunjukkan status STOPPED atau FATAL
- Email tidak terkirim
- Jobs menumpuk di database

**Solutions:**

1. **Cek log supervisor:**
```bash
sudo tail -f /var/log/supervisor/supervisord.log
```

2. **Cek permission storage:**
```bash
sudo chown -R www-data:www-data /var/www/eservice-app/storage
sudo chmod -R 775 /var/www/eservice-app/storage
```

3. **Cek apakah PHP path benar:**
```bash
which php
# Update path di /etc/supervisor/conf.d/eservice-worker.conf jika perlu
```

4. **Restart supervisor:**
```bash
sudo systemctl restart supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start eservice-worker:*
```

### Problem: Workers running tapi email tidak terkirim

**Symptoms:**
- Workers status RUNNING
- Tapi email tidak sampai
- Jobs di tabel `jobs` tidak berkurang

**Solutions:**

1. **Cek worker logs:**
```bash
tail -f /var/www/eservice-app/storage/logs/worker.log
```

2. **Cek failed jobs:**
```bash
php artisan queue:failed
```

3. **Test email configuration:**
```bash
php artisan tinker
>>> Mail::raw('Test email', function($msg) { $msg->to('your-email@example.com')->subject('Test'); });
>>> exit
```

4. **Cek .env mail configuration:**
```bash
cat .env | grep MAIL
```

Pastikan:
- MAIL_HOST benar
- MAIL_PORT benar (587 untuk TLS, 465 untuk SSL)
- MAIL_USERNAME benar
- MAIL_PASSWORD benar (app-specific password untuk Gmail)
- MAIL_ENCRYPTION benar (tls atau ssl)

5. **Test queue manually:**
```bash
php artisan queue:work --once
```

### Problem: Workers crash atau restart terus-menerus

**Symptoms:**
- Workers status berubah-ubah antara RUNNING dan STARTING
- Log menunjukkan error berulang

**Solutions:**

1. **Cek memory:**
```bash
free -h
```

2. **Tambahkan memory limit di supervisor config:**
```ini
command=php -d memory_limit=512M /var/www/eservice-app/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
```

3. **Cek error di Laravel log:**
```bash
tail -f /var/www/eservice-app/storage/logs/laravel.log
```

4. **Reduce jumlah workers:**
Edit `/etc/supervisor/conf.d/eservice-worker.conf`:
```ini
numprocs=1  # Kurangi dari 2 ke 1
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
```

### Problem: Jobs stuck di queue

**Symptoms:**
- Jobs ada di tabel `jobs` tapi tidak diproses
- Workers running normal

**Solutions:**

1. **Cek apakah ada jobs yang timeout:**
```sql
SELECT * FROM jobs WHERE available_at < UNIX_TIMESTAMP();
```

2. **Clear dan restart:**
```bash
# Backup jobs dulu
mysqldump -u username -p eservice_production jobs > jobs_backup.sql

# Truncate jobs table (HATI-HATI!)
php artisan tinker
>>> DB::table('jobs')->truncate();
>>> exit

# Restart workers
sudo supervisorctl restart eservice-worker:*
```

3. **Retry failed jobs:**
```bash
php artisan queue:retry all
```

## Email Issues

### Problem: Email masuk spam

**Solutions:**

1. **Setup SPF record** di DNS:
```
v=spf1 include:_spf.google.com ~all
```

2. **Setup DKIM** (jika menggunakan Gmail/Google Workspace)

3. **Setup DMARC record:**
```
v=DMARC1; p=none; rua=mailto:admin@yourdomain.com
```

4. **Gunakan domain email yang sama dengan APP_URL**

### Problem: Gmail blocking/rejecting emails

**Solutions:**

1. **Gunakan App-Specific Password:**
   - Buka Google Account Settings
   - Security → 2-Step Verification
   - App passwords → Generate new password
   - Gunakan password ini di MAIL_PASSWORD

2. **Enable "Less secure app access"** (tidak direkomendasikan)

3. **Gunakan OAuth2** (lebih aman, tapi lebih kompleks)

4. **Alternatif: Gunakan email service seperti:**
   - SendGrid
   - Mailgun
   - Amazon SES
   - Mailtrap (untuk testing)

### Problem: Email delay/lambat terkirim

**Solutions:**

1. **Cek jumlah workers:**
```bash
sudo supervisorctl status eservice-worker:*
```

Tambah workers jika perlu:
```ini
numprocs=4  # Increase dari 2 ke 4
```

2. **Gunakan Redis untuk queue** (lebih cepat dari database):
```env
QUEUE_CONNECTION=redis
```

3. **Reduce sleep time:**
```ini
command=php /var/www/eservice-app/artisan queue:work database --sleep=1
```

## Database Issues

### Problem: Tabel jobs terlalu besar

**Solutions:**

1. **Cek ukuran tabel:**
```sql
SELECT 
    table_name AS 'Table',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.TABLES
WHERE table_schema = 'eservice_production'
    AND table_name = 'jobs';
```

2. **Clear old jobs:**
```bash
php artisan queue:flush
```

3. **Setup automatic cleanup** (tambahkan ke cron):
```bash
# Cleanup jobs older than 7 days
0 0 * * * cd /var/www/eservice-app && php artisan queue:flush
```

### Problem: Failed jobs menumpuk

**Solutions:**

1. **Review failed jobs:**
```bash
php artisan queue:failed
```

2. **Retry specific job:**
```bash
php artisan queue:retry <job-id>
```

3. **Retry all failed jobs:**
```bash
php artisan queue:retry all
```

4. **Clear failed jobs setelah direview:**
```bash
php artisan queue:flush
```

## Performance Issues

### Problem: Queue processing lambat

**Solutions:**

1. **Increase workers:**
```ini
numprocs=4  # atau lebih, sesuai server capacity
```

2. **Use Redis instead of database:**
```env
QUEUE_CONNECTION=redis
```

3. **Optimize database:**
```sql
-- Add index to jobs table
ALTER TABLE jobs ADD INDEX jobs_queue_index (queue);
```

4. **Enable OPcache:**
Edit `/etc/php/8.1/fpm/php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=10000
```

### Problem: Server overload saat kirim banyak email

**Solutions:**

1. **Limit concurrent workers:**
```ini
numprocs=2  # Jangan terlalu banyak
```

2. **Add rate limiting di code:**
```php
// Di notification class
public function viaQueues()
{
    return [
        'mail' => 'emails',  // Separate queue untuk email
    ];
}
```

3. **Process email queue dengan priority lebih rendah:**
```bash
php artisan queue:work --queue=default,emails
```

## Common Error Messages

### "Class 'Redis' not found"

**Solution:**
```bash
sudo apt-get install php-redis
sudo systemctl restart php8.1-fpm
sudo supervisorctl restart eservice-worker:*
```

### "SQLSTATE[HY000]: General error: 2006 MySQL server has gone away"

**Solution:**

1. **Increase MySQL timeout:**
Edit `/etc/mysql/mysql.conf.d/mysqld.cnf`:
```ini
wait_timeout = 28800
max_allowed_packet = 64M
```

```bash
sudo systemctl restart mysql
```

2. **Add reconnect in queue config:**
Edit `config/queue.php`:
```php
'database' => [
    'driver' => 'database',
    'connection' => env('DB_QUEUE_CONNECTION'),
    'table' => env('DB_QUEUE_TABLE', 'jobs'),
    'queue' => env('DB_QUEUE', 'default'),
    'retry_after' => (int) env('DB_QUEUE_RETRY_AFTER', 90),
    'after_commit' => false,
],
```

### "Maximum execution time exceeded"

**Solution:**

1. **Increase timeout di supervisor:**
```ini
command=php /var/www/eservice-app/artisan queue:work database --timeout=120
```

2. **Increase PHP max_execution_time:**
Edit `/etc/php/8.1/cli/php.ini`:
```ini
max_execution_time = 120
```

## Monitoring Commands

### Quick Health Check

```bash
# Check workers
sudo supervisorctl status eservice-worker:*

# Check pending jobs
mysql -u username -p -e "SELECT COUNT(*) as pending_jobs FROM eservice_production.jobs;"

# Check failed jobs
php artisan queue:failed | wc -l

# Check disk space
df -h

# Check memory
free -h

# Check recent errors
tail -n 50 /var/www/eservice-app/storage/logs/laravel.log | grep ERROR
```

### Detailed Monitoring

```bash
# Watch worker logs in real-time
tail -f /var/www/eservice-app/storage/logs/worker.log

# Watch Laravel logs
tail -f /var/www/eservice-app/storage/logs/laravel.log

# Watch supervisor logs
sudo tail -f /var/log/supervisor/supervisord.log

# Watch nginx error logs
sudo tail -f /var/log/nginx/error.log

# Monitor system resources
htop
```

## Emergency Procedures

### Complete Queue Reset

**WARNING: This will delete all pending jobs!**

```bash
# 1. Stop workers
sudo supervisorctl stop eservice-worker:*

# 2. Backup database
mysqldump -u username -p eservice_production > backup_$(date +%Y%m%d_%H%M%S).sql

# 3. Clear jobs
php artisan tinker
>>> DB::table('jobs')->truncate();
>>> DB::table('failed_jobs')->truncate();
>>> exit

# 4. Clear cache
php artisan cache:clear
php artisan config:clear

# 5. Restart workers
sudo supervisorctl start eservice-worker:*

# 6. Verify
sudo supervisorctl status
```

### Complete System Restart

```bash
# 1. Stop workers
sudo supervisorctl stop eservice-worker:*

# 2. Restart services
sudo systemctl restart mysql
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx
sudo systemctl restart supervisor

# 3. Start workers
sudo supervisorctl start eservice-worker:*

# 4. Verify all services
sudo systemctl status mysql
sudo systemctl status php8.1-fpm
sudo systemctl status nginx
sudo supervisorctl status
```

## Getting Help

1. **Check logs first:**
   - Worker log: `storage/logs/worker.log`
   - Laravel log: `storage/logs/laravel.log`
   - Supervisor log: `/var/log/supervisor/supervisord.log`

2. **Search error message** di Google atau Stack Overflow

3. **Check Laravel documentation:**
   - https://laravel.com/docs/queues
   - https://laravel.com/docs/mail

4. **Contact system administrator** dengan informasi:
   - Error message lengkap
   - Log files
   - Steps to reproduce
   - Server environment info
