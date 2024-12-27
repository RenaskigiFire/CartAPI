FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libicu-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl opcache

RUN pecl install xdebug

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html/cart-api

COPY .docker/php.ini /usr/local/etc/php/

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
