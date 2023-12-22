FROM php:8.2-fpm

RUN apt-get update \
  && apt-get upgrade -y \
  && apt-get install -y libzip-dev libpng-dev libjpeg-dev pdftk-java \
  && pecl install -o -f redis xdebug \
  && rm -rf /tmp/pear \
  && docker-php-ext-install mysqli pdo pdo_mysql zip exif gd \
  && docker-php-ext-enable redis xdebug

COPY --from=composer /usr/bin/composer /usr/bin/composer
