#!/bin/bash
set -e

APP_DIR="/var/www/laravel-frankenphp"
RELEASES_DIR="$APP_DIR/releases"
STORAGE_DIR="$APP_DIR/storage"
BRANCH="main"
NOW=$(date +"%Y%m%d%H%M%S")
NEW_RELEASE="$RELEASES_DIR/$NOW"

echo "=== Deploy Laravel Release $NOW (branch: $BRANCH) ==="
echo "Script path: $(realpath "$0")"
echo "APP_DIR aktif: $APP_DIR"

mkdir -p "$APP_DIR" "$RELEASES_DIR" "$STORAGE_DIR"

if [ ! -L "$APP_DIR/current" ] && [ ! -e "$APP_DIR/current" ]; then
    echo "Symlink current belum ada, mencari release terakhir..."
    LAST_RELEASE=$(ls -1dt "$RELEASES_DIR"/*/ 2>/dev/null | head -n 1 | sed 's:/*$::')

    if [ -n "$LAST_RELEASE" ] && [ -d "$LAST_RELEASE" ]; then
        ln -sfn "$LAST_RELEASE" "$APP_DIR/current"
        echo "Symlink current dibuat -> $LAST_RELEASE"
    else
        echo "Tidak ada release sebelumnya, symlink akan dibuat setelah deploy"
    fi
fi

if [ -L "$APP_DIR/current" ]; then
    CURRENT_RELEASE=$(readlink -f "$APP_DIR/current")
    echo "Release aktif saat ini: $CURRENT_RELEASE"
else
    echo "Belum ada release aktif (deploy pertama kali)"
fi

git clone -b "$BRANCH" --single-branch https://github.com/agif1705/sikucur.git "$NEW_RELEASE"
cd "$NEW_RELEASE"

composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

echo "Mencari file .env..."
if [ -f "$APP_DIR/current/.env" ]; then
    cp "$APP_DIR/current/.env" "$NEW_RELEASE/.env"
    echo "File .env berhasil di-copy dari release sebelumnya"
elif [ -f "$APP_DIR/.env" ]; then
    cp "$APP_DIR/.env" "$NEW_RELEASE/.env"
    echo "File .env berhasil di-copy dari $APP_DIR/.env"
else
    echo "ERROR: File .env tidak ditemukan!"
    echo "Silakan buat file .env di $APP_DIR/.env atau $APP_DIR/current/.env"
    exit 1
fi

if ! grep -q "DB_CONNECTION=" "$NEW_RELEASE/.env" || ! grep -q "DB_DATABASE=" "$NEW_RELEASE/.env"; then
    echo "WARNING: File .env mungkin tidak lengkap (DB_CONNECTION atau DB_DATABASE tidak ditemukan)"
fi

rm -rf "$NEW_RELEASE/storage"
ln -s "$STORAGE_DIR" "$NEW_RELEASE/storage"

echo "Testing database connection..."
if php artisan db:show 2>/dev/null; then
    echo "Database connection berhasil"
else
    echo "WARNING: Tidak bisa connect ke database. Check konfigurasi .env!"
    echo "Deploy dilanjutkan, tapi migration dan cache mungkin gagal."
fi

echo "Running migrations..."
php artisan migrate --force || echo "WARNING: Migration gagal atau tidak ada perubahan"

if [ -f package.json ]; then
    echo "Building frontend assets..."

    if [ -f package-lock.json ]; then
        npm ci --include=dev
    else
        npm install --include=dev
    fi

    npm run build
    npm run filament:build
    npm prune --omit=dev || true
fi

echo "Clearing caches..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan cache:clear || true

echo "Building caches..."
php artisan config:cache || echo "WARNING: Config cache gagal"
php artisan route:cache || echo "WARNING: Route cache gagal"
php artisan view:cache || echo "WARNING: View cache gagal"
php artisan filament:cache-components || echo "WARNING: Filament cache gagal"
php artisan app:bersihkan || echo "WARNING: app:bersihkan gagal"

echo "Mengalihkan aplikasi ke release baru..."
rm -f "$APP_DIR/current"
ln -sfn "$NEW_RELEASE" "$APP_DIR/current"

if [ -L "$APP_DIR/current" ] && [ "$(readlink -f "$APP_DIR/current")" = "$NEW_RELEASE" ]; then
    echo "Symlink berhasil: $APP_DIR/current -> $NEW_RELEASE"
else
    echo "ERROR: Symlink gagal dibuat!"
    exit 1
fi

echo "Restarting FrankenPHP service..."
sudo systemctl restart laravel-frankenphp.service

if sudo systemctl is-active --quiet laravel-frankenphp.service; then
    echo "FrankenPHP berhasil di-restart"
else
    echo "WARNING: FrankenPHP mungkin gagal restart"
fi

echo "Membersihkan release lama..."
cd "$RELEASES_DIR"
OLD_RELEASES=$(ls -1dt */ | tail -n +6)
if [ -n "$OLD_RELEASES" ]; then
    echo "Menghapus release lama:"
    echo "$OLD_RELEASES"
    ls -1dt */ | tail -n +6 | xargs rm -rf || true
else
    echo "Tidak ada release lama yang perlu dihapus"
fi

echo ""
echo "=== Deploy selesai ==="
echo "Release baru: $NOW (branch: $BRANCH)"
echo "Lokasi: $NEW_RELEASE"
echo "Symlink aktif: $APP_DIR/current -> $(readlink -f "$APP_DIR/current")"
echo "Release tersimpan: $(ls -1t "$RELEASES_DIR" | wc -l)"
