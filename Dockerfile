FROM php:8.2-apache

RUN a2enmod rewrite

RUN echo '<Directory "/var/www/html/my-files">\n    AllowOverride Options\n</Directory>' >> /etc/apache2/apache2.conf

COPY . .

RUN chown -R www-data:www-data .

EXPOSE 80
