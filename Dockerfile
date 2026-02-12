FROM php:8.2-apache

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Instalar Composer dentro del contenedor
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar proyecto
COPY . /var/www/html/

WORKDIR /var/www/html/

# Instalar dependencias (PHPMailer)
RUN composer install

RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    pkg-config \
    && docker-php-ext-install curl


EXPOSE 80

