#!/bin/sh
set -e

# Fix storage and cache permissions on every startup so www-data can write
# (needed because the host volume mount overrides the image's chown)
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/database
chmod -R 775 /var/www/storage /var/www/bootstrap/cache
chmod 664 /var/www/database/database.sqlite 2>/dev/null || true

exec docker-php-entrypoint "$@"
