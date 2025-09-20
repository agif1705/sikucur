#!/bin/bash
set -e

# Path project
PROJECT_DIR="/var/www/sikucur"

echo "🔄 Mulai deploy..."

# Hapus folder lama jika ada
if [ -d "$PROJECT_DIR" ]; then
    echo "🗑️ Menghapus folder lama $PROJECT_DIR ..."
    rm -rf "$PROJECT_DIR"
fi

# Buat folder base
mkdir -p /var/www
cd /var/www

# Clone project baru
echo "⬇️ Clone repository..."
git clone https://github.com/agif1705/sikucur.git
cd sikucur

# Set permission
echo "🔧 Setting permission..."
chown -R www-data:www-data "$PROJECT_DIR"
chmod -R 755 "$PROJECT_DIR"
chmod -R 775 "$PROJECT_DIR/storage"
chmod -R 775 "$PROJECT_DIR/bootstrap/cache"
chmod -R 775 "$PROJECT_DIR/database"

# Install dependency
echo "📦 Install composer dependencies..."
composer install --optimize-autoloader --no-dev

echo "📦 Install npm dependencies..."
npm install
npm run build

# Laravel optimize
echo "⚡ Optimize Laravel..."
php artisan app:bersihkan || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Deploy selesai!"
