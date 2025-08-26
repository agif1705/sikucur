#!/bin/bash
set -e

APP_DIR="/var/www/laravel-frankenphp"
RELEASES_DIR="$APP_DIR/releases"
CURRENT_LINK="$APP_DIR/current"

echo "=== Rollback Laravel Release ==="

# Pastikan symlink current ada
if [ ! -L "$CURRENT_LINK" ]; then
    echo "❌ Symlink 'current' tidak ditemukan. Rollback dibatalkan."
    exit 1
fi

CURRENT_RELEASE=$(readlink -f $CURRENT_LINK)
echo "Current release: $CURRENT_RELEASE"

# Cari release sebelumnya (skip release yang sedang aktif)
PREVIOUS_RELEASE=$(ls -1dt $RELEASES_DIR/* | grep -v "$CURRENT_RELEASE" | head -n 1)

if [ -z "$PREVIOUS_RELEASE" ]; then
    echo "❌ Tidak ada release sebelumnya untuk rollback."
    exit 1
fi

echo "Rollback ke: $PREVIOUS_RELEASE"

# Update symlink
ln -sfn $PREVIOUS_RELEASE $CURRENT_LINK

# Restart FrankenPHP
sudo systemctl restart laravel-frankenphp.service

echo "✅ Rollback selesai! Sekarang aktif: $PREVIOUS_RELEASE"
