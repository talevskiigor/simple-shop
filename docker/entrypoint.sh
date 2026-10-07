#!/bin/sh
set -eu

if [ -n "${LOCAL_GID:-}" ] && [ "$(id -g www-data)" != "$LOCAL_GID" ]; then
    groupmod -o -g "$LOCAL_GID" www-data
fi
if [ -n "${LOCAL_UID:-}" ] && [ "$(id -u www-data)" != "$LOCAL_UID" ]; then
    usermod -o -u "$LOCAL_UID" www-data
fi
mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
exec docker-php-entrypoint "$@"
