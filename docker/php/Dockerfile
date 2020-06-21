FROM php:7.2-apache

# install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get update \
  && apt-get install zip unzip

WORKDIR /var/www
