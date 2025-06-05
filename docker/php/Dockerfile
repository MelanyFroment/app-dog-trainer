FROM php:8.3-fpm-alpine3.18 AS base

ARG PHP_EXTENSIONS="pdo_pgsql zip opcache"
ARG BUILD_DEPS="curl libzip-dev oniguruma postgresql-dev unzip"
ARG SYSTEM_LIBRARIES="libzip libpq oniguruma"
ARG APP_DIR=/www/symfony

FROM base AS builder

ENV COMPOSER_HOME=/tmp/composer

WORKDIR ${APP_DIR}

RUN set -ex \
    && echo "==> Installing system dependencies..." \
    && apk update \
    && apk add --no-cache --virtual .build-deps \
       $(echo "$BUILD_DEPS $PHPIZE_DEPS" | tr ' ' '\n' | sort | tr '\n' ' ') \
    \
    && echo "==> Installing PHP extensions..." \
    && docker-php-ext-install ${PHP_EXTENSIONS} \
    \
    && echo "==> Installing Composer..." \
    && curl -sS https://getcomposer.org/installer | php -- \
       --install-dir=/usr/local/bin\
       --filename=composer \
    \
    && echo "==> Cleanning up..." \
    && apk del .build-deps ${PHPIZE_DEPS} \
    && rm -rf /var/cache/apk/*

# Install dependencies
COPY symfony/composer.* ./
RUN composer install --no-dev --optimize-autoloader --prefer-dist \
    || (echo "==> Composer installation failed" \ && cat var/log/*.log || true)

COPY symfony ./

FROM base AS production
WORKDIR ${APP_DIR}

# Install runtime dependencies
RUN echo "==> Setting up production environment..." \
    && apk update \
    && apk add --no-cache ${SYSTEM_LIBRARIES} \
    && rm -rf /var/cache/apk/* \
    \
    && echo "==> Creating directory structure..." \
    && mkdir -p /www/symfony/var /www/symfony/public \
    && chown -R www-data:www-data /www/symfony/var /www/symfony/public

# Copy artifacts from builder
COPY --from=builder /usr/local/bin/composer /usr/local/bin/composer
COPY --from=builder /www/symfony /www/symfony

# Production configuration
# USER www-data # Uncomment in production
CMD ["php-fpm"]
EXPOSE 9000