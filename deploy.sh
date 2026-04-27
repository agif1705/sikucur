#!/bin/bash
# git checkout main
# git pull origin main        # update dulu biar sinkron
# git merge dev               # gabungkan perubahan dari dev
# git push origin main
set -e

APP_DIR="/var/www/sikucur"
RELEASES_DIR="$APP_DIR/releases"
STORAGE_DIR="$APP_DIR/storage"
BRANCH="main"   # ganti jadi 'dev' kalau mau branch dev
NOW=$(date +"%Y%m%d%H%M%S")
NEW_RELEASE="$RELEASES_DIR/$NOW"

echo "=== Deploy Laravel Release $NOW (branch: $BRANCH) ==="

# 0. Pastikan direktori yang diperlukan sudah ada cek
mkdir -p $APP_DIR
mkdir -p $RELEASES_DIR
mkdir -p $STORAGE_DIR

# Cek dan buat symlink current jika belum ada
if [ ! -L "$APP_DIR/current" ] && [ ! -e "$APP_DIR/current" ]; then
    echo "Symlink 'current' belum ada, mencari release terakhir..."
    LAST_RELEASE=$(ls -1dt $RELEASES_DIR/*/ 2>/dev/null | head -n 1 | sed 's:/*$::')
    
    if [ -n "$LAST_RELEASE" ] && [ -d "$LAST_RELEASE" ]; then
        ln -sfn $LAST_RELEASE $APP_DIR/current
        echo "✓ Symlink 'current' dibuat -> $LAST_RELEASE"
    else
        echo "Tidak ada release sebelumnya, symlink akan dibuat setelah deploy"
    fi
fi

# Tampilkan release yang sedang aktif sekarang
if [ -L "$APP_DIR/current" ]; then
    CURRENT_RELEASE=$(readlink -f $APP_DIR/current)
    echo "Release aktif saat ini: $CURRENT_RELEASE"
else
    echo "Belum ada release aktif (deploy pertama kali)"
fi

# 1. Clone dari git branch
git clone -b $BRANCH --single-branch https://github.com/agif1705/sikucur.git $NEW_RELEASE
cd $NEW_RELEASE

# 2. Install PHP dependencies dari composer.lock
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

# 3. Copy .env lama dengan validasi
echo "Mencari file .env..."
if [ -f "$APP_DIR/current/.env" ]; then
    cp $APP_DIR/current/.env $NEW_RELEASE/.env
    echo "✓ File .env berhasil di-copy dari release sebelumnya"
elif [ -f "$APP_DIR/.env" ]; then
    cp $APP_DIR/.env $NEW_RELEASE/.env
    echo "✓ File .env berhasil di-copy dari $APP_DIR/.env"
else
    echo "✗ ERROR: File .env tidak ditemukan!"
    echo "Silakan buat file .env di $APP_DIR/.env atau $APP_DIR/current/.env"
    exit 1
fi

# Validasi .env memiliki konfigurasi database yang benar
if ! grep -q "DB_CONNECTION=" $NEW_RELEASE/.env || ! grep -q "DB_DATABASE=" $NEW_RELEASE/.env; then
    echo "⚠ WARNING: File .env mungkin tidak lengkap (DB_CONNECTION atau DB_DATABASE tidak ditemukan)"
fi

# 4. Symlink storage
rm -rf $NEW_RELEASE/storage
ln -s $STORAGE_DIR $NEW_RELEASE/storage

# 5. Test database connection
echo "Testing database connection..."
if php artisan db:show 2>/dev/null; then
    echo "✓ Database connection berhasil"
else
    echo "⚠ WARNING: Tidak bisa connect ke database. Check konfigurasi .env!"
    echo "Deploy akan dilanjutkan, tapi migration dan cache mungkin gagal."
fi

# 6. Jalankan migration
echo "Running migrations..."
php artisan migrate --force || echo "⚠ Migration gagal atau tidak ada perubahan"

# 7. Build frontend (Vite + Tailwind)
if [ -f package.json ]; then
    echo "Building frontend assets..."

    if [ -f package-lock.json ]; then
        npm ci --include=dev
    else
        npm install --include=dev
    fi

    npm run build
    npm run filament:build

    # Node modules hanya dibutuhkan saat build, bukan runtime Laravel.
    npm prune --omit=dev || true
fi

# 8. Cache ulang
echo "Clearing caches..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan cache:clear || true

echo "Building caches..."
php artisan config:cache || echo "⚠ Config cache gagal"
php artisan route:cache || echo "⚠ Route cache gagal"
php artisan view:cache || echo "⚠ View cache gagal"
php artisan filament:cache-components || echo "⚠ Filament cache gagal"
php artisan app:bersihkan || echo "⚠ app:bersihkan gagal"

# 9. Update symlink current ke release terbaru
echo "Mengalihkan aplikasi ke release baru..."
rm -f $APP_DIR/current
ln -sfn $NEW_RELEASE $APP_DIR/current

# Verifikasi symlink berhasil dibuat
if [ -L "$APP_DIR/current" ] && [ "$(readlink -f $APP_DIR/current)" == "$NEW_RELEASE" ]; then
    echo "✓ Symlink berhasil: $APP_DIR/current -> $NEW_RELEASE"
else
    echo "✗ ERROR: Symlink gagal dibuat!"
    exit 1
fi

# 10. Restart FrankenPHP (agar menggunakan release baru)
echo "Restarting FrankenPHP service..."
sudo systemctl restart laravel-frankenphp.service

if sudo systemctl is-active --quiet laravel-frankenphp.service; then
    echo "✓ FrankenPHP berhasil di-restart"
else
    echo "✗ WARNING: FrankenPHP mungkin gagal restart"
fi

# 11. Cleanup release lama (hanya simpan 5 release terakhir)
echo "Membersihkan release lama..."
cd $RELEASES_DIR
OLD_RELEASES=$(ls -1dt */ | tail -n +6)
if [ -n "$OLD_RELEASES" ]; then
    echo "Menghapus release lama:"
    echo "$OLD_RELEASES"
    ls -1dt */ | tail -n +6 | xargs rm -rf || true
else
    echo "Tidak ada release lama yang perlu dihapus"
fi

echo ""
echo "=== Deploy selesai! ==="
echo "Release baru: $NOW (branch: $BRANCH)"
echo "Lokasi: $NEW_RELEASE"
echo "Symlink aktif: $APP_DIR/current -> $(readlink -f $APP_DIR/current)"
echo "Release yang tersimpan: $(ls -1t $RELEASES_DIR | wc -l) release"



