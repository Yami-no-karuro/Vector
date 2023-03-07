FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    libjpeg-dev \
    libpng-dev \
    libxml2-dev \
    libicu-dev \
    libcurl4-openssl-dev \
    redis-tools \
    && docker-php-ext-install \
    pdo_mysql \
    mysqli \
    mbstring \
    zip \
    gd \
    bcmath \
    opcache \
    exif \
    pcntl \
    intl \
    xml \
    curl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite
RUN a2enmod deflate
RUN a2enmod headers

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

EXPOSE 80
