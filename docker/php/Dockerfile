FROM php:8.3-fpm-alpine3.18

RUN apk update \
    && apk add \
    openssl\
    bash \
    vim \
    zip \
    unzip \
    libzip-dev \
    curl \
    git \
    unzip \
    nodejs \
    npm \
    yarn \
    && apk add libpq-dev && docker-php-ext-install pdo_pgsql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer