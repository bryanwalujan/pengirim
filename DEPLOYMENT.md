# Deployment Guide - E-Service Application

## Prerequisites

- Server Linux (Ubuntu/Debian/CentOS)
- PHP 8.1+
- Composer
- MySQL/MariaDB
- Nginx/Apache
- Supervisor
- Git

## Quick Deployment Steps

### 1. Setup Supervisor (One-time Setup)

```bash
# Install Supervisor
sudo apt-get update
sudo apt-get install supervisor

# Copy konfigurasi
sudo cp deployment/supervisor/eservice-worker.conf /etc/supervisor/conf.d/

# Edit konfigurasi sesuai server Anda
sudo nano /etc/supervisor/conf.d/eservice-worker.conf

# Update Supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start eservice-worker:*

# Verify
sudo supervisorctl status
```

### 2. Deploy Application

#### Manual Deployment

```bash
cd /var/www/eservice-app

# Enable maintenance mode
php artisan down

# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run migrations
php artisan migrate --force

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# IMPORTANT: Restart queue workers!
sudo supervisorctl restart eservice-worker:*

# Disable maintenance mode
php artisan up
```

#### Automated Deployment

```bash
# Make script executable (first time only)
chmod +x deployment/deploy.sh

# Run deployment script
sudo ./deployment/deploy.sh
```

## Environment Configuration

Pastikan file `.env` di production sudah dikonfigurasi dengan benar:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eservice_production
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

# Queue
QUEUE_CONNECTION=database

# Mail (Production SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@domain.com"
MAIL_FROM_NAME="E-Service Teknik Informatika"

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
```

## Monitoring

### Check Queue Workers

```bash
# Status workers
sudo supervisorctl status eservice-worker:*

# Restart workers
sudo supervisorctl restart eservice-worker:*

# View worker logs
sudo tail -f /var/www/eservice-app/storage/logs/worker.log
```

### Check Failed Jobs

```bash
# List failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry <job-id>

# Retry all
php artisan queue:retry all
```

### Application Logs

```bash
# Laravel logs
sudo tail -f /var/www/eservice-app/storage/logs/laravel.log

# Nginx access logs
sudo tail -f /var/log/nginx/access.log

# Nginx error logs
sudo tail -f /var/log/nginx/error.log
```

## Troubleshooting

### Workers Not Running

```bash
# Check supervisor logs
sudo tail -f /var/log/supervisor/supervisord.log

# Check permissions
sudo chown -R www-data:www-data /var/www/eservice-app/storage
sudo chmod -R 775 /var/www/eservice-app/storage
```

### Email Not Sending

```bash
# Test queue manually
php artisan queue:work --once

# Check failed jobs
php artisan queue:failed

# Test email configuration
php artisan tinker
>>> Mail::raw('Test', function($msg) { $msg->to('test@example.com')->subject('Test'); });
```

## Important Notes

⚠️ **ALWAYS restart queue workers after deployment!**

Queue workers load the application into memory. If you don't restart them, they will continue using the old code.

```bash
sudo supervisorctl restart eservice-worker:*
```

## Security Checklist

- [ ] `.env` file permissions: `chmod 600 .env`
- [ ] Storage permissions: `chmod -R 775 storage`
- [ ] Database credentials are secure
- [ ] `APP_DEBUG=false` in production
- [ ] HTTPS is enabled
- [ ] Firewall is configured
- [ ] Regular backups are scheduled

## Backup Strategy

### Database Backup

```bash
# Manual backup
mysqldump -u username -p eservice_production > backup_$(date +%Y%m%d_%H%M%S).sql

# Automated daily backup (add to crontab)
0 2 * * * mysqldump -u username -p eservice_production > /backups/eservice_$(date +\%Y\%m\%d).sql
```

### Application Backup

```bash
# Backup storage folder
tar -czf storage_backup_$(date +%Y%m%d).tar.gz storage/

# Full application backup
tar -czf eservice_backup_$(date +%Y%m%d).tar.gz --exclude='node_modules' --exclude='vendor' /var/www/eservice-app
```

## Performance Optimization

### Enable OPcache

Edit `/etc/php/8.1/fpm/php.ini`:

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
```

### Queue Configuration

For better performance, consider using Redis:

```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

Update supervisor config:
```ini
command=php /var/www/eservice-app/artisan queue:work redis --sleep=3 --tries=3
```

## Support

For issues or questions:
- Check logs: `storage/logs/laravel.log`
- Check worker logs: `storage/logs/worker.log`
- Check supervisor logs: `/var/log/supervisor/supervisord.log`
- Review this documentation
- Contact system administrator
