#!/bin/bash

# ============================================
# E-Service Deployment Script
# ============================================
# Script ini untuk deployment aplikasi e-service
# ke production server dengan Laravel Queue
# ============================================

set -e  # Exit on error

echo "=========================================="
echo "E-Service Deployment Script"
echo "=========================================="
echo ""

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Konfigurasi (sesuaikan dengan server Anda)
APP_DIR="/var/www/eservice-app"
PHP_BIN="php"
COMPOSER_BIN="composer"
SUPERVISOR_PROGRAM="eservice-worker"

# Fungsi untuk print dengan warna
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_info() {
    echo -e "→ $1"
}

# Cek apakah script dijalankan dari direktori yang benar
if [ ! -f "$APP_DIR/artisan" ]; then
    print_error "Error: artisan file tidak ditemukan di $APP_DIR"
    print_info "Pastikan APP_DIR di script ini sudah benar"
    exit 1
fi

cd $APP_DIR

echo ""
print_info "Step 1: Enable Maintenance Mode"
$PHP_BIN artisan down || true
print_success "Maintenance mode enabled"

echo ""
print_info "Step 2: Pull Latest Code"
git pull origin main
print_success "Code updated"

echo ""
print_info "Step 3: Install/Update Dependencies"
$COMPOSER_BIN install --no-dev --optimize-autoloader --no-interaction
print_success "Dependencies updated"

echo ""
print_info "Step 4: Clear Caches"
$PHP_BIN artisan config:clear
$PHP_BIN artisan cache:clear
$PHP_BIN artisan route:clear
$PHP_BIN artisan view:clear
print_success "Caches cleared"

echo ""
print_info "Step 5: Run Migrations"
$PHP_BIN artisan migrate --force
print_success "Migrations completed"

echo ""
print_info "Step 6: Optimize Application"
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache
print_success "Application optimized"

echo ""
print_info "Step 7: Set Permissions"
chown -R www-data:www-data $APP_DIR/storage
chown -R www-data:www-data $APP_DIR/bootstrap/cache
chmod -R 775 $APP_DIR/storage
chmod -R 775 $APP_DIR/bootstrap/cache
print_success "Permissions set"

echo ""
print_info "Step 8: Restart Queue Workers (CRITICAL!)"
if command -v supervisorctl &> /dev/null; then
    supervisorctl restart $SUPERVISOR_PROGRAM:*
    print_success "Queue workers restarted via Supervisor"
    
    # Cek status worker
    sleep 2
    if supervisorctl status $SUPERVISOR_PROGRAM:* | grep -q "RUNNING"; then
        print_success "All workers are running"
    else
        print_warning "Some workers might not be running. Check status manually:"
        print_info "sudo supervisorctl status"
    fi
else
    print_warning "Supervisor not found. Queue workers NOT restarted!"
    print_warning "If you're using queue, restart workers manually:"
    print_info "sudo supervisorctl restart $SUPERVISOR_PROGRAM:*"
fi

echo ""
print_info "Step 9: Disable Maintenance Mode"
$PHP_BIN artisan up
print_success "Application is now live"

echo ""
print_success "=========================================="
print_success "Deployment Completed Successfully!"
print_success "=========================================="
echo ""

# Tampilkan informasi penting
print_info "Post-Deployment Checklist:"
echo "  1. Cek aplikasi di browser"
echo "  2. Test fitur email notification"
echo "  3. Monitor queue workers: sudo supervisorctl status"
echo "  4. Monitor logs: tail -f $APP_DIR/storage/logs/laravel.log"
echo "  5. Monitor worker logs: tail -f $APP_DIR/storage/logs/worker.log"
echo ""

# Tampilkan status queue
print_info "Current Queue Status:"
$PHP_BIN artisan queue:failed | head -n 10

echo ""
print_success "Done! 🚀"
