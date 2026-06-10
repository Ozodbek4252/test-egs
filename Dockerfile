FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
        nodejs npm \
        libpng-dev libzip-dev zip unzip curl \
    && docker-php-ext-install pdo_sqlite pdo zip gd opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader \
    && npm ci \
    && npm run build \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
