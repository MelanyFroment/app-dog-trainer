FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    default-mysql-client \
    && docker-php-ext intl pdo pdo-mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY ./symfony /var/www/html

RUN composer install --no-interaction --optimize-autoloader --no-dev

RUN chown -R www-data:www-data /var/www/html/symfony/var /var/www/html/symfony/vendor

EXPOSE 9000

CMD [ "php-fpm" ]