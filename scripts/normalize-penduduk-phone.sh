#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${1:-/var/www/laravel-frankenphp/current}"

if [ ! -d "$APP_DIR" ]; then
  echo "ERROR: Directory tidak ditemukan: $APP_DIR"
  exit 1
fi

cd "$APP_DIR"

php artisan tinker --execute="
\$updated = 0;
App\\Models\\Penduduk::query()->whereNotNull('no_hp')->chunkById(200, function (\$rows) use (&\$updated) {
    foreach (\$rows as \$row) {
        \$original = (string) \$row->no_hp;
        \$digits = preg_replace('/\D+/', '', \$original) ?: '';
        if (! \$digits) {
            continue;
        }
        if (str_starts_with(\$digits, '0')) {
            \$digits = '62'.substr(\$digits, 1);
        } elseif (! str_starts_with(\$digits, '62') && str_starts_with(\$digits, '8')) {
            \$digits = '62'.\$digits;
        }
        if (\$digits !== \$original) {
            \$row->no_hp = \$digits;
            \$row->save();
            \$updated++;
        }
    }
});
echo 'UPDATED='.\$updated.PHP_EOL;
"
