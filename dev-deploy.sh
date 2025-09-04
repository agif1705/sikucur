#!/bin/bash ini dev
# git checkout main
# git pull origin main        # update dulu biar sinkron
# git merge dev               # gabungkan perubahan dari dev
# git push origin main
set -e

APP_DIR="/var/www/sikucur"
RELEASES_DIR="$APP_DIR/releases"
STORAGE_DIR="$APP_DIR/storage"
BRANCH="dev"   # ganti jadi 'dev' kalau mau branch dev
NOW=$(date +"%Y%m%d%H%M%S")
NEW_RELEASE="$RELEASES_DIR/$NOW"

echo "=== Deploy Laravel Release $NOW (branch: $BRANCH) ==="

# 1. Clone dari git branch
git clone -b $BRANCH --single-branch https://github.com/agif1705/sikucur.git $NEW_RELEASE
cd $NEW_RELEASE

# 2. Install dependencies
composer install --no-dev --optimize-autoloader

# 3. Copy .env lama
if [ -f "$APP_DIR/current/.env" ]; then
    cp $APP_DIR/current/.env $NEW_RELEASE/.env
fi

# 4. Symlink storage
rm -rf $NEW_RELEASE/storage
ln -s $STORAGE_DIR $NEW_RELEASE/storage

# 5. Jalankan migration
php artisan migrate --force || true

# 6. Cache ulang
php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan cache:clear
php artisan config:cache && php artisan route:cache && php artisan view:cache

# 7. Update symlink current
ln -sfn $NEW_RELEASE $APP_DIR/current

# 8. Build frontend (Vite)
if [ -f package.json ]; then
    npm install --omit=dev
    npm run build
fi

# 9. Restart FrankenPHP
sudo systemctl restart laravel-frankenphp.service

# 10. Cleanup release lama
cd $RELEASES_DIR
ls -1dt */ | tail -n +6 | xargs rm -rf || true

echo "=== Deploy selesai! Release $NOW aktif dari branch $BRANCH ==="
