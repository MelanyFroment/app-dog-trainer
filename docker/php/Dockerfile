FROM php:8.3-fpm-alpine3.18 AS base

ARG PHP_EXTENSIONS="pdo_pgsql zip opcache"
ARG BUILD_DEPS="curl libzip-dev oniguruma postgresql-dev unzip autoconf g++ make linux-headers"
ARG SYSTEM_LIBRARIES="libzip libpq oniguruma"
ARG APP_DIR=/www/symfony

RUN apk update \
    && apk add --no-cache ${SYSTEM_LIBRARIES} \
    && apk add --no-cache --virtual .build-deps ${BUILD_DEPS} $PHPIZE_DEPS \
    \
    && docker-php-ext-install ${PHP_EXTENSIONS} \
    \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && curl -sS https://getcomposer.org/installer \
        | php -- --install-dir=/usr/local/bin --filename=composer \
    \
    && apk del .build-deps $PHPIZE_DEPS \
    && rm -rf /var/cache/apk/*

FROM base AS production
WORKDIR ${APP_DIR}

RUN echo "==> Setting up production environment..." \
    && rm -rf /var/cache/apk/* \
    && mkdir -p /www/symfony/var /www/symfony/public \
    && chown -R www-data:www-data /www/symfony/var /www/symfony/public

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
EXPOSE 9000
