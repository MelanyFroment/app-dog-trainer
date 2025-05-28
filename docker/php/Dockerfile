FROM php:8.3-fpm-alpine3.18 AS base

FROM base AS builder

ENV COMPOSER_HOME=/tmp/composer

WORKDIR /www/symfony

ARG BUILD_DEPS="curl libzip-dev unzip postgresql-dev oniguruma"
ARG PHP_EXTENSIONS="pdo_pgsql zip opcache"

RUN set ex \
    # Update system
    && apk update \
    # Install temporaries deps
    && apk add --no-cache --virtual .build-deps $BUILD_DEPS $PHPIZE_DEPS \
    # Install PHP extensions
    && docker-php-ext-install $PHP_EXTENSIONS \
    # Install Composer
    && curl -sS https://getcomposer.org/installer | php -- \
       --install-dir=/usr/local/bin\
       --filename=composer \
    # Clean
    && apk del .build-deps $PHPIZE_DEPS \
    && rm -rf /var/cache/apk/*

COPY symfony/composer.json symfony/composer.lock ./
RUN composer install --no-dev --optimize-autoloader --prefer-dist || (echo "--- Composer Error Output ---" && cat var/log/*.log || true)

COPY symfony ./

FROM base AS production
WORKDIR /www/symfony

ARG SYSTEM_LIBRARIES="libzip libpq oniguruma"

# Install runtime PHP extensions only
RUN apk update \
    && echo "Install system libraries..." \
    && apk add --no-cache $SYSTEM_LIBRARIES \
    && echo "Clean cache..." \
    && rm -rf /var/cache/apk/* \
    && echo "Installation completed"

# Copy artifacts from builder
COPY --from=builder /usr/local/bin/composer /usr/local/bin/composer
COPY --from=builder /www/symfony /www/symfony

RUN mkdir -p /www/symfony/var /www/symfony/public \
    && chown -R www-data:www-data /www/symfony/var /www/symfony/public

# USER www-data # Uncomment in production
CMD ["php-fpm"]
EXPOSE 9000