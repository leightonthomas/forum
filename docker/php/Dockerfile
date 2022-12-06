FROM composer:2.4 AS composer
FROM php:8.1-fpm

ENV TZ=Europe/London

RUN apt-get update && apt-get install -y \
        libzip-dev \
        libonig-dev \
        git

RUN apt-get update \
    && docker-php-ext-install sockets \
    && docker-php-source delete

RUN docker-php-ext-install \
        zip \
        pdo_mysql \
        opcache \
        bcmath \
        calendar \
        pcntl

RUN apt-get update && apt-get install -y \
        libcurl4-openssl-dev \
    && docker-php-ext-install curl \
    && docker-php-source delete

RUN apt-get update && apt-get install -y \
        libxml2-dev \
        libxslt-dev \
    && docker-php-ext-install xml soap xsl \
    && docker-php-source delete

RUN apt-get update && apt-get install -y \
        zlib1g-dev \
        libicu-dev \
        libpq-dev \
    && docker-php-ext-install intl \
    && docker-php-source delete

RUN docker-php-ext-install pdo pdo_mysql zip
RUN pecl install ast

COPY --from=composer "/usr/bin/composer" "/usr/bin/composer"

COPY php.ini /usr/local/etc/php/php.ini
RUN usermod -u 1000 www-data
