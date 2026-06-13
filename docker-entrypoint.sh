#!/usr/bin/env sh
set -e

# Create schema on first boot; harmless if file already exists.
if [ ! -f var/data.db ]; then
    php bin/console doctrine:schema:create --no-interaction || true
fi

exec "$@"
