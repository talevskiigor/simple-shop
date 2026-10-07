FROM composer:2 AS composer
FROM php:8.3-apache-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip default-mysql-client libfreetype6-dev libjpeg62-turbo-dev \
    libpng-dev libwebp-dev libzip-dev libicu-dev libonig-dev libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" bcmath exif gd intl mbstring pdo_mysql pdo_sqlite pcntl zip \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY docker/apache/site.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/store.ini
COPY docker/entrypoint.sh /usr/local/bin/store-entrypoint
RUN chmod +x /usr/local/bin/store-entrypoint
WORKDIR /var/www/html
ENTRYPOINT ["store-entrypoint"]
CMD ["apache2-foreground"]
