FROM php:8.2-apache

RUN apt-get update && apt-get install -y libonig-dev libzip-dev libjpeg-dev libpng-dev libxml2-dev libicu-dev libcurl4-openssl-dev cron
RUN docker-php-ext-install pdo_mysql mysqli mbstring zip gd bcmath opcache exif pcntl intl xml curl
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./.docker/cron.txt /etc/cron.d/cron
RUN chmod 644 /etc/cron.d/cron
RUN crontab /etc/cron.d/cron

COPY ./* /var/www/html

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

RUN chown -R www-data:www-data /var/www/html
RUN find /var/www/html -type d -exec chmod 755 {} \;
RUN find /var/www/html -type f -exec chmod 644 {} \;

RUN a2enmod rewrite
RUN a2enmod deflate
RUN a2enmod headers

WORKDIR /var/www/html
