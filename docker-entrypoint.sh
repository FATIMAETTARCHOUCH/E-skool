#!/bin/sh
set -e

# Set default port if not provided by Railway
PORT="${PORT:-8080}"

# Dynamic Apache configuration for the Railway assigned port
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Recreate storage symlink in the container
php artisan storage:link --force || true

# Cache configurations for maximum performance in production
if [ "$APP_ENV" = "production" ]; then
    echo "Caching configuration for production..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
else
    echo "Clearing configuration cache for development/staging..."
    php artisan config:clear || true
    php artisan route:clear || true
    php artisan view:clear || true
fi

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force || true

# Execute Apache in the foreground
echo "Starting Apache web server on port ${PORT}..."
exec apache2-foreground
