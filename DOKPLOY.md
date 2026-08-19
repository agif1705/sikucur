# Dokploy deploy

## Pakai

- `Dockerfile`
- `dokploy.compose.yml`

## Service

- `app` web
- `queue` worker
- `scheduler` cron tiap 1 menit

## Port

- `8000`

## Env wajib

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://domain-anda`
- `APP_KEY=base64:...`
- `DB_CONNECTION`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `REDIS_CLIENT=phpredis`
- `REDIS_HOST`
- `REDIS_PORT`
- `CACHE_STORE=redis`
- `SESSION_DRIVER=redis`
- `QUEUE_CONNECTION=redis`
- `BROADCAST_CONNECTION=log` atau `reverb`
- `FILESYSTEM_DISK=public`

## Catatan

- `storage` dan `bootstrap/cache` sudah dipersist volume.
- `queue` dan `scheduler` wajib hidup kalau fitur WA, broadcast, atau job dipakai.
- Kalau Dokploy pakai domain proxy, expose publik cukup lewat ingress, bukan port manual.
