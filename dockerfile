FROM php:8.0-apache

EXPOSE 8090

WORKDIR /code/forma/Projet-php

COPY php/ .

RUN docker-php-ext-install mysqli pdo pdo_mysql

CMD ["apache2-foreground"]
