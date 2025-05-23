FROM php:8.3-fpm-alpine3.18 AS base

FROM base AS builder

ENV COMPOSER_HOME=/tmp/composer

WORKDIR /www/symfony

RUN apk update \
    && apk add --no-cache --virtual .build-deps \
       curl libzip-dev unzip postgresql-dev oniguruma $PHPIZE_DEPS \
    && docker-php-ext-install pdo_pgsql zip opcache \
    && curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php \
    && apk del .build-deps $PHPIZE_DEPS \
    && rm -rf /var/cache/apk/*

COPY symfony/composer.json symfony/composer.lock ./
RUN composer install --no-dev --optimize-autoloader --prefer-dist || (echo "--- Composer Error Output ---" && cat var/log/*.log || true)

COPY symfony ./

FROM base AS production
WORKDIR /www/symfony

# Install runtime PHP extensions only
RUN apk update \
    && apk add --no-cache \
       libzip libpq oniguruma \
    && rm -rf /var/cache/apk/*

# Copy artifacts from builder
COPY --from=builder /usr/local/bin/composer /usr/local/bin/composer
COPY --from=builder /www/symfony /www/symfony

RUN chown -R www-data:www-data /www/symfony/var /www/symfony/public

# USER www-data # Uncomment in production
CMD ["php-fpm"]
EXPOSE 9000