FROM php:8.3.4-apache

RUN docker-php-ext-install pdo_mysql

RUN echo "deb http://archive.debian.org/debian stretch main contrib non-free" > /etc/apt/sources.list


RUN apt-get update && \
    apt-get install -y zip unzip --force-yes && \
    rm -rf /var/lib/apt/lists/*

RUN echo "date.timezone = Europe/Paris" > /usr/local/etc/php/php.ini

COPY Docker/apache2.conf /etc/apache2/apache2.conf

COPY Docker/apache.conf /etc/apache2/sites-available/000-default.conf

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY composer.json composer.lock /var/www/html/

COPY . /var/www/html

WORKDIR /var/www/html

RUN a2enmod rewrite

# RUN chown -R 775 www-data:www-data /var/www/html/var

# RUN chmod -R 775 /var/logs /var/www/html/var/cache

ENV APP_ENV=prod

EXPOSE 80

CMD ["apache2-foreground"]
