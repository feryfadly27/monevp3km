#!/bin/sh
set -e

# Generate APP_KEY jika belum ada
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# Pastikan file database.sqlite ada
if [ ! -f "database/database.sqlite" ]; then
    echo "Creating SQLite database..."
    touch database/database.sqlite
fi

# Jalankan migration
echo "Running migrations..."
php artisan migrate --force

# Jalankan seeder jika database kosong
echo "Running seeders..."
php artisan db:seed --force 2>/dev/null || echo "Seeding skipped (already seeded)"

# Bersihkan dan optimalkan cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Laravel on port 8000..."
exec php artisan serve --host=0.0.0.0 --port=8000
