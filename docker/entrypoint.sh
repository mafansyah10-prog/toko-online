#!/bin/bash
set -e

# Clear caches first just in case
php artisan optimize:clear

# Run database migrations
php artisan migrate --force

# Optimize Laravel for production
php artisan optimize
php artisan filament:optimize

# Execute Apache foreground
exec "$@"
