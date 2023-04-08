FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    libjpeg-dev \
    libpng-dev \
    libxml2-dev \
    libicu-dev \
    libcurl4-openssl-dev \
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
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite
RUN a2enmod deflate
RUN a2enmod headers

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

RUN chown -R www-data:www-data /var/www/html
RUN find /var/www/html -type d -exec chmod 755 {} \;
RUN find /var/www/html -type f -exec chmod 644 {} \;
RUN chmod 755 /var/www/html/bin/console

WORKDIR /var/www/html

EXPOSE 80