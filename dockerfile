FROM php:8.3-apache

# Extensión mysqli que usa CodeIgniter para conectarse a la BD
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

COPY . /var/www/html/