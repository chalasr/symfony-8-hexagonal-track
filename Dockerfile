FROM dunglas/frankenphp:php8.4-bookworm

WORKDIR /app

RUN install-php-extensions \
        pdo_sqlite \
        opcache \
        intl \
        zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Composer manifest first for layer cache. Dev deps are kept so attendees
# can run `bin/phpunit` and `deptrac` inside the container.
COPY composer.json composer.lock ./
RUN composer install \
        --no-scripts \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader

COPY . .
RUN composer dump-autoload --optimize \
 && mkdir -p var \
 && chmod -R 0777 var

ENV APP_ENV=dev \
    APP_DEBUG=0 \
    SERVER_NAME=:80

EXPOSE 80

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
