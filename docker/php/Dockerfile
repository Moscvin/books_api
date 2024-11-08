FROM php:8.2.24-fpm

# Install dependencies
RUN apt-get -y update && apt-get upgrade -y 
RUN docker-php-ext-install pdo pdo_mysql && docker-php-ext-enable pdo_mysql

RUN pecl install xdebug-3.2.0 && docker-php-ext-enable xdebug

COPY docker/php/conf.d/* $PHP_INI_DIR/conf.d/

RUN pecl install redis \
&& rm -rf /tmp/pear \
&& docker-php-ext-enable redis

CMD ["php-fpm"] 