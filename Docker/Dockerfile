FROM php:7.4.33-apache

RUN docker-php-ext-install pdo_mysql

RUN echo "deb http://archive.debian.org/debian stretch main contrib non-free" > /etc/apt/sources.list


RUN apt-get update && \
    apt-get install -y zip unzip --force-yes && \
    rm -rf /var/lib/apt/lists/*

RUN echo "date.timezone = Europe/Paris" > /usr/local/etc/php/php.ini

COPY Docker/apache2.conf /etc/apache2/apache2.conf

COPY Docker/apache.conf /etc/apache2/sites-available/000-default.conf

COPY --from=composer:2.2 /usr/bin/composer /usr/local/bin/composer

COPY composer.json composer.lock /var/www/html/

COPY . /var/www/html

WORKDIR /var/www/html

RUN a2enmod rewrite

RUN chown -R www-data:www-data /var/www/html/var

RUN chmod -R 775 /var/www/html/var/logs /var/www/html/var/cache

ENV SYMFONY_ENV=prod

ENV database_host=127.0.0.1

ENV database_port=3306

ENV database_name=todo_and_co

ENV database_user=root

ENV database_password=example

ENV secret=346fefezd364554gfvrf5634FGGVRFdd

EXPOSE 80

CMD ["apache2-foreground"]
