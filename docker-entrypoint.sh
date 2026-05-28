#!/bin/sh
set -e

# Set default port if not provided by Railway
PORT="${PORT:-8080}"

# Dynamic Apache configuration for the Railway assigned port
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Force disable conflicting MPMs by removing their symlinks and enabling prefork
rm -f /etc/apache2/mods-enabled/mpm_event.load
rm -f /etc/apache2/mods-enabled/mpm_event.conf
rm -f /etc/apache2/mods-enabled/mpm_worker.load
rm -f /etc/apache2/mods-enabled/mpm_worker.conf
ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load
ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

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
