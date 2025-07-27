FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    zip unzip git curl libpq-dev libzip-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer el directorio de trabajo
WORKDIR /var/www/html
