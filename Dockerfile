FROM php:8.2-apache

RUN a2enmod rewrite

COPY . .

RUN chown -R www-data:www-data .

EXPOSE 80
