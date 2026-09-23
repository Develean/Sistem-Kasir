#!/bin/bash
set -e

# Sesuaikan port Apache dengan environment variable PORT dari Render (biasanya 10000)
RENDER_PORT=${PORT:-80}
echo "Starting Apache on port $RENDER_PORT..."
sed -i "s/80/$RENDER_PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Jika menggunakan SQLite, pastikan filenya ada
if [ "$DB_CONNECTION" = "sqlite" ]; then
    mkdir -p /var/www/html/database
    touch /var/www/html/database/database.sqlite
    chown -R www-data:www-data /var/www/html/database
fi

# Generate key jika belum ada
if [ -z "$APP_KEY" ]; then
    echo "Warning: APP_KEY is empty. Generating temporary key..."
    php artisan key:generate --force || true
fi

# Jalankan migrasi database jika database terkonfigurasi
echo "Running database migrations..."
php artisan migrate --force || echo "Database migration skipped or failed (check DB credentials in Render)"

# Cache Laravel configuration and routes for production speed
php artisan config:cache || true
php artisan route:cache || true

exec apache2-foreground
