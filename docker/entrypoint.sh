#!/bin/bash
set -e

# Ensure PORT is 8080 if not provided
PORT=${PORT:-8080}

# Replace port in Apache configuration at runtime
sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Clear caches first just in case
php artisan optimize:clear

# Run database migrations
php artisan migrate --force

# Optimize Laravel for production
php artisan optimize
php artisan filament:optimize

# Execute Apache foreground
exec "$@"
