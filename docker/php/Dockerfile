FROM php:8.2-fpm-alpine

# System deps
RUN apk add --no-cache \
    bash git \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    curl-dev

# PHP extensions required by Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
        opcache \
        zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Basic PHP tuning
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini \
    && echo "upload_max_filesize=32M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=32M" >> /usr/local/etc/php/conf.d/uploads.ini

CMD ["php-fpm"]