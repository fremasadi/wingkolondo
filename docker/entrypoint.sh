#!/bin/sh
set -eu

cd /var/www/html

mkdir -p \
    storage/app/public \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache

if [ ! -L public/storage ]; then
    rm -rf public/storage
    gosu www-data php artisan storage:link
fi

gosu www-data php artisan config:cache
gosu www-data php artisan route:cache
gosu www-data php artisan view:cache

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    gosu www-data php artisan migrate --force
fi

if [ "$1" = "apache2-foreground" ]; then
    exec "$@"
fi

exec gosu www-data "$@"

