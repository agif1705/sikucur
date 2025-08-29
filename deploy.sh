#!/bin/bash
set -e

APP_DIR="/var/www/laravel-frankenphp"
RELEASES_DIR="$APP_DIR/releases"
STORAGE_DIR="$APP_DIR/storage"
NOW=$(date +"%Y%m%d%H%M%S")
NEW_RELEASE="$RELEASES_DIR/$NOW"

echo "=== Deploy Laravel Release $NOW ==="

# 1. Buat folder release baru
mkdir -p $NEW_RELEASE

# 2. Copy source dari dev (exclude vendor, .git, storage)
rsync -av --exclude 'vendor' --exclude '.git' --exclude 'storage' $APP_DIR/ $NEW_RELEASE/

cd $NEW_RELEASE

# 3. Install dependencies (PHP)
composer install --no-dev --optimize-autoloader

# 4. Copy .env lama
if [ -f "$APP_DIR/current/.env" ]; then
    cp $APP_DIR/current/.env $NEW_RELEASE/.env
fi

# 5. Symlink storage
rm -rf $NEW_RELEASE/storage
ln -s $STORAGE_DIR $NEW_RELEASE/storage


# 7. Clear & cache config/route/view
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Update symlink current (baru update setelah semua siap)
ln -sfn $NEW_RELEASE $APP_DIR/current

# 9. Build frontend (Vite) setelah current aktif
if [ -f package.json ]; then
    echo "=== Build frontend assets ==="
    npm install --omit=dev
    npm run build
fi

# 10. Restart FrankenPHP service
sudo systemctl restart laravel-frankenphp.service

# 11. Cleanup release lama (simpan 5 terbaru)
cd $RELEASES_DIR
ls -1dt */ | tail -n +6 | xargs rm -rf || true

echo "=== Deploy selesai! Release $NOW aktif ==="
