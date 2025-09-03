FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    unzip libzip-dev \
    icu-dev \
    oniguruma-dev \
    libsodium-dev \
    && docker-php-ext-install \
    zip \
    intl \
    bcmath \
    mbstring \
    opcache \
    sodium \
    && docker-php-ext-configure \
    zip \
    && docker-php-ext-enable \
    opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
