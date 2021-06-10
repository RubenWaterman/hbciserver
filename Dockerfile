FROM php:7.3-apache
LABEL maintainer="Cosmin Harangus <cosmin@around25.com>"

WORKDIR /var/www/html/

COPY config/vhost.conf /etc/apache2/sites-available/000-default.conf

# Install the gmp and mcrypt extensions
RUN apt-get update -y
RUN apt-get install -y git unzip

RUN curl -sS https://getcomposer.org/installer | php

# Copy all dependencies files
COPY config/date.ini $PHP_INI_DIR/conf.d/
WORKDIR /var/www/html/fints/
COPY ./src/fints/composer.json .

WORKDIR /var/www/html/fints/
RUN php /var/www/html/composer.phar install --no-plugins --no-scripts

COPY ./src/fints /var/www/html/fints
